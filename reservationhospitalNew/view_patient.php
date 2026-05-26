<?php 
include 'config.php';

// Hari ini (jika perlu dipakai)
$today = date('Y-m-d');
function formatDate($date) {
    return date('d M Y', strtotime($date));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Master Data - Doctor Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="doctor_schedule.php"><i class="fas fa-clock"></i> Practice Schedule</a></li>
                <li><a href="patient_records.php"><i class="fas fa-file-medical"></i> Medical Records</a></li>
                <li><a href="view_patient.php" class="active"><i class="fas fa-user-injured"></i> Patient Master Data</a></li>
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
            <h2>Data Patient</h2>
            <div class="doctor-info">
                <!-- <div class="doctor-avatar"><?= strtoupper(substr($doctor['name'],0,1)) ?></div> -->
                <!-- <div>
                    <div class="doctor-name">Dr. <?= htmlspecialchars($doctor['name']) ?></div>
                    <div class="doctor-role">Spesialis ID: <?= $doctor['speciality_id'] ?></div>
                </div> -->
            </div>
        </div>

        <div class="section">
    <div class="section-header">
        <h3 class="section-title">Patient List</h3>
        <a href="form_add_update_patient.php" class="btn">+ Add Patient</a>
    </div>
    <table class="schedule-list">
        <thead>
            <tr>
                <th>Name</th>
                <th>Gender</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php
    $result = $conn->query("SELECT * FROM patients");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $statusClass = ($row['status'] === 'active') ? 'badge-confirmed' : 'badge-cancelled';
            $statusText = ucfirst($row['status']);
            // Tombol untuk Activate atau Deactivate sesuai dengan status pasien
            $actionButton = ($row['status'] === 'active') ? 
                "<a href='delete_patient.php?action=deactivate&id={$row['patient_id']}' class='btn btn-sm btn-outlined deactivate-btn'>Deactivate Acc</a>" :
                "<a href='delete_patient.php?action=activate&id={$row['patient_id']}' class='btn btn-sm btn-outlined activate-btn'>Activate</a>";

            // Tombol Delete
            $deleteButton = "<a href='delete_patient.php?action=delete&id={$row['patient_id']}' class='btn btn-sm delete-btn'>Delete</a>";

            echo "<tr>
                    <td>{$row['name']}</td>
                    <td>{$row['gender']}</td>
                    <td>{$row['phone']}</td>
                    <td>{$row['email']}</td>
                    <td><span class='status-badge $statusClass'>$statusText</span></td>
                    <td class='action-buttons'>
                        <a href='form_add_update_patient.php?id={$row['patient_id']}' class='btn btn-sm'>Edit</a>
                        $actionButton
                        $deleteButton
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='6' class='empty-text'>Belum ada data pasien.</td></tr>";
    }
    ?>
</tbody>

    </table>
</div>
    </div>
</div>
<script>
    // SweetAlert untuk konfirmasi Deactivate/Activate
    document.querySelectorAll('.deactivate-btn, .activate-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const action = this.href;
            const actionType = this.innerText.trim();

            Swal.fire({
                title: `Are you sure want to ${actionType.toLowerCase()}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = action;
                }
            });
        });
    });

    // SweetAlert untuk konfirmasi Delete
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const action = this.href;

            Swal.fire({
                title: 'Are you sure you want to delete this patient? All records will also be deleted',
                icon: 'error',
                showCancelButton: true,
                confirmButtonText: 'Delete',
                cancelButtonText: 'cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = action;
                }
            });
        });
    });
</script>


</body>
</html>
