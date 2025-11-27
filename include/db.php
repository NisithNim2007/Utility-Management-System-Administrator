<?php
$host = "159.65.158.217";
$port = "1433";
$dbname = "UMS1";
$username = "imrgroup_project_login";
$password = "TUIY43afwejin123JKH";

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


function executeQuery(PDO $conn, string $sql, array $params=[], bool $single=false
){
    try{
        $stmt = $conn-> prepare($sql);
    if(!empty($params)){
        foreach ($params as $key => $value){

            if(is_string($key)){
                $stmt -> bindValue($key, $value);
            }else{
                $stmt -> bindValue($key + 1, $value);
            }
        }

    }
        $stmt -> execute();
        
        return $single
            ? $stmt-> fetch(PDO::FETCH_ASSOC)
            : $stmt->fetchAll(PDO::FETCH_ASSOC);
       
    }catch(PDOException $e){
        die("Database error: " . $e->getMessage());
    }
}





function executeNonQuery(PDO $conn, $sql, $params=[]){
    try{
        $stmt = $conn->prepare($sql);

        foreach($params as $key=>$value){
            $stmt->bindValue($key,$value);
        }
        $stmt->execute();
        return "success";
    }catch (PDOException $e){
        return "Databse error: " . $e->getMessage();
    }
}
?>
