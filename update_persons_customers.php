<?php
ini_set('display_errors', 0);
error_reporting(0);
header("Content-Type: application/json; charset=UTF-8");

require "connection.php";

$response = [
    "success" => false,
    "message" => "Unknown error"
];

try {
    if (!isset($_POST["PersonID"], $_POST["CustomerID"])) {
        throw new Exception("Missing IDs");
    }

    // ---------- PERSON UPDATE ----------
    $sql1 = "UPDATE Persons SET 
                NIC = ?, 
                FirstName = ?, 
                MiddleName = ?, 
                LastName = ?, 
                Email = ?, 
                PhoneNumber = ?
             WHERE PersonID = ?";

    $stmt1 = $pdo->prepare($sql1);
    $stmt1->execute([
        $_POST["NIC"] ?? null,
        $_POST["FirstName"] ?? null,
        $_POST["MiddleName"] ?? null,
        $_POST["LastName"] ?? null,
        $_POST["Email"] ?? null,
        $_POST["Phone"] ?? null,
        $_POST["PersonID"]
    ]);

    // ---------- CUSTOMER UPDATE ----------
    $sql2 = "UPDATE Customers SET
                CustomerTypeID = ?, 
                Address = ?, 
                City = ?, 
                State = ?, 
                PostalCode = ?,
                Status = ?
             WHERE CustomerID = ?";

    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute([
        $_POST["CustomerTypeID"] ?? null,
        $_POST["Address"] ?? null,
        $_POST["City"] ?? null,
        $_POST["State"] ?? null,
        $_POST["PostalCode"] ?? null,
        $_POST["Status"] ?? null,
        $_POST["CustomerID"]
    ]);

    $response["success"] = true;
    $response["message"] = "Updated successfully!";
}
catch (Exception $e) {
    $response["success"] = false;
    $response["message"] = "Update failed";
}

echo json_encode($response);
exit;
