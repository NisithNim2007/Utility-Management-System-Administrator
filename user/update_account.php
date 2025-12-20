<?php
session_start();
if(!isset($_SESSION['Username'])){
    header("Location: ../login.php");
    exit;
}
include('../include/db.php');

$data = json_decode(file_get_contents('php://input'), true);

$userId = intval($data['userId'] ?? 0);
$isActive = isset($data['isActive']) ? intval($data['isActive']) : null;

if(!$userId || !isset($isActive)){
    echo json_encode(['success'=>false, 'message'=>'Invalid input']);
    exit;
}
try{
    $stmt = $pdo-> prepare("EXEC sp_updateAcc @UserID=?, @isActive=?");
    $stmt->execute([$userId,$isActive]);

    echo json_encode(['success'=>true]);
}catch(PDOException $e){
    $msg = 'Failed to update account status. Please try again later';

    if( isset($e->errorInfo[2]) &&
        strpos($e->errorInfo[2], 'ADMIN_DEACTIVATION_BLOCK') !== false){
        $msg = 'Admin accounts cannot be deactivated';
    }
    echo json_encode(['success'=>false, 'message'=>$msg]);
    exit;
}

?>