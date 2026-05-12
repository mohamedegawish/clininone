-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 09:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clinicone`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `appointment_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `queue_number` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `source` enum('clinic','online') NOT NULL DEFAULT 'clinic',
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `clinic_id`, `appointment_date`, `start_time`, `end_time`, `queue_number`, `status`, `source`, `is_paid`, `notes`, `cancellation_reason`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-04-27', '19:14:00', '19:44:00', 1, 'completed', 'clinic', 1, NULL, NULL, '2026-04-27 13:12:08', '2026-04-27 13:12:34'),
(2, 3, 1, 1, '2026-04-27', '08:39:00', '09:09:00', 2, 'completed', 'clinic', 1, NULL, NULL, '2026-04-27 20:37:06', '2026-04-27 20:40:38');

-- --------------------------------------------------------

--
-- Table structure for table `blood_requests`
--

CREATE TABLE `blood_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `blood_type` varchar(255) NOT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `governorate` varchar(255) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hospital` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'normal',
  `urgency_level` enum('high','medium','low') NOT NULL DEFAULT 'low',
  `status` varchar(255) NOT NULL DEFAULT 'new',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blood_requests`
--

INSERT INTO `blood_requests` (`id`, `name`, `phone`, `blood_type`, `quantity`, `governorate`, `city`, `address`, `hospital`, `type`, `urgency_level`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Esraa Mohamed Elsheikh', '01004947096', 'B+', '2', 'القليوبية', NULL, NULL, 'مستشفى بنها الجامعي', 'urgent', 'low', 'pending', '2026-04-27 19:11:50', '2026-04-27 19:11:50'),
(2, 'esraa', '01000000', 'A-', '2', 'الإسكندرية', NULL, NULL, 'مستشفى الجامعة الرئيسي', 'urgent', 'low', 'pending', '2026-04-27 19:43:53', '2026-04-27 19:43:53'),
(3, 'Ms. Esraa Mohamed Elsheikh', '01000000', 'B+', '1', 'القاهرة', NULL, NULL, 'مستشفى بنها الجامعي', 'normal', 'low', 'pending', '2026-04-27 19:52:10', '2026-04-27 19:52:10'),
(4, 'Esraa Mohamed Elsheikh', '01004947096', 'A-', '1', 'الشرقية', NULL, NULL, 'مستشفى بنها الجامعي', 'normal', 'low', 'pending', '2026-04-27 19:54:06', '2026-04-27 19:54:06'),
(5, 'Eng.Esraa Mohamed Elsheikh', '01004947096', 'B+', '1', 'الفيوم', NULL, NULL, 'مستشفى الفيوم العام', 'urgent', 'low', 'pending', '2026-04-27 19:54:34', '2026-04-27 19:54:34'),
(6, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', '2', 'الشرقية', NULL, NULL, 'مستشفى الزقازيق العام', 'urgent', 'low', 'pending', '2026-04-27 19:55:04', '2026-04-27 19:55:04'),
(7, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', '2', 'الشرقية', NULL, NULL, NULL, 'urgent', 'low', 'pending', '2026-04-27 19:57:25', '2026-04-27 19:57:25'),
(8, 'project1', '01004947096', 'AB+', '1', 'الإسكندرية', NULL, NULL, 'مستشفى دار الشفاء', 'normal', 'low', 'pending', '2026-04-27 20:10:28', '2026-04-27 20:10:28'),
(9, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', '1', 'الشرقية', NULL, NULL, 'مستشفى الزقازيق العام', 'urgent', 'low', 'pending', '2026-04-27 20:11:09', '2026-04-27 20:11:09'),
(10, 'Esraa Mohamed Elsheikh', '01004947096', 'B+', '1', 'القاهرة', NULL, NULL, 'مستشفى قصر العيني', 'urgent', 'low', 'pending', '2026-04-27 20:12:15', '2026-04-27 20:12:15'),
(11, 'Esraa Mohamed Elsheikh', '01004947096', 'B+', '1', 'القاهرة', NULL, NULL, 'مستشفى عين شمس التخصصي', 'normal', 'low', 'pending', '2026-04-27 20:19:17', '2026-04-27 20:19:17'),
(12, 'Esraa Mohamed Elsheikh', '01004947096', 'B+', '1', 'القاهرة', NULL, NULL, NULL, 'normal', 'low', 'pending', '2026-04-27 20:19:35', '2026-04-27 20:19:35'),
(13, 'Team Team', '010', 'A+', NULL, 'الشرقية', 'مدينة نصر', NULL, NULL, 'normal', 'low', 'new', '2026-04-28 14:32:13', '2026-04-28 14:32:13');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba', 'i:9;', 1777404032),
('laravel-cache-5c785c036466adea360111aa28563bfd556b5fba:timer', 'i:1777404032;', 1777404032);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clinics`
--

