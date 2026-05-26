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

$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

$query = "SELECT mr.record_id, d.name AS doctor_name, mr.diagnosis, mr.prescription, mr.notes, mr.created_at
          FROM medical_records mr
          JOIN doctors d ON mr.doctor_id = d.doctor_id
          WHERE mr.patient_id = '$patient_id'";

if (!empty($start_date) && !empty($end_date)) {
    $query .= " AND DATE(mr.created_at) BETWEEN '$start_date' AND '$end_date'";
}

$query .= " ORDER BY mr.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Records - Hospital Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .filter-form { display: flex; gap: 15px; margin-bottom: 30px; align-items: center; background: white; padding: 20px; border-radius: var(--radius); box-shadow: var(--shadow); flex-wrap: wrap; }
        .filter-form input[type="date"] { padding: 10px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: 'Inter', sans-serif; outline: none; }
        .filter-form input[type="date"]:focus { border-color: var(--clr-primary); }
        .btn-filter { padding: 10px 20px; background: var(--clr-primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500; }
        .btn-clear { padding: 10px 20px; background: #94a3b8; color: white; text-decoration: none; border-radius: 8px; font-weight: 500; }
        .btn-filter:hover { background: #1e40af; }
        .btn-clear:hover { background: #64748b; }
        
        .record-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 20px; transition: var(--transition); border-left: 4px solid var(--clr-primary); }
        .record-card:hover { box-shadow: var(--shadow); transform: translateY(-3px); }
        .record-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
        .doctor-name { font-weight: 600; font-size: 1.2rem; color: var(--clr-dark); display: flex; align-items: center; gap: 10px; }
        .record-date { color: #64748b; font-size: 0.9rem; background: #f1f5f9; padding: 5px 12px; border-radius: 20px; }
        .record-body { display: grid; gap: 15px; }
        .record-item .label { font-weight: 600; color: #475569; margin-bottom: 5px; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .record-item .text { background: #f8fafc; padding: 15px; border-radius: 8px; color: #334155; line-height: 1.6; white-space: pre-line; }
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
                <li><a href="medical_records.php" class="active"><i class="fas fa-file-medical"></i> Medical Record</a></li>
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
            <h2>Medical Records</h2>
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

        <form class="filter-form" method="GET">
            <div style="display:flex; align-items:center; gap:10px;">
                <label style="font-weight:500; color:var(--clr-dark);">From:</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($start_date); ?>" required>
            </div>
            <div style="display:flex; align-items:center; gap:10px;">
                <label style="font-weight:500; color:var(--clr-dark);">To:</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($end_date); ?>" required>
            </div>
            <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filter</button>
            <a href="?" class="btn-clear"><i class="fas fa-times"></i> Clear</a>
        </form>

        <div class="records-container">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <div class="record-card">
                        <div class="record-header">
                            <div class="doctor-name"><i class="fas fa-user-md" style="color:var(--clr-primary);"></i> Dr. <?= htmlspecialchars($row['doctor_name']); ?></div>
                            <div class="record-date"><i class="far fa-calendar-alt"></i> <?= date('d M Y', strtotime($row['created_at'])); ?></div>
                        </div>
                        <div class="record-body">
                            <div class="record-item">
                                <div class="label">Diagnosis</div>
                                <div class="text"><?= htmlspecialchars($row['diagnosis']); ?></div>
                            </div>
                            <div class="record-item">
                                <div class="label">Prescription</div>
                                <div class="text"><?= htmlspecialchars($row['prescription']); ?></div>
                            </div>
                            <div class="record-item">
                                <div class="label">Notes</div>
                                <div class="text"><?= htmlspecialchars($row['notes']); ?></div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="section" style="text-align:center; padding:50px 20px;">
                    <div class="empty-state">
                        <i class="fas fa-folder-open empty-icon"></i>
                        <p class="empty-text">No medical records found.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body> 
</html>