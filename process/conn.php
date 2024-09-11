<?php
// $servername = 'localhost';
// $username = 'root';
// $pass = '';

// mysql conncention
//     try {
//         $conn = new PDO ("mysql:host=$servername;dbname=qualifv2",$username,$pass);

//          $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//   // echo "Connected successfully";

//     }catch(PDOException $e){
//         echo 'NO CONNECTION'.$e->getMessage();
//     }
date_default_timezone_set('Asia/Manila');
$server_date_time = date('Y-m-d H:i:s');
$server_date_only = date('Y-m-d');
$server_date_month = date('M');
$server_date_day = date('d');
$server_date_month_time = date('Y-m-01 H:i:s');
$server_time = date('H:i:s');

//get the 7 days before
$date = new DateTime($server_date_only);
$date_month_delay = (clone $date)->modify('-1 month');
$date_month_before = $date_month_delay->format('Y-m-d');


$servername = '172.25.116.188';
$username = 'SA';
$password = 'SystemGroup@2022';

try {
    // Connection to the qualif database
    $conn = new PDO("sqlsrv:Server=$servername;Database=qualif", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'NO CONNECTION to qualif: ' . $e->getMessage();
}