CREATE TABLE `clinics` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clinics`
--

INSERT INTO `clinics` (`id`, `name`, `address`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Test Clinic', 'Test Address', 'inactive', '2026-04-27 12:07:16', NULL),
(2, 'عيادة كلينك ون', 'بلطيم', 'active', '2026-04-27 13:21:19', '2026-04-27 13:21:19'),
(3, 'project1', 'بلطيم', 'active', '2026-04-27 13:26:02', '2026-04-27 13:29:52');

-- --------------------------------------------------------

--
-- Table structure for table `clinic_doctor`
--

CREATE TABLE `clinic_doctor` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `role` enum('admin','doctor') NOT NULL DEFAULT 'doctor',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clinic_doctor`
--

INSERT INTO `clinic_doctor` (`id`, `doctor_id`, `clinic_id`, `role`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'doctor', '2026-04-27 12:07:43', '2026-04-27 12:07:43'),
(2, 2, 3, 'doctor', '2026-04-27 14:08:53', '2026-04-27 14:08:53'),
(3, 3, 2, 'doctor', '2026-04-28 16:09:44', '2026-04-28 16:09:44');

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `symptoms` text DEFAULT NULL,
  `diagnosis` text DEFAULT NULL,
  `treatment` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `bp` varchar(20) DEFAULT NULL,
  `temp` varchar(10) DEFAULT NULL,
  `pulse` varchar(10) DEFAULT NULL,
  `hr` varchar(10) DEFAULT NULL,
  `rr` varchar(10) DEFAULT NULL,
  `spo2` varchar(10) DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `medications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medications`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consultations`
--

INSERT INTO `consultations` (`id`, `appointment_id`, `patient_id`, `doctor_id`, `clinic_id`, `symptoms`, `diagnosis`, `treatment`, `notes`, `bp`, `temp`, `pulse`, `hr`, `rr`, `spo2`, `weight`, `height`, `medications`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, NULL, 'mm', NULL, NULL, NULL, ',', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27 13:12:34', '2026-04-27 13:12:34'),
(2, 2, 3, 1, 1, NULL, 'headache', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-27 20:40:37', '2026-04-27 20:40:37');

-- --------------------------------------------------------

--
-- Table structure for table `consultation_medications`
--

CREATE TABLE `consultation_medications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `consultation_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `generic` varchar(255) DEFAULT NULL,
  `frequency` varchar(255) DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `duration` varchar(255) DEFAULT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `consultation_medications`
--

INSERT INTO `consultation_medications` (`id`, `consultation_id`, `name`, `generic`, `frequency`, `route`, `duration`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'panadol', '50', 'Once Daily', 'حقن (Injection)', '5 Days', 0, '2026-04-27 13:12:34', '2026-04-27 13:12:34'),
(2, 2, 'panadol', '50', 'Every 8 Hours', 'حقن وريدي (IV)', '7 Days', 0, '2026-04-27 20:40:37', '2026-04-27 20:40:37');

-- --------------------------------------------------------

--
-- Table structure for table `diagnoses`
--

CREATE TABLE `diagnoses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `diagnoses`
--

