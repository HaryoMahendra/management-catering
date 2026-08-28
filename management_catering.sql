-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table management_catering.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table management_catering.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table management_catering.karyawans
CREATE TABLE IF NOT EXISTS `karyawans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nif` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shift` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `karyawans_nif_unique` (`nif`)
) ENGINE=InnoDB AUTO_INCREMENT=209 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table management_catering.karyawans: ~8 rows (approximately)
INSERT INTO `karyawans` (`id`, `nif`, `nama`, `departemen`, `lokasi`, `shift`, `no_hp`, `created_at`, `updated_at`) VALUES
	(182, 'NIF0001', 'nama1', 'Internal Audit', 'GCPI 1', 'Shift 1', NULL, '2026-08-20 05:10:01', '2026-08-26 05:10:01'),
	(183, 'NIF0002', 'nama2', 'Operating Company', 'GCPI 2', 'Shift 2', NULL, '2026-08-21 05:10:01', '2026-08-26 05:10:01'),
	(184, 'NIF0003', 'nama3', 'Quasi Holding', 'GCPI 3', 'Shift 3', NULL, '2026-08-22 05:10:01', '2026-08-26 05:10:01'),
	(185, 'NIF0004', 'nama4', 'Shared Services Center', 'GCPI 1', 'Non Shift', NULL, '2026-08-23 05:10:01', '2026-08-26 05:10:01'),
	(186, 'NIF0005', 'nama5', 'General Affairs', 'GCPI 2', 'Shift 1', NULL, '2026-08-24 05:10:01', '2026-08-26 05:10:01'),
	(187, 'NIF0006', 'nama6', 'HR Operations', 'GCPI 3', 'Shift 2', NULL, '2026-08-25 05:10:01', '2026-08-26 05:10:01'),
	(188, 'NIF0007', 'nama7', 'HRPP', 'GCPI 1', 'Shift 1', NULL, '2026-08-25 17:00:00', '2026-08-26 05:10:38'),
	(189, 'NIF0008', 'nama8', 'Industrial Relations', 'GCPI 2', 'Non Shift', NULL, '2026-08-27 05:10:01', '2026-08-26 05:10:01'),
	(190, 'NIF0009', 'nama9', 'Rewards', 'GCPI 3', 'Shift 1', NULL, '2027-08-20 05:10:01', '2026-08-26 05:10:01'),
	(191, 'NIF0010', 'nama10', 'General Affairs', 'GCPI 1', 'Shift 2', NULL, '2027-08-21 05:10:01', '2026-08-26 05:10:01'),
	(192, 'NIF0011', 'nama11', 'HR & GA', 'GCPI 2', 'Shift 3', NULL, '2027-08-22 05:10:01', '2026-08-26 05:10:01'),
	(193, 'NIF0012', 'nama12', 'IT', 'GCPI 3', 'Non Shift', NULL, '2027-08-23 05:10:01', '2026-08-26 05:10:01'),
	(194, 'NIF0013', 'nama13', 'IT Business Partner', 'GCPI 1', 'Shift 1', NULL, '2027-08-24 05:10:01', '2026-08-26 05:10:01'),
	(195, 'NIF0014', 'nama14', 'IT Infrastructure', 'GCPI 2', 'Shift 2', NULL, '2027-08-25 05:10:01', '2026-08-26 05:10:01'),
	(196, 'NIF0015', 'nama15', 'Legal', 'GCPI 3', 'Shift 3', NULL, '2027-08-26 05:10:01', '2026-08-26 05:10:01'),
	(197, 'NIF0016', 'nama16', 'Field Sales', 'GCPI 1', 'Non Shift', NULL, '2027-08-27 05:10:01', '2026-08-26 05:10:01'),
	(198, 'NIF0017', 'nama17', 'GT', 'GCPI 2', 'Shift 1', NULL, '2028-08-20 05:10:01', '2026-08-26 05:10:01'),
	(199, 'NIF0018', 'nama18', 'Customer Services', 'GCPI 3', 'Shift 2', NULL, '2028-08-21 05:10:01', '2026-08-26 05:10:01'),
	(200, 'NIF0019', 'nama19', 'HSE', 'GCPI 1', 'Shift 3', NULL, '2028-08-22 05:10:01', '2026-08-26 05:10:01'),
	(201, 'NIF0020', 'nama20', 'Indirect Sourcing', 'GCPI 2', 'Non Shift', NULL, '2028-08-23 05:10:01', '2026-08-26 05:10:01'),
	(202, 'NIF0021', 'nama21', 'PPIC', 'GCPI 3', 'Shift 1', NULL, '2028-08-24 05:10:01', '2026-08-26 05:10:01'),
	(203, 'NIF0022', 'nama22', 'Production ', 'GCPI 1', 'Shift 2', NULL, '2028-08-25 05:10:01', '2026-08-26 05:10:01'),
	(204, 'NIF0023', 'nama23', 'QC', 'GCPI 2', 'Shift 3', NULL, '2028-08-26 05:10:01', '2026-08-26 05:10:01'),
	(205, 'NIF0024', 'nama24', 'Secretary', 'GCPI 3', 'Non Shift', NULL, '2028-08-27 05:10:01', '2026-08-26 05:10:01'),
	(206, 'NIF0025', 'nama25', 'Supply Chain', 'GCPI 1', 'Shift 1', NULL, '2029-08-20 05:10:01', '2026-08-26 05:10:01'),
	(207, 'NIF0026', 'nama26', 'Technic', 'GCPI 2', 'Shift 2', NULL, '2029-08-21 05:10:01', '2026-08-26 05:10:01'),
	(208, 'NIF0027', 'nama27', 'Warehouse Operation', 'GCPI 3', 'Shift 3', NULL, '2029-08-22 05:10:01', '2026-08-26 05:10:01');

-- Dumping structure for table management_catering.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table management_catering.migrations: ~0 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2026_08_01_014601_create_karyawans_table', 1),
	(6, '2026_08_01_014617_create_pengambilan_caterings_table', 1),
	(7, '2026_08_03_090012_add_role_to_users_table', 2),
	(8, '2026_08_04_100426_add_foto_to_users_table', 3);

-- Dumping structure for table management_catering.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table management_catering.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table management_catering.pengambilan_caterings
CREATE TABLE IF NOT EXISTS `pengambilan_caterings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `karyawan_id` bigint unsigned NOT NULL,
  `tanggal` date NOT NULL,
  `waktu_scan` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengambilan_caterings_karyawan_id_foreign` (`karyawan_id`),
  CONSTRAINT `pengambilan_caterings_karyawan_id_foreign` FOREIGN KEY (`karyawan_id`) REFERENCES `karyawans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table management_catering.pengambilan_caterings: ~22 rows (approximately)
