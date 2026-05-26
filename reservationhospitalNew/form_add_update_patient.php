<?php
include 'config.php';
session_start();
$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $username = $_POST['username'];
    $status = "active";

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $password = $data['password']; // Password lama dari data yang sudah di-fetch
    }

    if ($_POST['id']) {
        // Edit
        $stmt = $conn->prepare("UPDATE patients SET name=?, dob=?, gender=?, phone=?, email=?, address=?, username=?, password=?, status=? WHERE patient_id=?");
        $stmt->bind_param("sssssssssi", $name, $dob, $gender, $phone, $email, $address, $username, $password, $status, $_POST['id']);
    } else {
        // Tambah
        $stmt = $conn->prepare("INSERT INTO patients (name, dob, gender, phone, email, address, username, password, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $name, $dob, $gender, $phone, $email, $address, $username, $password, $status);
    }

    $stmt->execute();
    header("Location: view_patient.php");
    exit();
}

$data = [
    'name' => '', 'dob' => '', 'gender' => 'male',
    'phone' => '', 'email' => '', 'address' => '',
    'username' => '', 'password' => '', 'status' => 'active'
];

if ($id) {
    $res = $conn->query("SELECT * FROM patients WHERE patient_id = $id");
    $data = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? 'Edit' : 'Tambah' ?> Pasien</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
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

        /* Form Layout */
        form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        form label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
        }

        form input, form select, form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1em;
        }

        form button {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        form button:hover {
            background-color: #2980b9;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-buttons a {
            padding: 8px 15px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9em;
        }

        .action-buttons a:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Hospital RS</h3>
            <p>Doctor Dashboard</p>
        </div>
        
        <div class="sidebar-menu">
            <h4>Main Menu</h4>
            <ul>
                <li><a href="dashboard_doctor.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="doctor_appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <!-- <li><a href="patient_records.php"><i class="fas fa-users"></i> Pasien Saya</a></li> -->
                <li><a href="doctor_schedule.php"><i class="fas fa-clock"></i> Practical schedule</a></li>
                <li><a href="patient_records.php"><i class="fas fa-file-medical"></i> Medical Record</a></li>
                <li><a href="view_patient.php"><i class="fas fa-user-injured"></i> Master Patient Data</a></li>
            </ul>
            
            <h4>Settings</h4>
            <ul>
                <li><a href="doctor_profile.php"><i class="fas fa-user-md"></i>My Profile </a></li>
                <li><a href="doctor_settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="main-header">
            <h2><?= $id ? 'Edit' : 'Tambah' ?> Patient</h2>
        </div>

        <!-- Form -->
        <form method="post">
            <input type="hidden" name="id" value="<?= $id ?>">
            <label>Name:</label><input type="text" name="name" value="<?= $data['name'] ?>" required><br>
            <label>Date of birth:</label><input type="date" name="dob" value="<?= $data['dob'] ?>" required><br>
            <label>Gender:</label>
            <select name="gender">
                <option value="male" <?= $data['gender'] == 'male' ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= $data['gender'] == 'female' ? 'selected' : '' ?>>Female</option>
                <option value="other" <?= $data['gender'] == 'other' ? 'selected' : '' ?>>Other</option>
            </select><br>
            <label>Phone:</label><input type="text" name="phone" value="<?= $data['phone'] ?>"><br>
            <label>Email:</label><input type="email" name="email" value="<?= $data['email'] ?>"><br>
            <label>Address:</label><textarea name="address"><?= $data['address'] ?></textarea><br>
            <label>Username:</label><input type="text" name="username" value="<?= $data['username'] ?>" required><br>
            
            <label>Password: <small>If not changed, leave blank</small></label><input type="password" name="password" ><br>
           
            <button type="submit">Save</button>
        </form>

        
    </div>
</div>

</body>
</html>

