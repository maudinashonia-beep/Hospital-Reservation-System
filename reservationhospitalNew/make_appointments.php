<?php
session_start();
include 'config.php';

// Cek apakah user sudah login sebagai patient
if (!isset($_SESSION['user_id'], $_SESSION['role']) || $_SESSION['role'] !== 'patient') {
    header('Location: login.php?role=patient');
    exit();
}

$patient_id = (int)$_SESSION['user_id'];

// Ambil info pasien untuk header
$patient_query = "SELECT * FROM patients WHERE patient_id = $patient_id";
$patient_res = mysqli_query($conn, $patient_query);
$patient = mysqli_fetch_assoc($patient_res);

// Cek kalau form disubmit
$message = "";
$status = "";
if (isset($_POST['submit'])) {
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $appointment_time = mysqli_real_escape_string($conn, $_POST['appointment_time']);
    
    // Gabungkan tanggal dan waktu menjadi satu datetime
    $appointment_datetime = $appointment_date . ' ' . $appointment_time;

    // Query insert ke tabel appointments
    $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_datetime, status) 
            VALUES ('$patient_id', '$doctor_id', '$appointment_datetime', 'pending')";
    
    if (mysqli_query($conn, $sql)) {
        $status = "success";
        $message = "Appointment booked successfully! Our staff will confirm it shortly.";
    } else {
        $status = "error";
        $message = "Failed to make an appointment: " . mysqli_error($conn);
    }
}

