<?php
// fetch_utility_types.php
require 'connection.php';

header('Content-Type: application/json; charset=UTF-8');

try {
    $stmt = $pdo->prepare("SELECT UtilityTypeID, UtilityTypeName FROM UtilityTypes ORDER BY UtilityTypeName");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (PDOException $ex) {
    http_response_code(500);
    echo json_encode(['error' => 'DB error: ' . $ex->getMessage()]);
}
