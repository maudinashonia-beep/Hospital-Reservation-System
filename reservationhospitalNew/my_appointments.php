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

// Ambil daftar janji
$query = "SELECT a.appointment_id, d.name AS doctor_name, s.name AS speciality, a.appointment_datetime, a.status
          FROM appointments a
          JOIN doctors d ON a.doctor_id = d.doctor_id
          LEFT JOIN specialities s ON d.speciality_id = s.speciality_id
          WHERE a.patient_id = '$patient_id'
          ORDER BY a.appointment_datetime DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments - Hospital Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="my_appointments.php" class="active"><i class="fas fa-calendar-check"></i> Appointment</a></li>
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
            <h2>My Appointments</h2>
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

        <!-- Appointments Table -->
        <div class="section">
            <div class="section-header">
                <h3 class="section-title">Appointment History</h3>
            </div>
            
            <div style="overflow-x:auto;">
                <table class="schedule-list">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Specialties</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td style="font-weight:500; color:var(--clr-dark);"><i class="fas fa-user-md" style="color:var(--clr-primary); margin-right:8px;"></i> <?= htmlspecialchars($row['doctor_name']); ?></td>
                                <td><?= htmlspecialchars($row['speciality'] ?? 'General'); ?></td>
                                <td><span class="time-slot"><i class="far fa-clock"></i> <?= date('d M Y, H:i', strtotime($row['appointment_datetime'])); ?></span></td>
                                <td>
                                    <?php
                                    $badge = 'pending';
                                    if($row['status'] == 'confirmed' || $row['status'] == 'scheduled') $badge = 'confirmed';
                                    else if($row['status'] == 'completed') $badge = 'completed';
                                    else if($row['status'] == 'cancelled') $badge = 'cancelled';
                                    ?>
                                    <span class="status-badge badge-<?= $badge; ?>"><?= ucfirst(htmlspecialchars($row['status'])); ?></span>
                                </td>
                                <td>
                                    <?php if($row['status'] === 'scheduled' || $row['status'] === 'pending'): ?>
                                        <form id="cancelForm<?= $row['appointment_id']; ?>" action="cancel.php" method="POST" style="margin:0;">
                                            <input type="hidden" name="appointment_id" value="<?= $row['appointment_id']; ?>">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmCancel(<?= $row['appointment_id']; ?>)"><i class="fas fa-times"></i> Cancel</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color:#aaa;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:30px;">
                                    <div class="empty-state" style="padding:0;">
                                        <i class="far fa-calendar-times empty-icon"></i>
                                        <p class="empty-text">No appointment yet.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmCancel(appointmentId) {
    Swal.fire({
        title: 'Cancel Appointment?',
        text: "Are you sure you want to cancel this appointment?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74c3c',
        cancelButtonColor: '#bdc3c7',
        confirmButtonText: 'Yes, cancel it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancelForm' + appointmentId).submit();
        }
    });
}
</script>
</body>
</html>
