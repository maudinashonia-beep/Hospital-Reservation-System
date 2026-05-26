<?php
session_start();
include 'config.php';

// Pastikan login sebagai pasien
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header('Location: login.php?role=patient');
    exit();
}

$patient_id = (int)$_SESSION['user_id'];

// Ambil info pasien untuk header
$patient_query = "SELECT * FROM patients WHERE patient_id = $patient_id";
$patient_res = mysqli_query($conn, $patient_query);
$patient = mysqli_fetch_assoc($patient_res);

// Ambil jadwal dokter
$query = "SELECT d.doctor_id, d.name AS doctor_name, s.name AS speciality, ds.day_of_week, ds.start_time, ds.end_time
          FROM doctors d
          LEFT JOIN specialities s ON d.speciality_id = s.speciality_id
          JOIN doctor_schedules ds ON d.doctor_id = ds.doctor_id
          ORDER BY d.name, ds.day_of_week";

$result = mysqli_query($conn, $query);

// Gabung data dokter berdasarkan doctor_id
$doctors = [];
while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['doctor_id'];
    if (!isset($doctors[$id])) {
        $doctors[$id] = [
            'doctor_name' => $row['doctor_name'],
            'speciality' => $row['speciality'] ?? 'General',
            'schedules' => []
        ];
    }
    $doctors[$id]['schedules'][] = [
        'day_of_week' => $row['day_of_week'],
        'start_time' => substr($row['start_time'], 0, 5),
        'end_time' => substr($row['end_time'], 0, 5),
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Schedules - Hospital Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .doctors-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; }
        .doctor-card { background: white; border-radius: var(--radius); padding: 25px; box-shadow: var(--shadow); border: 1px solid #E2E8F0; transition: var(--transition); display: flex; flex-direction: column; }
        .doctor-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .doc-header { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px; }
        .doc-avatar { width: 60px; height: 60px; border-radius: 50%; background: #eff6ff; color: var(--clr-primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        .doc-name { font-weight: 600; font-size: 1.1rem; color: var(--clr-dark); margin-bottom: 3px; }
        .doc-spec { color: #64748b; font-size: 0.9rem; }
        .schedule-table th, .schedule-table td { padding: 8px; border-bottom: 1px solid #f1f5f9; text-align: center; font-size: 0.9rem; }
        .schedule-table th { color: #475569; font-weight: 600; background: #f8fafc; text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.8rem; }
        .schedule-table td { color: #334155; }
        .btn-book { margin-top: auto; padding: 10px; border-radius: 8px; background: #eff6ff; color: var(--clr-primary); font-weight: 500; border: 1px solid #bfdbfe; transition: var(--transition); cursor: pointer; width: 100%; font-size: 0.95rem; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-book:hover { background: var(--clr-primary); color: white; border-color: var(--clr-primary); }
    </style>
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
                <li><a href="dashboard_patient.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="make_appointments.php"><i class="fas fa-calendar-plus"></i> Make Appointment</a></li>
                <li><a href="my_appointments.php"><i class="fas fa-calendar-check"></i> Appointment</a></li>
                <li><a href="medical_records.php"><i class="fas fa-file-medical"></i> Medical Record</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            </ul>
            
            <h4>More</h4>
            <ul>
                <li><a href="doctors.php" class="active"><i class="fas fa-user-md"></i> Doctor</a></li>
                <li><a href="help.php"><i class="fas fa-question-circle"></i> Help</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h2>Our Specialists</h2>
            <div class="user-info">
                <div class="user-profile">
                    <?php echo substr($patient['name'] ?? 'U', 0, 1); ?>
                </div>
                <div>
                    <p class="doctor-name"><?php echo $patient['name'] ?? 'User'; ?></p>
                    <p class="doctor-role">Patient</p>
                </div>
            </div>
        </div>

        <div class="doctors-grid">
            <?php foreach ($doctors as $doctor_id => $doctor): ?>
            <div class="doctor-card">
                <div class="doc-header">
                    <div class="doc-avatar"><i class="fas fa-user-md"></i></div>
                    <div>
                        <div class="doc-name"><?= htmlspecialchars($doctor['doctor_name']); ?></div>
                        <div class="doc-spec"><?= htmlspecialchars($doctor['speciality']); ?></div>
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <table class="schedule-table" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Start</th>
                                <th>End</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($doctor['schedules'] as $schedule): ?>
                            <tr>
                                <td style="font-weight:500; color:var(--clr-dark);"><?= ucfirst(htmlspecialchars($schedule['day_of_week'])); ?></td>
                                <td><?= $schedule['start_time']; ?></td>
                                <td><?= $schedule['end_time']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($doctor['schedules'])): ?>
                            <tr><td colspan="3" style="color:#94a3b8; padding:15px;">No schedule available</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: auto;">
                    <form action="make_appointments.php" method="GET">
                        <button type="submit" class="btn-book"><i class="far fa-calendar-check"></i> Book Appointment</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($doctors)): ?>
                <div style="grid-column: 1 / -1; text-align:center; padding:50px; background:white; border-radius:12px;">
                    <i class="fas fa-user-md" style="font-size:3rem; color:#cbd5e1; margin-bottom:15px;"></i>
                    <p style="color:#64748b;">No doctors are currently available.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
