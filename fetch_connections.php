<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'connection.php'; 

header('Content-Type: application/json; charset=UTF-8');

try {
    // Accepting...
    $customerID = isset($_GET['customerID']) ? (int)$_GET['customerID'] : (isset($_POST['customerID']) ? (int)$_POST['customerID'] : 0);
    if (!$customerID) {
        echo json_encode([]);
        exit;
    }

    
    $colStmt = $pdo->prepare("
        SELECT COLUMN_NAME
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_NAME = 'ServiceConnections'
    ");
    $colStmt->execute();
    $cols = $colStmt->fetchAll(PDO::FETCH_COLUMN);
    $cols = array_map('strval', $cols); 

    
    $selectParts = [
        "sc.ConnectionID",
        "sc.CustomerID",
        "sc.UtilityTypeID"
    ];

   
    $selectParts[] = "ISNULL(ut.UtilityTypeName, '') AS UtilityTypeName";

    if (in_array('CurrentBalance', $cols)) {
        $selectParts[] = "sc.CurrentBalance";
    }

    if (in_array('AccountStatus', $cols)) {
        $selectParts[] = "sc.AccountStatus AS Status";
    } elseif (in_array('AccountStatusID', $cols)) {
        $selectParts[] = "CAST(sc.AccountStatusID AS VARCHAR(20)) AS Status";
    } elseif (in_array('ConnectionStatusID', $cols)) {
        $selectParts[] = "CAST(sc.ConnectionStatusID AS VARCHAR(20)) AS Status";
    }

   //dates
    if (in_array('ConnectionDate', $cols)) {
        $selectParts[] = "CONVERT(varchar(10), sc.ConnectionDate, 23) AS ConnectionDate";
    }
    if (in_array('DisconnectionDate', $cols)) {
        $selectParts[] = "CONVERT(varchar(10), sc.DisconnectionDate, 23) AS DisconnectionDate";
    }


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
