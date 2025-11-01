<?php
$host = "localhost";
$port = "1433";
$dbname = "";
$username = "";
$password = "";

try {
    $dsn = "sqlsrv:Server=$host,$port;Database=$dbname";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Throw exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch associative arrays
        PDO::ATTR_EMULATE_PREPARES => false, // Use real prepared statements
    ]);
} catch (PDOException $e) {
    //log errors
    error_log("Database connection failed." . $e->getMessage(), 0);
    die("Database connection failed. Please try again later.");
}
?>
