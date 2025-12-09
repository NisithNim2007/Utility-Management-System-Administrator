<?php
// tariff-backend.php
session_start();

if(!isset($_SESSION['Username'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/include/db.php'; // makes $pdo and helper functions available

function abortWith($msg) {
    $_SESSION['tariff_msg'] = $msg;
    header("Location: Tariff1.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    abortWith("Invalid request method.");
}

$action = $_POST['action'] ?? '';
$username = $_SESSION['Username'] ?? null;

try {
    if ($action === 'add_slab') {
        $tariffPlanID = intval($_POST['TariffPlanID'] ?? 0);
        $slabStart = intval($_POST['SlabStart'] ?? 0);
        $slabEndRaw = trim($_POST['SlabEnd'] ?? '');
        $slabEnd = ($slabEndRaw === '') ? null : intval($slabEndRaw);
        $rate = isset($_POST['RatePerUnit']) ? floatval($_POST['RatePerUnit']) : null;
        $fixed = isset($_POST['FixedCharge']) ? floatval($_POST['FixedCharge']) : 0.0;

        // basic validations
        if ($tariffPlanID <= 0) abortWith("TariffPlanID missing.");
        if (!is_numeric($rate) || $rate < 0) abortWith("Rate per unit must be a non-negative number.");
        if ($slabStart < 0) abortWith("Slab start must be >= 0.");
        if ($slabEnd !== null && $slabEnd < $slabStart) abortWith("Slab end must be empty or >= slab start.");

        // overlap check (server-side)
        $existing = executeQuery($pdo, "SELECT RateID, SlabStart, SlabEnd FROM TariffRates WHERE TariffPlanID = ?", [$tariffPlanID]);
        foreach ($existing as $row) {
            $es = intval($row['SlabStart']);
            $ee = $row['SlabEnd'] === null ? PHP_INT_MAX : intval($row['SlabEnd']);
            $newE = $slabEnd === null ? PHP_INT_MAX : $slabEnd;
            if (!($newE < $es || $slabStart > $ee)) {
                abortWith("New slab overlaps existing slab: {$es} - " . ($ee===PHP_INT_MAX ? 'Over' : $ee));
            }
        }

        // insert via stored proc
        $res = executeNonQuery($pdo, "EXEC dbo.sp_AddTariffRate ?, ?, ?, ?, ?", [$tariffPlanID, $slabStart, $slabEnd, $rate, $fixed]);
        if (strpos($res, 'Databse error') === 0) abortWith($res);

        // audit log (optional): use AuditLogs table
        $detail = "Added slab to plan {$tariffPlanID}: {$slabStart} - " . ($slabEnd===null ? 'Over' : $slabEnd) . " @{$rate} fixed={$fixed}";
        executeNonQuery($pdo, "INSERT INTO AuditLogs (PersonID, Action, Details) VALUES (NULL, 'TariffRate-ADD', ?)", [$detail]);

        $_SESSION['tariff_msg'] = "Slab added successfully.";
        header("Location: Tariff1.php");
        exit;
    }


    // ---------- Replace update_slab block with this ----------
    elseif ($action === 'update_slab') {
        $rateID = intval($_POST['RateID'] ?? 0);
        $rate = isset($_POST['RatePerUnit']) ? floatval($_POST['RatePerUnit']) : null;
        $fixed = isset($_POST['FixedCharge']) ? floatval($_POST['FixedCharge']) : null;

        if ($rateID <= 0) abortWith("RateID missing.");
        if ($rate === null || $rate < 0) abortWith("Rate must be provided and >= 0.");
        if ($fixed === null || $fixed < 0) abortWith("Fixed charge must be provided and >= 0.");

        // Confirm the RateID exists
        $exists = executeQuery($pdo, "SELECT RateID FROM TariffRates WHERE RateID = ?", [$rateID], true);
        if (!$exists) abortWith("Rate not found.");

        // Use the safe proc (updates only values) - fallback: do direct UPDATE if proc missing
        try {
            $res = executeNonQuery($pdo, "EXEC dbo.sp_UpdateTariffRateValues ?, ?, ?", [$rateID, $rate, $fixed]);
            if (strpos($res, 'Databse error') === 0) { // your helper returns strings on error
                throw new Exception($res);
            }
        } catch (Exception $e) {
            // If the proc doesn't exist, fallback to direct UPDATE:
            $sql = "UPDATE TariffRates SET RatePerUnit = :r, FixedCharge = :f, UpdatedTime = SYSUTCDATETIME() WHERE RateID = :rid";
            $res2 = executeNonQuery($pdo, $sql, [':r'=>$rate, ':f'=>$fixed, ':rid'=>$rateID]);
            if (strpos($res2, 'Databse error') === 0) throw new Exception($res2);
        }

        $_SESSION['tariff_msg'] = "Tariff values updated successfully.";
        header("Location: Tariff1.php");
        exit;
    }



    elseif ($action === 'delete_slab') {
        // backend safety delete (UI will no longer show delete)
        $rateID = intval($_POST['RateID'] ?? 0);
        if ($rateID <= 0) abortWith("RateID missing.");
        $res = executeNonQuery($pdo, "EXEC dbo.sp_DeleteTariffRate ?", [$rateID]);
        if (strpos($res, 'Databse error') === 0) abortWith($res);
        executeNonQuery($pdo, "INSERT INTO AuditLogs (PersonID, Action, Details) VALUES (NULL, 'TariffRate-DELETE', ?)", ["Deleted RateID {$rateID}"]);
        $_SESSION['tariff_msg'] = "Slab deleted.";
        header("Location: Tariff1.php");
        exit;
    }

    else {
        abortWith("Unknown action.");
    }
} catch (Exception $ex) {
    abortWith("Server error: " . $ex->getMessage());
}
