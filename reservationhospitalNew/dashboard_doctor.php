<?php
session_start();
include 'config.php'; // koneksi database $conn

// Cek apakah user sudah login sebagai dokter
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'doctor') {
    header('Location: login.php?role=doctor');
    exit();
}

// Ambil data dokter
$doctor_id = (int) $_SESSION['user_id'];

$doctor_query = "SELECT doctor_id, name, speciality_id, room_number, phone, email, username FROM doctors WHERE doctor_id = $doctor_id";
$doctor_res = mysqli_query($conn, $doctor_query);
if (!$doctor_res) {
    die("Query Error (doctors): " . mysqli_error($conn));
}
$doctor = mysqli_fetch_assoc($doctor_res);

// Format hari ini
date_default_timezone_set('Asia/Jakarta');
$today = date('Y-m-d');

// Query untuk mengambil semua appointment pada tanggal hari ini (tanpa memperhatikan jam)
$today_query = "
  SELECT a.*, 
         p.patient_id,
         p.name AS patient_name, 
         p.phone, 
         p.email, 
         p.gender, 
         p.dob 
  FROM appointments a
  JOIN patients p ON a.patient_id = p.patient_id
  WHERE a.doctor_id = $doctor_id 
    AND DATE(a.appointment_datetime) = '$today'
  ORDER BY a.appointment_datetime ASC
";

$today_result = mysqli_query($conn, $today_query);
if (!$today_result) {
    error_log("Error Query Hari Ini: " . mysqli_error($conn));
    $today_count = 0;
} else {
    $today_count = mysqli_num_rows($today_result);
}

// Query untuk appointment mendatang
$now = date('Y-m-d H:i:s');

$upcoming_query = "
  SELECT a.*, 
         p.patient_id,
         p.name AS patient_name 
  FROM appointments a
  JOIN patients p ON a.patient_id = p.patient_id
  WHERE a.doctor_id = $doctor_id 
    AND DATE(a.appointment_datetime) > '$today'
  ORDER BY a.appointment_datetime
  LIMIT 5
";

$upcoming_result = mysqli_query($conn, $upcoming_query);
if (!$upcoming_result) {
    error_log("Error Query Mendatang: " . mysqli_error($conn));
    $upcoming_count = 0;
} else {
    $upcoming_count = mysqli_num_rows($upcoming_result);
}

// Format tanggal ke format Indonesia
define('LOCALE', 'id_ID');
function formatDate($date) {
    setlocale(LC_TIME, LOCALE);
    $timestamp = strtotime($date);
    return strftime('%d %B %Y', $timestamp);
}

// Format waktu 24 jam ke format 12 jam
function formatTime($time) {
    $timestamp = strtotime($time);
    return date('h:i A', $timestamp);
}

// Hitung umur berdasarkan tanggal lahir
function calculateAge($dob) {
    $today = new DateTime();
    $birth = new DateTime($dob);
    return $birth->diff($today)->y;
}

// Hitung statistik
$stats_query = "
  SELECT
    (SELECT COUNT(*) FROM appointments WHERE doctor_id = $doctor_id) AS total_appointments,
    (SELECT COUNT(*) FROM appointments WHERE doctor_id = $doctor_id AND DATE(appointment_datetime) = '$today') AS today_appointments,
    (SELECT COUNT(DISTINCT patient_id) FROM appointments WHERE doctor_id = $doctor_id) AS total_patients,
    (SELECT COUNT(*) FROM appointments WHERE doctor_id = $doctor_id AND status = 'completed') AS completed_appointments
  FROM DUAL
