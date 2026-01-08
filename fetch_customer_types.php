<?php
require 'connection.php'; 

header('Content-Type: application/json; charset=UTF-8');

try {
    $stmt = $pdo->prepare("SELECT CustomerTypeID, CustomerTypeName FROM CustomerTypes ORDER BY CustomerTypeName");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (PDOException $ex) {
    http_response_code(500);
    echo json_encode(['error' => 'DB error: ' . $ex->getMessage()]);
}
