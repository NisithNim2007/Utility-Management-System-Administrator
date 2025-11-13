<?php
session_start();
if (!isset($_SESSION['RoleID']) || $_SESSION['RoleID'] != 1) {
    header("Location: ../login.php");
    exit();
}
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