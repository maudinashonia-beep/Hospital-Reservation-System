<?php
// Mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah dokter sudah login
if (!isset($_SESSION['doctor_id'])) {
    echo "Anda belum login sebagai dokter.";
    exit;
}

$doctorId = $_SESSION['doctor_id'];

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "reservation_hospital");

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$today = date('Y-m-d');
$now = date('Y-m-d H:i:s');

// SQL dan Statement
$stmt = null;

// Filter berdasarkan tanggal
if (isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])) {
    $filterDate = $_GET['date'];
    $sql = "SELECT a.*, p.name AS patient_name 
            FROM appointments a
            JOIN patients p ON a.patient_id = p.patient_id
            WHERE a.doctor_id = ?
              AND DATE(a.appointment_datetime) = ?
            ORDER BY a.appointment_datetime ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $doctorId, $filterDate);

} elseif (isset($_GET['filter']) && $_GET['filter'] === 'upcoming') {
    $sql = "SELECT a.*, p.name AS patient_name 
            FROM appointments a
            JOIN patients p ON a.patient_id = p.patient_id
            WHERE a.doctor_id = ?
              AND a.appointment_datetime > ?
            ORDER BY a.appointment_datetime ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $doctorId, $now);

} else {
    // Semua janji
    $sql = "SELECT a.*, p.name AS patient_name 
            FROM appointments a
            JOIN patients p ON a.patient_id = p.patient_id
            WHERE a.doctor_id = ?
            ORDER BY a.appointment_datetime DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctorId);
}

// Eksekusi dan ambil hasil
if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
} else {
    echo "Terjadi kesalahan saat mengambil data.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Doctor Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

<div class="container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Graha Medika</h3>
            <p>Doctor Dashboard</p>
        </div>
        
        <div class="sidebar-menu">
            <h4>Main Menu</h4>
            <ul>
                <li><a href="dashboard_doctor.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="doctor_appointments.php" class="active"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="doctor_schedule.php"><i class="fas fa-clock"></i> Practice Schedule</a></li>
                <li><a href="patient_records.php"><i class="fas fa-file-medical"></i> Medical Records</a></li>
                <li><a href="view_patient.php"><i class="fas fa-user-injured"></i> Patient Master Data</a></li>
            </ul>
            
            <h4>Settings</h4>
            <ul>
                <li><a href="doctor_profile.php"><i class="fas fa-user-md"></i> My Profile</a></li>
                <li><a href="doctor_settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
    <div class="main-header">
        <h2>Doctor Appointments</h2>
    </div>

    <div class="section">
        <div class="section-header">
            <h3 class="section-title">All  of Appointments</h3>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="appointment-cards">
                <?php while ($row = $result->fetch_assoc()):
                    $status = strtolower($row['status']);
                    
                    // Menggunakan switch sebagai pengganti match
                    switch ($status) {
                        case 'confirmed':
                            $badgeClass = 'badge-confirmed';
                            break;
                        case 'completed':
                            $badgeClass = 'badge-completed';
                            break;
                        case 'cancelled':
                            $badgeClass = 'badge-cancelled';
                            break;
                        default:
                            $badgeClass = 'badge-pending';
                    }
                ?>
                    <div class="appointment-card">
                        <div class="appointment-date">
                            <i class="far fa-calendar-alt"></i> <strong><?= date('d M Y, H:i', strtotime($row['appointment_datetime'])) ?></strong>
                        </div>
                        <div class="patient-row">
                            <div class="patient-avatar"><?= strtoupper(substr($row['patient_name'], 0, 1)) ?></div>
                            <div>
                                <div class="patient-name"><?= htmlspecialchars($row['patient_name']) ?></div>
                                <div class="patient-details">Status: <span class="status-badge <?= $badgeClass ?>"><?= ucfirst($row['status']) ?></span></div>
                            </div>
                        </div>
                        <div class="appointment-footer" style="margin-top:10px; text-align:right;">
                            <?php if (in_array($status, ['confirmed','pending'])): ?>
                                <a href="start_appointment.php?id=<?= $row['appointment_id'] ?>&patient_id=<?= $row['patient_id'] ?>" class="btn btn-sm"><i class="fas fa-play"></i> Start</a>
                                <a href="update_status.php?id=<?= $row['appointment_id'] ?>&status=cancelled" class="btn btn-sm btn-outlined" style="color:#e74c3c;border-color:#e74c3c;"><i class="fas fa-times"></i> Cancel</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="far fa-calendar-times"></i></div>
                <div class="empty-text">No appointments.</div>
            </div>
        <?php endif; ?>
    </div>
    </div>
</div>

</body>
</html>