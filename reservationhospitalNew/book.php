<?php
session_start(); // Penting untuk menggunakan $_SESSION

$conn = mysqli_connect('localhost', 'root', '', 'reservation_hospital');

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan pasien sudah login dan session tersedia
    if (!isset($_SESSION['patient_id'])) {
        echo "<script>alert('Anda harus login terlebih dahulu.'); window.location.href='login.php';</script>";
        exit();
    }

    $doctor_id = $_POST['doctor_id'];
    $patient_id = $_SESSION['patient_id']; // Ambil dari session
    $appointment_datetime = date('Y-m-d H:i:s'); // Default sekarang
    $status = 'scheduled';

    $query = "INSERT INTO appointments (patient_id, doctor_id, appointment_datetime, status)
              VALUES ('$patient_id', '$doctor_id', '$appointment_datetime', '$status')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Booking berhasil!'); window.location.href='doctors.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
