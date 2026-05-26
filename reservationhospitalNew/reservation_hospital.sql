-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 16 Apr 2025 pada 19.27
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reservation_hospital`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_datetime` datetime NOT NULL,
  `status` enum('scheduled','completed','cancelled','no_show') DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `speciality_id` int(11) NOT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `name`, `speciality_id`, `room_number`, `phone`, `email`, `username`, `password`, `created_at`) VALUES
(1, 'Dr. Andi Wijaya', 1, '101', '081234500001', 'andi@klinik.com', 'andiwijaya', 'hashed_pass1', '2025-04-16 16:42:51'),
(2, 'Dr. Lisa Handayani', 1, '102', '081234500002', 'lisa@klinik.com', 'lisahandayani', 'hashed_pass2', '2025-04-16 16:42:51'),
(3, 'Dr. Rina Kusuma', 2, '103', '081234500003', 'rina@klinik.com', 'rinakusuma', 'hashed_pass3', '2025-04-16 16:42:51'),
(4, 'Dr. Tommy Santoso', 2, '104', '081234500004', 'tommy@klinik.com', 'tommysantoso', 'hashed_pass4', '2025-04-16 16:42:51'),
(5, 'Dr. Siti Rahma', 3, '105', '081234500005', 'siti@klinik.com', 'sitirahma', 'hashed_pass5', '2025-04-16 16:42:51'),
(6, 'Dr. Dewi Larasati', 3, '106', '081234500006', 'dewi@klinik.com', 'dewilarasati', 'hashed_pass6', '2025-04-16 16:42:51'),
(7, 'Drg. Bambang Hermawan', 4, '201', '081234500007', 'bambang@klinik.com', 'bambanghermawan', 'hashed_pass7', '2025-04-16 16:42:51'),
(8, 'Drg. Nadia Salsabila', 4, '202', '081234500008', 'nadia@klinik.com', 'nadiasalsa', 'hashed_pass8', '2025-04-16 16:42:51'),
(9, 'Dr. Diah Permata', 5, '301', '081234500009', 'diah@klinik.com', 'diahpermata', 'hashed_pass9', '2025-04-16 16:42:51'),
(10, 'Dr. Reza Mahendra', 5, '302', '081234500010', 'reza@klinik.com', 'rezamahendra', 'hashed_pass10', '2025-04-16 16:42:51'),
(11, 'Dr. Herman Prasetyo', 6, '401', '081234500011', 'herman@klinik.com', 'hermanpras', 'hashed_pass11', '2025-04-16 16:42:51'),
(12, 'Dr. Clara Yuliana', 6, '402', '081234500012', 'clara@klinik.com', 'clarayuliana', 'hashed_pass12', '2025-04-16 16:42:51'),
(13, 'Dr. Samuel Aditya', 7, '501', '081234500013', 'samuel@klinik.com', 'samueladitya', 'hashed_pass13', '2025-04-16 16:42:51'),
(14, 'Dr. Melati Citra', 7, '502', '081234500014', 'melati@klinik.com', 'melaticitra', 'hashed_pass14', '2025-04-16 16:42:51'),
(15, 'Dr. Yudi Darmawan', 7, '503', '081234500015', 'yudi@klinik.com', 'yudidarmawan', 'hashed_pass15', '2025-04-16 16:42:51');

-- --------------------------------------------------------

--
-- Struktur dari tabel `medical_records`
--

CREATE TABLE `medical_records` (
  `record_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `diagnosis` text NOT NULL,
  `prescription` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `payment_method` enum('cash','credit_card','debit_card','e-wallet','transfer') NOT NULL,
  `status` enum('pending','paid','failed','refunded') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `specialities`
--

CREATE TABLE `specialities` (
  `speciality_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `specialities`
--

INSERT INTO `specialities` (`speciality_id`, `name`) VALUES
(1, 'General Practitioner'),
(2, 'Pediatrician'),
(3, 'Obstetrics & Gynecology'),
(4, 'Dentistry'),
(5, 'Dermatology & Venereology'),
(6, 'Cardiology'),
(7, 'Neurology');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indeks untuk tabel `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `speciality_id` (`speciality_id`);

--
-- Indeks untuk tabel `medical_records`
--
ALTER TABLE `medical_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indeks untuk tabel `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indeks untuk tabel `specialities`
--
ALTER TABLE `specialities`
  ADD PRIMARY KEY (`speciality_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT untuk tabel `medical_records`
--
ALTER TABLE `medical_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `specialities`
--
ALTER TABLE `specialities`
  MODIFY `speciality_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);

--
-- Ketidakleluasaan untuk tabel `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`speciality_id`) REFERENCES `specialities` (`speciality_id`);

--
-- Ketidakleluasaan untuk tabel `medical_records`
--
ALTER TABLE `medical_records`
  ADD CONSTRAINT `medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `medical_records_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `medical_records_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`);

--
-- Ketidakleluasaan untuk tabel `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
