<?php


 $db_host = 'localhost';
 $db_user = 'root';
 $db_pass = '22092209';
 $db_name = 'Kenya_Tech';

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


?>