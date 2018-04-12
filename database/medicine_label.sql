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

DROP TABLE IF EXISTS `transaction_medicine`;
CREATE TABLE `transaction_medicine` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) DEFAULT NULL,
  `registered_id` int(11) DEFAULT NULL,
  `medical_record_number` char(14) DEFAULT NULL,
  `care_type` smallint(6) DEFAULT NULL COMMENT '0 = Rawat Jalan, 1 = Rawat Inap',
  `medicine_date` date DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `transaction_medicine` (`id`, `doctor_id`, `registered_id`, `medical_record_number`, `care_type`, `medicine_date`, `receipt_number`, `created_at`, `updated_at`) VALUES
(6,	127,	1234,	'0034044',	0,	'2018-04-11',	'12353',	'2018-04-11 02:42:18',	'2018-04-11 02:42:18'),
(7,	39,	1234,	'0002271',	0,	'2018-04-11',	'3211',	'2018-04-11 04:16:02',	'2018-04-11 04:16:02'),
(8,	323,	1234,	'0008121',	0,	'2018-04-11',	'5999',	'2018-04-11 04:44:39',	'2018-04-11 04:44:39'),
(9,	154,	1234,	'0000021',	0,	'2018-04-12',	'2311',	'2018-04-11 21:29:14',	'2018-04-11 21:29:14');

DROP TABLE IF EXISTS `transaction_medicine_detail`;
CREATE TABLE `transaction_medicine_detail` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
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

INSERT INTO `transaction_medicine_detail` (`id`, `transaction_medicine_id`, `unit_id`, `medicine_id`, `name`, `quantity`, `how_to_use`, `price`, `receipt_number`, `trx_number`, `drink`, `data`, `created_at`, `updated_at`) VALUES
(25,	6,	NULL,	315,	'00000394 - Ciptadent Pasta Gigi',	2,	'3 x 1',	NULL,	NULL,	NULL,	0,	NULL,	'2018-04-11 02:42:18',	'2018-04-11 04:15:05'),
(26,	7,	NULL,	93,	'00000103 - Trental  400 mg',	34,	'3 x 1',	NULL,	NULL,	NULL,	2,	NULL,	'2018-04-11 04:16:02',	'2018-04-11 04:16:02'),
(27,	7,	NULL,	801,	'00000929 - Amoxsan 500 mg',	2,	'1 x 1',	NULL,	NULL,	NULL,	0,	NULL,	'2018-04-11 04:16:02',	'2018-04-11 04:16:02'),
(28,	8,	NULL,	93,	'00000103 - Trental  400 mg',	25,	'3 x 1/2',	NULL,	NULL,	NULL,	2,	NULL,	'2018-04-11 04:44:39',	'2018-04-11 04:44:39'),
(29,	8,	NULL,	801,	'00000929 - Amoxsan 500 mg',	20,	'1 x 1',	NULL,	NULL,	NULL,	1,	NULL,	'2018-04-11 04:44:39',	'2018-04-11 04:44:39'),
(30,	8,	NULL,	58,	'00000064 - Paracetamol 500  mg Loz',	15,	'3 x 2',	NULL,	NULL,	NULL,	0,	NULL,	'2018-04-11 04:44:39',	'2018-04-11 04:44:39'),
(31,	9,	NULL,	93,	'00000103 - Trental  400 mg',	2,	'3 x 1',	NULL,	NULL,	NULL,	NULL,	NULL,	'2018-04-11 21:29:15',	'2018-04-11 21:29:15');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` smallint(6) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id`, `name`, `email`, `password`, `remember_token`, `role`, `created_at`, `updated_at`) VALUES
(1,	'Administrator',	'admin.label@rs.com',	'$2y$10$Crl/mrqZRG7lR3bCtLZ4bODRfwHgwsAAiywfu1QkNACR.mQu31Xna',	'YspUzD5XFNrkIN1M9sazlt4zpxHTCY5OpmUqQPiPJ73t3vetwExAiLZBJnLZ',	NULL,	'2018-04-04 02:16:48',	'2018-04-04 02:16:48');

-- 2018-04-12 04:30:02