INSERT INTO `diagnoses` (`id`, `clinic_id`, `name`, `created_at`, `updated_at`) VALUES
(1, 1, 'mm', '2026-04-27 13:12:20', '2026-04-27 13:12:20'),
(2, 1, 'headache', '2026-04-27 20:40:27', '2026-04-27 20:40:27');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `arabic_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `governorate` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `specialty` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `photo_path` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `gender` enum('male','female') DEFAULT NULL,
  `experience_years` int(11) DEFAULT NULL,
  `qualification` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `name`, `arabic_name`, `email`, `phone`, `address`, `governorate`, `city`, `specialty`, `price`, `photo_path`, `status`, `gender`, `experience_years`, `qualification`, `bio`, `created_at`, `updated_at`) VALUES
(1, 2, 'Dr. Test', 'د. تجربة', 'doctor@test.com', '0123456789', NULL, 'القاهرة', 'التجمع الخامس', 'General', 100.00, NULL, 'active', NULL, NULL, NULL, NULL, '2026-04-27 12:07:33', '2026-04-28 15:48:43'),
(2, 4, 'Esraa Mohamed Elsheikh', 'إسراء محمد الشيخ', 'israa.m.elshikh@gmail.com', '01004947096', NULL, 'القاهرة', 'المعادي', 'lhuiui', 100.00, NULL, 'active', 'male', 0, NULL, NULL, '2026-04-27 14:08:53', '2026-04-28 15:48:43'),
(3, 5, 'Osama Marzouk', 'وساما مارزوك', 'dr.osama@clinicone.com', '01004753710', NULL, NULL, NULL, 'اطفال', 200.00, NULL, 'active', 'male', 10, NULL, NULL, '2026-04-28 16:09:44', '2026-04-28 16:09:44');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedules`
--

