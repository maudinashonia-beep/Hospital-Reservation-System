<?php
include 'config.php';

$id = $_GET['id'];
$action = $_GET['action'];

if ($action == 'delete') {
    // Hapus relasi dulu jika ada (contoh: appointments, medical_records, dsb.)
    $conn->query("DELETE FROM appointments WHERE patient_id = $id");
    $conn->query("DELETE FROM medical_records WHERE patient_id = $id");

    // Hapus pasien
    $conn->query("DELETE FROM patients WHERE patient_id = $id");
} elseif ($action == 'deactivate') {
    $conn->query("UPDATE patients SET status='deactive' WHERE patient_id = $id");
}elseif($action == 'activate'){
    $conn->query("UPDATE patients SET status='active' WHERE patient_id = $id");
}

header("Location: view_patient.php");
exit();
