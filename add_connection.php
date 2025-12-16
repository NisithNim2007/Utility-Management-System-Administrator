<?php
// add_connection.php (final cleaned version, duplicates allowed)

ini_set('display_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

require 'connection.php'; // must define $pdo

header('Content-Type: application/json; charset=UTF-8');

function respond($code, $payload) {
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

try {
    // Accept JSON or form POST
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        $data = $_POST; // fallback
    }

    // Read incoming values
    $personID       = isset($data['PersonID']) ? (int)$data['PersonID'] : 0;
    $utilityTypeID  = isset($data['UtilityTypeID']) ? (int)$data['UtilityTypeID'] : 0;
    $customerTypeID = isset($data['CustomerTypeID']) ? (int)$data['CustomerTypeID'] : 1;
    $connectionDate = !empty($data['ConnectionDate']) ? $data['ConnectionDate'] : date('Y-m-d');

    if (!$personID || !$utilityTypeID) {
        respond(400, ['success'=>false, 'message'=>'Missing PersonID or UtilityTypeID.']);
    }

    // Get available columns from ServiceConnections
    $colStmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = 'ServiceConnections'
    ");
    $colStmt->execute();
    $cols = $colStmt->fetchAll(PDO::FETCH_COLUMN);
    $cols = array_map('strval', $cols);

    // Possible columns to insert if they exist
    $candidateCols = [
        'CustomerID'         => $personID,
        'UtilityTypeID'      => $utilityTypeID,
        'CurrentBalance'     => 0.00,
        'AccountStatus'      => 'Active',
        'AccountStatusID'    => 1,
        'ConnectionStatusID' => 1,
        'ConnectionDate'     => $connectionDate,
        'DisconnectionDate'  => null
    ];

    // Build final insert column list
    $insertCols = [];
    $placeholders = [];
    $params = [];

    foreach ($candidateCols as $col => $val) {
        if (in_array($col, $cols)) {
            $insertCols[] = $col;
            $placeholders[] = '?';
            $params[] = $val;
        }
    }

    // Validate required columns
    if (!in_array('CustomerID', $insertCols) || !in_array('UtilityTypeID', $insertCols)) {
        respond(500, ['success'=>false, 'message'=>'Database missing required columns in ServiceConnections']);
    }

    $pdo->beginTransaction();

    // Ensure customer row exists (your DB design needs matching CustomerID)
    $chk = $pdo->prepare("SELECT CustomerID FROM Customers WHERE CustomerID = ?");
    $chk->execute([$personID]);

    if (!$chk->fetchColumn()) {
        $insCust = $pdo->prepare("
            INSERT INTO Customers (CustomerID, CustomerTypeID, Address, City, State, PostalCode)
            VALUES (?, ?, '', '', '', '')
        ");
        $insCust->execute([$personID, $customerTypeID]);
    }

    // ---------------------------------------------------
    // DUPLICATE CHECK REMOVED → unlimited connections OK
    // ---------------------------------------------------

    // Build SQL insert
    $colsList  = implode(', ', $insertCols);
    $placeList = implode(', ', $placeholders);

    $insertSql = "
        INSERT INTO ServiceConnections ($colsList)
        OUTPUT INSERTED.ConnectionID
        VALUES ($placeList)
    ";

    $ins = $pdo->prepare($insertSql);
    $ins->execute($params);

    // Get new Connection ID
    $inserted = $ins->fetch(PDO::FETCH_ASSOC);
    $newConnId = isset($inserted['ConnectionID']) ? (int)$inserted['ConnectionID'] : null;

    $pdo->commit();

    respond(200, [
        'success' => true,
        'message' => 'Connection added successfully.',
        'ConnectionID' => $newConnId
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    respond(500, ['success'=>false, 'message'=>'Server error: '.$e->getMessage()]);
}
