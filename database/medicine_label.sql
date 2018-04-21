-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Apr 20, 2018 at 07:26 PM
-- Server version: 5.7.21-0ubuntu0.16.04.1
-- PHP Version: 7.0.28-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `medicine_label`
--

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE `setting` (
  `id` int(11) NOT NULL,
  `address` varchar(600) NOT NULL,
  `apoteker` varchar(255) NOT NULL,
  `sik` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`id`, `address`, `apoteker`, `sik`, `created_at`, `updated_at`) VALUES
(1, 'Jl Dr. Semeru No. 144 - Bogor', 'Dra. Lea P. Sjamsudin', '165/SIK/JB/1993', NULL, '2018-04-11 00:49:31');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_medicine`
--

CREATE TABLE `transaction_medicine` (
  `id` bigint(20) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `registered_id` int(11) DEFAULT NULL,
  `medical_record_number` char(14) DEFAULT NULL,
  `care_type` smallint(6) DEFAULT NULL COMMENT '0 = Rawat Jalan, 1 = Rawat Inap',
  `medicine_date` date DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction_medicine`
--

INSERT INTO `transaction_medicine` (`id`, `doctor_id`, `registered_id`, `medical_record_number`, `care_type`, `medicine_date`, `receipt_number`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 116, 231, '0035976', 0, '2018-04-19', '3213', '2018-04-19 13:01:23', '2018-04-19 13:01:23', 5, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_medicine_detail`
--

CREATE TABLE `transaction_medicine_detail` (
  `id` bigint(20) NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaction_medicine_detail`
--

INSERT INTO `transaction_medicine_detail` (`id`, `transaction_medicine_id`, `unit_id`, `medicine_id`, `name`, `quantity`, `how_to_use`, `price`, `receipt_number`, `trx_number`, `drink`, `data`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 19, '00000020 - Antasida DOEN Tablet Strip 10x10', 3, '3 x 1', NULL, NULL, NULL, NULL, NULL, '2018-04-19 13:01:23', '2018-04-19 13:01:23'),
(2, 1, NULL, 4262, '00004616 - Meptin Swinghaler', 5, '1 x 1', NULL, NULL, NULL, NULL, NULL, '2018-04-19 13:01:23', '2018-04-19 13:01:23');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `dcl_user_id` int(10) UNSIGNED DEFAULT NULL,
  `nip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` smallint(6) DEFAULT NULL,
  `apoteker_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `apoteker_sik` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `dcl_user_id`, `nip`, `name`, `username`, `password`, `remember_token`, `role`, `apoteker_name`, `apoteker_sik`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'Administrator', 'superadmin', '$2y$10$Crl/mrqZRG7lR3bCtLZ4bODRfwHgwsAAiywfu1QkNACR.mQu31Xna', 'QaiKwMeFCPwMhya3H4N1rN6Pj2mNUXC06J4S0dgOB0Phil3QwJQbFfV1e5Un', 1, NULL, NULL, '2018-04-04 02:16:48', '2018-04-04 02:16:48'),
(2, 82, 'HR-587', 'SEKAR AYU TANJUNG SARI, DR', 'sekar', '$2y$10$Fxkr2YXItk2CHkPlMMobk.epDfTlHgzhr7qC8LO8MrRQ4c9rIJv8K', '2kVBlItwilNAnc7WyAubsqKNPn8YHuFiubcKcPMHR6H8CaOHaX1ZhCpRwyRj', 10, NULL, NULL, '2018-04-17 08:10:16', '2018-04-17 12:03:47'),
(4, NULL, NULL, 'Dra. Lea P. Sjamsudin', 'apoteker', '$2y$10$gNHqioCGekTOAzHJCWbUAuvBd.jmJpKeP8BwZb9seyoN0gRLHhw12', 'kq4CzUlS1kteEw4Ue8fw8sBm69txYhWp86Gq3fWQ0cGDKfMFz0sp6ak2KqsA', 5, 'Dra. Lea P. Sjamsudin', '165/SIK/JB/1993', '2018-04-19 07:38:37', '2018-04-19 07:38:37'),
(5, NULL, NULL, 'Kimia Parma Apotek', 'apoteker2', '$2y$10$gkXxONPHD/7hXux7Qr9LM.j13SDPy08Wku8Ht3IueOo5xHIcZSF5O', 'PnEfGSAR1QeOuqIREiIu4sqouE3Lu0NcgzwKP1nK7mudO9cr6RnS473c4ypJ', 5, 'Kimia Parma Apotek', '12309/123891', '2018-04-19 07:55:08', '2018-04-19 07:55:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `setting`
--
ALTER TABLE `setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_medicine`
--
ALTER TABLE `transaction_medicine`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_medicine_detail`
--
ALTER TABLE `transaction_medicine_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_medicine_id` (`transaction_medicine_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `transaction_medicine`
--
ALTER TABLE `transaction_medicine`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `transaction_medicine_detail`
--
ALTER TABLE `transaction_medicine_detail`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaction_medicine_detail`
--
ALTER TABLE `transaction_medicine_detail`
  ADD CONSTRAINT `transaction_medicine_detail_ibfk_2` FOREIGN KEY (`transaction_medicine_id`) REFERENCES `transaction_medicine` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