INSERT INTO `pengambilan_caterings` (`id`, `karyawan_id`, `tanggal`, `waktu_scan`, `created_at`, `updated_at`) VALUES
	(48, 188, '2026-08-26', '12:10:52', '2026-08-26 05:10:52', '2026-08-26 05:10:52');

-- Dumping structure for table management_catering.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table management_catering.personal_access_tokens: ~0 rows (approximately)

-- Dumping structure for table management_catering.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('super_admin','admin_catering','petugas_catering') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'petugas_catering',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table management_catering.users: ~4 rows (approximately)
INSERT INTO `users` (`id`, `name`, `email`, `foto`, `role`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
	(1, 'Super Admin', 'superadmin@gmail.com', '1785813413_1.png', 'super_admin', NULL, '$2y$12$6k03/LdIPokKzMq1lgTMzu0LCf8kRAV8JxYCryb0R5/RXDtSUe99C', NULL, '2026-08-03 02:07:28', '2026-08-04 03:16:53'),
	(2, 'Admin Catering', 'admin@gmail.com', '1785813353_2.png', 'admin_catering', NULL, '$2y$12$m/XscciPjQB2akiAVvcLQOSFfLY49nueeYLyr.oD9ZDZFlxTN9NPa', NULL, '2026-08-03 02:07:28', '2026-08-04 03:15:53'),
	(3, 'Petugas Catering', 'petugas@gmail.com', '1785813383_3.png', 'petugas_catering', NULL, '$2y$12$Gg7ERBl7pETR1X0NaoaeEuDrQgPg9TJyULSWYm9WOo2RvX.UPqiqG', NULL, '2026-08-03 02:07:29', '2026-08-04 03:16:23'),
	(10, 'Master Admin', 'masteradmin@gmail.com', '1787540180_10.jpg', 'super_admin', NULL, '$2y$12$wgleZeRaDrAtYXgJtwm0oe8QDfa96Tu1bof942tOW3XTNeK0P8GMm', NULL, '2026-08-22 05:59:30', '2026-08-24 02:56:21');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
