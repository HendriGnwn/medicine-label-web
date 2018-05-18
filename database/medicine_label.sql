-- Adminer 4.6.2 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1,	'2014_10_12_000000_create_users_table',	1),
(2,	'2014_10_12_100000_create_password_resets_table',	1);

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `setting`;
CREATE TABLE `setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address` varchar(600) NOT NULL,
  `apoteker` varchar(255) NOT NULL,
  `sik` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `setting` (`id`, `address`, `apoteker`, `sik`, `created_at`, `updated_at`) VALUES
(1,	'Jl Dr. Semeru No. 144 - Bogor',	'Dra. Lea P. Sjamsudin',	'165/SIK/JB/1993',	NULL,	'2018-04-11 00:49:31');

DROP TABLE IF EXISTS `transaction_add_medicine_additional`;
CREATE TABLE `transaction_add_medicine_additional` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_registration_id` int(11) NOT NULL,
  `print_count` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `transaction_add_medicine_additional` (`id`, `patient_registration_id`, `print_count`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	3646481,	4,	'2018-05-08 12:03:19',	'2018-05-08 12:13:58',	NULL,	NULL),
(2,	3647182,	1,	'2018-05-08 12:27:52',	'2018-05-08 12:27:52',	NULL,	NULL),
(3,	19,	1,	'2018-05-17 08:51:56',	'2018-05-17 08:51:56',	NULL,	NULL);

DROP TABLE IF EXISTS `transaction_add_medicine_additional_detail`;
CREATE TABLE `transaction_add_medicine_additional_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_add_medicine_additional_id` int(11) NOT NULL,
  `transaction_medicine_id` int(11) NOT NULL COMMENT 'berelasi dengan mm_transaksi_add_obat',
  `how_to_use` varchar(100) DEFAULT NULL,
  `receipt_number` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_add_medicine_additional_id` (`transaction_add_medicine_additional_id`),
  CONSTRAINT `transaction_add_medicine_additional_detail_ibfk_2` FOREIGN KEY (`transaction_add_medicine_additional_id`) REFERENCES `transaction_add_medicine_additional` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `transaction_add_medicine_additional_detail` (`id`, `transaction_add_medicine_additional_id`, `transaction_medicine_id`, `how_to_use`, `receipt_number`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1,	1,	464944,	'3 x 1',	1803211869,	'2018-05-08 12:12:41',	'2018-05-08 12:12:41',	4,	4),
(2,	1,	464945,	'3 x 1',	1803211869,	'2018-05-08 12:12:41',	'2018-05-08 12:12:41',	4,	4),
(3,	1,	464946,	'3 x 1',	1803211869,	'2018-05-08 12:12:41',	'2018-05-08 12:12:41',	4,	4),
(4,	1,	464947,	'3 x 1',	1803211869,	'2018-05-08 12:12:41',	'2018-05-08 12:12:41',	4,	4),
(5,	1,	464948,	'3 x 1',	1803211869,	'2018-05-08 12:12:41',	'2018-05-08 12:12:41',	4,	4),
(6,	1,	464949,	'3 x 1',	1803211869,	'2018-05-08 12:12:41',	'2018-05-08 12:12:41',	4,	4),
(7,	1,	464950,	'3 x 1',	1803211869,	'2018-05-08 12:12:41',	'2018-05-08 12:12:41',	4,	4),
(8,	1,	464951,	'3 x 1',	1803211869,	'2018-05-08 12:12:41',	'2018-05-08 12:12:41',	4,	4),
(9,	1,	464952,	'3 x 2',	1803211869,	'2018-05-08 12:12:41',	'2018-05-08 12:12:41',	4,	4);

DROP TABLE IF EXISTS `transaction_medicine`;
CREATE TABLE `transaction_medicine` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) DEFAULT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `registered_id` int(11) DEFAULT NULL,
  `medical_record_number` char(14) DEFAULT NULL,
  `care_type` smallint(6) DEFAULT NULL COMMENT '0 = Rawat Jalan, 1 = Rawat Inap',
  `medicine_date` date DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `is_post_to_db` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 = belum; 1 = sudah',
  `post_to_db_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `transaction_medicine` (`id`, `doctor_id`, `unit_id`, `registered_id`, `medical_record_number`, `care_type`, `medicine_date`, `receipt_number`, `is_post_to_db`, `post_to_db_at`, `deleted_at`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(3,	154,	82,	139172,	'0139172',	0,	'2009-01-01',	'2445',	1,	'2018-05-17 09:12:11',	NULL,	'2018-05-17 07:12:44',	'2018-05-17 09:12:11',	1,	1),
(4,	253,	25,	139166,	'0139166',	0,	'2009-01-01',	'180513992',	1,	'2018-05-17 09:11:08',	NULL,	'2018-05-17 08:42:49',	'2018-05-17 09:11:08',	1,	1);

DROP TABLE IF EXISTS `transaction_medicine_detail`;
CREATE TABLE `transaction_medicine_detail` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `mm_transaction_add_medicine_id` bigint(20) DEFAULT NULL,
  `transaction_medicine_id` bigint(20) NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT '0',
  `how_to_use` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `receipt_number` int(11) DEFAULT NULL,
  `trx_number` char(15) DEFAULT NULL,
  `drink` smallint(6) DEFAULT NULL,
  `data` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_medicine_id` (`transaction_medicine_id`),
  CONSTRAINT `transaction_medicine_detail_ibfk_2` FOREIGN KEY (`transaction_medicine_id`) REFERENCES `transaction_medicine` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `transaction_medicine_detail` (`id`, `mm_transaction_add_medicine_id`, `transaction_medicine_id`, `unit_id`, `medicine_id`, `name`, `quantity`, `how_to_use`, `price`, `receipt_number`, `trx_number`, `drink`, `data`, `created_at`, `updated_at`) VALUES
