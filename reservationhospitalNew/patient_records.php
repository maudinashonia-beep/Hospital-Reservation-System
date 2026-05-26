<?php
$conn = mysqli_connect('localhost', 'root', '', 'reservation_hospital');
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
session_start();
// ambil session dari login dokter
$doctor_id = $_SESSION['doctor_id']; 

$query = "SELECT mr.record_id, mr.patient_id, p.name AS patient_name, mr.diagnosis, mr.prescription, mr.notes, mr.created_at
          FROM medical_records mr
          JOIN patients p ON mr.patient_id = p.patient_id
          WHERE mr.doctor_id = '$doctor_id'
          ORDER BY mr.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records - Doctor Dashboard</title>
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
                <li><a href="doctor_appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="doctor_schedule.php"><i class="fas fa-clock"></i> Practice Schedule</a></li>
                <li><a href="patient_records.php" class="active"><i class="fas fa-file-medical"></i> Medical Records</a></li>
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
<div class="main-content">
    <div class="main-header">
        <h2> Patient's Medical Record</h2>
    </div>

    <div class="section">
        <div class="section-header" style="display:flex; justify-content:space-between; align-items:center;">
            <h3 class="section-title">Medical Record List</h3>
            <a href="form_medical_record.php" class="btn btn-add">+ Add Medical Record</a>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="appointment-cards">
                <?php while($row = mysqli_fetch_assoc($result)):
                    $tanggal = date('d M Y, H:i', strtotime($row['created_at']));
                ?>
                    <div class="appointment-card">
                        <div class="appointment-date">
                            <i class="fas fa-calendar-medical"></i>
                            <strong><?= $tanggal ?></strong>
                        </div>

                        <div class="patient-row">
                            <div class="patient-avatar"><?= strtoupper(substr($row['patient_name'], 0, 1)) ?></div>
                            <div>
                                <div class="patient-name"><?= htmlspecialchars($row['patient_name']) ?></div>
                                <div class="patient-details"><strong>Diagnosis:</strong> <?= htmlspecialchars($row['diagnosis']) ?></div>
                            </div>
                        </div>

                        <div class="record-info" style="background:#f8fafc; padding:15px; border-radius:8px; margin-top:15px;">
                            <p style="margin-bottom:8px;"><strong>Prescription:</strong> <br><?= nl2br(htmlspecialchars($row['prescription'])) ?></p>
                            <p><strong>Notes:</strong> <br><?= nl2br(htmlspecialchars($row['notes'])) ?></p>
                        </div>

                        <div class="appointment-footer">
                            <a href="form_medical_record.php?id=<?= $row['record_id']; ?>" class="btn btn-sm">Edit</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="far fa-file-medical-alt"></i></div>
                <div class="empty-text">No Medical Records to Display .</div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
