<?php
session_start();
include '../db.php';

if (!isset($_SESSION['UserID']) || !isset($_SESSION['RoleID']) || $_SESSION['RoleID'] != 1) {
  header("Location: ../login.php");
  exit();
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