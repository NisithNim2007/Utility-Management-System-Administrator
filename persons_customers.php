<?php
require "connection.php";

header("Content-Type: application/json; charset=UTF-8");

$id = $_GET['id'];

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
    c.PostalCode,
    c.Status
FROM Persons p
LEFT JOIN Customers c ON p.PersonID = c.PersonID
WHERE p.PersonID = ?
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

echo json_encode($stmt->fetch(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT);
