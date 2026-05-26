<?php
session_start();
include 'config.php';

// Pastikan login sebagai pasien
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header('Location: login.php?role=patient');
    exit();
}

$patient_id = (int)$_SESSION['user_id'];

// Ambil info pasien untuk header
$patient_query = "SELECT * FROM patients WHERE patient_id = $patient_id";
$patient_res = mysqli_query($conn, $patient_query);
$patient = mysqli_fetch_assoc($patient_res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Help Center - Hospital Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <style>
        .help-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .help-section { background: white; border-radius: var(--radius); padding: 30px; box-shadow: var(--shadow); border: 1px solid #E2E8F0; }
        .help-title { color: var(--clr-primary); font-size: 1.3rem; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; padding-bottom: 15px; border-bottom: 1px solid #f1f5f9; }
        
        .faq-item { margin-bottom: 25px; }
        .faq-item:last-child { margin-bottom: 0; }
        .faq-question { font-weight: 600; color: var(--clr-dark); margin-bottom: 8px; display: flex; align-items: flex-start; gap: 10px; }
        .faq-question i { color: #3b82f6; margin-top: 3px; }
        .faq-answer { color: #475569; padding-left: 26px; line-height: 1.6; }

        .contact-list { list-style: none; padding: 0; margin: 0; }
        .contact-item { display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid #f1f5f9; }
        .contact-item:last-child { border-bottom: none; }
        .contact-icon { width: 40px; height: 40px; border-radius: 50%; background: #eff6ff; color: var(--clr-primary); display: flex; align-items: center; justify-content: center; margin-right: 15px; font-size: 1.1rem; }
        .contact-label { color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 3px; }
        .contact-value { color: var(--clr-dark); font-weight: 600; }
        .contact-value a { color: var(--clr-primary); text-decoration: none; }
        .contact-value a:hover { text-decoration: underline; }

        @media (max-width: 992px) {
            .help-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Graha Medika</h3>
            <p>Patient Dashboard</p>
        </div>
        
        <div class="sidebar-menu">
            <h4>Main Menu</h4>
            <ul>
                <li><a href="dashboard_patient.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="make_appointments.php"><i class="fas fa-calendar-plus"></i> Make Appointment</a></li>
                <li><a href="my_appointments.php"><i class="fas fa-calendar-check"></i> Appointment</a></li>
                <li><a href="medical_records.php"><i class="fas fa-file-medical"></i> Medical Record</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            </ul>
            
            <h4>More</h4>
            <ul>
                <li><a href="doctors.php"><i class="fas fa-user-md"></i> Doctor</a></li>
                <li><a href="help.php" class="active"><i class="fas fa-question-circle"></i> Help</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h2>Help Center</h2>
            <div class="user-info">
                <div class="user-profile">
                    <?php echo substr($patient['name'] ?? 'U', 0, 1); ?>
                </div>
                <div>
                    <p class="doctor-name"><?php echo $patient['name'] ?? 'User'; ?></p>
                    <p class="doctor-role">Patient</p>
                </div>
            </div>
        </div>

        <p style="color: #64748b; margin-bottom: 30px; font-size: 1.05rem;">Need assistance? Find answers to common questions or reach out to our support team.</p>

        <div class="help-grid">
            <!-- FAQ Section -->
            <div class="help-section">
                <h3 class="help-title"><i class="fas fa-comments"></i> Frequently Asked Questions</h3>
                
                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How do I make an appointment?</div>
                    <div class="faq-answer">Log in to your account, select the "Make Appointment" menu from the sidebar, choose your preferred doctor and time, and confirm your booking.</div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How can I cancel an appointment?</div>
                    <div class="faq-answer">Go to the "My Appointments" page, locate the scheduled appointment you wish to cancel, and click the red "Cancel" button.</div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> Where can I see my medical history?</div>
                    <div class="faq-answer">Your medical history, including doctor diagnoses and prescriptions, is securely stored under the "Medical Record" menu.</div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question"><i class="fas fa-question-circle"></i> How do I update my personal information?</div>
                    <div class="faq-answer">Click on "My Profile" to view and edit your personal details such as phone number, email address, and home address.</div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="help-section">
                <h3 class="help-title"><i class="fas fa-headset"></i> Contact Us</h3>
                
                <ul class="contact-list">
                    <li class="contact-item">
                        <div class="contact-icon"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <div class="contact-label">Call Center (24/7)</div>
                            <div class="contact-value">1500-123</div>
                        </div>
                    </li>
                    
                    <li class="contact-item">
                        <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <div class="contact-label">Email Support</div>
                            <div class="contact-value"><a href="mailto:cs@rs.grahamedika.com">cs@rs.grahamedika.com</a></div>
                        </div>
                    </li>
                    
                    <li class="contact-item">
                        <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <div class="contact-label">Main Hospital Address</div>
                            <div class="contact-value">Jl. Sehat No. 88, Jakarta</div>
                        </div>
                    </li>
                    
                    <li class="contact-item">
                        <div class="contact-icon"><i class="fab fa-whatsapp"></i></div>
                        <div>
                            <div class="contact-label">WhatsApp Admin</div>
                            <div class="contact-value"><a href="#">+62 811-1234-5678</a></div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

</body>
</html>
