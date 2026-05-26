<?php
$conn = mysqli_connect('localhost', 'root', '', 'reservation_hospital');

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// simulasi user login (misalnya patient_id = 1)
$patient_id = 1;

$query = "SELECT mr.record_id, d.name AS doctor_name, mr.diagnosis, mr.prescription, mr.notes, mr.created_at
          FROM medical_records mr
          JOIN doctors d ON mr.doctor_id = d.doctor_id
          WHERE mr.patient_id = '$patient_id'
          ORDER BY mr.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekam Medis Saya</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
 
    <style>
       
       
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 14px;
            border-bottom: 1px solid #eaeaea;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #1976d2;
            color: white;
            text-transform: uppercase;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background-color: #f1f8ff;
        }
        tr:hover {
            background-color: #e3f2fd;
        }
        .note-box {
            max-width: 350px;
            white-space: pre-line;
        }
    </style>
     <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f0f4f8;
            color: #333;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: #ecf0f1;
            min-height: 100vh;
            position: fixed;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar-header h3 {
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .sidebar-header p {
            font-size: 0.9em;
            opacity: 0.7;
        }
        
        .sidebar-menu {
            padding: 20px 0;
        }
        
        .sidebar-menu h4 {
            padding: 0 20px;
            font-size: 0.8em;
            text-transform: uppercase;
            color: #95a5a6;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .sidebar-menu ul {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 2px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a.active, .sidebar-menu a:hover {
            background: #34495e;
            border-left-color: #3498db;
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            font-size: 1.1em;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 30px;
        }
        
        .main-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .main-header h2 {
            color: #2c3e50;
            font-weight: 600;
        }
        
        .doctor-info {
            display: flex;
            align-items: center;
        }
        
        .doctor-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #3498db;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
            margin-right: 10px;
        }
        
        .doctor-name {
            font-weight: 500;
        }
        
        .doctor-role {
            font-size: 0.8em;
            color: #7f8c8d;
        }
        
        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }
        
        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.5em;
        }
        
        .card-blue {
            background: #e7f5fe;
            color: #3498db;
        }
        
        .card-green {
            background: #e3f7ee;
            color: #2ecc71;
        }
        
        .card-orange {
            background: #fff8e6;
            color: #f39c12;
        }
        
        .card-purple {
            background: #f4ecff;
            color: #9b59b6;
        }
        
        .card-info h3 {
            font-size: 1.5em;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .card-info p {
            font-size: 0.9em;
            color: #7f8c8d;
        }
        
        /* Sections */
        .section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 1.2em;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background: #3498db;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-outlined {
            background: transparent;
            border: 1px solid #3498db;
            color: #3498db;
        }
        
        .btn-outlined:hover {
            background: #f0f7fb;
        }
        
        /* Today's Schedule */
        .schedule-list {
            width: 100%;
            border-collapse: collapse;
        }
        
        .schedule-list th {
            text-align: left;
            padding: 12px 15px;
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 500;
            font-size: 0.9em;
            border-bottom: 1px solid #e9ecef;
        }
        
        .schedule-list td {
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            color: #333;
            font-size: 0.95em;
        }
        
        .schedule-list tr:last-child td {
            border-bottom: none;
        }
        
        .schedule-list tr:hover {
            background: #f8f9fa;
        }
        
        .time-slot {
            display: inline-block;
            padding: 5px 10px;
            background: #e7f5fe;
            color: #3498db;
            border-radius: 5px;
            font-size: 0.85em;
        }
        
        .gender-icon {
            margin-right: 5px;
        }
        
        .patient-info {
            display: flex;
            align-items: center;
        }
        
        .patient-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #f0f7fb;
            color: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 0.9em;
        }
        
        .patient-name {
            font-weight: 500;
        }
        
        .patient-details {
            font-size: 0.85em;
            color: #7f8c8d;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 500;
        }
        
        .badge-confirmed {
            background: #e3f7ee;
            color: #2ecc71;
        }
        
        .badge-pending {
            background: #fff8e6;
            color: #f39c12;
        }
        
        .badge-cancelled {
            background: #feeaea;
            color: #e74c3c;
        }
        
        .badge-completed {
            background: #f4ecff;
            color: #9b59b6;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 0.8em;
        }
        
        /* Upcoming Appointments */
        .appointment-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .appointment-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            transition: all 0.3s;
            border-left: 4px solid #3498db;
        }
        
        
        .appointment-date {
            margin-bottom: 15px;
            font-size: 0.9em;
            color: #7f8c8d;
        }
        
        .appointment-date strong {
            color: #2c3e50;
            font-size: 1.1em;
        }
        
        .patient-row {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .appointment-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        
        .appointment-status {
            display: inline-block;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 30px;
        }
        
        .empty-icon {
            font-size: 3em;
            color: #bdc3c7;
            margin-bottom: 15px;
        }
        
        .empty-text {
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .summary-cards, .appointment-cards {
                grid-template-columns: 1fr;
            }
            
            .schedule-list {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
<div class="sidebar">
        <div class="sidebar-header">
            <h3>Hospital RS</h3>
            <p>Doctor Dashboard</p>
        </div>
        
        <div class="sidebar-menu">
            <h4>Main Menu</h4>
            <ul>
                <li><a href="dashboard_doctor.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="doctor_appointments.php"><i class="fas fa-calendar-check"></i> Semua Janji</a></li>
                <li><a href="patient_records.php"><i class="fas fa-users"></i> Pasien Saya</a></li>
                <li><a href="doctor_schedule.php"><i class="fas fa-clock"></i> Jadwal Praktek</a></li>
                <li><a href="medical_records.php"><i class="fas fa-file-medical"></i> Rekam Medis</a></li>
                <li><a href="view_patient.php"><i class="fas fa-user-injured"></i> Master Data Pasien</a></li>
                
            </ul>
            
            <h4>Settings</h4>
            <ul>
                <li><a href="doctor_profile.php"><i class="fas fa-user-md"></i> Profil Saya</a></li>
                <li><a href="doctor_settings.php"><i class="fas fa-cog"></i> Pengaturan</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    <div class="main-content">
        <div class="card">
    <h2 style="text-align:center; margin-bottom: 30px;">Rekam Medis Saya</h2>

    <style>
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }
        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 20px;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }
      
        .card h3 {
            margin-top: 0;
            color: #1976d2;
            font-size: 18px;
            margin-bottom: 12px;
        }
        .card p {
            margin: 4px 0;
            font-size: 14px;
            color: #333;
        }
        .card .date {
            margin-top: 10px;
            font-size: 13px;
            color: #777;
        }
        .no-records {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #999;
        }
    </style>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <div class="card-grid">
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="card">
                    <h3>Dr. <?= htmlspecialchars($row['doctor_name']); ?></h3>
                    <p><strong>Diagnosa:</strong> <?= nl2br(htmlspecialchars($row['diagnosis'])); ?></p>
                    <p><strong>Resep:</strong> <?= nl2br(htmlspecialchars($row['prescription'])); ?></p>
                    <p><strong>Catatan:</strong> <?= nl2br(htmlspecialchars($row['notes'])); ?></p>
                    <p class="date"><strong>Tanggal:</strong> <?= date('d M Y', strtotime($row['created_at'])); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="no-records">Belum ada rekam medis tersedia.</div>
    <?php endif; ?>
</div>
    </div>
            <!-- </div> -->
</body>
</html>
