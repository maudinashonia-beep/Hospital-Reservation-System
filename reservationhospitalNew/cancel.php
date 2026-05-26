<?php
$conn = mysqli_connect('localhost', 'root', '', 'reservation_hospital');

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];

    $query = "UPDATE appointments SET status = 'cancelled' WHERE appointment_id = '$appointment_id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Appointment berhasil dibatalkan.'); window.location.href='my_appointments.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
