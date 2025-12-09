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
    ':uid' => $_SESSION['UserID']
];
$result = executeNonQuery($pdo,$query,$params);

if($result !== false){
    echo "success";
}else{
    echo "Error";
}

?>