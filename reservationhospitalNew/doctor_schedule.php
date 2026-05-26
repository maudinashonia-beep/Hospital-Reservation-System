<?php
session_start();
include('config.php');

// Cek apakah dokter sudah login
if (!isset($_SESSION['doctor_id'])) {
    echo "Anda belum login sebagai dokter.";
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

// Query untuk ambil jadwal dokter yang login
$sql = "SELECT d.name AS doctor_name, s.name AS specialis_name, ds.day_of_week, ds.start_time, ds.end_time
        FROM doctors d
        INNER JOIN specialities s ON d.speciality_id = s.speciality_id
        INNER JOIN doctor_schedules ds ON d.doctor_id = ds.doctor_id
        WHERE d.doctor_id = '$doctor_id'
        ORDER BY FIELD(ds.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')";

$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practice Schedule - Doctor Dashboard</title>
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
                <li><a href="dashboard_doctor.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="doctor_appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="doctor_schedule.php" class="active"><i class="fas fa-clock"></i> Practice Schedule</a></li>
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
            <h2>My Practice Schedule</h2>
            <a href="doctor_settings.php" class="btn"><i class="fas fa-edit"></i> Edit Schedule</a>
        </div>

        <div class="section">
            <?php
            if ($result->num_rows > 0) {
                echo "<table class='schedule-list'>";
                echo "<thead><tr><th>Doctor Name</th><th>Specialist</th><th>Day</th><th>Time</th></tr></thead><tbody>";
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td><strong>" . htmlspecialchars($row['doctor_name']) . "</strong></td>";
                    echo "<td>" . htmlspecialchars($row['specialis_name']) . "</td>";
                    echo "<td><span class='status-badge badge-confirmed'>" . htmlspecialchars($row['day_of_week']) . "</span></td>";
                    echo "<td><span class='time-slot'><i class='far fa-clock'></i> " . substr($row['start_time'], 0, 5) . " - " . substr($row['end_time'], 0, 5) . "</span></td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='empty-state'><div class='empty-icon'><i class='far fa-calendar-times'></i></div><div class='empty-text'>You haven't set up a practice schedule yet.</div><a href='doctor_settings.php' class='btn'>Set up Schedule</a></div>";
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>

</html>
