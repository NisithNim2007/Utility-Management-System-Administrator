<?php
include 'connection.php';

$personID = $_POST['personID'];
$utilityTypeID = $_POST['utilityTypeID'];
$utilityTypeName = $_POST['utilityTypeName'];

$sql = "INSERT INTO ServiceConnections 
        (CustomerID, UtilityTypeID, CurrentBalance, AccountStatusID, ConnectionStatusID, ConnectionDate)
        VALUES (?, ?, 0.00, 1, 1, GETDATE())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $personID, $utilityTypeID);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
?>
