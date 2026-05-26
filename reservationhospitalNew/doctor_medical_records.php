<?php
$conn = mysqli_connect('localhost', 'root', '', 'reservation_hospital');
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
session_start();
// Simulasi login dokter
$doctor_id = $_SESSION['doctor_id']; // Ganti dengan $_SESSION['doctor_id'] jika sudah login

$query = "SELECT mr.record_id, mr.patient_id, p.name AS patient_name, mr.diagnosis, mr.prescription, mr.notes, mr.created_at
          FROM medical_records mr
          JOIN patients p ON mr.patient_id = p.patient_id
          WHERE mr.doctor_id = '$doctor_id'
          ORDER BY mr.created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekam Medis Pasien</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f4f6f9;
            padding: 40px;
            color: #333;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .btn-add {
            display: inline-block;
            padding: 10px 18px;
            margin-bottom: 20px;
            background-color: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
        }
        .btn-add:hover {
            background-color: #125ea9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 14px;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: top;
        }
        th {
            background-color: #1565c0;
            color: white;
            text-transform: uppercase;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background-color: #f9fbfc;
        }
        tr:hover {
            background-color: #eef4ff;
        }
        .note-box {
            max-width: 300px;
            white-space: pre-line;
        }
        .btn-edit {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
        }
        .btn-edit:hover {
            background-color: #388e3c;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Rekam Medis Pasien Anda</h2>
    <a href="form_medical_record.php" class="btn-add">+ Tambah Rekam Medis</a>
    <table>
        <thead>
            <tr>
                <th>Nama Pasien</th>
                <th>Diagnosa</th>
                <th>Resep</th>
                <th>Catatan</th>
                <th>Tanggal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['patient_name']); ?></td>
                    <td class="note-box"><?= htmlspecialchars($row['diagnosis']); ?></td>
                    <td class="note-box"><?= htmlspecialchars($row['prescription']); ?></td>
                    <td class="note-box"><?= htmlspecialchars($row['notes']); ?></td>
                    <td><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                    <td>
                        <a href="form_medical_record.php?id=<?= $row['record_id']; ?>" class="btn-edit">Edit</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center;">Belum ada rekam medis.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
