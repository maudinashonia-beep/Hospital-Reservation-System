<?php
session_start();
session_destroy();

// Bikin pesan logout
session_start();
$_SESSION['message'] = 'Logout berhasil!';

// Redirect ke halaman index.php
header("Location: index.php");
exit();
?>
