<?php
$host = "localhost";
$dbname = "rokworld";
$user = "root";
$pass = ""; // XAMPP default - CHANGE IN PRODUCTION

try {
  $pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $user,
    $pass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
  );
} catch (PDOException $e) {
  die("Database connection failed");
}