";
$stats_res = mysqli_query($conn, $stats_query);
if (!$stats_res) {
    die("Query Error (stats): " . mysqli_error($conn));
}
$stats = mysqli_fetch_assoc($stats_res);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Dokter - Hospital Reservation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Graha Medika</h3>
            <p>Doctor Dashboard</p>
        </div>
        
        <div class="sidebar-menu">
            <h4>Main Menu</h4>
            <ul>
                <li><a href="dashboard_doctor.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="doctor_appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="doctor_schedule.php"><i class="fas fa-clock"></i> Practice Schedule</a></li>
                <li><a href="patient_records.php"><i class="fas fa-file-medical"></i> Medical Records</a></li>
                <li><a href="view_patient.php"><i class="fas fa-user-injured"></i> Patient Master Data</a></li>
            </ul>
            
            <h4>Settings</h4>
            <ul>
                <li><a href="doctor_profile.php"><i class="fas fa-user-md"></i> My Profile</a></li>
                <li><a href="doctor_settings.php"><i class="fas fa-cog"></i> Setting</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="main-header">
            <h2>Dashboard Doctor</h2>
            <div class="doctor-info">
                <div class="doctor-avatar"><?= strtoupper(substr($doctor['name'],0,1)) ?></div>
                <div>
                    <div class="doctor-name">Dr. <?= htmlspecialchars($doctor['name']) ?></div>
                    <div class="doctor-role">Spesialis ID: <?= $doctor['speciality_id'] ?></div>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <div class="card-icon card-blue"><i class="fas fa-calendar-check"></i></div>
                <div class="card-info">
                    <h3><?= $stats['today_appointments'] ?></h3>
                    <p>Today's Visits</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon card-green"><i class="fas fa-users"></i></div>
                <div class="card-info">
                    <h3><?= $stats['total_patients'] ?></h3>
                    <p>Total Patients</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon card-orange"><i class="fas fa-check-circle"></i></div>
                <div class="card-info">
                    <h3><?= $stats['completed_appointments'] ?></h3>
                    <p>Completed Visits</p>
                </div>
            </div>
            <div class="summary-card">
                <div class="card-icon" style="background:#f3e8ff; color:#9333ea;"><i class="fas fa-notes-medical"></i></div>
                <div class="card-info">
                    <h3><?= $stats['total_appointments'] ?></h3>
                    <p>Total Appointments</p>
                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="section">
            <div class="section-header">
                <h3 class="section-title">Today's Schedule (<?= formatDate($today) ?>)</h3>
                <a href="doctor_appointments.php?date=<?= $today ?>" class="btn btn-outlined">Show All</a>
            </div>
            <?php if ($today_count > 0): ?>
            <table class="schedule-list">
                <thead>
                    <tr><th>Time</th><th>Patient</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                <?php while ($apt = mysqli_fetch_assoc($today_result)): ?>
                    <tr>
                        <td><span class="time-slot"><?= formatTime($apt['appointment_datetime']) ?></span></td>
                        <td>
                            <div class="patient-info">
                                <div class="patient-avatar"><?= strtoupper(substr($apt['patient_name'],0,1)) ?></div>
                                <div>
                                    <div class="patient-name"><?= htmlspecialchars($apt['patient_name']) ?></div>
                                    <div class="patient-details">
                                        <?php if ($apt['gender']==='male'): ?><i class="fas fa-mars"></i><?php else: ?><i class="fas fa-venus"></i><?php endif; ?>
                                        <?= calculateAge($apt['dob']) ?> tahun • <?= htmlspecialchars($apt['phone']) ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php $badge = ['confirmed'=>'badge-confirmed','pending'=>'badge-pending','cancelled'=>'badge-cancelled','completed'=>'badge-completed'][$apt['status']] ?? 'badge-pending'; ?>
                            <span class="status-badge <?= $badge ?>"><?= ucfirst($apt['status']) ?></span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="view_patient.php?id=<?= $apt['patient_id'] ?>" class="btn btn-sm btn-outlined"><i class="fas fa-eye"></i></a>
                                <?php if (in_array($apt['status'], ['confirmed','pending'])): ?>
                                <a href="start_appointment.php?id=<?= $apt['appointment_id'] ?>&patient_id=<?= $apt['patient_id'] ?>" class="btn btn-sm"><i class="fas fa-play"></i> Start</a>
                                <a href="update_status.php?id=<?= $apt['appointment_id'] ?>&status=cancelled" class="btn btn-sm btn-outlined" style="color:#e74c3c;border-color:#e74c3c;"><i class="fas fa-times"></i> Cancel</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="far fa-calendar"></i></div>
                <div class="empty-text">No Appointments for Today.</div>
                <a href="doctor_schedule.php" class="btn">Manage Schedule</a>
            </div>
            <?php endif; ?>
        </div>


        <!-- Upcoming Appointments -->
        <div class="section">
            <div class="section-header">
                <h3 class="section-title">Upcoming Appointments</h3>
                <a href="doctor_appointments.php?filter=upcoming" class="btn btn-outlined">Show All</a>
            </div>
            <?php if ($upcoming_count > 0): ?>
            <div class="appointment-cards">
                <?php while($up = mysqli_fetch_assoc($upcoming_result)): ?>
                <div class="appointment-card">
                    <div class="appointment-date"><i class="far fa-calendar-alt"></i> <strong><?= formatDate($up['appointment_datetime']) ?></strong></div>
                    <div class="patient-row">
                        <div class="patient-avatar"><?= strtoupper(substr($up['patient_name'],0,1)) ?></div>
                        <div>
                            <div class="patient-name"><?= htmlspecialchars($up['patient_name']) ?></div>
                            <div class="patient-details"><?= $up['notes']?:'Tidak ada catatan' ?></div>
                        </div>
                    </div>
                    <div class="appointment-footer">
                        <div class="appointment-status">
                            <?php 
                                $badge = ['confirmed'=>'badge-confirmed','pending'=>'badge-pending','cancelled'=>'badge-cancelled','completed'=>'badge-completed'][$up['status']] ?? 'badge-pending'; 
                            ?>
                            <span class="status-badge <?= $badge ?>"><?= ucfirst($up['status']) ?></span>
                        </div>
                     </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="far fa-calendar-check"></i></div>
                <div class="empty-text">No Appointments.</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>