<?php 
// Koneksi ke database
include 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_GET['role'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $check_query = ($role === 'doctor') ?
        "SELECT * FROM doctors WHERE username = '$username' OR email = '$email'" :
        "SELECT * FROM patients WHERE username = '$username' OR email = '$email'";

    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error = 'Username atau Email sudah terdaftar!';
    } else {
        if ($role === 'doctor') {
            $speciality_id = $_POST['speciality_id'];
            $room_number   = $_POST['room_number'];

            $insert_query = "INSERT INTO doctors (name, speciality_id, room_number, phone, email, username, password, created_at) 
                             VALUES ('$name', '$speciality_id', '$room_number', '$phone', '$email', '$username', '$password', NOW())";
        } else if ($role === 'patient') {
            $dob     = $_POST['dob'];
            $gender  = $_POST['gender'];
            $address = $_POST['address'];

            $insert_query = "INSERT INTO patients (name, dob, gender, phone, email, address, username, password) 
                             VALUES ('$name', '$dob', '$gender', '$phone', '$email', '$address', '$username', '$password')";
        }

        if (mysqli_query($conn, $insert_query)) {
            $success = 'Pendaftaran berhasil! Silakan login.';
        } else {
            $error = 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar - Hospital Reservation</title>
    <link rel="stylesheet" href="assets/css/main.css">
</head>
<body class="auth-body">

<a href="login.php?role=<?= $_GET['role'] ?? 'patient' ?>" class="back-btn">
  <i class="fas fa-arrow-left"></i> Back to Login
</a>

<div class="auth-wrapper wide">
    <div class="auth-card wide">
        <div class="auth-header">
            <h2>Register as <?php echo ucfirst($_GET['role']); ?></h2>
            <p>Join Graha Medika to manage your healthcare</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php?role=<?php echo $_GET['role']; ?>">
            <div class="form-grid">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                    <i class="fas fa-id-card form-icon"></i>
                </div>

                <?php if ($_GET['role'] === 'patient'): ?>
                    <div class="form-group">
                        <input type="date" name="dob" class="form-control" required>
                        <i class="fas fa-calendar form-icon"></i>
                    </div>
                    <div class="form-group">
                        <select name="gender" class="form-control" required>
                            <option value="">-- Gender --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <i class="fas fa-venus-mars form-icon"></i>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
                    <i class="fas fa-phone form-icon"></i>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                    <i class="fas fa-envelope form-icon"></i>
                </div>

                <?php if ($_GET['role'] === 'patient'): ?>
                    <div class="form-group">
                        <input type="text" name="address" class="form-control" placeholder="Address" required>
                        <i class="fas fa-map-marker-alt form-icon"></i>
                    </div>
                <?php endif; ?>

                <?php if ($_GET['role'] === 'doctor'): ?>
                    <div class="form-group">
                        <select name="speciality_id" class="form-control" required>
                            <option value="">-- Speciality --</option>
                            <option value="1">General Practitioner</option>
                            <option value="2">Pediatrician</option>
                            <option value="3">Dentist</option>
                        </select>
                        <i class="fas fa-stethoscope form-icon"></i>
                    </div>
                    <div class="form-group">
                        <input type="text" name="room_number" class="form-control" placeholder="Room Number" required>
                        <i class="fas fa-door-closed form-icon"></i>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                    <i class="fas fa-user form-icon"></i>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <i class="fas fa-lock form-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-primary btn-block" style="margin-top: 1rem;">Register</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php?role=<?php echo $_GET['role']; ?>">Login Now</a>
        </div>
    </div>
</div>

</body>
</html>
