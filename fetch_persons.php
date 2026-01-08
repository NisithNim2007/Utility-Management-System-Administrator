<?php
require "connection.php";

header("Content-Type: application/json; charset=UTF-8");

try {

    
    $sql = "SELECT 
                PersonID,
                NIC,
                FirstName,
                MiddleName,
                LastName,
                Email,
                PhoneNumber,
                RegistrationDate AS RegDate
            FROM Persons";

    $stmt = $pdo->query($sql);

    $persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($persons, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode([
        "error" => $e->getMessage()
    ]);
}
