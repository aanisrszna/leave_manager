-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 07, 2025 at 10:00 AM
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
-- Database: `aci_leave`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '2020-11-03 05:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `employee_leave`
--

CREATE TABLE `employee_leave` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `available_day` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_leave`
--

INSERT INTO `employee_leave` (`id`, `emp_id`, `leave_type_id`, `available_day`) VALUES
(117, 29, 2, 7.50),
(118, 29, 4, 10.00),
(119, 29, 5, 60.00),
(120, 29, 6, 98.00),
(121, 52, 2, 8.50),
(122, 52, 4, 12.00),
(123, 52, 5, 60.00),
(124, 52, 7, 7.00),
(125, 50, 1, 8.50),
(126, 50, 4, 11.00),
(127, 50, 5, 60.00),
(128, 50, 7, 7.00),
(129, 52, 9, 365.00),
(130, 52, 33, 2.00),
(131, 50, 9, 365.00),
(132, 50, 33, 0.00),
(133, 51, 2, 11.00),
(134, 51, 4, 12.00),
(135, 51, 5, 60.00),
(136, 51, 7, 7.00),
(137, 51, 8, 365.00),
(138, 51, 9, 365.00),
(139, 53, 3, 4.00),
(140, 53, 4, 14.00),
(141, 53, 5, 57.00),
(142, 53, 6, 98.00),
(143, 53, 8, 365.00),
(144, 53, 9, 365.00),
(145, 29, 8, 365.00),
(146, 29, 9, 365.00),
(148, 50, 34, 4.00),
(149, 51, 33, 0.00),
(150, 51, 35, 2.00),
(151, 29, 37, 3.00),
(152, 52, 36, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `tblattendance`
--

CREATE TABLE `tblattendance` (
  `attendance_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `total_hours` float DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblattendance`
--

INSERT INTO `tblattendance` (`attendance_id`, `staff_id`, `date`, `time_in`, `time_out`, `total_hours`, `remark`) VALUES
(40, 33, '2025-02-10', '09:49:10', '09:49:12', NULL, ''),
(41, 33, '2025-02-10', '09:55:16', '09:55:18', NULL, ''),
(42, 33, '2025-02-10', '16:58:57', '16:59:02', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `tbldepartments`
--

CREATE TABLE `tbldepartments` (
  `id` int(11) NOT NULL,
  `DepartmentName` varchar(150) DEFAULT NULL,
  `DepartmentShortName` varchar(100) NOT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbldepartments`
--

