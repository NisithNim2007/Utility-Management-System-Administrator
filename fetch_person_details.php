<?php
require "connection.php";

header("Content-Type: application/json; charset=UTF-8");

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Person ID missing"]);
    exit;
}

$personID = $_GET['id'];

$sql = "
SELECT 
    p.PersonID,
    p.NIC,
    p.FirstName,
    p.MiddleName,
    p.LastName,
    p.Email,
    p.PhoneNumber,
    p.RegistrationDate AS RegDate,

    c.CustomerID,
    c.CustomerTypeID,
    c.Address,
    c.City,
    c.State,
    c.PostalCode
FROM Persons p
LEFT JOIN Customers c ON p.PersonID = c.CustomerID
WHERE p.PersonID = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$personID]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