(5,	464961,	3,	NULL,	5,	'00000006 - Diazepam   5  mg',	5,	'1 x 1',	NULL,	NULL,	NULL,	NULL,	NULL,	'2018-05-17 07:12:44',	'2018-05-17 08:34:03'),
(6,	464962,	4,	NULL,	62,	'00000068 - Pyrantel  125  mg',	3,	'1 x 1',	NULL,	NULL,	NULL,	NULL,	NULL,	'2018-05-17 08:42:49',	'2018-05-17 08:42:49'),
(7,	464963,	4,	NULL,	800,	'00000928 - Amitriptyline 25 mg',	3,	'1 x 1',	NULL,	NULL,	NULL,	NULL,	NULL,	'2018-05-17 08:42:49',	'2018-05-17 08:42:49'),
(8,	464964,	4,	NULL,	5,	'00000006 - Diazepam   5  mg',	6,	'2 x 1',	NULL,	NULL,	NULL,	NULL,	NULL,	'2018-05-17 08:42:49',	'2018-05-17 08:42:49'),
(9,	464965,	4,	NULL,	51,	'00000056 - Myconazol  Cream  10  gr',	4,	'2 x 1',	NULL,	NULL,	NULL,	NULL,	NULL,	'2018-05-17 09:10:01',	'2018-05-17 09:11:08'),
(10,	464966,	4,	NULL,	51,	'00000056 - Myconazol  Cream  10  gr',	4,	'2 x 1',	NULL,	NULL,	NULL,	NULL,	NULL,	'2018-05-17 09:10:49',	'2018-05-17 09:11:08'),
(11,	464967,	4,	NULL,	51,	'00000056 - Myconazol  Cream  10  gr',	4,	'2 x 1',	NULL,	NULL,	NULL,	NULL,	NULL,	'2018-05-17 09:11:08',	'2018-05-17 09:11:08'),
(12,	464968,	3,	NULL,	40,	'00000043 - Gliceril Guaiacolas 100 mg',	5,	'2 x 1',	NULL,	NULL,	NULL,	NULL,	NULL,	'2018-05-17 09:12:11',	'2018-05-17 09:12:11');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dcl_user_id` int(10) unsigned DEFAULT NULL,
  `nip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` smallint(6) DEFAULT NULL,
  `apoteker_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apoteker_sik` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id`, `dcl_user_id`, `nip`, `name`, `username`, `password`, `remember_token`, `role`, `apoteker_name`, `apoteker_sik`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1,	NULL,	NULL,	'Administrator',	'superadmin',	'$2y$10$Crl/mrqZRG7lR3bCtLZ4bODRfwHgwsAAiywfu1QkNACR.mQu31Xna',	'fUAc2zi8BNxL9bHcbNp1W2VrdOWf8PGI90RfXjtrP04QUCV0JbgBuoQISfm3',	1,	NULL,	NULL,	NULL,	'2018-04-04 02:16:48',	'2018-04-04 02:16:48'),
(2,	82,	'HR-587',	'SEKAR AYU TANJUNG SARI, DR',	'sekar',	'$2y$10$Fxkr2YXItk2CHkPlMMobk.epDfTlHgzhr7qC8LO8MrRQ4c9rIJv8K',	'O7O2edq3eXpkFGL223OyRmwIv6WrposB95tGp3T0nwRA3IZVkZhTFG5DX7Qs',	10,	NULL,	NULL,	NULL,	'2018-04-17 08:10:16',	'2018-04-17 12:03:47'),
(4,	NULL,	NULL,	'Dra. Lea P. Sjamsudin',	'apoteker',	'$2y$10$gNHqioCGekTOAzHJCWbUAuvBd.jmJpKeP8BwZb9seyoN0gRLHhw12',	'sMXYQgeRcGaXoi6tIYln2ut8zNv2Ne7m6yRYncaToH2DiKoXprzHhy8D9bHA',	5,	'Dra. Lea P. Sjamsudin',	'165/SIK/JB/1993',	NULL,	'2018-04-19 07:38:37',	'2018-04-19 07:38:37'),
(5,	NULL,	NULL,	'Kimia Parma Apotek',	'apoteker2',	'$2y$10$gkXxONPHD/7hXux7Qr9LM.j13SDPy08Wku8Ht3IueOo5xHIcZSF5O',	'PnEfGSAR1QeOuqIREiIu4sqouE3Lu0NcgzwKP1nK7mudO9cr6RnS473c4ypJ',	5,	'Kimia Parma Apotek',	'12309/123891',	NULL,	'2018-04-19 07:55:08',	'2018-04-19 07:55:08');

-- 2018-05-17 09:43:00
