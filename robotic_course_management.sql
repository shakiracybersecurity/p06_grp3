-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 23, 2025 at 10:15 AM
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
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `NAME` varchar(50) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `PASSWORD_HASH` char(60) DEFAULT NULL,
  `PASSWORD_SET` varchar(255) DEFAULT NULL,
  `CREATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `UPDATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `ROLE_ID` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`ID`, `NAME`, `EMAIL`, `PASSWORD_HASH`, `PASSWORD_SET`, `CREATED_AT`, `UPDATED_AT`, `ROLE_ID`) VALUES
(1, 'admin', NULL, '$2y$10$/tYny.o4tscn0gc0rOnOMeAEo2i/zRVexlc5mhF0jh4DSnhPsi85.', 'adminpass', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', '3');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `NAME` varchar(50) DEFAULT NULL,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `TEACHER_ID` varchar(50) DEFAULT NULL,
  `MODULES_ID` varchar(50) DEFAULT NULL,
  `MODE` varchar(10) DEFAULT NULL,
  `DEPARTMENT_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`ID`, `NAME`, `STUDENT_ID`, `TEACHER_ID`, `MODULES_ID`, `MODE`, `DEPARTMENT_ID`) VALUES
(1, 'class1', NULL, NULL, NULL, 'term', 0),
(2, 'class 2', NULL, NULL, NULL, 'term', 5),
(3, 'test 3', NULL, NULL, NULL, 'term', 6);


-- Indexes for dumped tables
--

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `ADMIN_ID` varchar(50) DEFAULT NULL,
  `STAFF_ID` varchar(50) DEFAULT NULL,
  `TOKEN` varchar(50) DEFAULT NULL,
  `ISSUED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `EXPIRES_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `NAME` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`ID`, `NAME`) VALUES
(1, 'dep 1'),
(2, 'dep 2');

-- --------------------------------------------------------

--
-- Table structure for table `error_logs`
--

CREATE TABLE `error_logs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `NAME` varchar(50) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `PHONE_NUMBER` int(8) DEFAULT NULL,
  `DEPARTMENT` varchar(50) DEFAULT NULL,
  `PASSWORD_HASH` char(60) DEFAULT NULL,
  `PASSWORD_SET` varchar(50) DEFAULT NULL,
  `CREATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `UPDATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `ROLE_ID` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`ID`, `NAME`, `EMAIL`, `PHONE_NUMBER`, `DEPARTMENT`, `PASSWORD_HASH`, `PASSWORD_SET`, `CREATED_AT`, `UPDATED_AT`, `ROLE_ID`) VALUES
(1, 'faculty', NULL, NULL, NULL, '$2y$10$fNNYal8fxm2k.gP9AbCYbOlPBP/ZPGtvLzPhxSk61cHX1J1Alc0pa', 'facultypass', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', '2');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `COURSE_ID` varchar(50) DEFAULT NULL,
  `SCORE` int(11) DEFAULT NULL,
  `GRADE` varchar(11) DEFAULT NULL,
  `ENTERED_BY` varchar(50) DEFAULT NULL,
  `ENTERED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`ID`, `STUDENT_ID`, `COURSE_ID`, `SCORE`, `GRADE`, `ENTERED_BY`, `ENTERED_AT`) VALUES
(1, '3', '3', 0, 'yes', NULL, '2025-01-20 04:42:57.781894');

-- --------------------------------------------------------

--
-- Table structure for table `granted_permissions`
--

CREATE TABLE `granted_permissions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `PERMISSION_ID` varchar(50) DEFAULT NULL,
  `ROLE_DESCRIPTION` varchar(50) DEFAULT NULL,
  `PERMISSION_DESCRIPTION` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hash_algorithms`
--

CREATE TABLE `hash_algorithms` (
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `ALGORITHM_NAME` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `PASSWORD_HASH` varchar(50) DEFAULT NULL,
  `PASSWORD_SET` varchar(50) DEFAULT NULL,
  `HASH_ALGORITHM_ID` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `STUDENT_ID` varchar(50) DEFAULT NULL,
  `STAFF_ID` varchar(50) DEFAULT NULL,
  `ADMIN_ID` varchar(50) DEFAULT NULL,
  `EMAIL` varchar(50) DEFAULT NULL,
  `RESET_TOKEN` varchar(50) DEFAULT NULL,
  `RESET_LINK` varchar(50) DEFAULT NULL,
  `TOKEN_EXPIRY` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `REQUESTED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`ID`, `STUDENT_ID`, `STAFF_ID`, `ADMIN_ID`, `EMAIL`, `RESET_TOKEN`, `RESET_LINK`, `TOKEN_EXPIRY`, `REQUESTED_AT`) VALUES
(1, '1', NULL, NULL, 'yuhaann@gmail.com', 'a61322efd175f7ab8c089c27e51235e2', NULL, '2025-01-21 10:54:04.000000', '2025-01-21 16:54:04.769762'),
(2, NULL, NULL, NULL, 'yuhaann@gmail.com', '7cbc284a14a1f5327b7a596bd9959be27fede3b19335422ea2', NULL, '2025-01-22 02:07:53.000000', '2025-01-22 08:07:53.034908'),
(3, '1', NULL, NULL, 'yuhaann@gmail.com', 'c894d7d7617ce6f9c8858deff989a661', NULL, '2025-01-22 02:08:47.000000', '2025-01-22 08:08:47.711307'),
(4, '1', NULL, NULL, 'yuhaann@gmail.com', 'b53eb871fa5df6267cec5967f16957b2', NULL, '2025-01-23 02:14:39.000000', '2025-01-23 08:14:39.302553'),
(5, '1', NULL, NULL, 'yuhaann@gmail.com', 'b44c04d613d316fc35e446584dffe465', NULL, '2025-01-23 02:44:54.000000', '2025-01-23 08:44:54.673185');

-- --------------------------------------------------------

--
-- Table structure for table `sensitive_data`
--

CREATE TABLE `sensitive_data` (
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
  `ID` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `NAME` varchar(50) DEFAULT NULL,
  `PHONE NUMBER` int(8) DEFAULT NULL,
  `EMAIL` text DEFAULT NULL,
  `PASSWORD_HASH` char(60) DEFAULT NULL,
  `PASSWORD_SET` varchar(50) DEFAULT NULL,
  `CREATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `UPDATED_AT` timestamp(6) NOT NULL DEFAULT current_timestamp(6),
  `COURSE_ID` varchar(11) DEFAULT NULL,
  `FACULTY` varchar(11) DEFAULT NULL,
  `DEPARTMENT_ID` int(11) DEFAULT NULL,
  `GRADE_ID` varchar(11) DEFAULT NULL,
  `CLASS` varchar(11) DEFAULT NULL,
  `ROLE_ID` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`ID`, `NAME`, `PHONE NUMBER`, `EMAIL`, `PASSWORD_HASH`, `PASSWORD_SET`, `CREATED_AT`, `UPDATED_AT`, `COURSE_ID`, `FACULTY`, `DEPARTMENT_ID`, `GRADE_ID`, `CLASS`, `ROLE_ID`) VALUES
(1, 'student', NULL, 'yuhaann@gmail.com', '$2y$10$kQy.cTpOx4IEsUJX5ldwduOv2vsvhojVp1tkfmei1dTMSHSbR.oJq', '5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a', '0000-00-00 00:00:00.000000', '0000-00-00 00:00:00.000000', '1', NULL, NULL, NULL, NULL, '1');

--
-- Indexes for dumped tables
--

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
