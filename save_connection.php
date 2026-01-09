<?php
include 'connection.php';


mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

header('Content-Type: application/json; charset=UTF-8');

$response = [
    'step' => 'start'
];

try {
    // ---------------- Input----------------
    $personID      = $_POST['personID'] ?? null;
    $utilityTypeID = $_POST['utilityTypeID'] ?? null;

    if (!$personID || !$utilityTypeID) {
        throw new Exception("Missing personID or utilityTypeID");
    }

    $response['step'] = 'input_ok';

    $meterNumber = $_POST['meterNumber'] ?? null;

if (empty($meterNumber)) {
    throw new Exception("Meter number is required");
}


    // ---------------- Inserting the connection ----------------
    $sql = "INSERT INTO ServiceConnections 
            (CustomerID, UtilityTypeID, CurrentBalance, AccountStatusID, ConnectionStatusID, ConnectionDate)
            VALUES (?, ?, 0.00, 1, 1, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $personID, $utilityTypeID);
    $stmt->execute();

    $response['step'] = 'service_connection_inserted';

    // ---------------- GET ConnectionID ----------------
    $newConnId = $conn->insert_id;

    if (!$newConnId) {
        throw new Exception("insert_id is empty after ServiceConnections insert");
    }

    $response['ConnectionID'] = $newConnId;

    $check = $conn->prepare(
        "SELECT COUNT(*) FROM ServiceConnections WHERE ConnectionID = ?"
    );
    $check->bind_param("i", $newConnId);
    $check->execute();
    $check->bind_result($count);
    $check->fetch();

    if ($count == 0) {
        throw new Exception("ConnectionID not visible in ServiceConnections");
    }

    $response['step'] = 'fk_visible';

    // ---------------- Inserting the meters ----------------


    $sql2 = "INSERT INTO Meters
             (MeterNumber, ConnectionID, InstallationDate, InitialReading)
             VALUES (?, ?, NOW(), 0)";

    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("si", $meterNumber, $newConnId);
    $stmt2->execute();

    $response['step'] = 'meter_inserted';
    $response['MeterNumber'] = $meterNumber;

    $response['success'] = true;

} catch (Throwable $e) {

    $response['success'] = false;
    $response['error']   = $e->getMessage();
    $response['mysql_error'] = $conn->error ?? null;
}

echo json_encode($response, JSON_PRETTY_PRINT);
