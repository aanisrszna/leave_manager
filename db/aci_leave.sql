-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2024 at 02:26 AM
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
(3, 'Software Engineering', 'SE', '2021-05-21 08:27:45'),
(4, 'Computer Science', 'CS', '2023-09-13 06:47:27');

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
  `Av_leave` varchar(150) NOT NULL,
  `Phonenumber` char(11) NOT NULL,
  `Status` varchar(10) NOT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` varchar(30) NOT NULL,
  `location` varchar(200) NOT NULL,
  `signature` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblemployees`
--

INSERT INTO `tblemployees` (`emp_id`, `FirstName`, `LastName`, `Staff_ID`, `Position_Staff`, `EmailId`, `Password`, `Gender`, `Dob`, `Department`, `Address`, `Av_leave`, `Phonenumber`, `Status`, `RegDate`, `role`, `location`, `signature`) VALUES
(11, 'Huda Imanah', '', 'AD-01', 'Senior Lecturer', 'HoD_CS@gmail.com', '123', 'female', '06 April 2000', 'CS', 'Perak', '', '0132969235', 'Offline', '2023-09-13 05:49:01', 'HOD', '1.jfif', 'reg_ud_0132969235_11.png'),
(12, 'Kaiyisah Baiduri', '', 'AD-02', 'Senior Lecturer', 'HoD_SE@gmail.com', '123', 'female', '10 September 2023', 'SE', 'Tampin', '', '0123456789', 'Offline', '2023-09-13 05:51:00', 'HOD', '2.jfif', 'reg_ai_0123456789_12.png'),
(13, 'Zulaikha Kamaruddin', '', 'CS-01', 'Student', 'ika_CS@gmail.com', '000128104472', 'female', '10 July 2023', 'CS', 'Klang', '', '0123344555', 'Offline', '2023-09-13 05:55:52', 'Student', '5.jfif', ''),
(14, 'Muhammad Syahrizal', '', 'CO-01', 'Lecturer', 'coor_CS@gmail.com', '123', 'male', '04 September 2023', 'CS', 'Puchong', '', '0114567888', 'Offline', '2023-09-13 05:57:20', 'Coordinator', '4.jfif', 'hod_uh_0114567888_14.png'),
(15, 'Nur Lidiya', '', 'D20202096012', 'AC10', 'lidiya_SE@gmail.com', '000910104456', 'female', '13 June 2023', 'SE', 'Semenyihh', '', '0193366754', 'Online', '2023-09-13 05:58:28', 'Student', '6.jfif', 'hod_ur_0193366754_15.png'),
(16, 'Muhammad Adrian', '', 'CO-02', 'Lecturer', 'coor_SE@gmail.com', '123', 'male', '05 February 2023', 'SE', 'Kelana Jaya', '', '0135677234', 'Offline', '2023-09-13 05:59:50', 'Coordinator', '3.jfif', 'hod_uh_0135677234_16.png'),
(29, 'Ericka', 'Ensimau', '', '', 'ict@gmail.com', 'ict', 'female', '06 September 2023', 'SE', 'Sarawak', '', '0114567234', 'Offline', '2023-09-28 00:26:23', 'Admin', '7.jfif', '');

-- --------------------------------------------------------

--
-- Table structure for table `tblleave`
--

CREATE TABLE `tblleave` (
  `id` int(11) NOT NULL,
  `LeaveType` varchar(110) NOT NULL,
  `RequestedDays` int(11) NOT NULL,
  `DaysOutstand` int(11) NOT NULL,
  `FromDate` varchar(120) NOT NULL,
  `ToDate` varbinary(120) DEFAULT NULL,
  `Sign` varchar(120) DEFAULT NULL,
  `PostingDate` date DEFAULT NULL,
  `WorkCovered` varchar(120) DEFAULT NULL,
  `HodRemarks` int(11) NOT NULL DEFAULT 0,
  `HodSign` varchar(200) NOT NULL,
  `HodDate` varchar(120) NOT NULL,
  `RegRemarks` int(1) NOT NULL DEFAULT 0,
  `RegSign` varchar(200) NOT NULL,
  `RegDate` varchar(120) NOT NULL,
  `IsRead` int(1) NOT NULL,
  `empid` int(11) DEFAULT NULL,
  `num_days` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblleave`
--

INSERT INTO `tblleave` (`id`, `LeaveType`, `RequestedDays`, `DaysOutstand`, `FromDate`, `ToDate`, `Sign`, `PostingDate`, `WorkCovered`, `HodRemarks`, `HodSign`, `HodDate`, `RegRemarks`, `RegSign`, `RegDate`, `IsRead`, `empid`, `num_days`) VALUES
(64, '', 0, 0, '', NULL, 'sig_ur_0193366754_15.', '2024-03-03', '', 0, '', '', 0, '', '', 1, 15, 0),
(68, '', 0, 0, '', NULL, 'sig_ur_0193366754_15.', '2024-03-06', '', 1, 'hod_uh_0135677234_16.png', '2024-03-06 ', 1, 'reg_ai_0123456789_12.png', '2024-03-06 ', 1, 15, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tblleavetype`
--

CREATE TABLE `tblleavetype` (
  `id` int(11) NOT NULL,
  `LeaveType` varchar(200) DEFAULT NULL,
  `Description` mediumtext DEFAULT NULL,
  `date_from` varchar(200) NOT NULL,
  `date_to` varchar(200) NOT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblleavetype`
--

INSERT INTO `tblleavetype` (`id`, `LeaveType`, `Description`, `date_from`, `date_to`, `CreationDate`) VALUES
(9, 'Diploma', NULL, '', '', '2023-09-13 06:54:24'),
(16, 'Degree', NULL, '', '', '2023-09-13 07:35:24');

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
-- AUTO_INCREMENT for table `tbldepartments`
--
ALTER TABLE `tbldepartments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblemployees`
--
ALTER TABLE `tblemployees`
  MODIFY `emp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tblleave`
--
ALTER TABLE `tblleave`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `tblleavetype`
--
ALTER TABLE `tblleavetype`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_message`
--
ALTER TABLE `tbl_message`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
