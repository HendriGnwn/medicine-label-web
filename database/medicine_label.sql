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


DROP TABLE IF EXISTS `transaction_medicine`;
CREATE TABLE `transaction_medicine` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) DEFAULT NULL,
  `registered_id` int(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `medical_record_number` char(14) DEFAULT NULL,
  `care_type` smallint(6) NOT NULL COMMENT '0 = Rawat Jalan, 1 = Rawat Inap',
  `payment_detail_status` smallint(6) NOT NULL COMMENT 'Status Detail Pembayaran: 0 = BERJALAN, 1 = SELESAI, 2 = BATAL, 3 = PANGGILAN PERTAMA, 4 = PROSES, 5 = RETUR',
  `payment_detail_date` timestamp NULL DEFAULT NULL,
  `approval_status` smallint(6) NOT NULL COMMENT '1 = Simpan, 2 = Approve, 3 = Batal',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `transaction_medicine` (`id`, `doctor_id`, `registered_id`, `payment_id`, `medical_record_number`, `care_type`, `payment_detail_status`, `payment_detail_date`, `approval_status`, `created_at`, `updated_at`) VALUES
(1,	1,	1,	1,	'1',	1,	1,	'2018-04-04 11:46:40',	1,	NULL,	NULL);

DROP TABLE IF EXISTS `transaction_medicine_detail`;
CREATE TABLE `transaction_medicine_detail` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `transaction_medicine_id` bigint(20) NOT NULL,
  `unit_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT '0',
  `price` decimal(12,2) DEFAULT NULL,
  `receipt_number` int(11) DEFAULT NULL,
  `trx_number` char(15) DEFAULT NULL,
  `data` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_medicine_id` (`transaction_medicine_id`),
  CONSTRAINT `transaction_medicine_detail_ibfk_2` FOREIGN KEY (`transaction_medicine_id`) REFERENCES `transaction_medicine` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id`, `name`, `email`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1,	'Administrator',	'admin.label@rs.com',	'$2y$10$Crl/mrqZRG7lR3bCtLZ4bODRfwHgwsAAiywfu1QkNACR.mQu31Xna',	'kqpLQt6NwbtCzojcqiwhVxa8wGw22Jl9jNeIJy3YjPBCaAtjAT1Ll6AtiLH6',	'2018-04-04 02:16:48',	'2018-04-04 02:16:48');

-- 2018-04-04 13:18:31
