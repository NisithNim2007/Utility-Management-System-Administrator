<?php
require "connection.php"; 

try {
    
    $pdo->beginTransaction();

    // -------- Validation--------
    if (!preg_match('/^[0-9]{10}$/', $_POST["phone"])) {
        throw new Exception("Invalid phone number.");
    }

    // -------- Receive validation --------
    $firstName      = $_POST["firstName"];
    $middleName     = $_POST["middleName"];
    $lastName       = $_POST["lastName"];
    $email          = $_POST["email"];
    $phone          = $_POST["phone"];
    $nic            = $_POST["nic"];

    $customerType   = $_POST["customerType"];
    $address        = $_POST["address"];
    $city           = $_POST["city"];
    $state          = $_POST["state"];
    $postalCode     = $_POST["postalCode"];

    $utilityName    = $_POST["utilityName"];
    $meterNumber = $_POST["meterNumber"] ?? null;

if (empty($meterNumber)) {
    throw new Exception("Meter number is required.");
}

    $connectionDate = $_POST["connectionDate"];

    // -------- Inserting into persons --------
    $query1 = "INSERT INTO Persons
               (FirstName, MiddleName, LastName, Email, PhoneNumber, NIC)
               VALUES (?, ?, ?, ?, ?, ?)";

    executeNonQuery($pdo, $query1, [
        $firstName, $middleName, $lastName, $email, $phone, $nic
    ]);

    $personID = $pdo->lastInsertId();
    if (!$personID) {
        throw new Exception("Failed to retrieve PersonID.");
    }

    // -------- Inserting into Customers --------
    $query2 = "INSERT INTO Customers
               (CustomerID, CustomerTypeID, Address, City, State, PostalCode)
               VALUES (?, ?, ?, ?, ?, ?)";

    executeNonQuery($pdo, $query2, [
        $personID, $customerType, $address, $city, $state, $postalCode
    ]);

   
    $query3 = "SELECT UtilityTypeID FROM UtilityTypes WHERE UtilityTypeName = ?";
    $row = executeQuery($pdo, $query3, [$utilityName], true);

    if (!$row) {
        throw new Exception("Invalid Utility Type.");
    }

    $utilityTypeID = $row["UtilityTypeID"];

    // -------- Inserting into ServiceConnections--------
    $query4 = "INSERT INTO ServiceConnections
               (CustomerID, UtilityTypeID, ConnectionStatusID, ConnectionDate)
               VALUES (?, ?, ?, ?)";

    executeNonQuery($pdo, $query4, [
        $personID,
        $utilityTypeID,
        1,
        $connectionDate
    ]);

 
    $connectionID = $pdo->lastInsertId();
    if (!$connectionID) {
        throw new Exception("Failed to retrieve ConnectionID.");
    }

    // -------- Inserting into Meters--------
  

    $meterSql = "INSERT INTO Meters (
                    MeterNumber,
                    ConnectionID,
                    InstallationDate,
                    InitialReading,
                    AddedDate
                 ) VALUES (?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($meterSql);
    $stmt->execute([
        $meterNumber,
        $connectionID,
        date('Y-m-d'),
        0,
        date('Y-m-d H:i:s')
    ]);


    $pdo->commit();

    echo "Customer successfully created with ID: " . $personID;

} catch (Exception $e) {
  
    $pdo->rollBack();
    die("ERROR: " . $e->getMessage());
}
?>
