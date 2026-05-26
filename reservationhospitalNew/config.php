<?php
$servername = "localhost";  // biasanya localhost
$username = "root";         // username database kamu
$password = "";             // password database kamu (kosongin kalau default XAMPP)
$database = "reservation_hospital";  // nama database kamu

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
