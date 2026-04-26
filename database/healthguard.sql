-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 26, 2026 at 01:53 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `healthguard`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

DROP TABLE IF EXISTS `appointments`;
CREATE TABLE IF NOT EXISTS `appointments` (
  `appointment_id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`appointment_id`),
  KEY `fk_appointments_patient_user` (`patient_id`),
  KEY `fk_appointments_doctors` (`doctor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `created_at`) VALUES
(1, 3, 6, '2026-03-30', '04:32:00', 'completed', '2026-03-29 14:31:14'),
(2, 3, 6, '2026-04-10', '23:55:00', 'pending', '2026-04-03 07:50:25');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE IF NOT EXISTS `doctors` (
  `doctor_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `specialization` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `license_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `illness_id` int DEFAULT NULL,
  PRIMARY KEY (`doctor_id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `fk_doctors_illnesses_specialization` (`specialization`),
  KEY `fk_doctors_illnesses` (`illness_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `user_id`, `specialization`, `license_number`, `illness_id`) VALUES
(1, 5, 'cardiologist', NULL, NULL),
(2, 6, 'dermatologist', NULL, NULL),
(3, 7, 'ophthalmologist', NULL, NULL),
(4, 8, 'orthopedic', NULL, NULL),
(5, 9, 'dentist', NULL, NULL),
(6, 10, 'cardiologist', NULL, NULL),
(7, 16, 'dentist', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `drugs`
--

DROP TABLE IF EXISTS `drugs`;
CREATE TABLE IF NOT EXISTS `drugs` (
  `drug_id` int NOT NULL AUTO_INCREMENT,
  `drug_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allergy_group` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`drug_id`),
  UNIQUE KEY `drug_name` (`drug_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `drugs`
--

INSERT INTO `drugs` (`drug_id`, `drug_name`, `allergy_group`, `notes`, `created_at`) VALUES
(1, 'Amoxicillin', 'Penicillin', 'Antibiotic', '2026-04-03 07:04:27'),
(2, 'Azithromycin', 'Macrolide', 'Antibiotic', '2026-04-03 07:04:27'),
(3, 'Ibuprofen', 'NSAID', 'Pain reliever', '2026-04-03 07:04:27'),
(4, 'Paracetamol', 'Paracetamol', 'Pain reliever / fever', '2026-04-03 07:04:27'),
(5, 'Diclofenac', 'NSAID', 'Pain reliever', '2026-04-03 07:04:27'),
(6, 'Cetirizine', 'Antihistamine', 'Allergy medicine', '2026-04-03 07:04:27'),
(7, 'Metformin', 'Metformin', 'Diabetes medicine', '2026-04-03 07:04:27'),
(8, 'Omeprazole', 'PPI', 'Stomach acid medicine', '2026-04-03 07:04:27');

-- --------------------------------------------------------

--
-- Table structure for table `illnesses`
--

DROP TABLE IF EXISTS `illnesses`;
CREATE TABLE IF NOT EXISTS `illnesses` (
  `illness_id` int NOT NULL AUTO_INCREMENT,
  `illness_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialization` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`illness_id`),
  UNIQUE KEY `specialization` (`specialization`),
  UNIQUE KEY `specialization_2` (`specialization`),
  UNIQUE KEY `specialization_3` (`specialization`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `illnesses`
--

INSERT INTO `illnesses` (`illness_id`, `illness_name`, `specialization`) VALUES
(1, 'Heart Problem', 'cardiologist'),
(2, 'Skin Problem', 'dermatologist'),
(3, 'Eye Problem', 'ophthalmologist'),
(4, 'Bone Problem', 'orthopedic'),
(5, 'Teeth Problem', 'dentist');

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

DROP TABLE IF EXISTS `meals`;
CREATE TABLE IF NOT EXISTS `meals` (
  `meal_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `meal_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meal_time` time DEFAULT NULL,
  `date` date NOT NULL,
  `calories` int DEFAULT '0',
  PRIMARY KEY (`meal_id`),
  KEY `fk_meals_users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`meal_id`, `user_id`, `meal_name`, `meal_time`, `date`, `calories`) VALUES
(1, 3, 'rice', '08:50:00', '2026-04-06', 500);

-- --------------------------------------------------------

--
-- Table structure for table `medical_reports`
--

DROP TABLE IF EXISTS `medical_reports`;
CREATE TABLE IF NOT EXISTS `medical_reports` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `appointment_id` int NOT NULL,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `diagnosis` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `examination` text COLLATE utf8mb4_unicode_ci,
  `treatment_done` text COLLATE utf8mb4_unicode_ci,
  `doctor_notes` text COLLATE utf8mb4_unicode_ci,
  `follow_up_needed` enum('no','yes') COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `follow_up_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`),
  UNIQUE KEY `appointment_id` (`appointment_id`),
  KEY `fk_medical_reports_patient_user` (`patient_id`),
  KEY `fk_medical_reports_doctor` (`doctor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medical_reports`
--

INSERT INTO `medical_reports` (`report_id`, `appointment_id`, `patient_id`, `doctor_id`, `diagnosis`, `examination`, `treatment_done`, `doctor_notes`, `follow_up_needed`, `follow_up_note`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 6, 'Acute upper respiratory tract infection (common cold) with mild fever and sore throat.', 'Patient presented with cough, nasal congestion, sore throat, and mild fever (38°C).\r\nNo chest pain or shortness of breath.\r\nThroat slightly red, lungs clear on auscultation.', 'Provided symptomatic treatment for infection.\r\nAdvised rest, hydration, and monitoring of symptoms.', 'Patient condition is stable.\r\nNo signs of bacterial infection at this stage.\r\nIf symptoms worsen or fever persists more than 3 days, patient should return.', 'yes', 'Review symptoms and check recovery progress.', '2026-04-03 07:50:25', '2026-04-03 07:50:25');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `fk_messages_sender` (`sender_id`),
  KEY `fk_messages_receiver` (`receiver_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `content`, `timestamp`) VALUES
(1, 3, 10, 'hello doctor', '2026-03-29 22:43:12'),
(2, 10, 3, 'hello how can i help you', '2026-03-29 22:44:13'),
(3, 10, 3, 'hi', '2026-04-06 03:20:38'),
(4, 3, 10, 'yes', '2026-04-06 03:30:57'),
(5, 10, 3, 'ok', '2026-04-06 03:31:25'),
(6, 10, 3, 'okey', '2026-04-06 05:28:22');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` enum('unread','read') COLLATE utf8mb4_unicode_ci DEFAULT 'unread',
  PRIMARY KEY (`notification_id`),
  KEY `fk_notifications_users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `type`, `created_at`, `message`, `status`) VALUES
(1, 3, 'message', '2026-04-06 03:20:38', 'You received a new reply from your doctor.', 'read'),
(2, 10, 'message', '2026-04-06 03:30:57', 'You received a new message from a patient.', 'read'),
(3, 3, 'message', '2026-04-06 03:31:25', 'You received a new reply from your doctor.', 'read'),
(4, 3, 'message', '2026-04-06 05:28:22', 'You received a new reply from your doctor.', 'read');

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_plans`
--

DROP TABLE IF EXISTS `nutrition_plans`;
CREATE TABLE IF NOT EXISTS `nutrition_plans` (
  `plan_id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int NOT NULL,
  `doctor_id` int NOT NULL,
  `week_start` date NOT NULL,
  `day_name` varchar(20) NOT NULL,
  `breakfast` text,
  `lunch` text,
  `dinner` text,
  `snack` text,
  `calories_goal` int DEFAULT NULL,
  `illness_advice` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`plan_id`),
  KEY `idx_nutrition_patient_id` (`patient_id`),
  KEY `idx_nutrition_doctor_id` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_tracking`
--

DROP TABLE IF EXISTS `nutrition_tracking`;
CREATE TABLE IF NOT EXISTS `nutrition_tracking` (
  `tracking_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `total_calories` int DEFAULT '0',
  `date` date NOT NULL,
  PRIMARY KEY (`tracking_id`),
  KEY `fk_nutrition_tracking_users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nutrition_tracking`
