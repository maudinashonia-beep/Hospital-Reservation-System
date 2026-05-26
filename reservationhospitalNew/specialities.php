<?php
$conn = mysqli_connect('localhost', 'root', '', 'reservation_hospital');

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$query = "SELECT * FROM specialities ORDER BY speciality_id ASC";
$result = mysqli_query($conn, $query);
$specialities = [];
while ($row = mysqli_fetch_assoc($result)) {
    $specialities[] = $row;
}

$emojiMap = [
    1 => '🩺', // General Practitioner
    2 => '👶', // Pediatrician
    3 => '🤱', // Obstetrics & Gynecology
    4 => '🦷', // Dentistry
    5 => '🧴', // Dermatology
    6 => '❤️‍🩹', // Cardiology
    7 => '🧠', // Neurology
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Spesialisasi Dokter</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: #f7f9fc;
            margin: 0;
            padding: 50px 20px;
        }

        h2 {
            text-align: center;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 40px;
            font-size: 32px;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 30px;
            max-width: 1100px;
            margin: auto;
        }

        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 30px 20px;
            text-align: center;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
        }

        .emoji {
            font-size: 42px;
            margin-bottom: 12px;
        }

        .card h3 {
            font-size: 17px;
            color: #4a5568;
            margin: 0;
        }
    </style>
</head>
<body>

<h2>Spesialisasi Dokter</h2>

<div class="card-container">
    <?php foreach ($specialities as $s): ?>
        <div class="card">
            <div class="emoji"><?= $emojiMap[$s['speciality_id']] ?? '❔' ?></div>
            <h3><?= htmlspecialchars($s['name']) ?></h3>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