CREATE TABLE `doctor_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` tinyint(4) NOT NULL COMMENT '0=Sunday, 1=Monday, ..., 6=Saturday',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `slot_duration` smallint(5) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `blood_type` varchar(255) NOT NULL,
  `governorate` varchar(255) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `last_donation_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`id`, `name`, `phone`, `blood_type`, `governorate`, `city`, `address`, `status`, `last_donation_date`, `created_at`, `updated_at`) VALUES
(1, 'Esraa Mohamed Elsheikh', '01004947096', 'O+', 'القاهرة', NULL, NULL, 'active', '2026-04-28', '2026-04-27 18:25:15', '2026-04-27 18:25:15'),
(2, 'Esraa Mohamed Elsheikh', '01004947096', 'O+', 'القاهرة', NULL, NULL, 'active', '2026-04-28', '2026-04-27 18:25:16', '2026-04-27 18:25:16'),
(3, 'Esraa Mohamed Elsheikh', '01004947096', 'O+', 'القاهرة', NULL, NULL, 'active', '2026-04-28', '2026-04-27 18:25:16', '2026-04-27 18:25:16'),
(4, 'Esraa Mohamed Elsheikh', '01004947096', 'O+', 'القاهرة', NULL, NULL, 'active', '2026-04-28', '2026-04-27 18:25:16', '2026-04-27 18:25:16'),
(5, 'Esraa Elsheikh', '01000000', 'A-', 'القليوبية', NULL, NULL, 'active', '2026-04-28', '2026-04-27 18:39:32', '2026-04-27 18:39:32'),
(6, 'Esraa Elsheikh', '01000000', 'A-', 'القليوبية', NULL, NULL, 'active', '2026-04-28', '2026-04-27 18:39:34', '2026-04-27 18:39:34'),
(7, 'Esraa Mohamed Elsheikh', '01000000', 'A+', 'القليوبية', NULL, NULL, 'active', NULL, '2026-04-27 18:58:43', '2026-04-27 18:58:43'),
(8, 'Esraa Mohamed Elsheikh', '01000000', 'A+', 'القليوبية', NULL, NULL, 'active', NULL, '2026-04-27 18:58:45', '2026-04-27 18:58:45'),
(9, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'القليوبية', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:03:04', '2026-04-27 19:03:04'),
(10, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'القليوبية', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:03:05', '2026-04-27 19:03:05'),
(11, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'القليوبية', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:03:05', '2026-04-27 19:03:05'),
(12, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'القليوبية', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:03:06', '2026-04-27 19:03:06'),
(13, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'القليوبية', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:03:06', '2026-04-27 19:03:06'),
(14, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'القليوبية', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:03:06', '2026-04-27 19:03:06'),
(15, 'Esraa Mohamed Elsheikh', '01004947096', 'A-', 'المنيا', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:07:42', '2026-04-27 19:07:42'),
(16, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'الفيوم', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:11:33', '2026-04-27 19:11:33'),
(17, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'الفيوم', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:11:35', '2026-04-27 19:11:35'),
(18, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'الفيوم', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:11:36', '2026-04-27 19:11:36'),
(19, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'الفيوم', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:11:36', '2026-04-27 19:11:36'),
(20, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'الفيوم', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:11:36', '2026-04-27 19:11:36'),
(21, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'الفيوم', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:11:37', '2026-04-27 19:11:37'),
(22, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', NULL, '2026-04-27 19:15:31', '2026-04-27 19:15:31'),
(23, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', NULL, '2026-04-27 19:16:34', '2026-04-27 19:16:34'),
(24, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', NULL, '2026-04-27 19:17:07', '2026-04-27 19:17:07'),
(25, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', NULL, '2026-04-27 19:17:19', '2026-04-27 19:17:19'),
(26, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:17:48', '2026-04-27 19:17:48'),
(27, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:18:02', '2026-04-27 19:18:02'),
(28, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:18:04', '2026-04-27 19:18:04'),
(29, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:18:05', '2026-04-27 19:18:05'),
(30, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:18:05', '2026-04-27 19:18:05'),
(31, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:18:06', '2026-04-27 19:18:06'),
(32, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:18:06', '2026-04-27 19:18:06'),
(33, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:18:06', '2026-04-27 19:18:06'),
(34, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:18:07', '2026-04-27 19:18:07'),
(35, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:18:07', '2026-04-27 19:18:07'),
(36, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:19:12', '2026-04-27 19:19:12'),
(37, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:19:12', '2026-04-27 19:19:12'),
(38, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:19:13', '2026-04-27 19:19:13'),
(39, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:19:13', '2026-04-27 19:19:13'),
(40, 'Test', '01234567890', 'A+', 'الجيزة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:19:13', '2026-04-27 19:19:13'),
(41, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'الفيوم', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:22:25', '2026-04-27 19:22:25'),
(42, 'Final Test', '01000000000', 'B+', 'القاهرة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:25:06', '2026-04-27 19:25:06'),
(43, 'Final Test', '01000000000', 'B+', 'القاهرة', NULL, NULL, 'active', '2025-01-01', '2026-04-27 19:25:20', '2026-04-27 19:25:20'),
(44, 'Final Test', '01000000000', 'B+', 'القاهرة', NULL, NULL, 'active', NULL, '2026-04-27 19:26:28', '2026-04-27 19:26:28'),
(45, 'Esraa Mohamed Elsheikh', '01004947096', 'B-', 'دمياط', NULL, NULL, 'active', NULL, '2026-04-27 19:42:40', '2026-04-27 19:42:40'),
(46, 'Esraa Mohamed Elsheikh', '01004947096', 'A+', 'الشرقية', NULL, NULL, 'active', '2026-04-28', '2026-04-27 19:51:31', '2026-04-27 19:51:31'),
(47, 'Team Team', '010', 'A+', 'الشرقية', 'مدينة نصر', NULL, 'active', '2026-04-28', '2026-04-28 14:31:50', '2026-04-28 14:31:50');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `doctor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `clinic_id`, `doctor_id`, `category`, `amount`, `date`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Salaries', 5000.00, '2026-04-27', NULL, '2026-04-27 12:09:51', '2026-04-27 12:09:51'),
(2, 1, 1, 'Medical Supplies', 2000.00, '2026-04-27', NULL, '2026-04-27 20:27:35', '2026-04-27 20:27:35');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `governorate` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `name`, `governorate`, `created_at`, `updated_at`) VALUES
(1, 'مستشفى قصر العيني', 'القاهرة', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(2, 'مستشفى عين شمس التخصصي', 'القاهرة', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(3, 'مستشفى دار الفؤاد', 'القاهرة', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(4, 'مستشفى السلام الدولي', 'القاهرة', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(5, 'مستشفى أم المصريين', 'الجيزة', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(6, 'مستشفى الهرم', 'الجيزة', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(7, 'مستشفى الشيخ زايد التخصصي', 'الجيزة', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(8, 'مستشفى الجامعة الرئيسي', 'الإسكندرية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(9, 'مستشفى شرق المدينة', 'الإسكندرية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(10, 'مستشفى دار الشفاء', 'الإسكندرية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(11, 'مستشفى المنصورة الجامعي', 'الدقهلية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(12, 'مستشفى الطوارئ', 'الدقهلية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(13, 'مستشفى الزقازيق العام', 'الشرقية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(14, 'مستشفى الأحرار التعليمي', 'الشرقية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(15, 'مستشفى طنطا الجامعي', 'الغربية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(16, 'مستشفى المنشاوي العام', 'الغربية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(17, 'مستشفى شبين الكوم التعليمي', 'المنوفية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(18, 'مستشفى الجامعة', 'المنوفية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(19, 'مستشفى بنها الجامعي', 'القليوبية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(20, 'مستشفى ناصر العام', 'القليوبية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(21, 'مستشفى كفر الشيخ العام', 'كفر الشيخ', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(22, 'مستشفى بلطيم النموذجي', 'كفر الشيخ', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(23, 'مستشفى دمنهور التعليمي', 'البحيرة', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(24, 'مستشفى كفر الدوار العام', 'البحيرة', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(25, 'مستشفى دمياط العام', 'دمياط', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(26, 'مستشفى بورسعيد العام', 'بورسعيد', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(27, 'مستشفى جامعة قناة السويس', 'الإسماعيلية', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(28, 'مستشفى السويس العام', 'السويس', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(29, 'مستشفى الفيوم العام', 'الفيوم', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(30, 'مستشفى بني سويف العام', 'بني سويف', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(31, 'مستشفى المنيا الجامعي', 'المنيا', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(32, 'مستشفى أسيوط الجامعي', 'أسيوط', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(33, 'مستشفى سوهاج الجامعي', 'سوهاج', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(34, 'مستشفى قنا العام', 'قنا', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(35, 'مستشفى الأقصر الدولي', 'الأقصر', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(36, 'مستشفى أسوان الجامعي', 'أسوان', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(37, 'مستشفى العريش العام', 'شمال سيناء', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(38, 'مستشفى شرم الشيخ الدولي', 'جنوب سيناء', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(39, 'مستشفى الغردقة العام', 'البحر الأحمر', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(40, 'مستشفى مطروح العام', 'مطروح', '2026-04-27 18:12:33', '2026-04-27 18:12:33'),
(41, 'مستشفى بنها الجامعي', 'القاهرة', '2026-04-27 19:52:10', '2026-04-27 19:52:10'),
(42, 'مستشفى بنها الجامعي', 'الشرقية', '2026-04-27 19:54:06', '2026-04-27 19:54:06');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_04_20_180202_create_personal_access_tokens_table', 1),
(5, '2026_04_20_181608_create_password_resets_table', 1),
(6, '2026_04_20_183848_add_expires_at_to_personal_access_tokens', 1),
(7, '2026_04_20_185331_create_plans_table', 1),
(8, '2026_04_20_185340_create_clinics_table', 1),
(9, '2026_04_20_185351_create_subscriptions_table', 1),
(10, '2026_04_20_185404_create_payments_table', 1),
(11, '2026_04_20_185436_create_invoices_table', 1),
(12, '2026_04_20_185608_create_doctors_table', 1),
(13, '2026_04_20_185620_create_patients_table', 1),
(14, '2026_04_20_185643_create_appointments_table', 1),
(15, '2026_04_20_185657_create_ratings_table', 1),
(16, '2026_04_20_185718_create_clinic_doctor_table', 1),
(17, '2026_04_20_185744_create_usage_logs_table', 1),
(18, '2026_04_21_191206_add_address_fields_to_doctors_table', 1),
(19, '2026_04_21_223411_add_detailed_fields_to_doctors_table', 1),
(20, '2026_04_22_011255_add_fields_to_ratings_table', 1),
(21, '2026_04_22_020500_create_doctor_schedules_table', 1),
(22, '2026_04_22_020501_rebuild_appointments_table', 1),
(23, '2026_04_22_020502_add_queue_number_to_appointments_table', 1),
(24, '2026_04_26_173347_add_features_to_plans_table', 1),
(25, '2026_04_26_174301_add_super_admin_fields_to_doctors_table', 1),
(26, '2026_04_26_214522_make_doctor_schedules_columns_nullable', 1),
(27, '2026_04_27_001229_create_settings_table', 1),
(28, '2026_04_27_005453_create_consultations_table', 1),
(29, '2026_04_27_005454_create_expenses_table', 1),
(30, '2026_04_27_005455_create_otp_verifications_table', 1),
(31, '2026_04_27_010458_add_source_and_payment_to_appointments_table', 1),
(32, '2026_04_27_014730_add_vitals_and_medications_to_consultations_table', 1),
(33, '2026_04_27_014735_create_diagnoses_table', 1),
(34, '2026_04_27_021758_create_consultation_medications_table', 1),
(35, '2026_04_27_171950_create_specialties_table', 2),
(36, '2026_04_27_204406_create_donors_table', 3),
(37, '2026_04_27_204407_create_blood_requests_table', 3),
(38, '2026_04_27_210240_add_hospital_to_blood_requests_table', 4),
(39, '2026_04_27_211041_create_hospitals_table', 5),
(40, '2026_04_27_214528_add_urgency_level_to_blood_requests_table', 6),
(41, '2026_04_28_140703_update_blood_requests_and_donors_tables', 7);

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(10) NOT NULL,
  `type` varchar(255) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `english_name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `ssn` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `nationality` varchar(255) DEFAULT 'Egypt',
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `policy_name` varchar(255) DEFAULT NULL,
  `class` varchar(255) DEFAULT NULL,
  `card_no` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT 'active',
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `full_name`, `english_name`, `phone`, `ssn`, `birth_date`, `age`, `gender`, `nationality`, `address`, `email`, `company`, `policy_name`, `class`, `card_no`, `status`, `clinic_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Esraa Mohamed Elsheikh', 'ESraa', '01004947096', '30504108800987', '2026-04-02', 5, 'female', 'Egypt', 'egypt', 'israa.m.elshikh@gmail.com', NULL, NULL, NULL, NULL, 'active', 1, '2026-04-27 13:11:32', '2026-04-27 13:11:32', NULL),
(3, 'Esraa Mohamed Elsheikh', 'ESraa', '010045555555', '30504108800987', '2026-04-16', 21, 'female', 'Egypt', 'egypt', 'israa.m.elshikh@gmail.com', NULL, NULL, NULL, NULL, 'active', 1, '2026-04-27 20:35:55', '2026-04-27 20:35:55', NULL),
(4, 'Mohamed Elsheikh', 'Mohamed Elsheikh', '01004947096', NULL, '2026-04-02', NULL, 'female', 'Egypt', 'egypt', NULL, NULL, NULL, NULL, NULL, 'active', 2, '2026-04-27 21:06:38', '2026-04-27 21:06:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `duration` int(11) NOT NULL,
  `max_patients` int(11) NOT NULL DEFAULT 0,
  `max_appointments` int(11) NOT NULL DEFAULT 0,
  `features` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`id`, `name`, `price`, `duration`, `max_patients`, `max_appointments`, `features`, `created_at`, `updated_at`) VALUES
(1, 'الباقة الأساسية', 199.00, 1, 1, 100, 'إدارة المواعيد\r\nإدارة المرضى\r\nعرض جدول العمل\r\nحجز المرضى أونلاين\r\nواجهة سهلة الاستخدام\r\nدعم فني عبر الإيميل', '2026-04-27 13:46:57', '2026-04-27 14:11:14'),
(2, 'الباقة الاحترافية', 499.00, 1, 1000, 500, 'دعم فني 24/7\\nتقارير متقدمة\\nإرسال SMS للمرضى', '2026-04-27 14:11:14', '2026-04-27 14:11:14'),
(3, 'الباقة المتكاملة', 999.00, 1, 5000, 2000, 'كل مميزات الاحترافية\\nتطبيق موبايل خاص\\nدفع إلكتروني', '2026-04-27 14:11:14', '2026-04-27 14:11:14');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `doctor_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('3Eb5dbDPlMoipgOtVhpO8LEjmkz9BwzI6AH1uANY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTFBNTEt0TzE3VFNiOFcxUE5XcFFmbzJtV0x3bFowQnh6cGhramlvUyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NjoibG9jYWxlIjtzOjI6ImVuIjt9', 1777397814),
('bvgt3QlHDpXT8b8wmpmIhzsHRFmdENhsok7GdGb0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoieGpPbHJUM3lxRmVFTHRDY0xPazBYc1M5WlI1b1B2UVhTbnVaSktNSSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czoxMjoicHVibGljLmluZGV4Ijt9czo2OiJsb2NhbGUiO3M6MjoiZW4iO30=', 1777403979);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'system_name', 'كلينيك وان', '2026-04-27 14:00:45', '2026-04-27 14:00:45'),
(2, 'system_logo', 'logo_1777309245.jpg', '2026-04-27 14:00:45', '2026-04-27 14:00:45'),
(3, 'public_logo', 'public_logo_1777309245.jpg', '2026-04-27 14:00:45', '2026-04-27 14:00:45'),
(4, 'landing_bg', 'bg_1777335128.jpg', '2026-04-27 14:00:45', '2026-04-27 21:12:08');

-- --------------------------------------------------------

--
-- Table structure for table `specialties`
--

CREATE TABLE `specialties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `specialties`
--

INSERT INTO `specialties` (`id`, `name`, `icon`, `created_at`, `updated_at`) VALUES
(1, 'أسنان', 'ph-bold ph-tooth', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(2, 'جلدية', 'ph-bold ph-drop', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(3, 'نساء وتوليد', 'ph-bold ph-gender-female', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(4, 'باطنة', 'ph-bold ph-stethoscope', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(5, 'أطفال', 'ph-bold ph-baby', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(6, 'عيون', 'ph-bold ph-eye', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(7, 'عظام', 'ph-bold ph-bone', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(8, 'أنف وأذن وحنجرة', 'ph-bold ph-ear', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(9, 'جراحة عامة', 'ph-bold ph-scissors', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(10, 'قلب وأوعية دموية', 'ph-bold ph-heartbeat', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(11, 'مخ وأعصاب', 'ph-bold ph-brain', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(12, 'مسالك بولية', 'ph-bold ph-drop', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(13, 'نفسية', 'ph-bold ph-brain', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(14, 'تخسيس وتغذية', 'ph-bold ph-bowl-food', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(15, 'علاج طبيعي', 'ph-bold ph-accessibility', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(16, 'أشعة', 'ph-bold ph-radioactive', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(17, 'تحاليل', 'ph-bold ph-test-tube', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(18, 'كبد', 'ph-bold ph-activity', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(19, 'كلى', 'ph-bold ph-activity', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(20, 'أورام', 'ph-bold ph-virus', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(21, 'صدر وجهاز تنفسي', 'ph-bold ph-wind', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(22, 'غدد صماء وسكر', 'ph-bold ph-thermometer', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(23, 'مسالك بولية وتناسلية', 'ph-bold ph-drop', '2026-04-27 14:39:27', '2026-04-27 14:39:27'),
(24, 'جراحة سمنة ومناظير', 'ph-bold ph-gauge', '2026-04-27 14:39:27', '2026-04-27 14:39:27');

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `clinic_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','active','expired','cancelled') NOT NULL DEFAULT 'pending',
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `auto_renew` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usage_logs`
--

CREATE TABLE `usage_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','patient','doctor') NOT NULL DEFAULT 'patient',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Test User', 'test@example.com', '2026-04-27 12:05:56', '$2y$12$6SqOv7pvCwp4DbUDfJNXTOHgOAVzSSLP2.Z1v52BFwEP4MzF7il36', 'patient', 'NUlan8lwcQ', '2026-04-27 12:05:57', '2026-04-27 12:52:18'),
(2, 'Dr. Test', 'doctor@test.com', NULL, '$2y$12$6SqOv7pvCwp4DbUDfJNXTOHgOAVzSSLP2.Z1v52BFwEP4MzF7il36', 'doctor', NULL, '2026-04-27 12:06:48', '2026-04-27 12:52:18'),
(3, 'Admin', 'admin@example.com', NULL, '$2y$12$6SqOv7pvCwp4DbUDfJNXTOHgOAVzSSLP2.Z1v52BFwEP4MzF7il36', 'admin', NULL, '2026-04-27 12:21:21', '2026-04-27 12:52:18'),
(4, 'Esraa Mohamed Elsheikh', 'israa.m.elshikh@gmail.com', NULL, '$2y$12$HkxJA3Vy60EFr23xkGAuzeMMRb97lTtOWBPo3.egiTCjwyMLVajwC', 'doctor', NULL, '2026-04-27 14:08:53', '2026-04-27 14:08:53'),
(5, 'Osama Marzouk', 'dr.osama@clinicone.com', NULL, '$2y$12$DsTw/IkF8LaHHM3Q1A8ifOf8LF.EBQXZPg0An4C.VSF.K/0lh/.wi', 'doctor', NULL, '2026-04-28 16:09:44', '2026-04-28 16:09:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_doctor_slot` (`doctor_id`,`appointment_date`,`start_time`),
  ADD KEY `appointments_clinic_id_appointment_date_index` (`clinic_id`,`appointment_date`),
  ADD KEY `appointments_patient_id_appointment_date_index` (`patient_id`,`appointment_date`),
  ADD KEY `appointments_doctor_id_appointment_date_status_index` (`doctor_id`,`appointment_date`,`status`),
  ADD KEY `appointments_status_index` (`status`);

--
-- Indexes for table `blood_requests`
--
ALTER TABLE `blood_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `clinics`
--
ALTER TABLE `clinics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clinics_status_index` (`status`);

--
-- Indexes for table `clinic_doctor`
--
ALTER TABLE `clinic_doctor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clinic_doctor_doctor_id_clinic_id_unique` (`doctor_id`,`clinic_id`),
  ADD KEY `clinic_doctor_doctor_id_index` (`doctor_id`),
  ADD KEY `clinic_doctor_clinic_id_index` (`clinic_id`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultations_appointment_id_foreign` (`appointment_id`),
  ADD KEY `consultations_patient_id_foreign` (`patient_id`),
  ADD KEY `consultations_doctor_id_foreign` (`doctor_id`),
  ADD KEY `consultations_clinic_id_foreign` (`clinic_id`);

--
-- Indexes for table `consultation_medications`
--
ALTER TABLE `consultation_medications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consultation_medications_consultation_id_foreign` (`consultation_id`);

--
-- Indexes for table `diagnoses`
--
ALTER TABLE `diagnoses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diagnoses_clinic_id_foreign` (`clinic_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `doctors_email_unique` (`email`),
  ADD KEY `doctors_user_id_foreign` (`user_id`),
  ADD KEY `doctors_email_index` (`email`),
  ADD KEY `doctors_phone_index` (`phone`),
  ADD KEY `doctors_status_index` (`status`);

--
-- Indexes for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_doctor_clinic_day` (`doctor_id`,`clinic_id`,`day_of_week`),
  ADD KEY `doctor_schedules_doctor_id_is_active_index` (`doctor_id`,`is_active`),
  ADD KEY `doctor_schedules_clinic_id_day_of_week_index` (`clinic_id`,`day_of_week`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_clinic_id_foreign` (`clinic_id`),
  ADD KEY `expenses_doctor_id_foreign` (`doctor_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `otp_verifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patients_phone_clinic_id_unique` (`phone`,`clinic_id`),
  ADD KEY `patients_clinic_id_index` (`clinic_id`),
  ADD KEY `patients_phone_index` (`phone`),
  ADD KEY `patients_ssn_index` (`ssn`),
  ADD KEY `patients_card_no_index` (`card_no`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ratings_doctor_id_foreign` (`doctor_id`),
  ADD KEY `ratings_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `specialties`
--
ALTER TABLE `specialties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriptions_plan_id_foreign` (`plan_id`),
  ADD KEY `subscriptions_clinic_id_status_index` (`clinic_id`,`status`),
  ADD KEY `subscriptions_end_at_index` (`end_at`);

--
-- Indexes for table `usage_logs`
--
ALTER TABLE `usage_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `blood_requests`
--
ALTER TABLE `blood_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `clinics`
--
ALTER TABLE `clinics`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `clinic_doctor`
--
ALTER TABLE `clinic_doctor`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `consultation_medications`
--
ALTER TABLE `consultation_medications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `diagnoses`
--
ALTER TABLE `diagnoses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `specialties`
--
ALTER TABLE `specialties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usage_logs`
--
ALTER TABLE `usage_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `clinic_doctor`
--
ALTER TABLE `clinic_doctor`
  ADD CONSTRAINT `clinic_doctor_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `clinic_doctor_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consultations_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `consultation_medications`
--
ALTER TABLE `consultation_medications`
  ADD CONSTRAINT `consultation_medications_consultation_id_foreign` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `diagnoses`
--
ALTER TABLE `diagnoses`
  ADD CONSTRAINT `diagnoses_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD CONSTRAINT `doctor_schedules_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_schedules_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD CONSTRAINT `otp_verifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_clinic_id_foreign` FOREIGN KEY (`clinic_id`) REFERENCES `clinics` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
