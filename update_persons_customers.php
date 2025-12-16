<?php
require "connection.php";

$response = [];

try {
    // PERSON UPDATE
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
        $_POST["NIC"],
        $_POST["FirstName"],
        $_POST["MiddleName"],
        $_POST["LastName"],
        $_POST["Email"],
        $_POST["Phone"],
        $_POST["PersonID"]
    ]);

    // CUSTOMER UPDATE
    $sql2 = "UPDATE Customers SET
                CustomerTypeID = ?, 
                Address = ?, 
                City = ?, 
                State = ?, 
                PostalCode = ?
             WHERE CustomerID = ?";

    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute([
        $_POST["CustomerTypeID"],
        $_POST["Address"],
        $_POST["City"],
        $_POST["State"],
        $_POST["PostalCode"],
        $_POST["CustomerID"]
    ]);

    $response["message"] = "Updated successfully!";
} 
catch (Exception $e) {
    $response["message"] = "Error: " . $e->getMessage();
}

echo json_encode($response);
?>