INSERT INTO `tbldepartments` (`id`, `DepartmentName`, `DepartmentShortName`, `CreationDate`) VALUES
(3, 'Engineering', 'eg', '2021-05-21 08:27:45'),
(4, 'Procurement', 'PC', '2023-09-13 06:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `tblemployees`
--

CREATE TABLE `tblemployees` (
  `emp_id` int(11) NOT NULL,
  `FirstName` varchar(150) NOT NULL,
  `LastName` varchar(150) NOT NULL,
  `Staff_ID` varchar(50) NOT NULL,
  `Position_Staff` varchar(100) NOT NULL,
  `EmailId` varchar(200) NOT NULL,
  `Password` varchar(180) NOT NULL,
  `Gender` varchar(100) NOT NULL,
  `Dob` varchar(100) NOT NULL,
  `Department` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `Phonenumber` char(11) NOT NULL,
  `Status` varchar(10) NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(30) NOT NULL,
  `location` varchar(200) NOT NULL,
  `signature` varchar(200) NOT NULL,
  `date_joined` varchar(100) DEFAULT NULL,
  `Emergency_Contact` varchar(15) NOT NULL,
  `Car_Plate` varchar(20) DEFAULT NULL,
  `Reporting_To` varchar(50) DEFAULT NULL,
  `IC_Number` varchar(20) NOT NULL,
  `Service_Year` int(11) NOT NULL,
  `Emergency_Name` varchar(255) NOT NULL,
  `Emergency_Relation` varchar(100) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblemployees`
--

INSERT INTO `tblemployees` (`emp_id`, `FirstName`, `LastName`, `Staff_ID`, `Position_Staff`, `EmailId`, `Password`, `Gender`, `Dob`, `Department`, `Address`, `Phonenumber`, `Status`, `RegDate`, `role`, `location`, `signature`, `date_joined`, `Emergency_Contact`, `Car_Plate`, `Reporting_To`, `IC_Number`, `Service_Year`, `Emergency_Name`, `Emergency_Relation`, `reset_token`, `reset_expiry`) VALUES
(29, 'Nur Edrinna Binti Mohd Rezal', 'Eryn', 'PR023', 'Admin & Procurement Executive', 'nur.edrinna@riverraven.com.my', '$2y$10$QSPz8Hfw6MifsP6ngT/YDeBDWwiZ0GsGYKzUFLtB0UwH7jSgNbRUm', 'female', '1994-04-01', 'eg', 'Lot 1080 batu 8 1/4 Gombak 53100 KL', '0133954765', 'Offline', '2023-09-28 00:26:23', 'Manager', 'WhatsApp_Image_2025-02-06_at_15.24.44_32ce8558-removebg-preview (1).png', 'hod_ur_0133954765_29.png', '2025-02-11', '0172469098', 'VGW728', 'Teh Tze Wey', '940401-14-5498', 1, 'Muhammad Faiq', 'Husband', NULL, NULL),
(49, 'Teh Tze Wey ', 'TzeWey', 'RR01', 'Director', 'tzewey@riverraven.com.my', '$2y$10$3ySzd1QRx/z0CCyJhJALoOoqR0OqhBkVSqlOIVs6OTvwcZIsPvMLO', 'Male', '1984-11-11', 'eg', '-', '0123383788', 'Offline', '2025-03-14 02:19:57', 'Director', 'NO-IMAGE-AVAILABLE.jpg', 'reg_eh_0123383788_49.png', '2017-07-01', '-', 'RR887', '-', '841111-10-5515', 8, '-', '-', NULL, NULL),
(50, 'Cheong Kok Jeng', 'KJ', 'E002', 'Technical Manager', 'kokjeng@riverraven.com.my', '$2y$10$v9ac9CMQp9jhbwH3.zPWUOFXbMHCrr2sVJGUnjj.tlkJXfOoK8YRO', 'Male', '1983-06-04', 'eg', '-', '0139239535', 'Offline', '2025-03-14 02:24:27', 'Manager', 'NO-IMAGE-AVAILABLE.jpg', 'hod_he_0139239535_50.png', '2017-07-01', '0197916943', 'BSC6300', 'Teh Tze Wey', '830604-14-5589', 8, 'Sharon Ng Pei Woon', 'Spouse', NULL, NULL),
(51, 'Chan Kok Fei', 'Murphy', 'E015', 'Technical Engineer', 'murphy.chan@riverraven.com.my', '$2y$10$W5ssxaKaDk46CUXhQzRBVOhBNA.qQ3KVv6vJ36Rxwhp/e0E6b7z8a', 'Male', '1981-04-28', 'eg', 'Lot 11, Jalan 10/26 Taman Sri Rampai, 53300 Kuala Lumpur', '0122934148', 'Online', '2025-03-14 02:28:01', 'Staff', 'NO-IMAGE-AVAILABLE.jpg', '', '2021-03-08', '0163355875', 'WTU8187', 'Cheong Kok Jeng', '810428-14-6735', 4, 'Ashley Chan', 'Wife', NULL, NULL),
(52, 'Muhamad Fazreen Bin Badri', 'Faz', 'E021', 'Technical Engineer', 'fazreen@riverraven.com.my', '$2y$10$hqvG4HdPaoIpPyc8N35q9u7GoLQxoaO14k0fX8yvpppV/BzZmJ7PS', 'Male', '1995-08-12', 'eg', 'No 25 Jln Mahagoni 2A/4 bandar baru batang kali', '01115525735', 'Offline', '2025-03-14 02:31:04', 'Staff', 'NO-IMAGE-AVAILABLE.jpg', '', '2023-05-02', '0132761649', 'VFG6687', 'Cheong Kok Jeng', '950812-01-5937', 2, 'Muhd Faez Iqbal', 'Brother', NULL, NULL),
(53, 'Anis Ruszanna Binti Rosli', 'Anis', 'E026', 'Protege Software Engineer', 'anis@riverraven.com.my', '$2y$10$BspHsGovqrJd9upqUdlpD.VBbQ.9hKVvukprzjtPSbAzTHYR7tDJ2', 'Female', '2001-08-08', 'eg', 'No 202, Blok E3, Wangsa Maju Seksyen 1, Setapak, Kuala Lumpur', '0174757964', 'Offline', '2025-03-14 02:34:12', 'Staff', 'NO-IMAGE-AVAILABLE.jpg', '', '2024-10-01', '2131', 'www', 'Cheong Kok Jeng', '010808-07-0642', 1, 'Surinah Hashim', 'Mother', '6b6f7e3168914257483930d631c1fb6a37bf3168ccd3ec8510e3937460911dc0bdf61a2794ec6c325103853930b2c1897d88', '2025-03-27 10:23:14'),
(56, 'Admin System', '', 'PR023', 'Admin', 'system@riverraven.com.my', '$2y$10$sIY7Rm3IqdosDkSXkQ7VHO8Khp1J9EtS0/UEiA3/YQNseXHRfjUea', '', '0000-00-00', '', 'B-1-9, Plaza Damas 3\r\nNo. 63, Jalan Sri Hartamas 1,\r\nSri Hartamas, 50480 Kuala Lumpur.', '035888 8076', 'Offline', '0000-00-00 00:00:00', 'Admin', '', '', '', '', '', '', '', 0, '', '', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblleave`
--

CREATE TABLE `tblleave` (
  `id` int(11) NOT NULL,
  `LeaveType` varchar(110) NOT NULL,
  `RequestedDays` decimal(5,2) NOT NULL,
  `DaysOutstand` decimal(5,2) NOT NULL,
  `IsHalfDay` tinyint(1) DEFAULT 0,
  `HalfDayType` tinyint(1) DEFAULT NULL,
  `FromDate` varchar(120) NOT NULL,
  `ToDate` varbinary(120) DEFAULT NULL,
  `Sign` varchar(120) DEFAULT NULL,
  `PostingDate` date DEFAULT NULL,
  `HodRemarks` int(11) NOT NULL DEFAULT 0,
  `HodSign` varchar(200) NOT NULL,
  `HodDate` varchar(120) NOT NULL,
  `RegRemarks` int(1) NOT NULL DEFAULT 0,
  `RegSign` varchar(200) NOT NULL,
  `RegDate` varchar(120) NOT NULL,
  `IsRead` int(1) NOT NULL,
  `empid` int(11) DEFAULT NULL,
  `proof` varchar(255) DEFAULT NULL,
  `notification_shown` tinyint(1) DEFAULT 0,
  `reason` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblleave`
--

INSERT INTO `tblleave` (`id`, `LeaveType`, `RequestedDays`, `DaysOutstand`, `IsHalfDay`, `HalfDayType`, `FromDate`, `ToDate`, `Sign`, `PostingDate`, `HodRemarks`, `HodSign`, `HodDate`, `RegRemarks`, `RegSign`, `RegDate`, `IsRead`, `empid`, `proof`, `notification_shown`, `reason`) VALUES
(1, 'Annual Leave - Protege', 1.00, 8.00, 0, NULL, '2025-01-31', 0x323032352d30312d3331, NULL, '2025-01-24', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-24', 1, 53, NULL, 0, 'CNY'),
(2, 'Annual Leave - Protege', 0.50, 4.00, 1, NULL, '2025-01-28', 0x323032352d30312d3238, NULL, '2025-01-21', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 53, NULL, 0, 'CNY'),
(3, 'Annual Leave - Protege', 0.50, 4.50, 1, NULL, '2025-02-20', 0x323032352d30322d3230, NULL, '2025-02-13', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 53, NULL, 0, 'Halfday PM'),
(4, 'Annual Leave - Protege', 3.00, 5.00, 0, NULL, '2025-04-02', 0x323032352d30342d3034, NULL, '2025-03-26', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 53, NULL, 1, 'Cuti Hari Raya'),
(5, 'Annual Leave - Staff', 1.00, 8.50, 0, NULL, '2025-01-27', 0x323032352d30312d3237, NULL, '2025-01-20', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 52, NULL, 0, ''),
(6, 'Annual Leave - Staff', 0.50, 9.50, 1, NULL, '2025-01-28', 0x323032352d30312d3238, NULL, '2025-01-21', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 52, NULL, 0, ''),
(7, 'Annual Leave - Staff', 1.00, 10.00, 0, NULL, '2025-02-03', 0x323032352d30322d3033, NULL, '2025-01-27', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 52, NULL, 0, ''),
(8, 'Annual Leave - Staff', 1.00, 11.00, 0, NULL, '2025-02-10', 0x323032352d30322d3130, NULL, '2025-02-03', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 52, NULL, 0, ''),
(9, 'Annual Leave - Staff', 1.50, 9.00, 0, NULL, '2025-01-27', 0x323032352d30312d3238, NULL, '2025-01-22', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 51, NULL, 0, 'al - CNY'),
(10, 'Annual Leave - Staff', 1.00, 10.50, 0, NULL, '2025-02-03', 0x323032352d30322d3033, NULL, '2025-01-29', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 51, NULL, 0, 'al - CNY'),
(11, 'Annual Leave - Staff', 0.50, 11.50, 1, 1, '2025-03-03', 0x323032352d30332d3033, NULL, '2025-02-26', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 51, NULL, 0, 'al - AM _ Check up baby'),
(12, 'Replacement Leave ', 2.00, 12.00, 0, NULL, '2025-03-27', 0x323032352d30332d3238, NULL, '2025-03-20', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 51, NULL, 0, 'AL- Pindah rumah'),
(13, 'Medical/Sick Leave', 2.00, 12.00, 0, NULL, '2025-01-23', 0x323032352d30312d3234, NULL, '2025-01-18', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 52, NULL, 1, 'High fever'),
(14, 'Replacement Leave ', 0.50, 0.00, 1, NULL, '2025-03-06', 0x323032352d30332d3036, NULL, '2025-04-01', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 50, NULL, 0, 'EL - Take care daughter'),
(15, 'Replacement Leave ', 0.50, 0.50, 1, NULL, '2025-04-30', 0x323032352d30342d3330, NULL, '2025-04-25', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 50, NULL, 0, 'AL - Holiday'),
(16, 'Annual Leave- Manager', 4.00, 6.50, 0, NULL, '2025-06-03', 0x323032352d30362d3036, NULL, '2025-05-28', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 50, NULL, 0, 'AL - Holiday'),
(17, 'Annual Leave- Manager', 0.50, 14.50, 1, NULL, '2025-01-02', 0x323032352d30312d3032, NULL, '2024-12-28', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 50, NULL, 0, 'AL - Personal matter'),
(18, 'Annual Leave- Manager', 0.50, 14.00, 1, NULL, '2025-01-10', 0x323032352d30312d3130, NULL, '2025-01-05', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 50, NULL, 0, 'AL - Personal matter'),
(19, 'Annual Leave- Manager', 1.50, 12.50, 0, NULL, '2025-01-27', 0x323032352d30312d3238, NULL, '2025-01-20', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 50, NULL, 0, 'CNY'),
(20, 'Replacement Leave ', 1.00, -1.00, 0, NULL, '2025-02-03', 0x323032352d30322d3033, NULL, '2025-01-29', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 50, NULL, 0, 'CNY'),
(21, 'Annual Leave- Manager', 1.00, 1.00, 0, NULL, '2025-02-10', 0x323032352d30322d3130, NULL, '2025-02-05', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 50, NULL, 0, 'AL - Personal matter'),
(22, 'Medical/Sick Leave', 2.00, 12.00, 0, NULL, '2025-01-09', 0x323032352d30312d3130, NULL, '2025-01-08', 1, 'hod_he_0139239535_50.png', '2025-03-24 13:11:55', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 51, NULL, 1, 'Medical Leave'),
(23, 'Medical/Sick Leave', 3.00, 11.00, 0, NULL, '2025-02-26', 0x323032352d30322d3238, NULL, '2025-02-25', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 50, '', 1, 'Medical Leave'),
(24, 'Medical/Sick Leave', 1.00, 10.00, 0, NULL, '2025-02-28', 0x323032352d30322d3238, NULL, '2025-02-27', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 29, NULL, 0, 'Cabut gigi'),
(25, 'Medical/Sick Leave', 1.00, 11.00, 0, NULL, '2025-03-05', 0x323032352d30332d3035, NULL, '2025-03-04', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 29, NULL, 0, 'Eyes infection'),
(281, 'Annual Leave - Staff', 1.00, 7.50, 0, NULL, '2025-01-17', 0x323032352d30312d3137, NULL, '2025-01-16', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 29, NULL, 0, 'Pindah rumah'),
(282, 'Annual Leave - Staff', 1.50, 8.50, 0, NULL, '2025-01-28', 0x323032352d30312d3331, NULL, '2025-01-27', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 29, NULL, 0, 'CNY'),
(283, 'Annual Leave - Staff', 3.00, 10.00, 0, NULL, '2025-04-02', 0x323032352d30342d3034, NULL, '2025-04-01', 3, '', '', 1, 'reg_eh_0123383788_49.png', '2025-03-25', 1, 29, NULL, 1, 'Cuti Hari Raya'),
(288, 'Annual Leave - Protege', 1.00, 3.00, 0, NULL, '2025-04-10', 0x323032352d30342d3130, NULL, '2025-03-27', 0, '', '', 0, '', '', 0, 53, NULL, 0, 'vacation'),
(289, 'Annual Leave - Protege', 1.00, 3.00, 0, NULL, '2025-04-18', 0x323032352d30342d3138, NULL, '2025-03-27', 1, 'hod_he_0139239535_50.png', '2025-03-27 8:16:43', 0, '', '', 1, 53, NULL, 0, 'sick'),
(290, 'Annual Leave - Protege', 1.00, 3.00, 0, NULL, '2025-04-15', 0x323032352d30342d3135, NULL, '2025-03-27', 1, 'hod_he_0139239535_50.png', '2025-03-27 8:16:15', 0, '', '', 1, 53, NULL, 0, 'sick');

-- --------------------------------------------------------

--
-- Table structure for table `tblleavetype`
--

CREATE TABLE `tblleavetype` (
  `id` int(11) NOT NULL,
  `LeaveType` varchar(200) DEFAULT NULL,
  `Description` mediumtext DEFAULT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_day` decimal(5,2) DEFAULT NULL,
  `NeedProof` enum('Yes','No') DEFAULT 'No'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblleavetype`
--

INSERT INTO `tblleavetype` (`id`, `LeaveType`, `Description`, `CreationDate`, `assigned_day`, `NeedProof`) VALUES
(1, 'Annual Leave- Manager', 'Benefit that allows employees to take paid time off from work each year - for Manager only', '2025-01-07 02:11:55', 16.00, 'No'),
(2, 'Annual Leave - Staff', 'Benefit that allows employees to take paid time off from work each year - for Permanent Staff only', '2025-01-13 09:26:01', 14.00, 'No'),
(3, 'Annual Leave - Protege', 'Benefit that allows employees to take paid time off from work each year - Protege only	', '2025-01-20 01:26:10', 9.00, 'No'),
(4, 'Medical/Sick Leave', 'Leave of absence granted because of illness required medical certificate.	', '2025-01-20 01:27:35', 14.00, 'Yes'),
(5, 'Hospitalization Leave', 'Leave that allows employees to focus on their recovery and medical treatment when they are hospitalized.', '2025-01-13 09:26:29', 60.00, 'Yes'),
(6, 'Maternity Leave', 'Period of absence from work granted to a mother before and after the birth of her child.', '2025-01-13 09:26:39', 98.00, 'Yes'),
(7, 'Paternity Leave', 'Period of absence from work granted to a father after or shortly before the birth of his child.', '2025-01-13 09:26:50', 7.00, 'No'),
(8, 'Unpaid Leave', 'Period of time when an employee is away from work without receiving their regular salary or wages.', '2025-01-13 09:27:20', 365.00, 'No'),
(9, 'Emergency Leave', 'Time off from work that an employee can take to deal with urgent, unexpected, or personal situations.	', '2025-01-20 01:28:07', 365.00, 'No'),
(33, 'Replacement Leave ', 'replacement leave', '2025-03-12 04:34:33', 2.00, 'Yes'),
(34, 'Carry Forward Leave - KJ', 'Carry forward Leave 2024', '2025-03-25 08:03:03', 4.00, 'No'),
(35, 'Carry Forward Leave - Murphy', 'Carry Forward Leave 2024', '2025-03-25 08:11:28', 2.00, 'No'),
(36, 'Carry Forward Leave - Faz', 'Carry Forward Leave 2024', '2025-03-26 01:09:21', 0.00, 'No'),
(37, 'Carry Forward Leave - Eryn', 'Carry Forward Leave 2024', '2025-03-26 01:09:49', 3.00, 'No');

-- --------------------------------------------------------

--
-- Table structure for table `tblnotifications`
--

CREATE TABLE `tblnotifications` (
  `id` int(11) NOT NULL,
  `empid` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_message`
--

CREATE TABLE `tbl_message` (
  `msg_id` int(11) NOT NULL,
  `incoming_msg_id` text NOT NULL,
  `outgoing_msg_id` text NOT NULL,
  `text_message` text NOT NULL,
  `curr_date` text NOT NULL,
  `curr_time` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbl_message`
--

INSERT INTO `tbl_message` (`msg_id`, `incoming_msg_id`, `outgoing_msg_id`, `text_message`, `curr_date`, `curr_time`) VALUES
(1, '16', '15', 'hello adrian', 'September 20, 2023 ', '1:57 am'),
(2, '16', '15', 'heyy\n', 'September 20, 2023 ', '2:00 am'),
(3, '16', '15', 'h', 'September 20, 2023 ', '10:08 am'),
(4, '15', '16', 'yes lidiya\n', 'September 20, 2023 ', '10:12 am'),
(5, '15', '16', 'whats up', 'September 20, 2023 ', '10:13 am'),
(6, '14', '13', 'hey', 'September 22, 2023 ', '9:58 am'),
(7, '14', '13', 'hmm', 'September 22, 2023 ', '9:58 am'),
(8, '14', '13', 'h', 'September 22, 2023 ', '10:00 am'),
(9, '14', '13', 'a', 'September 22, 2023 ', '10:07 am'),
(10, '12', '15', 'hmm', 'September 27, 2023 ', '5:55 pm'),
(11, '12', '15', 'hh', 'September 27, 2023 ', '5:57 pm'),
(12, '12', '15', 'kk', 'September 27, 2023 ', '5:57 pm'),
(13, '12', '15', 'ok', 'September 27, 2023 ', '5:58 pm'),
(14, '16', '15', 'jhhjg', 'September 27, 2023 ', '5:59 pm'),
(15, '16', '15', 'hh', 'September 27, 2023 ', '6:02 pm'),
(16, '16', '15', 'hmmm', 'September 27, 2023 ', '6:02 pm'),
(17, '16', '15', 'hai sir', 'September 27, 2023 ', '6:03 pm'),
(18, '12', '15', 'salam', 'September 27, 2023 ', '6:13 pm'),
(19, '16', '15', 'sal', 'September 27, 2023 ', '6:21 pm');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_leave`
--
ALTER TABLE `employee_leave`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `tblattendance`
--
ALTER TABLE `tblattendance`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `tbldepartments`
--
ALTER TABLE `tbldepartments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblemployees`
--
ALTER TABLE `tblemployees`
  ADD PRIMARY KEY (`emp_id`);

--
-- Indexes for table `tblleave`
--
ALTER TABLE `tblleave`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserEmail` (`empid`);

--
-- Indexes for table `tblleavetype`
--
ALTER TABLE `tblleavetype`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblnotifications`
--
ALTER TABLE `tblnotifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_message`
--
ALTER TABLE `tbl_message`
  ADD PRIMARY KEY (`msg_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employee_leave`
--
ALTER TABLE `employee_leave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT for table `tblattendance`
--
ALTER TABLE `tblattendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `tbldepartments`
--
ALTER TABLE `tbldepartments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblemployees`
--
ALTER TABLE `tblemployees`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `tblleave`
--
ALTER TABLE `tblleave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=291;

--
-- AUTO_INCREMENT for table `tblleavetype`
--
ALTER TABLE `tblleavetype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `tblnotifications`
--
ALTER TABLE `tblnotifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tbl_message`
--
ALTER TABLE `tbl_message`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee_leave`
--
ALTER TABLE `employee_leave`
  ADD CONSTRAINT `employee_leave_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `tblemployees` (`emp_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_leave_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `tblleavetype` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
