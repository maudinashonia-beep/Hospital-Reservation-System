<?php
include 'config.php';

session_start();
$doctor_id = $_SESSION['doctor_id']; // Misal dokter dengan ID 1, bisa diganti sesuai login/session

// Ambil data dokter
$query_doctor = "SELECT d.*, s.name AS speciality FROM doctors d
                 LEFT JOIN specialities s ON d.speciality_id = s.speciality_id
                 WHERE doctor_id = $doctor_id";
$result_doctor = mysqli_query($conn, $query_doctor);
$doctor = mysqli_fetch_assoc($result_doctor);

// Ambil jadwal
$query_schedule = "SELECT * FROM doctor_schedules WHERE doctor_id = $doctor_id ORDER BY FIELD(day_of_week, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')";
$result_schedule = mysqli_query($conn, $query_schedule);

// Ambil statistik appointment
$query_stats = "SELECT 
    COUNT(*) AS total_appointments,
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed,
     SUM(CASE WHEN status = 'scheduled' THEN 1 ELSE 0 END) AS scheduled,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) AS cancelled,
    SUM(CASE WHEN status = 'no_show' THEN 1 ELSE 0 END) AS no_show
    FROM appointments WHERE doctor_id = $doctor_id";
$result_stats = mysqli_query($conn, $query_stats);
$stats = mysqli_fetch_assoc($result_stats);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Profile - Hospital Reservation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .profile-container { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .profile-card { background: white; border-radius: var(--radius); padding: 30px; box-shadow: var(--shadow); border: 1px solid #E2E8F0; }
        .profile-item { margin-bottom: 15px; font-size: 1rem; }
        .profile-item strong { color: var(--clr-dark); display: inline-block; width: 150px; }
    </style>
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
                <li><a href="dashboard_doctor.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="doctor_appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="doctor_schedule.php"><i class="fas fa-clock"></i> Practice Schedule</a></li>
                <li><a href="patient_records.php"><i class="fas fa-file-medical"></i> Medical Records</a></li>
                <li><a href="view_patient.php"><i class="fas fa-user-injured"></i> Patient Master Data</a></li>
            </ul>
            
            <h4>Settings</h4>
            <ul>
                <li><a href="doctor_profile.php" class="active"><i class="fas fa-user-md"></i> My Profile</a></li>
                <li><a href="doctor_settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="main-header">
            <h2>Doctor Profile</h2>
            <a href="dashboard_doctor.php" class="btn btn-outlined"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <div class="profile-container">
            <div class="profile-card">
                <h3 class="section-title"><i class="fas fa-user-md" style="color:var(--clr-primary);"></i> Personal Information</h3>
                <hr style="border:none; border-top:1px solid #f1f5f9; margin-bottom:20px;">
                <div class="profile-item"><strong>Name:</strong> Dr. <?= htmlspecialchars($doctor['name']) ?></div>
                <div class="profile-item"><strong>Specialties:</strong> <?= htmlspecialchars($doctor['speciality']) ?></div>
                <div class="profile-item"><strong>Email:</strong> <?= htmlspecialchars($doctor['email']) ?></div>
                <div class="profile-item"><strong>Phone:</strong> <?= htmlspecialchars($doctor['phone']) ?></div>
                <div class="profile-item"><strong>Practice Room:</strong> <?= htmlspecialchars($doctor['room_number']) ?></div>
            </div>

            <div class="profile-card">
                <h3 class="section-title"><i class="fas fa-clock" style="color:var(--clr-primary);"></i> Practice Schedule</h3>
                <hr style="border:none; border-top:1px solid #f1f5f9; margin-bottom:20px;">
                <table class="schedule-list">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = mysqli_fetch_assoc($result_schedule)): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($row['day_of_week']) ?></strong></td>
                            <td><span class="time-slot"><i class="far fa-clock"></i> <?= substr($row['start_time'], 0, 5) ?> - <?= substr($row['end_time'], 0, 5) ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section">
            <h3 class="section-title">Appointment Statistics</h3>
            <div class="summary-cards" style="margin-top: 20px;">
                <div class="summary-card">
                    <div class="card-icon card-blue"><i class="fas fa-notes-medical"></i></div>
                    <div class="card-info">
                        <h3><?= $stats['total_appointments'] ?? 0 ?></h3>
                        <p>Total</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="card-icon card-orange"><i class="fas fa-calendar-alt"></i></div>
                    <div class="card-info">
                        <h3><?= $stats['scheduled'] ?? 0 ?></h3>
                        <p>Scheduled</p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="card-icon" style="background:#feeaea; color:#e74c3c;"><i class="fas fa-times-circle"></i></div>
                    <div class="card-info">
                        <h3><?= $stats['cancelled'] ?? 0 ?></h3>
                        <p>Cancelled</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