--

INSERT INTO `nutrition_tracking` (`tracking_id`, `user_id`, `total_calories`, `date`) VALUES
(1, 3, 500, '2026-04-06');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
CREATE TABLE IF NOT EXISTS `patients` (
  `patient_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `age` int DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_notes` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`patient_id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `user_id`, `age`, `gender`, `medical_notes`) VALUES
(1, 3, 20, 'male', 'nothing');

-- --------------------------------------------------------

--
-- Table structure for table `patient_allergies`
--

DROP TABLE IF EXISTS `patient_allergies`;
CREATE TABLE IF NOT EXISTS `patient_allergies` (
  `allergy_id` int NOT NULL AUTO_INCREMENT,
  `patient_id` int NOT NULL,
  `allergy_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reaction` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`allergy_id`),
  KEY `fk_patient_allergies_user` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patient_allergies`
--

INSERT INTO `patient_allergies` (`allergy_id`, `patient_id`, `allergy_name`, `reaction`, `created_at`) VALUES
(2, 3, 'Penicillin', 'swelling', '2026-04-03 07:31:20');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `doctor_id` int NOT NULL,
  `user_id` int NOT NULL,
  `report_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `generated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`),
  KEY `fk_reports_doctors` (`doctor_id`),
  KEY `fk_reports_users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_drugs`
--

DROP TABLE IF EXISTS `report_drugs`;
CREATE TABLE IF NOT EXISTS `report_drugs` (
  `report_drug_id` int NOT NULL AUTO_INCREMENT,
  `report_id` int NOT NULL,
  `drug_id` int NOT NULL,
  `dosage` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `frequency` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `duration` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_drug_id`),
  KEY `fk_report_drugs_report` (`report_id`),
  KEY `fk_report_drugs_drug` (`drug_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_drugs`
--

INSERT INTO `report_drugs` (`report_drug_id`, `report_id`, `drug_id`, `dosage`, `frequency`, `duration`, `instructions`, `created_at`) VALUES
(1, 1, 6, '250mg', '2 times daily', '7 days', 'after food', '2026-04-03 07:50:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('patient','doctor','admin') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `role`, `email`, `password`, `created_at`) VALUES
(3, 'amir', 'patient', 'amir@example.com', '1234', '2026-03-10 18:24:26'),
(4, 'Admin User', 'admin', 'admin@gmail.com', '123', '2026-03-22 22:41:45'),
(5, 'Dr. Ahmad', 'doctor', 'doctor1@gmail.com', '123', '2026-03-22 22:41:45'),
(6, 'Dr. Sara', 'doctor', 'doctor2@gmail.com', '123', '2026-03-22 22:41:45'),
(7, 'Dr. Ali', 'doctor', 'doctor3@gmail.com', '123', '2026-03-22 22:41:45'),
(8, 'Dr. Lina', 'doctor', 'doctor4@gmail.com', '123', '2026-03-22 22:41:45'),
(9, 'Dr. Omar', 'doctor', 'doctor5@gmail.com', '123', '2026-03-22 22:41:45'),
(10, 'david', 'doctor', 'david@gmail.com', '123', '2026-03-22 23:47:31'),
(16, 'mike', 'doctor', 'mike@gmail.com', '123', '2026-04-19 18:12:35'),
(18, 'cizar', 'admin', 'cizar@gmail.com', '123', '2026-04-19 18:20:36');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointments_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appointments_doctors` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appointments_patient` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_appointments_patient_user` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `fk_doctors_illnesses` FOREIGN KEY (`illness_id`) REFERENCES `illnesses` (`illness_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doctors_illnesses_specialization` FOREIGN KEY (`specialization`) REFERENCES `illnesses` (`specialization`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doctors_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doctors_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `meals`
--
ALTER TABLE `meals`
  ADD CONSTRAINT `fk_meals_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_meals_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medical_reports`
--
ALTER TABLE `medical_reports`
  ADD CONSTRAINT `fk_medical_reports_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`appointment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_medical_reports_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_medical_reports_patient_user` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_notifications_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nutrition_plans`
--
ALTER TABLE `nutrition_plans`
  ADD CONSTRAINT `fk_nutrition_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nutrition_patient` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `nutrition_tracking`
--
ALTER TABLE `nutrition_tracking`
  ADD CONSTRAINT `fk_nutrition_tracking_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_nutrition_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_patients_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `patient_allergies`
--
ALTER TABLE `patient_allergies`
  ADD CONSTRAINT `fk_patient_allergies_user` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_reports_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reports_doctors` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reports_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reports_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `report_drugs`
--
ALTER TABLE `report_drugs`
  ADD CONSTRAINT `fk_report_drugs_drug` FOREIGN KEY (`drug_id`) REFERENCES `drugs` (`drug_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_report_drugs_report` FOREIGN KEY (`report_id`) REFERENCES `medical_reports` (`report_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
