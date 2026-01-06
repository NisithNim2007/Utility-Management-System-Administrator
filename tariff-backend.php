<?php
session_start();

if(!isset($_SESSION['Username'])) {
    header("Location: ../login.php");
    exit;
}

require_once __DIR__ . '/include/db.php'; 

function dbIsError($res) {
    if (is_string($res)) {
        return str_starts_with($res, 'Databse error') || str_starts_with(strtolower($res), 'database error');
    }
    if (is_array($res)) {
        return (isset($res['status']) && strtolower($res['status']) === 'error');
    }
    return false;
}

function dbErrorMessage($res) {
    if (is_string($res)) return $res;
    if (is_array($res)) return $res['error'] ?? json_encode($res);
    return 'Unknown DB error';
}
function dbIsSuccess($res) {
    if (is_string($res)) return strtolower($res) === 'success';
    if (is_array($res)) return !isset($res['status']) || strtolower($res['status']) !== 'error';
    return false;
}

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
        $slabEnd = $SENTINEL;
    } else {
        $slabEnd = intval($slabEndRaw);
    }

    $rate = isset($_POST['RatePerUnit']) ? floatval($_POST['RatePerUnit']) : null;
    $fixed = isset($_POST['FixedCharge']) ? floatval($_POST['FixedCharge']) : 0.0;

    error_log("DEBUG add_slab called by user: " . ($username ?? 'unknown'));
    error_log("DEBUG POST: " . json_encode($_POST));
    error_log("DEBUG parsed: tariffPlanID={$tariffPlanID}, slabStart={$slabStart}, slabEnd={$slabEnd}, rate={$rate}, fixed={$fixed}");error_log("DEBUG add_slab called by user: " . ($username ?? 'unknown'));
    error_log("DEBUG POST: " . json_encode($_POST));
    error_log("DEBUG parsed: tariffPlanID={$tariffPlanID}, slabStart={$slabStart}, slabEnd={$slabEnd}, rate={$rate}, fixed={$fixed}");

    if ($tariffPlanID <= 0) abortWith("TariffPlanID missing.");
    if (!is_numeric($rate) || $rate < 0) abortWith("Rate per unit must be a non-negative number.");
    if ($slabStart < 0) abortWith("Slab start must be >= 0.");
    if ($slabEnd !== null && $slabEnd <= $slabStart) abortWith("Slab end must be empty or greater than slab start.");

    $existing = executeQuery($pdo, "SELECT RateID, SlabStart, SlabEnd FROM TariffRates WHERE TariffPlanID = ? ORDER BY ISNULL(SlabStart,0) ASC", [$tariffPlanID]);

    $last = null;
    foreach ($existing as $row) {
        $last = $row;
    }

    if ($last !== null) {
        $lastRate = executeQuery(
            $pdo,
            "SELECT RatePerUnit, FixedCharge FROM TariffRates WHERE RateID = ?",
            [$last['RateID']],
            true
        );

        if ($lastRate) {
            if ($rate < floatval($lastRate['RatePerUnit'])) {
                abortWith("Rate per unit must be greater than or equal to the previous slab's rate.");
            }

            if ($fixed < floatval($lastRate['FixedCharge'])) {
                abortWith("Fixed charge must be greater than or equal to the previous slab's fixed charge.");
            }
        }
    }

if ($last !== null) {
        $prevEndRaw = $last['SlabEnd'];

        if (
            $prevEndRaw !== null &&
            intval($prevEndRaw) !== $SENTINEL &&   // 👈 CRITICAL FIX
            $slabStart <= intval($prevEndRaw)
        ) {
            abortWith(
                "Slab start must be greater than the previous slab end ({$prevEndRaw})."
            );
        }
    }

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
                if (dbIsError($upd)) {
                    // log, rollback and abort with DB error message
                    error_log("DB update previous slab failed: " . dbErrorMessage($upd));
                    if ($pdo->inTransaction()) $pdo->rollBack();
                    abortWith(dbErrorMessage($upd));
                }

                $insRes = executeNonQuery($pdo,"EXEC dbo.sp_AddTariffRate ?, ?, ?, ?, ?",[$tariffPlanID, $slabStart, $slabEnd, $rate, $fixed]);

                if (is_string($insRes) && str_starts_with($insRes, 'Databse error')) {
                    if ($pdo->inTransaction()) $pdo->rollBack();
                    abortWith($insRes);
                }

                // If SP returns a row with NewRateID, grab it (useful for audit)
                $insertedRateID = null;
                if (isset($insRes['row']) && isset($insRes['row']['NewRateID'])) {
                    $insertedRateID = intval($insRes['row']['NewRateID']);
                }

                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
                abortWith("Database error while updating previous slab and inserting new slab: " . $e->getMessage());
            }

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
    $resIns = executeNonQuery($pdo,"EXEC dbo.sp_AddTariffRate ?, ?, ?, ?, ?",[$tariffPlanID, $slabStart, $slabEnd, $rate, $fixed]);

    if (is_string($resIns) && str_starts_with($resIns, 'Databse error')) {
        abortWith($resIns);
    }

    $insertedRateID = null;
    if (isset($resIns['row']) && isset($resIns['row']['NewRateID'])) {
        $insertedRateID = intval($resIns['row']['NewRateID']);
    }

    error_log("DEBUG add_slab completed successfully");
    $_SESSION['tariff_msg'] = "Slab added successfully.";
    header("Location: Tariff1.php");
    exit;
}

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

        // Fetch current slab info
        $current = executeQuery(
            $pdo,
            "SELECT TariffPlanID, SlabStart FROM TariffRates WHERE RateID = ?",
            [$rateID],
            true
        );

        if (!$current) {
            abortWith("Current slab not found.");
        }

        // Fetch previous slab (largest SlabStart less than current)
        $prev = executeQuery(
            $pdo,
            "SELECT RatePerUnit, FixedCharge 
            FROM TariffRates
            WHERE TariffPlanID = ?
            AND SlabStart < ?
            ORDER BY SlabStart DESC",
            [$current['TariffPlanID'], $current['SlabStart']],
            true
        );

        // If a previous slab exists, validate values
        if ($prev) {
            if ($rate < floatval($prev['RatePerUnit'])) {
                abortWith("Rate per unit must be greater than or equal to the previous slab's rate.");
            }

            if ($fixed < floatval($prev['FixedCharge'])) {
                abortWith("Fixed charge must be greater than or equal to the previous slab's fixed charge.");
            }
        }


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