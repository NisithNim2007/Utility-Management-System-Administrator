<?php
session_start();
if(!isset($_SESSION['Username'])){
     header("Location: ../login.php");
    exit;
}
include('./include/db.php');

$data = json_decode(file_get_contents('php://input'), true);

$userId = intval($data['userId'] ?? 0);
$newPassword = trim($data['newPassword'] ?? '');

if(!$userId || strlen($newPassword) < 6){
    echo json_encode(['success'=>false, 'message'=>'Invalid input.Ensure password has minimum 6 characters.']);
    exit;
}

$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
try{
    $stmt = $pdo-> prepare("EXEC sp_UpdatePassword @UserID=?, @NewPassword=?");
    $stmt->execute([$userId,$hashedPassword]);

    echo json_encode(['success'=>true]);
}catch(PDOException $e){
    echo json_encode(['success'=>false, 'message'=> 'Failed to update password. Please try again later']);
    error_log("DB error in update_password.php: " . $e->getMessage());
}

?>