<?php
session_start();
include('config.php');

// Pastikan login sebagai pasien
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header('Location: login.php?role=patient');
    exit();
}

$patient_id = (int)$_SESSION['user_id'];

// Handle update data pasien
$message = "";
$status = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_patient'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $update_sql = "UPDATE patients SET 
                    name = '$name', 
                    gender = '$gender', 
                    dob = '$dob', 
                    email = '$email', 
                    phone = '$phone', 
                    address = '$address'
                   WHERE patient_id = '$patient_id'";
    
    if ($conn->query($update_sql)) {
        $status = "success";
        $message = "Profile updated successfully!";
    } else {
        $status = "error";
        $message = "Failed to update profile: " . mysqli_error($conn);
    }
}

// Ambil data pasien
$sql_patient = "SELECT * FROM patients WHERE patient_id = '$patient_id'";
$result_patient = $conn->query($sql_patient);
$patient = $result_patient->fetch_assoc();

// Ambil daftar janji temu pasien (Limit 5 untuk profil)
$sql_appointments = "SELECT a.*, d.name AS doctor_name, s.name AS speciality_name
                     FROM appointments a
                     JOIN doctors d ON a.doctor_id = d.doctor_id
                     LEFT JOIN specialities s ON d.speciality_id = s.speciality_id
                     WHERE a.patient_id = '$patient_id'
                     ORDER BY a.appointment_datetime DESC LIMIT 5";
$result_appointments = $conn->query($sql_appointments);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Hospital Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .profile-container { display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }
        .profile-card { background: white; border-radius: var(--radius); padding: 30px; box-shadow: var(--shadow); border: 1px solid #E2E8F0; }
        .profile-header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e2e8f0; }
        .profile-avatar { width: 100px; height: 100px; border-radius: 50%; background: var(--clr-primary); color: white; font-size: 2.5rem; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-weight: 600; }
        
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; font-weight: 500; }
        .alert-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        @media (max-width: 992px) {
            .profile-container { grid-template-columns: 1fr; }
        }
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
                <li><a href="profile.php" class="active"><i class="fas fa-user"></i> My Profile</a></li>
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
            <h2>My Profile</h2>
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

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $status; ?>">
                <i class="fas <?php echo $status === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="profile-container">
            <!-- Left: Info & Avatar -->
            <div class="profile-card" style="align-self: start;">
                <div class="profile-header">
                    <div class="profile-avatar"><?php echo substr($patient['name'], 0, 1); ?></div>
                    <h3 style="color:var(--clr-dark); font-size:1.4rem; margin-bottom:5px;"><?php echo htmlspecialchars($patient['name']); ?></h3>
                    <p style="color:#64748b; font-size:0.9rem;">Patient ID: #<?php echo str_pad($patient['patient_id'], 5, '0', STR_PAD_LEFT); ?></p>
                </div>
                
                <div style="display:flex; flex-direction:column; gap:15px;">
                    <div><strong style="color:#475569; font-size:0.85rem; text-transform:uppercase;">Email</strong><br><span style="color:var(--clr-dark);"><?php echo htmlspecialchars($patient['email']); ?></span></div>
                    <div><strong style="color:#475569; font-size:0.85rem; text-transform:uppercase;">Phone</strong><br><span style="color:var(--clr-dark);"><?php echo htmlspecialchars($patient['phone']); ?></span></div>
                    <div><strong style="color:#475569; font-size:0.85rem; text-transform:uppercase;">Date of Birth</strong><br><span style="color:var(--clr-dark);"><?php echo date('d M Y', strtotime($patient['dob'])); ?></span></div>
                </div>
            </div>

            <!-- Right: Edit Form & Recent Activity -->
            <div>
                <div class="premium-form-card" style="margin-bottom: 30px; max-width:100%; padding:30px; border-radius:16px;">
                    <h3 style="margin-bottom: 20px; color:var(--clr-dark); border-bottom:1px solid #f1f5f9; padding-bottom:15px; font-family:'Outfit';">Edit Profile Information</h3>
                    <form method="POST">
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                            <div class="premium-form-group">
                                <label class="premium-label">Full Name</label>
                                <div class="premium-input-wrapper">
                                    <i class="far fa-user"></i>
                                    <input type="text" name="name" class="premium-input" value="<?= htmlspecialchars($patient['name']) ?>" required>
                                </div>
                            </div>
                            <div class="premium-form-group">
                                <label class="premium-label">Gender</label>
                                <div class="premium-input-wrapper">
                                    <i class="fas fa-venus-mars"></i>
                                    <select name="gender" class="premium-input" required>
                                        <option value="male" <?= $patient['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                                        <option value="female" <?= $patient['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                                    </select>
                                </div>
                            </div>
                            <div class="premium-form-group">
                                <label class="premium-label">Date of Birth</label>
                                <div class="premium-input-wrapper">
                                    <i class="far fa-calendar-alt"></i>
                                    <input type="date" name="dob" class="premium-input" value="<?= $patient['dob'] ?>" required>
                                </div>
                            </div>
                            <div class="premium-form-group">
                                <label class="premium-label">Email Address</label>
                                <div class="premium-input-wrapper">
                                    <i class="far fa-envelope"></i>
                                    <input type="email" name="email" class="premium-input" value="<?= htmlspecialchars($patient['email']) ?>" required>
                                </div>
                            </div>
                            <div class="premium-form-group" style="grid-column: 1 / -1;">
                                <label class="premium-label">Phone Number</label>
                                <div class="premium-input-wrapper">
                                    <i class="fas fa-phone-alt"></i>
                                    <input type="text" name="phone" class="premium-input" value="<?= htmlspecialchars($patient['phone']) ?>" required>
                                </div>
                            </div>
                            <div class="premium-form-group" style="grid-column: 1 / -1;">
                                <label class="premium-label">Address</label>
                                <div class="premium-input-wrapper">
                                    <i class="fas fa-map-marker-alt" style="top:16px;"></i>
                                    <textarea name="address" class="premium-input" rows="3" required><?= htmlspecialchars($patient['address']) ?></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="update_patient" class="premium-btn" style="margin-top:10px;"><i class="fas fa-save"></i> Save Changes</button>
                    </form>
                </div>

                <div class="section" style="padding:0; overflow:hidden;">
                    <div class="section-header" style="padding:20px; margin:0; border-bottom:1px solid #e2e8f0; background:#f8fafc;">
                        <h3 class="section-title">Recent Appointments</h3>
                    </div>
                    <table class="schedule-list" style="margin:0;">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_appointments->num_rows > 0): ?>
                                <?php while ($row = $result_appointments->fetch_assoc()): ?>
                                    <tr>
                                        <td style="font-weight:500;"><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                                        <td><?php echo date("d M Y H:i", strtotime($row['appointment_datetime'])); ?></td>
                                        <td>
                                            <?php
                                            $badge = 'pending';
                                            if($row['status'] == 'confirmed' || $row['status'] == 'scheduled') $badge = 'confirmed';
                                            else if($row['status'] == 'completed') $badge = 'completed';
                                            else if($row['status'] == 'cancelled') $badge = 'cancelled';
                                            ?>
                                            <span class="status-badge badge-<?= $badge; ?>"><?= ucfirst(htmlspecialchars($row['status'])); ?></span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align:center; padding:20px; color:#94a3b8;">No recent appointments found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
