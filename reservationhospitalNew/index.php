<?php
// Koneksi & session
include 'config.php';
session_start();

if (isset($_SESSION['message'])) {
    $logout_message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Ambil spesialisasi untuk form pencarian dinamis
$specialities_query = mysqli_query($conn, "SELECT * FROM specialities ORDER BY name ASC");
$specialities = [];
if ($specialities_query) {
    while ($row = mysqli_fetch_assoc($specialities_query)) {
        $specialities[] = $row;
    }
}

// Ambil dokter untuk form pencarian dinamis
$doctors_query = mysqli_query($conn, "SELECT d.doctor_id, d.name, d.speciality_id, s.name as speciality FROM doctors d LEFT JOIN specialities s ON d.speciality_id = s.speciality_id ORDER BY d.name ASC");
$doctors_list = [];
if ($doctors_query) {
    while ($row = mysqli_fetch_assoc($doctors_query)) {
        $doctors_list[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rumah Sakit Graha Medika - Portal Kesehatan Modern & Terintegrasi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Portal Resmi Rumah Sakit Graha Medika - Layanan Reservasi Janji Temu Medis Cepat & Akses Rekam Medis Terintegrasi.">
  <!-- Google Fonts & Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Flatpickr untuk Kalender Premium -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
  <link rel="stylesheet" href="assets/css/main.css?v=2.1">
</head>
<body>

  <!-- Floating Ocean Blue Card Wrapper (As seen in Slide 1) -->
  <div class="hero-card-wrapper">
    
    <!-- Top Info Strip (Siloam Style inside hero) -->
    <div class="top-bar">
      <div>
        <i class="fas fa-map-marker-alt" style="color:var(--clr-secondary); margin-right: 6px;"></i> Jl. Sehat No.88, Jakarta Selatan &nbsp;|&nbsp; 
        <i class="fas fa-clock" style="color:var(--clr-secondary); margin-left: 8px; margin-right: 6px;"></i> Pelayanan Siaga 24 Jam
      </div>
      <div class="emergency">
        <i class="fas fa-phone-alt"></i> EMERGENCY CALL: 1500-888
      </div>
    </div>

    <!-- Navbar -->
    <header class="navbar" id="navbar">
      <div class="brand">
        <i class="fas fa-heartbeat"></i> Graha Medika
      </div>
      <nav>
        <a href="#">Home</a>
        <a href="#about">About</a>
        <a href="#services">Services</a>
        <a href="#doctors">Our Team</a>
        <a href="#facilities">Facilities</a>
      </nav>
      <div class="nav-actions">
        <?php if (isset($_SESSION['user_id'], $_SESSION['role'])): ?>
          <a href="<?= $_SESSION['role'] === 'patient' ? 'dashboard_patient.php' : 'dashboard_doctor.php' ?>" class="btn-nav-book">
            <i class="fas fa-user-shield"></i> Dashboard <i class="fas fa-chevron-right" style="font-size:0.7rem;"></i>
          </a>
        <?php else: ?>
          <a href="login.php?role=patient" class="btn-nav-book">
            Book Appointment <i class="fas fa-arrow-right" style="font-size:0.75rem;"></i>
          </a>
        <?php endif; ?>
      </div>
    </header>

    <!-- Hero Section Split Grid (Exactly like Dribbble Slide 1) -->
    <section class="hero">
      <div class="hero-glow-1"></div>
      <div class="hero-glow-2"></div>
      
      <!-- Left Info Column -->
      <div class="hero-content">
        <div class="hero-badge">
          <i class="fas fa-heartbeat"></i> Trusted by 125k+ people
        </div>
        <h1>Your Trusted<br>Partner in Modern<br><span>Healthcare</span></h1>
        
        <a href="#services" class="explore-pill">
          Explore Services <i class="fas fa-arrow-right"></i>
        </a>
        
        <p class="hero-desc">
          Graha Medika menghadirkan standard pelayanan kesehatan kelas dunia, didukung oleh tim spesialis berlisensi, rekam medis digital terintegrasi, dan atmosfer pemulihan yang damai dan profesional.
        </p>
      </div>

      <!-- Right Image Composition Column with Overlays -->
      <div class="hero-image-wrapper">
        <div class="hero-image-card">
          <img src="assets/img/team.png" alt="Tim Dokter Ahli Graha Medika">
        </div>
        
        <!-- Floating Stat 1 -->
        <div class="floating-card floating-card-1">
          <div class="floating-card-icon"><i class="fas fa-check-circle"></i></div>
          <div class="floating-card-info">
            <h4>97%</h4>
            <p>Trusted Care Rate</p>
          </div>
        </div>
        
        <!-- Floating Stat 2 -->
        <div class="floating-card floating-card-2">
          <div class="floating-card-icon select-blue"><i class="fas fa-user-md"></i></div>
          <div class="floating-card-info">
            <h4>Caring</h4>
            <p>Personalized &amp; Reliable</p>
          </div>
        </div>
      </div>
    </section>

  </div>

  <!-- Floating Booking Search Panel (Sits overlapping Hero Card Wrapper) -->
  <div class="floating-search-container">
    <div class="floating-search-card">
      <h3 style="font-family:'Outfit'; font-weight:700;"><i class="fas fa-calendar-alt"></i> Cari Dokter & Jadwalkan Kunjungan</h3>
      <form action="make_appointments.php" method="GET">
        <div class="search-grid">
          
          <!-- Pilih Spesialisasi -->
          <div class="search-group">
            <label for="search_speciality">Spesialisasi</label>
            <div class="search-input-wrapper">
              <i class="fas fa-stethoscope"></i>
              <select name="speciality_id" id="search_speciality" class="search-select">
                <option value="">-- Semua Poliklinik --</option>
                <?php foreach ($specialities as $spec): ?>
                  <option value="<?= $spec['speciality_id'] ?>"><?= htmlspecialchars($spec['name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Pilih Dokter -->
          <div class="search-group">
            <label for="search_doctor">Nama Dokter</label>
            <div class="search-input-wrapper">
              <i class="fas fa-user-md"></i>
              <select name="doctor_id" id="search_doctor" class="search-select">
                <option value="">-- Pilih Dokter --</option>
                <?php foreach ($doctors_list as $doc): ?>
                  <?php $spec_label = $doc['speciality'] ? " (" . $doc['speciality'] . ")" : ""; ?>
                  <option value="<?= $doc['doctor_id'] ?>" data-spec-id="<?= $doc['speciality_id'] ?>">
                    <?= htmlspecialchars($doc['name']) . $spec_label ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <!-- Pilih Tanggal -->
          <div class="search-group">
            <label for="search_date">Tanggal Kunjungan</label>
            <div class="search-input-wrapper">
              <i class="far fa-calendar-alt"></i>
              <input type="text" name="appointment_date" id="search_date" class="search-date-input" placeholder="Pilih dokter dahulu" disabled required>
            </div>
          </div>

          <!-- Tombol Aksi -->
          <button type="submit" class="btn-search">
            <i class="fas fa-search"></i> Cari Jadwal
          </button>

        </div>
      </form>
    </div>
  </div>

  <!-- About Us Section (Exactly like Dribbble Slide 1 middle section) -->
  <section id="about">
    <div class="section-label">✦ ABOUT US</div>
    <div class="about-dribbble">
      
      <!-- Left side: DNA Mesh + Floating Doctor Card -->
      <div class="about-dribbble-left">
        <div class="about-dna-mesh"></div>
        <div class="about-doctor-card">
          <img src="assets/img/doctor_andi.png" alt="Dr. Andi Wijaya">
          <div class="about-doctor-info">
            <h4>Dr. Andi Wijaya</h4>
            <p>Umum &amp; Konsultan Kesehatan</p>
          </div>
        </div>
        
        <div class="about-stats-bubble">
          <h3>50+</h3>
          <p>Healthcare Professionals<br>Supporting Lives</p>
        </div>
      </div>

      <!-- Right side: Content description + Feature cards -->
      <div class="about-dribbble-right">
        <p class="lead">
          Graha Medika connects doctors and patients effortlessly, providing smarter, safer, and compassionate healthcare from diagnosis to full recovery.
        </p>
        
        <div class="about-features-container">
          
          <!-- Card 1 -->
          <div class="about-feature-card">
            <div class="icon-holder">
              <i class="fas fa-heartbeat"></i>
            </div>
            <h4>Smart Care</h4>
            <p>Smart digital health tracking ensures accurate insights and better treatment outcomes.</p>
          </div>

          <!-- Card 2 -->
          <div class="about-feature-card">
            <div class="icon-holder">
              <i class="fas fa-shield-alt"></i>
            </div>
            <h4>Secure Data</h4>
            <p>Protecting patient rekam medis data through secure, HIPAA-compliant digital health systems.</p>
          </div>

        </div>
      </div>

    </div>
  </section>

  <!-- Why Choose Us Section (Exactly like Dribbble Slide 1 third section) -->
  <section id="services" style="padding-top: 2rem;">
    <div class="section-label">✦ WHY CHOOSE US</div>
    <h2 class="section-title-dribbble">A Simplified Path to<br>Comprehensive Medical Care</h2>
    
    <div class="why-dribbble">
      
      <!-- Column 1: Vertical Tabs -->
      <div class="why-column-left">
        <div class="why-tab-list">
          <div class="why-tab-item active" onclick="activateTab(this, 'compassion')">
            <span>Compassion</span>
            <i class="fas fa-arrow-right"></i>
          </div>
          <div class="why-tab-item" onclick="activateTab(this, 'collaboration')">
            <span>Collaboration</span>
            <i class="fas fa-arrow-right"></i>
          </div>
          <div class="why-tab-item" onclick="activateTab(this, 'transparency')">
            <span>Transparency</span>
            <i class="fas fa-arrow-right"></i>
          </div>
          <div class="why-tab-item" onclick="activateTab(this, 'flexibility')">
            <span>Flexibility</span>
            <i class="fas fa-arrow-right"></i>
          </div>
          <div class="why-tab-item" onclick="activateTab(this, 'excellence')">
            <span>Excellence</span>
            <i class="fas fa-arrow-right"></i>
          </div>
        </div>
        
        <a href="login.php?role=patient" class="btn-primary btn-blue" style="max-width:240px;">
          Book Appointment <i class="fas fa-chevron-right"></i>
        </a>
      </div>

      <!-- Column 2: Elegant Image representation -->
      <div class="why-column-middle">
        <img src="assets/img/interior.png" id="why-spec-image" alt="Fasilitas Rumah Sakit Graha Medika">
      </div>

      <!-- Column 3: Blue Info Card with pills -->
      <div class="why-column-right">
        <div class="why-blue-card">
          <div>
            <h3 id="why-card-text">We're committed to delivering the highest standard of medical care with sensitivity.</h3>
          </div>
          
          <div class="why-pills-container">
            <span class="why-pill">Compassion</span>
            <span class="why-pill">Collaboration</span>
            <span class="why-pill">Excellence</span>
            <span class="why-pill">Transparency</span>
            <span class="why-pill">Flexibility</span>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- Total Care Model (Clean Facility Slider Section - Slide 2 layout) -->
  <section class="total-care-dribbble" id="facilities">
    <div class="section-label" style="justify-content: center;">✦ APPROACH</div>
    <h2 class="total-care-title">The Graha Medika Total Care™ Model</h2>
    <p class="total-care-subtitle">Providing patient-centered care through expert guidance, innovative solutions, and personalized support at every step of the way.</p>
    
    <div class="carousel" id="carousel">
      <div class="slides">
        <img src="assets/img/slide_care.png" alt="Konsultasi Hangat Dokter Spesialis &amp; Pelayanan Pasien">
        <img src="assets/img/slide_mri.png" alt="Fasilitas Radiologi &amp; Pemindaian MRI Canggih">
        <img src="assets/img/slide_operating.png" alt="Instalasi Kamar Bedah & Laboratorium Canggih">
        <img src="assets/img/slide_consultation.png" alt="Ruang Konsultasi Dokter Spesialis yang Nyaman">
      </div>
      <div class="arrow prev" onclick="moveSlide(-1)"><i class="fas fa-chevron-left"></i></div>
      <div class="arrow next" onclick="moveSlide(1)"><i class="fas fa-chevron-right"></i></div>
    </div>
  </section>

  <!-- Testimonials & Specialists Section (Exactly like Testimonials Slide 2) -->
  <section id="doctors" class="specialists-dribbble">
    <div class="section-label" style="justify-content: center;">✦ TESTIMONIALS</div>
    <h2 class="section-title" style="margin-bottom: 1rem;">Real Stories, Real Healing —<br><span>From Our Community</span></h2>
    <p style="color:var(--clr-text); font-size:0.95rem; max-width:600px; margin: 0 auto 3rem;">Review ulasan nyata dan booking langsung dokter spesialis terbaik kami untuk perawatan yang nyaman.</p>
    
    <div class="doctor-list-dribbble">
      
      <!-- Doctor 1 (Robert Fox White Card) -->
      <div class="doctor-dribbble-card">
        <div>
          <div class="doctor-badge-status" style="margin-bottom: 1rem; display:inline-flex;"><i class="fas fa-circle"></i> Kardiologi</div>
          <div class="doctor-rating" style="justify-content: flex-start; margin-bottom: 1rem;">
            <i class="fas fa-star"></i> 4.9 <span>(48 ulasan)</span>
          </div>
          <h4 class="review-title">Friendly staff &amp; fast response</h4>
          <p class="review-text">"Penanganan kardiologi yang luar biasa. Dr. Dewi sangat sabar menjelaskan kondisi jantung saya dengan alat pendeteksi modern."</p>
        </div>
        
        <div class="doctor-profile-row">
          <div class="doctor-profile-left">
            <img src="assets/img/doctor.png" alt="Dr. Dewi Larasati">
            <div>
              <h5>Dr. Dewi Larasati</h5>
              <p>Spesialis Kardiologi</p>
            </div>
          </div>
          <a href="login.php?role=patient" class="doctor-action-btn-circle" title="Jadwalkan Kunjungan">
            <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <!-- Doctor 2 (Cody Fisher HIGHLIGHTED BLUE CARD) -->
      <div class="doctor-dribbble-card highlighted-card">
        <div>
          <div class="doctor-badge-status" style="background:rgba(255,255,255,0.1); color:#38BDF8; border-color:rgba(255,255,255,0.15); margin-bottom: 1rem; display:inline-flex;"><i class="fas fa-circle" style="color:#22C55E;"></i> Dokter Umum</div>
          <div class="doctor-rating" style="justify-content: flex-start; margin-bottom: 1rem; color:#F59E0B;">
            <i class="fas fa-star"></i> 4.8 <span>(32 ulasan)</span>
          </div>
          <h4 class="review-title" style="color:var(--clr-white)">Seamless medical checkup experience</h4>
          <p class="review-text" style="color:rgba(255,255,255,0.8)">"Pemeriksaan kesehatan umum yang sangat menyeluruh. Konsultasi berjalan santai tanpa terburu-buru, dan laporannya terintegrasi di portal rekam medis."</p>
        </div>
        
        <div class="doctor-profile-row">
          <div class="doctor-profile-left">
            <img src="assets/img/doctor_andi.png" alt="Dr. Andi Wijaya">
            <div>
              <h5 style="color:var(--clr-white)">Dr. Andi Wijaya</h5>
              <p style="color:rgba(255,255,255,0.6)">Spesialis Kedokteran Umum</p>
            </div>
          </div>
          <a href="login.php?role=patient" class="doctor-action-btn-circle" title="Jadwalkan Kunjungan">
            <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>

      <!-- Doctor 3 (Albert Flores White Card) -->
      <div class="doctor-dribbble-card">
        <div>
          <div class="doctor-badge-status" style="margin-bottom: 1rem; display:inline-flex;"><i class="fas fa-circle"></i> Kesehatan Anak</div>
          <div class="doctor-rating" style="justify-content: flex-start; margin-bottom: 1rem;">
            <i class="fas fa-star"></i> 4.9 <span>(54 ulasan)</span>
          </div>
          <h4 class="review-title">Wonderful pediatric treatment</h4>
          <p class="review-text">"Sangat ramah anak! Anak saya tidak takut saat diperiksa. Penjelasan obat dan dosisnya sangat mudah dipahami oleh orang tua."</p>
        </div>
        
        <div class="doctor-profile-row">
          <div class="doctor-profile-left">
            <img src="assets/img/doctor_nadia.png" alt="Dr. Nadia Salsabila">
            <div>
              <h5>Dr. Nadia Salsabila</h5>
              <p>Spesialis Kesehatan Anak</p>
            </div>
          </div>
          <a href="login.php?role=patient" class="doctor-action-btn-circle" title="Jadwalkan Kunjungan">
            <i class="fas fa-arrow-right"></i>
          </a>
        </div>
      </div>

    </div>
  </section>

  <!-- Modern Deep Teal Footer Banner (Exactly like Dribbble Footer) -->
  <footer id="contact">
    <div class="footer-dribbble-top">
      
      <!-- Left side: Newsletter Box -->
      <div class="footer-newsletter-box">
        <h3>Stay ahead of your<br>health journey</h3>
        <p>Get trusted health reviews, medical tips and monthly Graha Medika polyclinic info straight to your email.</p>
        <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Terima kasih! Email Anda telah terdaftar untuk menerima newsletter.');">
          <input type="email" placeholder="Enter Your Email" required>
          <button type="submit">Subscribe</button>
        </form>
      </div>

      <!-- Right side: Links -->
      <div class="footer-links-grid">
        <div>
          <h5>Quick Links</h5>
          <a href="#">Home</a>
          <a href="#about">About</a>
          <a href="#services">Services</a>
          <a href="#doctors">Our Team</a>
        </div>
        <div>
          <h5>Our Services</h5>
          <a href="#services">General Medicine</a>
          <a href="#services">Pediatrics</a>
          <a href="#services">Cardiology</a>
          <a href="#services">Dental Care</a>
        </div>
        <div>
          <h5>Contact Us</h5>
          <a style="cursor:default; color:rgba(255,255,255,0.5);" href="javascript:void(0)">Jl. Sehat No.88, Jak-Sel</a>
          <a style="cursor:default; color:rgba(255,255,255,0.5);" href="javascript:void(0)">Phone: (021) 1234-5678</a>
          <a href="mailto:info@grahamedika.com">info@grahamedika.com</a>
        </div>
      </div>

    </div>

    <!-- Bottom copyrights -->
    <div class="footer-dribbble-bottom">
      <div class="footer-brand">
        <i class="fas fa-heartbeat"></i> Graha Medika
      </div>
      
      <div class="footer-socials">
        <a href="#" class="footer-social-btn"><i class="fab fa-facebook-f"></i></a>
        <a href="#" class="footer-social-btn"><i class="fab fa-instagram"></i></a>
        <a href="#" class="footer-social-btn"><i class="fab fa-youtube"></i></a>
      </div>
      
      <div>
        &copy; <?= date('Y') ?> Graha Medika. All rights reserved. Designed with care for healthier communities.
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    let slideIndex = 0;

    function moveSlide(step) {
      const slides = document.querySelectorAll('.slides img');
      const totalSlides = slides.length;
      slideIndex += step;

      if (slideIndex >= totalSlides) {
        slideIndex = 0;
      } else if (slideIndex < 0) {
        slideIndex = totalSlides - 1;
      }

      const offset = -slideIndex * 100;
      document.querySelector('.slides').style.transform = `translateX(${offset}%)`;
    }

    // Auto slide carousel
    setInterval(() => {
      moveSlide(1);
    }, 6000);

    // Dynamic Filter Doctor Options based on Selected Specialty
    const specSelect = document.getElementById('search_speciality');
    const docSelect = document.getElementById('search_doctor');
    const originalDocOptions = Array.from(docSelect.options);

    specSelect.addEventListener('change', function() {
      const selectedSpecId = this.value;
      
      // Clear options and insert matching ones
      docSelect.innerHTML = '';
      
      originalDocOptions.forEach(option => {
        if (!selectedSpecId || option.value === "" || option.getAttribute('data-spec-id') === selectedSpecId) {
          docSelect.appendChild(option.cloneNode(true));
        }
      });

      // Reset calendar when specialty changes
      docSelect.value = "";
      docSelect.dispatchEvent(new Event('change'));
    });

    // Initialize Flatpickr on homepage search date input
    const dateInput = document.getElementById('search_date');
    let fp = flatpickr(dateInput, {
      dateFormat: "Y-m-d",
      minDate: "today",
      disable: [
        function(date) {
          return true; // Disable all days by default until doctor is chosen
        }
      ]
    });

    let currentDoctorSchedules = [];

    // Trigger Flatpickr update when doctor changes
    docSelect.addEventListener('change', function() {
      const doctorId = this.value;
      
      // Reset date input
      dateInput.value = '';
      dateInput.disabled = true;
      dateInput.placeholder = 'Loading schedule...';

      if (!doctorId) {
        dateInput.placeholder = 'Pilih dokter dahulu';
        fp.destroy();
        fp = flatpickr(dateInput, {
          dateFormat: "Y-m-d",
          minDate: "today",
          disable: [
            function(date) {
              return true;
            }
          ]
        });
        return;
      }

      // Fetch doctor schedules via AJAX
      fetch(`api_get_doctor_schedule.php?doctor_id=${doctorId}`)
        .then(response => response.json())
        .then(data => {
          if (data.success && data.schedules.length > 0) {
            currentDoctorSchedules = data.schedules;
            const allowedDays = currentDoctorSchedules.map(sch => sch.day_index);

            // Re-initialize Flatpickr to only enable doctor's practice days
            fp.destroy();
            fp = flatpickr(dateInput, {
              dateFormat: "Y-m-d",
              minDate: "today",
              disable: [
                function(date) {
                  // Disable non-practice days
                  return !allowedDays.includes(date.getDay());
                }
              ]
            });

            dateInput.disabled = false;
            dateInput.placeholder = 'Pilih Tanggal Praktik';
          } else {
            dateInput.placeholder = 'No practice days available';
          }
        })
        .catch(error => {
          console.error('Error fetching doctor schedule:', error);
          dateInput.placeholder = 'Error loading schedule';
        });
    });

    // Why Choose Us Tab Interactivity
    const tabImages = {
      'compassion': 'assets/img/interior.png',
      'collaboration': 'assets/img/exterior.png',
      'transparency': 'assets/img/interior.png',
      'flexibility': 'assets/img/team.png',
      'excellence': 'assets/img/exterior.png'
    };

    const tabTexts = {
      'compassion': "We're committed to delivering the highest standard of medical care with sensitivity.",
      'collaboration': "Collaboration: Working together as a cohesive team to coordinate excellent patient diagnostics.",
      'transparency': "Transparency: Honest, open, and transparent billing and treatment guides for your comfort.",
      'flexibility': "Flexibility: Flexible appointment booking, easy schedule updates, and instant emergency adjustments.",
      'excellence': "Excellence: Pushing limits to provide world-class clinical facilities and expert consultations."
    };

    function activateTab(element, tabKey) {
      // Remove active class from all tabs
      document.querySelectorAll('.why-tab-item').forEach(item => {
        item.classList.remove('active');
      });

      // Add active class to clicked tab
      element.classList.add('active');

      // Change Middle Image dynamically
      const imageElement = document.getElementById('why-spec-image');
      if(imageElement && tabImages[tabKey]) {
        imageElement.src = tabImages[tabKey];
      }

      // Change Card Text dynamically
      const textElement = document.getElementById('why-card-text');
      if(textElement && tabTexts[tabKey]) {
        textElement.textContent = tabTexts[tabKey];
      }
    }
  </script>
</body>
</html>
