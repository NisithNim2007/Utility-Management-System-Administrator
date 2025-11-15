<?php
session_start();
include '../db.php';

if (!isset($_SESSION['UserID']) || !isset($_SESSION['RoleID']) || $_SESSION['RoleID'] != 1) {
  header("Location: ../login.php");
  exit();
}

try {
  $stmt = $pdo->query("SELECT UtilityTypeID, UtilityTypeName FROM UtilityTypes ORDER BY UtilityTypeID");
  $utilities = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  error_log("Database error: " . $e->getMessage());
  $utilities = [];
}

$username = $_SESSION['Username'];
?>

<html>
    <head>
        <title>Admin</title>
    </head>
    <body>
        <center>
            <h1>Welcome Admin</h1>
        </center>
    </body>
</html>