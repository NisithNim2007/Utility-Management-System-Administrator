<?php
session_start();
include('../include/db.php');

if(!isset($_POST['complaintID']) || !isset($_POST['newStatus'])){
    echo "Error";
    exit;
}

$complaintID = intval($_POST['complaintID']);
$newStatus = intval($_POST['newStatus']);
$userID = $_SESSION['UserID'];

$query = "EXEC sp_updateComplaintStatus
            @complaintID = :cid,
            @NewStatusID = :sid,
            @AssignedToUserID = :uid";


$params = [
    ':cid' => $complaintID,
    ':sid' => $newStatus,
    ':uid' => $userID
];

$result = executeQuery($pdo,$query,$params,false);

if($result !== false){
    echo "Success";
}else{
    echo "Error";
}



?>