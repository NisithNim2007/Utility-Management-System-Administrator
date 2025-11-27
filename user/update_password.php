<?php
session_start();
if(!isset($_SESSION['Username'])){
    http_response_code(401);
    echo json_encode(['success'=>false, 'message'=>'Unauthorized']);
    exit;
}

include('../include/db.php');

$data = json_decode(file_get_contents('php://input'), true);

$userId = intval($data['userId'] ?? 0);
$newPassword = trim($data['newPassword'] ?? '');

if(!$userId || strlen($newPassword) < 8){
    echo json_encode(['success'=>false, 'message'=>'Invalid input']);
    exit;
}

$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

try{
    $stmt = $pdo-> prepare("EXEC sp_UpdatePassword @UserID=?, @NewPassword=?");
    $stmt->execute([$userId,$hashedPassword]);

    echo json_encode(['success'=>true]);
}catch(PDOException $e){
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}

?>