// Ambil daftar dokter dari database
$doctors = mysqli_query($conn, "SELECT d.doctor_id, d.name, s.name as speciality FROM doctors d LEFT JOIN specialities s ON d.speciality_id = s.speciality_id ORDER BY d.name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Make Appointment - Hospital Reservation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <!-- Flatpickr untuk Kalender Premium -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
    <!-- Removed inline styles as they are now in dashboard.css -->
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
                <li><a href="make_appointments.php" class="active"><i class="fas fa-calendar-plus"></i> Make Appointment</a></li>
                <li><a href="my_appointments.php"><i class="fas fa-calendar-check"></i> Appointment</a></li>
                <li><a href="medical_records.php"><i class="fas fa-file-medical"></i> Medical Record</a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
            </ul>
            
            <h4>More</h4>
            <ul>
                <li><a href="doctors.php"><i class="fas fa-user-md"></i> Doctor</a></li>
                <li><a href="help.php"><i class="fas fa-question-circle"></i> Help</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h2>Make Appointment</h2>
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

        <div class="premium-form-card">
            <div class="section-header" style="justify-content:center; border:none; margin-bottom:10px;">
                <h3 class="section-title" style="font-size:1.8rem; text-align:center;">Book a Visit</h3>
            </div>
            <p style="text-align:center; color:var(--clr-text); margin-bottom:40px;">Choose a doctor and schedule your appointment effortlessly.</p>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $status; ?>">
                    <i class="fas <?php echo $status === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form action="make_appointments.php" method="POST">
                <div class="premium-form-group">
                    <label class="premium-label">Select Doctor</label>
                    <div class="premium-input-wrapper">
                        <i class="fas fa-user-md"></i>
                        <select name="doctor_id" id="doctor_select" class="premium-input" required>
                            <option value="">-- Choose a Specialist --</option>
                            <?php
                            $get_doc_id = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;
                            while ($row = mysqli_fetch_assoc($doctors)) {
                                $spec = $row['speciality'] ? " - " . $row['speciality'] : "";
                                $selected = ($get_doc_id === (int)$row['doctor_id']) ? "selected" : "";
                                echo "<option value='" . $row['doctor_id'] . "' $selected>" . htmlspecialchars($row['name']) . $spec . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="premium-form-group">
                    <label class="premium-label">Appointment Date</label>
                    <div class="premium-input-wrapper">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="text" name="appointment_date" id="appointment_date" class="premium-input" placeholder="Select a doctor first" disabled required>
                    </div>
                </div>

                <div class="premium-form-group">
                    <label class="premium-label">Appointment Time</label>
                    <div class="premium-input-wrapper">
                        <i class="far fa-clock"></i>
                        <select name="appointment_time" id="appointment_time" class="premium-input" disabled required>
                            <option value="">-- Select a date first --</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="submit" class="premium-btn" style="margin-top:20px;">
                    <i class="fas fa-calendar-check"></i> Confirm Appointment
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const doctorSelect = document.getElementById('doctor_select');
        const dateInput = document.getElementById('appointment_date');
        const timeSelect = document.getElementById('appointment_time');
        
        let fp = flatpickr(dateInput, {
            dateFormat: "Y-m-d",
            minDate: "today",
            disable: [
                function(date) {
                    return true; // Disable all days by default
                }
            ]
        });

        let currentDoctorSchedules = [];

        doctorSelect.addEventListener('change', function() {
            const doctorId = this.value;
            
            // Reset input
            dateInput.value = '';
            dateInput.disabled = true;
            dateInput.placeholder = 'Loading schedule...';
            timeSelect.innerHTML = '<option value="">-- Select a date first --</option>';
            timeSelect.disabled = true;

            if (!doctorId) {
                dateInput.placeholder = 'Select a doctor first';
                return;
            }

            // Fetch AJAX
            fetch(`api_get_doctor_schedule.php?doctor_id=${doctorId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.schedules.length > 0) {
                        currentDoctorSchedules = data.schedules;
                        
                        // Ekstrak hari apa saja dokter praktek
                        const allowedDays = currentDoctorSchedules.map(sch => sch.day_index);

                        // Update Flatpickr
                        fp.destroy();
                        
                        // Cek parameter tanggal dari GET jika ada
                        const urlParams = new URLSearchParams(window.location.search);
                        const getPreDate = urlParams.get('appointment_date');
                        let defaultDateVal = "";
                        if (getPreDate) {
                            defaultDateVal = getPreDate;
                        }

                        fp = flatpickr(dateInput, {
                            dateFormat: "Y-m-d",
                            minDate: "today",
                            defaultDate: defaultDateVal,
                            disable: [
                                function(date) {
                                    // Disable hari yang tidak ada di jadwal dokter
                                    return !allowedDays.includes(date.getDay());
                                }
                            ],
                            onChange: function(selectedDates, dateStr, instance) {
                                if (selectedDates.length > 0) {
                                    generateTimeSlots(selectedDates[0]);
                                }
                            }
                        });

                        dateInput.disabled = false;
                        dateInput.placeholder = 'Choose available date';

                        // Jika ada defaultDateVal, jalankan generateTimeSlots secara manual
                        if (defaultDateVal) {
                            const parsedDate = new Date(defaultDateVal + "T00:00:00");
                            if (allowedDays.includes(parsedDate.getDay())) {
                                generateTimeSlots(parsedDate);
                            }
                        }
                    } else {
                        dateInput.placeholder = 'No schedule available for this doctor';
                    }
                })
                .catch(error => {
                    console.error('Error fetching schedule:', error);
                    dateInput.placeholder = 'Error loading schedule';
                });
        });

        // Trigger change event jika ada dokter yang terpilih dari GET saat halaman dimuat
        if (doctorSelect.value) {
            doctorSelect.dispatchEvent(new Event('change'));
        }

        function generateTimeSlots(selectedDate) {
            const dayIndex = selectedDate.getDay();
            
            // Cari jadwal dokter untuk hari yang dipilih
            const scheduleForDay = currentDoctorSchedules.find(sch => sch.day_index === dayIndex);
            
            timeSelect.innerHTML = '<option value="">-- Select Time --</option>';
            
            if (scheduleForDay) {
                const startTime = scheduleForDay.start_time; // format HH:MM
                const endTime = scheduleForDay.end_time;
                
                // Konversi string "HH:MM" ke menit untuk looping
                const startParts = startTime.split(':');
                const endParts = endTime.split(':');
                
                let startMins = parseInt(startParts[0]) * 60 + parseInt(startParts[1]);
                const endMins = parseInt(endParts[0]) * 60 + parseInt(endParts[1]);
                
                const interval = 30; // 30 minutes interval
                
                while (startMins < endMins) {
                    const hours = Math.floor(startMins / 60).toString().padStart(2, '0');
                    const mins = (startMins % 60).toString().padStart(2, '0');
                    const timeString = `${hours}:${mins}`;
                    
                    const option = document.createElement('option');
                    option.value = timeString;
                    option.textContent = timeString;
                    timeSelect.appendChild(option);
                    
                    startMins += interval;
                }
                
                timeSelect.disabled = false;
            } else {
                timeSelect.disabled = true;
                timeSelect.innerHTML = '<option value="">-- No times available --</option>';
            }
        }
    });
</script>

</body>
</html>
