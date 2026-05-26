<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header('Location: login.php?role=doctor');
    exit();
}

$doctor_id = (int)$_SESSION['user_id'];
$appointment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

if ($appointment_id === 0 || empty($status)) {
    die("Invalid request");
}

// Validasi status yang diizinkan (scheduled, completed, cancelled, no_show, dll - di DB enumnya: scheduled, completed, cancelled, no_show)
$allowed_statuses = ['scheduled', 'completed', 'cancelled', 'no_show', 'pending', 'confirmed'];
if (!in_array($status, $allowed_statuses)) {
    die("Invalid status");
}

$query = "UPDATE appointments SET status = '$status' WHERE appointment_id = $appointment_id AND doctor_id = $doctor_id";
if (mysqli_query($conn, $query)) {
    // Kembali ke halaman sebelumnya
    $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard_doctor.php';
    header("Location: $referer");
    exit();
} else {
    die("Error updating status: " . mysqli_error($conn));
}
?>
