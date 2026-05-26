<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header('Location: login.php?role=doctor');
    exit();
}

$doctor_id = (int)$_SESSION['user_id'];
$appointment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

if ($appointment_id === 0) {
    die("Invalid appointment ID");
}

// Ubah status janji menjadi completed
$query = "UPDATE appointments SET status = 'completed' WHERE appointment_id = $appointment_id AND doctor_id = $doctor_id";
if (mysqli_query($conn, $query)) {
    // Arahkan ke form medical record dengan membawa patient_id jika ada
    $redirect_url = 'form_medical_record.php';
    if ($patient_id > 0) {
        $redirect_url .= "?patient_id=" . $patient_id;
    }
    header("Location: $redirect_url");
    exit();
} else {
    die("Error updating appointment: " . mysqli_error($conn));
}
?>
