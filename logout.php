<?php
session_start();

include 'db.php';

$sql = "UPDATE UserLogins SET LogOutTime = SYSUTCDATETIME() WHERE SessionID = :SessionID AND UserID = :UserID 
          AND RoleID = :RoleID";

$stmt = $pdo->prepare($sql);

$stmt->execute([':SessionID' => $_SESSION['SessionID'], ':UserID'    => $_SESSION['UserID'],
    ':RoleID'    => $_SESSION['RoleID']
]);
session_unset();
session_destroy();
header("Location: login.php");
exit;
?>
