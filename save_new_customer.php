<?php
require "connection.php"; // loads $pdo

// -------- RECEIVE FORM DATA --------
$firstName     = $_POST["firstName"];
$middleName    = $_POST["middleName"];
$lastName      = $_POST["lastName"];
$email         = $_POST["email"];
$phone         = $_POST["phone"];
$nic           = $_POST["nic"];

if (!preg_match('/^[0-9]{10}$/', $_POST["phone"])) {
    die("ERROR: Invalid phone number.");
}


$customerType  = $_POST["customerType"];
$address       = $_POST["address"];
$city          = $_POST["city"];
$state         = $_POST["state"];
$postalCode    = $_POST["postalCode"];



$utilityName   = $_POST["utilityName"];
$connectionDate = $_POST["connectionDate"];




// -------- 1️⃣ INSERT INTO PERSONS --------
$query1 = "INSERT INTO Persons 
           (FirstName, MiddleName, LastName, Email, PhoneNumber,NIC) 
           VALUES (?, ?, ?, ?, ?,?)";

$result1 = executeNonQuery($pdo, $query1, [
    $firstName, $middleName, $lastName, $email, $phone ,$nic
]);

if ($result1 !== "success") {
    die("Error inserting into Persons: " . $result1);
}


// -------- 2️⃣ GET NEW PersonID USING PDO --------
$personID = $pdo->lastInsertId();

if (!$personID) {
    die("ERROR: Could not retrieve new PersonID.");
}


// -------- 3️⃣ INSERT INTO CUSTOMERS --------
$query2 = "INSERT INTO Customers 
          (CustomerID, CustomerTypeID, Address, City, State, PostalCode) 
          VALUES (?, ?, ?, ?, ?, ?)";

$result2 = executeNonQuery($pdo, $query2, [
    $personID,
    $customerType,
    $address,
    $city,
    $state,
    $postalCode
]);

if ($result2 !== "success") {
    die("Error inserting into Customers: " . $result2);
}


// -------- 4️⃣ LOOK UP UtilityTypeID --------
$query3 = "SELECT UtilityTypeID 
           FROM UtilityTypes 
           WHERE UtilityTypeName = ?";

$row = executeQuery($pdo, $query3, [$utilityName], true);

if (!$row || !isset($row["UtilityTypeID"])) {
    die("ERROR: Invalid Utility Name. Cannot find UtilityTypeID.");
}

$utilityTypeID = $row["UtilityTypeID"];



$query4 = "INSERT INTO ServiceConnections
           (CustomerID, UtilityTypeID, ConnectionStatusID, ConnectionDate)
           VALUES (?, ?, ?, ?)";

// using 1 as default ConnectionStatusID for now
$defaultConnectionStatusID = 1;

$result4 = executeNonQuery($pdo, $query4, [
    $personID,
    $utilityTypeID,
    $defaultConnectionStatusID,
    $connectionDate
]);

if ($result4 !== "success") {
    die("Error inserting into ServiceConnections: " . $result4);
}


// -------- 6️⃣ SUCCESS MESSAGE --------
echo "Customer successfully created with ID: " . $personID;

?>
