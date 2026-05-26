<?php
session_start();
include 'config.php'; // koneksi $conn

// 1) Tentukan dulu role dari query string (sebelum dipakai di redirect)
$valid_roles = ['patient','doctor'];
$role = isset($_GET['role']) && in_array($_GET['role'], $valid_roles)
          ? $_GET['role']
          : 'patient';

// 2) Cek session dan redirect jika role sama
if (isset($_SESSION['user_id'], $_SESSION['role']) && $_SESSION['role'] === $role) {
    header('Location: ' . ($role==='patient' 
                           ? 'dashboard_patient.php' 
                           : 'dashboard_doctor.php'));
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // sanitasi input
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // siapkan query sesuai role
    if ($role === 'patient') {
        $sql = "SELECT patient_id, username, name 
                FROM patients 
                WHERE username = '$username' 
                  AND password = '$password' AND status != 'deactive'";
    } else {
        $sql = "SELECT doctor_id, username, name 
                FROM doctors 
                WHERE username = '$username' 
                  AND password = '$password'";
    }

    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) > 0) {
        $user = mysqli_fetch_assoc($res);

        // **Perbaikan di sini:** pakai patient_id, bukan id
        if ($role === 'patient') {
            $_SESSION['user_id'] = $user['patient_id'];
            $_SESSION['patient_id'] = $user['patient_id'];
        } else {
            $_SESSION['user_id'] = $user['doctor_id'];
            $_SESSION['doctor_id'] = $user['doctor_id'];
        }
        $_SESSION['username'] = $user['username'];
        $_SESSION['name']     = $user['name'];
        $_SESSION['role']     = $role;

        header('Location: ' . ($role==='patient' 
                               ? 'dashboard_patient.php' 
                               : 'dashboard_doctor.php'));
        exit();
    } else {
        $error = 'incorrect username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login – RS Sehat Selalu</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>
<body class="auth-body">

  <a href="index.php" class="back-btn">
    <i class="fas fa-arrow-left"></i> Home
  </a>

  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-tabs">
        <div class="auth-tab <?= $role==='patient'?'active':'' ?>" onclick="window.location.href='login.php?role=patient'">Patient</div>
        <div class="auth-tab <?= $role==='doctor'?'active':'' ?>" onclick="window.location.href='login.php?role=doctor'">Doctor</div>
      </div>

      <div class="auth-header">
        <h2>Login <?= $role==='patient'?'Patient':'Doctor' ?></h2>
        <p>Welcome back to Graha Medika</p>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-error"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php?role=<?= $role ?>">
        <div class="form-group">
          <input id="username" type="text" name="username" class="form-control" required placeholder="Username">
          <i class="fas fa-user form-icon"></i>
        </div>
        <div class="form-group">
          <input id="password" type="password" name="password" class="form-control" required placeholder="Password">
          <i class="fas fa-lock form-icon"></i>
        </div>
        <button type="submit" class="btn-primary btn-block">Log in</button>
      </form>

      <p class="login-link">
        Don't have an account yet? <a href="register.php?role=<?= $role ?>">Register</a>
      </p>
    </div>
  </div>

</body>
</html>