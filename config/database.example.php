<?php

$host = "127.0.0.1";
$dbname = "THUVIENMINI";
$username = "root";
$password = "";
$port = 3306;

try {
    $conn = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}