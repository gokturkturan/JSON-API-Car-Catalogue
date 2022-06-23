<?php 

    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "api";

    try {
       $connect = mysqli_connect($host,$user,$pass);
       mysqli_select_db($connect, $db);
       //echo "Connected to the database.";
    } catch (Exception $e) {
        die($e->getMessage());
    }

?>