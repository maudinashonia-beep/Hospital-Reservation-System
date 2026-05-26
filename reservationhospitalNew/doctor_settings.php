<?php
session_start();
include 'config.php';

// Cek apakah dokter sudah login
$doctor_id = $_SESSION['doctor_id'];

// Ambil jadwal yang sudah ada
$schedule_sql = "SELECT * FROM doctor_schedules WHERE doctor_id = '$doctor_id'";
$schedule_result = $conn->query($schedule_sql);

// Array hari
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$schedule_data = [];
while ($row = $schedule_result->fetch_assoc()) {
    $schedule_data[$row['day_of_week']] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Practice Schedule Settings - Doctor Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .schedule-table th, .schedule-table td {
            text-align: center;
        }
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
                <li><a href="doctor_profile.php"><i class="fas fa-user-md"></i> My Profile</a></li>
                <li><a href="doctor_settings.php" class="active"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="main-header">
            <h2>Practice Schedule Setting</h2>
            <a href="dashboard_doctor.php" class="btn btn-outlined"><i class="fas fa-arrow-left"></i> Back</a>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Schedule saved successfully!
            </div>
        <?php endif; ?>

        <div class="premium-form-card" style="max-width:800px;">
            <form action="update_doctor_schedule.php" method="post">
                <table class="schedule-list schedule-table" style="margin-bottom: 25px;">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($days as $day): 
                            $day_key = strtolower($day);
                            $exists = isset($schedule_data[$day_key]);
                        ?>
                        <tr>
                            <td><strong><?= $day ?></strong></td>
                            <td><input type="time" name="start_time[<?= $day_key ?>]" class="premium-input" style="padding:10px; width:auto;" value="<?= $exists ? $schedule_data[$day_key]['start_time'] : '' ?>"></td>
                            <td><input type="time" name="end_time[<?= $day_key ?>]" class="premium-input" style="padding:10px; width:auto;" value="<?= $exists ? $schedule_data[$day_key]['end_time'] : '' ?>"></td>
                            <td><input type="checkbox" name="active_days[]" value="<?= $day_key ?>" <?= $exists ? 'checked' : '' ?> style="transform: scale(1.5);"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" class="premium-btn">
                    <i class="fas fa-save"></i> Save Schedule
                </button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
