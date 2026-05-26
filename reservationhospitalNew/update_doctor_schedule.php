<?php
session_start();
include 'config.php';

$doctor_id = $_SESSION['doctor_id'] ?? null;

if (!$doctor_id) {
    die("Dokter belum login.");
}

// Ambil data dari form
$active_days = $_POST['active_days'] ?? [];
$start_times = $_POST['start_time'];
$end_times = $_POST['end_time'];

// Hapus jadwal lama dokter
$delete_sql = "DELETE FROM doctor_schedules WHERE doctor_id = '$doctor_id'";
$conn->query($delete_sql);

// Simpan ulang jadwal baru
foreach ($active_days as $day) {
    $start = $start_times[$day];
    $end = $end_times[$day];

    if (!empty($start) && !empty($end)) {
        // Capitalize the first letter of the day
        $day_clean = ucfirst(strtolower($day));  // Ensures the first letter is capitalized

        $stmt = $conn->prepare("INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $doctor_id, $day_clean, $start, $end);
        $stmt->execute();
    }
}

header("Location: doctor_settings.php?success=1");
exit();
