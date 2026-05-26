<?php
session_start();
include 'config.php';

// Pastikan login sebagai pasien
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header('Location: login.php?role=patient');
    exit();
}

$patient_id = $_SESSION['user_id'];

// **Perbaikan di sini:** pakai kolom patient_id
$query = "SELECT * FROM patients WHERE patient_id = $patient_id";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    die("Error: Data pasien tidak ditemukan");
}
$patient = mysqli_fetch_assoc($result);

// Ambil janji mendatang
$upcoming_query = "
  SELECT a.*, s.name as name_speciality,
         d.name           AS doctor_name,
         d.speciality_id  AS speciality_id
  FROM appointments a
  JOIN doctors d 
    ON a.doctor_id = d.doctor_id
  JOIN specialities s 
    ON d.speciality_id = s.speciality_id
  WHERE a.patient_id     = $patient_id
    AND a.appointment_datetime >= CURDATE()
  ORDER BY a.appointment_datetime
  LIMIT 3
";

// eksekusi query
$upcoming_result = mysqli_query($conn, $upcoming_query);

// jika gagal, simpan error ke log dan treat as empty array
if (!$upcoming_result) {
    // catat error ke error_log untuk debugging
    error_log("MySQL error on upcoming_query: " . mysqli_error($conn));
    $upcoming_rows = [];
} else {
    // kumpulkan semua baris ke array
    $upcoming_rows = [];
    while ($row = mysqli_fetch_assoc($upcoming_result)) {
        $upcoming_rows[] = $row;
    }
}
// Format tanggal ke format Indonesia
function formatDate($date) {
    $timestamp = strtotime($date);
    return date("d F Y", $timestamp);
}

