-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 15, 2025 at 10:04 AM
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
-- Database: `robotic course management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(50) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `PASSWORD_HASH` varchar(255) DEFAULT NULL,
  `PASSWORD_SET` varchar(255) DEFAULT NULL,
  `CREATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `UPDATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `ROLE_ID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`ID`, `NAME`, `EMAIL`, `PASSWORD_HASH`, `PASSWORD_SET`, `CREATED_AT`, `UPDATED_AT`, `ROLE_ID`) VALUES
(2, 'admin', NULL, NULL, 'adminpass', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', '3');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `ID` int(11) NOT NULL,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `TEACHER_ID` varchar(50) DEFAULT NULL,
  `MODULES_ID` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(50) DEFAULT NULL,
  `START_DATE` date DEFAULT NULL,
  `END_DATE` date DEFAULT NULL,
  `STAFF_ID` int(50) DEFAULT NULL,
  `CLASS_ID` int(50) DEFAULT NULL,
  `STUDENT_ID` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `csrf`
--

CREATE TABLE `csrf` (
  `ID` int(11) NOT NULL,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `ADMIN_ID` varchar(50) DEFAULT NULL,
  `STAFF_ID` varchar(50) DEFAULT NULL,
  `TOKEN` varchar(50) DEFAULT NULL,
  `ISSUED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `EXPIRES_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `error_logs`
--

CREATE TABLE `error_logs` (
  `ID` int(11) NOT NULL,
  `ERROR_MESSAGE` varchar(255) DEFAULT NULL,
  `ERROR_TYPE` varchar(50) DEFAULT NULL,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `ADMIN_ID` varchar(50) DEFAULT NULL,
  `STAFF_ID` varchar(50) DEFAULT NULL,
  `OCCURED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(50) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `PHONE_NUMBER` int(8) DEFAULT NULL,
  `DEPARTMENT` varchar(50) DEFAULT NULL,
  `PASSWORD_HASH` varchar(50) DEFAULT NULL,
  `PASSWORD_SET` varchar(50) DEFAULT NULL,
  `CREATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `UPDATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `ROLE_ID` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`ID`, `NAME`, `EMAIL`, `PHONE_NUMBER`, `DEPARTMENT`, `PASSWORD_HASH`, `PASSWORD_SET`, `CREATED_AT`, `UPDATED_AT`, `ROLE_ID`) VALUES
(1, 'faculty', NULL, NULL, NULL, NULL, 'facultypass', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', '2');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `ID` int(11) NOT NULL,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `COURSE_ID` varchar(50) DEFAULT NULL,
  `SCORE` int(11) DEFAULT NULL,
  `GRADE` varchar(11) DEFAULT NULL,
  `ENTERED_BY` varchar(50) DEFAULT NULL,
  `ENTERED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `granted_permissions`
--

CREATE TABLE `granted_permissions` (
  `ID` int(11) NOT NULL,
  `PERMISSION_ID` varchar(50) DEFAULT NULL,
  `ROLE_DESCRIPTION` varchar(50) DEFAULT NULL,
  `PERMISSION_DESCRIPTION` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hash_algorithms`
--

CREATE TABLE `hash_algorithms` (
  `ID` int(11) NOT NULL,
  `ALGORITHM_NAME` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `ID` int(11) NOT NULL,
  `PASSWORD_HASH` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `ID` int(11) NOT NULL,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `ADMIN_ID` varchar(50) DEFAULT NULL,
  `STAFF_ID` varchar(50) DEFAULT NULL,
  `IP_ADDRESS` varchar(50) DEFAULT NULL,
  `ATTEMPT_TIME` timestamp(6) NULL DEFAULT current_timestamp(6),
  `SUCCESS` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_data`
--

CREATE TABLE `login_data` (
  `ID` int(11) NOT NULL,
  `PASSWORD_HASH` varchar(50) DEFAULT NULL,
  `PASSWORD_SET` varchar(50) DEFAULT NULL,
  `HASH_ALGORITHM_ID` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `ID` int(11) NOT NULL,
  `YEAR` year(4) DEFAULT NULL,
  `SEMESTER` varchar(50) DEFAULT NULL,
  `TERMS` varchar(50) DEFAULT NULL,
  `STAFF_ID` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `ID` int(11) NOT NULL,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `STAFF_ID` varchar(50) DEFAULT NULL,
  `ADMIN_ID` varchar(50) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `RESET_TOKEN` varchar(50) DEFAULT NULL,
  `RESET_LINK` varchar(50) DEFAULT NULL,
  `TOKEN_EXPIRY` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `REQUESTED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sensitive_data`
--

CREATE TABLE `sensitive_data` (
  `ID` int(11) NOT NULL,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `ADMIN_ID` varchar(50) DEFAULT NULL,
  `STAFF_ID` varchar(50) DEFAULT NULL,
  `ENCRYPTED_DATA` blob DEFAULT NULL,
  `ENCRYPTION_KEY_ID` int(50) DEFAULT NULL,
  `CREATED_AT` timestamp(6) NULL DEFAULT current_timestamp(6),
  `UPDATED_AT` timestamp(6) NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `ID` int(11) NOT NULL,
  `NAME` varchar(50) DEFAULT NULL,
  `PHONE NUMBER` int(8) DEFAULT NULL,
  `EMAIL` text DEFAULT NULL,
  `PASSWORD_HASH` varchar(50) DEFAULT NULL,
  `PASSWORD_SET` varchar(50) DEFAULT NULL,
  `CREATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `UPDATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `COURSE_ID` varchar(11) DEFAULT NULL,
  `FACULTY` varchar(11) DEFAULT NULL,
  `DEPARTMENT` varchar(11) DEFAULT NULL,
  `GRADE_ID` varchar(11) DEFAULT NULL,
  `CLASS` varchar(11) DEFAULT NULL,
  `ROLE_ID` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`ID`, `NAME`, `PHONE NUMBER`, `EMAIL`, `PASSWORD_HASH`, `PASSWORD_SET`, `CREATED_AT`, `UPDATED_AT`, `COURSE_ID`, `FACULTY`, `DEPARTMENT`, `GRADE_ID`, `CLASS`, `ROLE_ID`) VALUES
(1, 'student', NULL, NULL, NULL, 'studentpass', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', NULL, NULL, NULL, NULL, NULL, '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `csrf`
--
ALTER TABLE `csrf`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `error_logs`
--
ALTER TABLE `error_logs`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `granted_permissions`
--
ALTER TABLE `granted_permissions`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `hash_algorithms`
--
ALTER TABLE `hash_algorithms`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `login_data`
--
ALTER TABLE `login_data`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `sensitive_data`
--
ALTER TABLE `sensitive_data`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `csrf`
--
ALTER TABLE `csrf`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `error_logs`
--
ALTER TABLE `error_logs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `granted_permissions`
--
ALTER TABLE `granted_permissions`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hash_algorithms`
--
ALTER TABLE `hash_algorithms`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_data`
--
ALTER TABLE `login_data`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sensitive_data`
--
ALTER TABLE `sensitive_data`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
