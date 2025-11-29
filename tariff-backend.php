<?php
// tariff-backend.php
session_start();
if(!isset($_SESSION['Username'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/include/db.php'; // makes $pdo and helper functions available

// Helper: redirect back with message (simple, no flash lib)
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
        // required params
        $tariffPlanID = intval($_POST['TariffPlanID'] ?? 0);
        $slabStart = intval($_POST['SlabStart'] ?? 0);
        $slabEndRaw = trim($_POST['SlabEnd'] ?? '');
        $slabEnd = ($slabEndRaw === '') ? null : intval($slabEndRaw);
        $rate = floatval($_POST['RatePerUnit'] ?? 0);
        $rateUnitID = intval($_POST['RateUnitID'] ?? 0);
        $fixed = floatval($_POST['FixedCharge'] ?? 0);

        // Basic server-side checks
        if ($tariffPlanID <= 0) abortWith("TariffPlanID missing.");
        if ($slabStart < 0) abortWith("SlabStart invalid.");
        if ($slabEnd !== null && $slabEnd < $slabStart) abortWith("SlabEnd must be >= SlabStart.");
        if ($rate < 0) abortWith("Rate must be >= 0.");

        // Insert (temporarily insert into DB, but we first call validation - approach: insert in a transaction and call sp_ValidateSlabsForPlan then rollback if problem)
        // Simpler: check overlaps by loading existing slabs for plan
        $existing = executeQuery($pdo, "SELECT RateID, SlabStart, SlabEnd FROM TariffRates WHERE TariffPlanID = ?", [$tariffPlanID]);

        // overlap detect
        foreach ($existing as $row) {
            $es = intval($row['SlabStart']);
            $ee = $row['SlabEnd'] === null ? PHP_INT_MAX : intval($row['SlabEnd']);
            $newE = $slabEnd === null ? PHP_INT_MAX : $slabEnd;

            if (!($newE < $es || $slabStart > $ee)) {
                abortWith("New slab overlaps existing slab: {$es} - " . ($ee===PHP_INT_MAX ? 'Over' : $ee));
            }
        }

        // safe to insert
        $res = executeQuery($pdo, "EXEC dbo.sp_AddTariffRate ?, ?, ?, ?, ?, ?", [$tariffPlanID, $slabStart, $slabEnd, $rate, $rateUnitID, $fixed]);
        $_SESSION['tariff_msg'] = "Slab added successfully.";
        header("Location: Tariff1.php");
        exit;
    }

    elseif ($action === 'update_slab') {
        $rateID = intval($_POST['RateID'] ?? 0);
        $slabStart = intval($_POST['SlabStart'] ?? 0);
        $slabEndRaw = trim($_POST['SlabEnd'] ?? '');
        $slabEnd = ($slabEndRaw === '') ? null : intval($slabEndRaw);
        $rate = floatval($_POST['RatePerUnit'] ?? 0);
        $rateUnitID = intval($_POST['RateUnitID'] ?? 0);
        $fixed = floatval($_POST['FixedCharge'] ?? 0);
        $tariffPlanID = intval($_POST['TariffPlanID'] ?? 0);

        if ($rateID <= 0) abortWith("RateID missing.");
        if ($slabEnd !== null && $slabEnd < $slabStart) abortWith("SlabEnd must be >= SlabStart.");

        // fetch existing plan id for the rate
        $row = executeQuery($pdo, "SELECT TariffPlanID FROM TariffRates WHERE RateID = ?", [$rateID], true);
        if (!$row) abortWith("Rate not found.");

        // check overlaps excluding the current RateID
        $existing = executeQuery($pdo, "SELECT RateID, SlabStart, SlabEnd FROM TariffRates WHERE TariffPlanID = ? AND RateID <> ?", [$row['TariffPlanID'], $rateID]);
        foreach ($existing as $er) {
            $es = intval($er['SlabStart']);
            $ee = $er['SlabEnd'] === null ? PHP_INT_MAX : intval($er['SlabEnd']);
            $newE = $slabEnd === null ? PHP_INT_MAX : $slabEnd;
            if (!($newE < $es || $slabStart > $ee)) {
                abortWith("Updated slab would overlap existing slab: {$es} - " . ($ee===PHP_INT_MAX ? 'Over' : $ee));
            }
        }

        // update
        $res = executeNonQuery($pdo, "EXEC dbo.sp_UpdateTariffRate ?, ?, ?, ?, ?, ?", [$rateID, $slabStart, $slabEnd, $rate, $rateUnitID, $fixed]);
        $_SESSION['tariff_msg'] = "Slab updated successfully.";
        header("Location: Tariff1.php");
        exit;
    }

    elseif ($action === 'delete_slab') {
        $rateID = intval($_POST['RateID'] ?? 0);
        if ($rateID <= 0) abortWith("RateID missing.");

        // delete
        $res = executeNonQuery($pdo, "EXEC dbo.sp_DeleteTariffRate ?", [$rateID]);
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
