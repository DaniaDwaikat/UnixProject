<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // ضع 0 إذا الموقع Live

$DB_HOST = "localhost";
$DB_NAME = "project_db";
$DB_USER = "dania";  
$DB_PASS = "123";  

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("❌ فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

mysqli_set_charset($conn, "utf8mb4");
?>
