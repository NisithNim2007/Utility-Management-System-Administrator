<?php
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

    $SENTINEL = 2147483647;
    $slabEndRaw = trim($_POST['SlabEnd'] ?? '');
    if ($slabEndRaw === '') {
        $slabEnd = $SENTINEL;   // store sentinel instead of NULL
    } else {
        $slabEnd = intval($slabEndRaw);
    }

    $rate = isset($_POST['RatePerUnit']) ? floatval($_POST['RatePerUnit']) : null;
    $fixed = isset($_POST['FixedCharge']) ? floatval($_POST['FixedCharge']) : 0.0;

    // basic validations
    if ($tariffPlanID <= 0) abortWith("TariffPlanID missing.");
    if (!is_numeric($rate) || $rate < 0) abortWith("Rate per unit must be a non-negative number.");
    if ($slabStart < 0) abortWith("Slab start must be >= 0.");
    if ($slabEnd !== null && $slabEnd < $slabStart) abortWith("Slab end must be empty or >= slab start.");

    // fetch existing slabs for plan ordered by SlabStart asc and also find last slab
    $existing = executeQuery($pdo, "SELECT RateID, SlabStart, SlabEnd FROM TariffRates WHERE TariffPlanID = ? ORDER BY ISNULL(SlabStart,0) ASC", [$tariffPlanID]);

    // If there are existing slabs, perform overlap check (but we will allow special behavior when last slab is sentinel)
    // Determine last slab (highest SlabStart)
    $last = null;
    foreach ($existing as $row) {
        $last = $row; // after loop, $last is the last row
    }

    // Helper to convert DB slabEnd to numeric (PHP_INT_MAX if sentinel or NULL? we treat sentinel specially)
    $SENTINEL = 2147483647;

    // If last exists and last.SlabEnd is sentinel (or null?), allow special update of last slab end
    if ($last !== null) {
        $lastStart = intval($last['SlabStart']);
        $lastEndRaw = $last['SlabEnd'];
        $lastIsSentinel = ($lastEndRaw !== null && is_numeric($lastEndRaw) && intval($lastEndRaw) === $SENTINEL);

        // If last is sentinel, ensure new slabStart > lastStart
        if ($lastIsSentinel) {
            if ($slabStart <= $lastStart) {
                abortWith("New slab start must be greater than the existing final slab start ({$lastStart}).");
            }
            // We'll set previous slab's SlabEnd = newStart - 1. Validate that newStart -1 >= lastStart
            $newPrevEnd = $slabStart - 1;
            if ($newPrevEnd < $lastStart) {
                abortWith("Calculated previous slab end ({$newPrevEnd}) is invalid (must be >= {$lastStart}).");
            }

            // Update previous slab in DB before inserting new slab (in transaction ideally)
            // Use a transaction for safety
            $pdo->beginTransaction();
            try {
                // Update previous slab
                $upd = executeNonQuery($pdo, "UPDATE TariffRates SET SlabEnd = ?, UpdatedTime = SYSUTCDATETIME() WHERE RateID = ?", [$newPrevEnd, $last['RateID']]);
                if (strpos($upd, 'Databse error') === 0) {
                    $pdo->rollBack();
                    abortWith($upd);
                }

                // Now insert the new slab using stored proc (which will check overlaps)
                $ins = executeNonQuery($pdo, "EXEC dbo.sp_AddTariffRate ?, ?, ?, ?, ?", [$tariffPlanID, $slabStart, $slabEnd, $rate, $fixed]);
                if (strpos($ins, 'Databse error') === 0) {
                    // rollback and abort
                    $pdo->rollBack();
                    abortWith($ins);
                }

                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                abortWith("Database error while updating previous slab and inserting new slab: " . $e->getMessage());
            }

            // log and redirect
            $detail = "Adjusted previous RateID {$last['RateID']} SlabEnd to {$newPrevEnd} and inserted new slab {$slabStart} - " . ($slabEnd===null ? 'Over' : $slabEnd) . " for plan {$tariffPlanID}";
            executeNonQuery($pdo, "INSERT INTO AuditLogs (PersonID, Action, Details) VALUES (NULL, 'TariffRate-ADD-ADJ', ?)", [$detail]);

            $_SESSION['tariff_msg'] = "New slab added; previous final slab end adjusted.";
            header("Location: Tariff1.php");
            exit;
        } // end lastIsSentinel handling

        // If last is not sentinel, do normal overlap validation across existing slabs
        foreach ($existing as $row) {
            $es = intval($row['SlabStart']);
            $ee = $row['SlabEnd'] === null ? PHP_INT_MAX : intval($row['SlabEnd']);
            $newE = $slabEnd === null ? PHP_INT_MAX : $slabEnd;
            if (!($newE < $es || $slabStart > $ee)) {
                abortWith("New slab overlaps existing slab: {$es} - " . ($ee===PHP_INT_MAX ? 'Over' : $ee));
            }
        }
    } // end if last !== null

    // If no existing rows or non-sentinel path validated, just insert normally
    $res = executeNonQuery($pdo, "EXEC dbo.sp_AddTariffRate ?, ?, ?, ?, ?", [$tariffPlanID, $slabStart, $slabEnd, $rate, $fixed]);
    if (strpos($res, 'Databse error') === 0) abortWith($res);

    // audit log
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
