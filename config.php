<?php
$host = '192.168.56.101';
$user = 'devops';      // اسم المستخدم الجديد
$password = '123';     // كلمة المرور الجديدة
$dbname = 'project_db';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("❌ فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>

