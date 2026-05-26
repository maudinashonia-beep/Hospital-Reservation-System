<?php
// Koneksi ke database
include 'config.php';

$error = '';
$success = '';

// Proses form registrasi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mengambil data dan sanitasi
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $status = "active";
    // Validasi data
    if (empty($name) || empty($dob) || empty($gender) || empty($phone) || 
        empty($email) || empty($address) || empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } else if ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } else {
        // Cek apakah username/email sudah terdaftar
        $check_query = "SELECT * FROM patients WHERE username = '$username' OR email = '$email'";
        $check_result = mysqli_query($conn, $check_query);
       
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username atau Email sudah terdaftar!";
        } else {
            // Menggunakan prepared statement untuk mencegah SQL injection
            $insert_query = "INSERT INTO patients (name, dob, gender, phone, email, address, username, password, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, 'sssssssss', $name, $dob, $gender, $phone, $email, $address, $username, $password, $status);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Pendaftaran berhasil! Anda sekarang dapat login.";
            } else {
                $error = "Terjadi kesalahan: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pasien - Hospital Reservation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f0f4f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .register-container {
            width: 100%;
            max-width: 700px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }
        
        h2 {
            color: #3498db;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .address-input {
            grid-column: span 2;
        }
        
        .error, .success {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .error {
            background-color: #fdecea;
            color: #e74c3c;
        }
        
        .success {
            background-color: #e3f7ee;
            color: #2ecc71;
        }
        
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #2980b9;
        }
        
        .login-link {
            text-align: center;
            margin-top: 15px;
        }
        
        .login-link a {
            color: #3498db;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #3498db;
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .back-home svg {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<a href="index.php" class="back-home">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"></line>
        <polyline points="12 19 5 12 12 5"></polyline>
    </svg>
    Kembali ke Beranda
</a>

<div class="register-container">
    <h2>Registrasi Pasien Baru</h2>
    
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="register_patient.php">
        <div class="form-grid">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="dob">Tanggal Lahir</label>
                <input type="date" id="dob" name="dob" required>
            </div>
            
            <div class="form-group">
                <label for="gender">Jenis Kelamin</label>
                <select id="gender" name="gender" required>
                    <option value="">-- Pilih --</option>
                    <option value="Male">Laki-laki</option>
                    <option value="Female">Perempuan</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="phone">Nomor Telepon</label>
                <input type="tel" id="phone" name="phone" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group address-input">
                <label for="address">Alamat</label>
                <input type="text" id="address" name="address" required>
            </div>
        </div>
        
        <button type="submit">Daftar</button>
        
        <div class="login-link">
            Sudah memiliki akun? <a href="login.php?role=patient">Login disini</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validasi password match
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        if (passwordInput.value !== confirmPasswordInput.value) {
            e.preventDefault();
            alert('Password dan konfirmasi password tidak cocok!');
        }
    });
});
</script>

</body>
</html>