// Format waktu 24 jam ke format 12 jam
function formatTime($time) {
    $timestamp = strtotime($time);
    return date("h:i A", $timestamp);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien - Hospital Reservation</title>
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
            <p>Patient Dashboard</p>
        </div>
        
        <div class="sidebar-menu">
            <h4>Main Menu</h4>
            <ul>
                <li><a href="dashboard_patient.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="make_appointments.php"><i class="fas fa-calendar-plus"></i> Make Appointment</a></li>
                <li><a href="my_appointments.php"><i class="fas fa-calendar-check"></i> Appointment</a></li>
                <li><a href="medical_records.php"><i class="fas fa-file-medical"></i> Medical Record</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            </ul>
            
            <h4>More</h4>
            <ul>
                <li><a href="doctors.php"><i class="fas fa-user-md"></i> Doctor</a></li>
                <li><a href="help.php"><i class="fas fa-question-circle"></i> Help</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h2>Dashboard</h2>
            <div class="user-info">
                <div class="user-profile">
                    <?php echo substr($patient['name'] ?? 'U', 0, 1); ?>
                </div>
                <div>
                    <p><?php echo $patient['name'] ?? 'User'; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h2>Welcome, <?php echo $patient['name'] ?? 'User'; ?>!</h2>
            <p>Manage your visit schedule and medical records easily through our patient dashboard.</p>
        </div>
        
        <!-- Stats -->
        <div class="stats-container">
            <?php
            // Hitung total janji
            $total_appointments_query = "SELECT COUNT(*) AS total FROM appointments WHERE patient_id = $patient_id";
            $total_appointments_result = mysqli_query($conn, $total_appointments_query);
            $total_appointments = 0;
            if ($total_appointments_result && mysqli_num_rows($total_appointments_result) > 0) {
                $total_appointments_data = mysqli_fetch_assoc($total_appointments_result);
                $total_appointments = $total_appointments_data['total'];
            }
            
            // Hitung dokter yang dikunjungi
            $total_doctors_query = "SELECT COUNT(DISTINCT doctor_id) AS total FROM appointments WHERE patient_id = $patient_id";
            $total_doctors_result = mysqli_query($conn, $total_doctors_query);
            $total_doctors = 0;
            if ($total_doctors_result && mysqli_num_rows($total_doctors_result) > 0) {
                $total_doctors_data = mysqli_fetch_assoc($total_doctors_result);
                $total_doctors = $total_doctors_data['total'];
            }
            
            // Hitung rekam medis
            $total_records_query = "SELECT COUNT(*) AS total FROM medical_records WHERE patient_id = $patient_id";
            $total_records_result = mysqli_query($conn, $total_records_query);
            $total_records = 0;
            if ($total_records_result && mysqli_num_rows($total_records_result) > 0) {
                $total_records_data = mysqli_fetch_assoc($total_records_result);
                $total_records = $total_records_data['total'];
            }
            ?>
            
            <div class="stat-card">
                <div class="stat-icon appointments-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_appointments; ?></h3>
                    <p>Total Visits</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon doctors-icon">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_doctors; ?></h3>
                    <p>Doctor visited</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon records-icon">
                    <i class="fas fa-file-medical"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_records; ?></h3>
                    <p>Medical Record</p>
                </div>
            </div>
        </div>
        
        <!-- Upcoming Appointments -->
        <div class="section">
            <div class="section-header">
                <h3>Upcoming Appointments</h3>
                <a href="my_appointments.php" class="view-all">Show All</a>
            </div>
            
            <div class="appointment-list">
                <?php if (count($upcoming_rows) > 0): ?>
                <?php foreach ($upcoming_rows as $appointment): ?>
                    <div class="appointment-card">
                    <div class="appointment-date">
                        <strong><?= formatDate($appointment['appointment_datetime']) ?></strong>
                    
                    </div>
                    <div class="appointment-doctor">
                        <strong>Dr. <?= htmlspecialchars($appointment['doctor_name']) ?></strong>
                    </div>
                    <div class="appointment-specialty">
                        <strong>ID Spesialisasi: <?= $appointment['name_speciality'] ?></strong>
                    </div>
                    <!-- dst… -->
                    </div>
                <?php endforeach; ?>
                <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 20px;">
                    <p>There is No Promise that Will Come</p>
                    <a href="make_appointments.php" class="btn btn-primary mt-2" style="margin-top: 10px;">
                    New Appointment
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        
        <!-- Quick Access -->
        <div class="section">
            <div class="section-header">
                <h3>Quick Access</h3>
            </div>
            
            <div class="quick-access">
                <a href="make_appointments.php" class="quick-card">
                    <div class="quick-icon new-appointment-icon">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="quick-title">Appointment</div>
                    <div class="quick-desc">Schedule a visit with your doctor</div>
                </a>
                
                <a href="medical_records.php" class="quick-card">
                    <div class="quick-icon medical-records-icon">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="quick-title">Medical Record</div>
                    <div class="quick-desc">View your medical record history</div>
                </a>
                
                <a href="profile.php" class="quick-card">
                    <div class="quick-icon update-profile-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="quick-title">Edit Profile</div>
                    <div class="quick-desc">Update your personal information</div>
                </a>
                
                <a href="help.php" class="quick-card">
                    <div class="quick-icon contact-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div class="quick-title">Help</div>
                    <div class="quick-desc">Contact our team</div>
                </a>
            </div>
        </div>
        
    </div>
</div>

<script>
// Tambahkan waktu server untuk memastikan halaman real-time
function updateClock() {
    const now = new Date();
    const hours = now.getHours().toString().padStart(2, '0');
    const minutes = now.getMinutes().toString().padStart(2, '0');
    const seconds = now.getSeconds().toString().padStart(2, '0');
    const timeString = `${hours}:${minutes}:${seconds}`;
    
    // Jika ada elemen clock, update waktu
    if (document.getElementById('clock')) {
        document.getElementById('clock').textContent = timeString;
    }
}

// Update setiap detik
setInterval(updateClock, 1000);
updateClock(); // Panggil sekali di awal
</script>

</body>
</html>