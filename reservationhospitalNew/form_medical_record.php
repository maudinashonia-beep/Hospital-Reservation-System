<?php
$conn = mysqli_connect('localhost', 'root', '', 'reservation_hospital');
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
session_start();
// Simulasi login dokter
$doctor_id = $_SESSION['doctor_id']; // Ganti dengan $_SESSION['doctor_id'] jika sudah login

// Ambil data pasien untuk dropdown
$patients = mysqli_query($conn, "SELECT patient_id, name FROM patients ORDER BY name ASC");

// Cek apakah mode edit
$is_edit = isset($_GET['id']);
$record = [
    'patient_id' => isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : '',
    'diagnosis' => '',
    'prescription' => '',
    'notes' => ''
];

if ($is_edit) {
    $record_id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM medical_records WHERE record_id='$record_id'");
    if ($data = mysqli_fetch_assoc($result)) {
        $record = $data;
    }
}

// Handle Submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $prescription = mysqli_real_escape_string($conn, $_POST['prescription']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    if ($is_edit) {
        // Update
        $update = "UPDATE medical_records SET 
                    diagnosis='$diagnosis', 
                    prescription='$prescription', 
                    notes='$notes' 
                   WHERE record_id='$record_id'";
        if (mysqli_query($conn, $update)) {
            echo "<script>alert('Medical record updated successfully'); window.location.href='patient_records.php?id=$record_id';</script>";
        } else {
            echo "Failed to add: " . mysqli_error($conn);
        }
    } else {
        // Create
        $sql = "INSERT INTO medical_records (patient_id, doctor_id, diagnosis, prescription, notes) 
                VALUES ('$patient_id', '$doctor_id', '$diagnosis', '$prescription', '$notes')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Medical record created successfully'); window.location.href='patient_records.php';</script>";
        } else {
            echo "Failed to add: " . mysqli_error($conn);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_edit ? 'Edit' : 'Create' ?> Medical Record</title>
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
            <h2><?= $is_edit ? 'Edit Medical Record' : 'Create Medical Record' ?></h2>
        </div>
        <div class="premium-form-card" style="max-width:100%;">
            <form method="POST">
                <?php if (!$is_edit): ?>
                <div class="premium-form-group">
                    <label class="premium-label" for="patient_id">Select Patient</label>
                    <div class="premium-input-wrapper">
                        <i class="fas fa-user"></i>
                        <select name="patient_id" class="premium-input" required>
                            <option value="">-- Select Patient --</option>
                            <?php while ($row = mysqli_fetch_assoc($patients)): ?>
                                <option value="<?= $row['patient_id']; ?>" <?= ($record['patient_id'] == $row['patient_id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($row['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <?php else: ?>
                <div class="premium-form-group">
                    <p style="margin-bottom:20px;"><strong>Patient ID:</strong> <?= htmlspecialchars($record['patient_id']) ?></p>
                </div>
                <?php endif; ?>

                <div class="premium-form-group">
                    <label class="premium-label">Diagnosis</label>
                    <div class="premium-input-wrapper">
                        <i class="fas fa-stethoscope" style="top:16px;"></i>
                        <textarea name="diagnosis" class="premium-input" rows="4" required><?= htmlspecialchars($record['diagnosis']); ?></textarea>
                    </div>
                </div>

                <div class="premium-form-group">
                    <label class="premium-label">Prescription (Recipe)</label>
                    <div class="premium-input-wrapper">
                        <i class="fas fa-pills" style="top:16px;"></i>
                        <textarea name="prescription" class="premium-input" rows="4"><?= htmlspecialchars($record['prescription']); ?></textarea>
                    </div>
                </div>

                <div class="premium-form-group">
                    <label class="premium-label">Additional Notes</label>
                    <div class="premium-input-wrapper">
                        <i class="fas fa-notes-medical" style="top:16px;"></i>
                        <textarea name="notes" class="premium-input" rows="3"><?= htmlspecialchars($record['notes']); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="premium-btn">
                    <i class="fas fa-save"></i> <?= $is_edit ? 'Update' : 'Save' ?> Medical Record
                </button>
            </form>
        </div>
</div>
</body>
</html>
