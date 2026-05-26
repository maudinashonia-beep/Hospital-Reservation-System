<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

// Informasi jadwal praktek dokter bersifat publik sehingga dapat diakses oleh tamu di homepage

$doctor_id = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;

if ($doctor_id === 0) {
    echo json_encode(['error' => 'Invalid doctor ID']);
    exit();
}

// Map nama hari ke format angka untuk JavaScript (0=Sunday, 1=Monday, dll)
$day_map = [
    'Sunday' => 0,
    'Monday' => 1,
    'Tuesday' => 2,
    'Wednesday' => 3,
    'Thursday' => 4,
    'Friday' => 5,
    'Saturday' => 6
];

$query = "SELECT day_of_week, start_time, end_time FROM doctor_schedules WHERE doctor_id = $doctor_id";
$result = mysqli_query($conn, $query);

$schedules = [];

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $day_name = ucfirst(strtolower($row['day_of_week']));
        if (isset($day_map[$day_name])) {
            $schedules[] = [
                'day_name' => $day_name,
                'day_index' => $day_map[$day_name],
                'start_time' => substr($row['start_time'], 0, 5), // format HH:MM
                'end_time' => substr($row['end_time'], 0, 5)
            ];
        }
    }
}

echo json_encode(['success' => true, 'schedules' => $schedules]);
exit();
?>
