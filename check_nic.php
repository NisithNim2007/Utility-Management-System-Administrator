<?php
require "connection.php";

$nic = strtoupper(trim($_GET["nic"] ?? ""));

$response = ["exists" => false];

if ($nic !== "") {
    $sql = "SELECT 1 FROM Persons WHERE NIC = ?";
    $row = executeQuery($pdo, $sql, [$nic], true);

    if ($row) {
        $response["exists"] = true;
    }
}

header("Content-Type: application/json");
echo json_encode($response);
