<?php
// fetch_connections.php (robust dynamic-select version)
// Backup old file first.

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connection.php'; // must create $pdo (PDO to SQL Server)

header('Content-Type: application/json; charset=UTF-8');

try {
    // Accept GET or POST
    $customerID = isset($_GET['customerID']) ? (int)$_GET['customerID'] : (isset($_POST['customerID']) ? (int)$_POST['customerID'] : 0);
    if (!$customerID) {
        echo json_encode([]);
        exit;
    }

    // 1) Read actual columns from INFORMATION_SCHEMA
    $colStmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = 'ServiceConnections'
    ");
    $colStmt->execute();
    $cols = $colStmt->fetchAll(PDO::FETCH_COLUMN);
    $cols = array_map('strval', $cols); // normalize

    // 2) Build SELECT parts based on existing columns
    $selectParts = [
        "sc.ConnectionID",
        "sc.CustomerID",
        "sc.UtilityTypeID"
    ];

    // join utility name if UtilityTypes table exists (we assume it does)
    // include UtilityTypeName via LEFT JOIN alias ut (we'll add join later)
    $selectParts[] = "ISNULL(ut.UtilityTypeName, '') AS UtilityTypeName";

    if (in_array('CurrentBalance', $cols)) {
        $selectParts[] = "sc.CurrentBalance";
    }

    // Build a single 'Status' column using whichever exists
    if (in_array('AccountStatus', $cols)) {
        $selectParts[] = "sc.AccountStatus AS Status";
    } elseif (in_array('AccountStatusID', $cols)) {
        $selectParts[] = "CAST(sc.AccountStatusID AS VARCHAR(20)) AS Status";
    } elseif (in_array('ConnectionStatusID', $cols)) {
        $selectParts[] = "CAST(sc.ConnectionStatusID AS VARCHAR(20)) AS Status";
    }

    // Dates
    if (in_array('ConnectionDate', $cols)) {
        $selectParts[] = "CONVERT(varchar(10), sc.ConnectionDate, 23) AS ConnectionDate";
    }
    if (in_array('DisconnectionDate', $cols)) {
        $selectParts[] = "CONVERT(varchar(10), sc.DisconnectionDate, 23) AS DisconnectionDate";
    }

    // If none of the optional fields were present, ensure SELECT has at least something for JS to show
    // (we already have ConnectionID, UtilityTypeID etc.)

    // 3) Build the final SQL string
    $selectSql = implode(",\n       ", $selectParts);

    $sql = "
      SELECT
        {$selectSql}
      FROM ServiceConnections sc
      LEFT JOIN UtilityTypes ut ON ut.UtilityTypeID = sc.UtilityTypeID
      WHERE sc.CustomerID = ?
      ORDER BY sc.ConnectionDate DESC, sc.ConnectionID DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$customerID]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($rows);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
    exit;
}
