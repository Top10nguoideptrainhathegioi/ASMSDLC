<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cart_db";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
