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

    $tariffPlanID = intval($_POST['TariffPlanID'] ?? 0);
    $slabStart = intval($_POST['SlabStart'] ?? 0);
    $slabEndRaw = trim($_POST['SlabEnd'] ?? '');
    $slabEnd = ($slabEndRaw === '') ? null : intval($slabEndRaw);
    $rate = floatval($_POST['RatePerUnit'] ?? 0);
    $fixed = floatval($_POST['FixedCharge'] ?? 0);

    if ($tariffPlanID <= 0) abortWith("TariffPlanID missing.");

    //Correct RateUnitID detection (clean, final version)
    // 1. Check existing slabs under this plan
    $unitRow = executeQuery(
        $pdo,
        "SELECT TOP 1 RateUnitID FROM TariffRates WHERE TariffPlanID = ?",
        [$tariffPlanID],
        true
    );
    $rateUnitID = $unitRow['RateUnitID'] ?? null;

    // 2. If no slabs exist, detect RateUnit based on utility
    if (!$rateUnitID) {
        $utilityRow = executeQuery(
            $pdo,
            "SELECT UtilityTypeID FROM TariffPlans WHERE TariffPlanID = ?",
            [$tariffPlanID],
            true
        );

        if ($utilityRow) {
            $rateUnitID = executeQuery(
                $pdo,
                "SELECT TOP 1 RateUnitID FROM RateUnits 
                 WHERE UnitName = (
                    SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?
                 )",
                [$utilityRow['UtilityTypeID']],
                true
            )['RateUnitID'] ?? null;
        }
    }

    // 3. Final fallback (should rarely ever be used)
    if (!$rateUnitID) {
        $rateUnitID = executeQuery(
            $pdo,
            "SELECT TOP 1 RateUnitID FROM RateUnits ORDER BY RateUnitID",
            [],
            true
        )['RateUnitID'];
    }

    // Overlap validation (leave unchanged)
    $existing = executeQuery($pdo,
        "SELECT RateID, SlabStart, SlabEnd FROM TariffRates WHERE TariffPlanID = ?",
        [$tariffPlanID]
    );

    foreach ($existing as $row) {
        $es = intval($row['SlabStart']);
        $ee = $row['SlabEnd'] === null ? PHP_INT_MAX : intval($row['SlabEnd']);
        $newE = $slabEnd === null ? PHP_INT_MAX : $slabEnd;
        if (!($newE < $es || $slabStart > $ee)) {
            abortWith("New slab overlaps existing slab: {$es} - " . ($ee===PHP_INT_MAX ? 'Over' : $ee));
        }
    }

    // Insert
    executeQuery($pdo,
        "EXEC dbo.sp_AddTariffRate ?, ?, ?, ?, ?, ?",
        [$tariffPlanID, $slabStart, $slabEnd, $rate, $rateUnitID, $fixed]
    );

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
    $fixed = floatval($_POST['FixedCharge'] ?? 0);

    if ($rateID <= 0) abortWith("RateID missing.");

    //Get TariffPlanID from RateID (correct!)
    $row = executeQuery(
        $pdo,
        "SELECT TariffPlanID FROM TariffRates WHERE RateID = ?",
        [$rateID],
        true
    );

    if (!$row) abortWith("Rate not found.");

    $tariffPlanID = $row['TariffPlanID'];

    //Detect RateUnitID exactly like in add_slab
    $unitRow = executeQuery(
        $pdo,
        "SELECT TOP 1 RateUnitID FROM TariffRates WHERE TariffPlanID = ?",
        [$tariffPlanID],
        true
    );
    $rateUnitID = $unitRow['RateUnitID'] ?? null;

    if (!$rateUnitID) {
        $utilityRow = executeQuery(
            $pdo,
            "SELECT UtilityTypeID FROM TariffPlans WHERE TariffPlanID = ?",
            [$tariffPlanID],
            true
        );

        if ($utilityRow) {
            $rateUnitID = executeQuery(
                $pdo,
                "SELECT TOP 1 RateUnitID FROM RateUnits 
                 WHERE UnitName = (
                    SELECT UnitName FROM UtilityTypes WHERE UtilityTypeID = ?
                 )",
                [$utilityRow['UtilityTypeID']],
                true
            )['RateUnitID'] ?? null;
        }
    }

    if (!$rateUnitID) {
        $rateUnitID = executeQuery(
            $pdo,
            "SELECT TOP 1 RateUnitID FROM RateUnits ORDER BY RateUnitID",
            [],
            true
        )['RateUnitID'];
    }

    //Validate overlaps
    $existing = executeQuery($pdo,
        "SELECT RateID, SlabStart, SlabEnd 
         FROM TariffRates 
         WHERE TariffPlanID = ? AND RateID <> ?",
        [$tariffPlanID, $rateID]
    );

    foreach ($existing as $er) {
        $es = intval($er['SlabStart']);
        $ee = $er['SlabEnd'] === null ? PHP_INT_MAX : intval($er['SlabEnd']);
        $newE = $slabEnd === null ? PHP_INT_MAX : $slabEnd;
        if (!($newE < $es || $slabStart > $ee)) {
            abortWith("Updated slab would overlap existing slab.");
        }
    }

    
    executeNonQuery(
        $pdo,
        "EXEC dbo.sp_UpdateTariffRate ?, ?, ?, ?, ?, ?",
        [$rateID, $slabStart, $slabEnd, $rate, $rateUnitID, $fixed]
    );

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
