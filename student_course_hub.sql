-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 25, 2025 at 02:09 PM
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
-- Database: `student_course_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `activitylog`
--

CREATE TABLE `activitylog` (
  `LogID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `Action` varchar(100) NOT NULL,
  `TableName` varchar(50) DEFAULT NULL,
  `RecordID` int(11) DEFAULT NULL,
  `OldValues` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`OldValues`)),
  `NewValues` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`NewValues`)),
  `IPAddress` varchar(45) DEFAULT NULL,
  `UserAgent` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activitylog`
--

INSERT INTO `activitylog` (`LogID`, `UserID`, `Action`, `TableName`, `RecordID`, `OldValues`, `NewValues`, `IPAddress`, `UserAgent`, `CreatedAt`) VALUES
(1, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:08:26'),
(2, 1, 'Logout', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:14:01'),
(3, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:14:19'),
(4, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:14:32'),
(5, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:15:10'),
(6, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:17:02'),
(7, 1, 'Staff Logout', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:24:06'),
(8, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:24:27'),
(9, 1, 'Delete Programme', 'Programmes', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:28:47'),
(10, 1, 'Delete Programme', 'Programmes', 4, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:28:52'),
(11, 1, 'Delete Programme', 'Programmes', 9, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:28:57'),
(12, 1, 'Delete Programme', 'Programmes', 6, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:29:03'),
(13, 1, 'Update Programme', 'Programmes', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:47:23'),
(14, 1, 'Update Programme', 'Programmes', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:47:34'),
(15, 1, 'Update Programme', 'Programmes', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:47:46'),
(16, 1, 'Update Programme', 'Programmes', 7, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:47:56'),
(17, 1, 'Update Programme', 'Programmes', 8, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:48:08'),
(18, 1, 'Update Programme', 'Programmes', 10, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:48:18'),
(19, 1, 'Update Programme', 'Programmes', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:49:20'),
(20, 1, 'Staff Logout', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:50:31'),
(21, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:52:13'),
(22, 1, 'Update Programme', 'Programmes', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:52:30'),
(23, 1, 'Update Programme', 'Programmes', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:52:38'),
(24, 1, 'Update Programme', 'Programmes', 5, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 09:54:37'),
(25, 1, 'Update Programme', 'Programmes', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:00:40'),
(26, 1, 'Update Programme', 'Programmes', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:00:44'),
(27, 1, 'Update Programme', 'Programmes', 2, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:00:49'),
(28, 1, 'Logout', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:00:59'),
(29, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:07:17'),
(30, 1, 'Update Programme', 'Programmes', 3, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:10:34'),
(31, 1, 'Logout', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:12:35'),
(32, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:12:52'),
(33, 1, 'Staff Logout', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:13:26'),
(34, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:19:58'),
(35, 1, 'Staff Logout', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:33:58'),
(36, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:41:08'),
(37, 1, 'Staff Logout', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:52:33'),
(38, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 10:54:11'),
(39, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:02:14'),
(40, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:05:58'),
(41, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:06:15'),
(42, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:06:48'),
(43, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:25:30'),
(44, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:25:46'),
(45, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:27:21'),
(46, 1, 'Logout', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:28:19'),
(47, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:28:40'),
(48, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:29:19'),
(49, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:29:49'),
(50, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:35:04'),
(51, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Mobile Safari/537.36', '2025-07-25 11:36:28'),
(52, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:38:04'),
(53, 1, 'Staff Login', 'Staff', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:42:16'),
(54, 1, 'Login', 'Users', 1, NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', '2025-07-25 11:42:29');

-- --------------------------------------------------------

--
-- Table structure for table `interestedstudents`
--

CREATE TABLE `interestedstudents` (
  `InterestID` int(11) NOT NULL,
  `ProgrammeID` int(11) NOT NULL,
  `StudentName` varchar(100) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Country` varchar(100) DEFAULT NULL,
  `CurrentEducation` varchar(200) DEFAULT NULL,
  `MessageToUniversity` text DEFAULT NULL,
  `IsSubscribed` tinyint(1) DEFAULT 1,
  `RegisteredAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UnsubscribeToken` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interestedstudents`
--

INSERT INTO `interestedstudents` (`InterestID`, `ProgrammeID`, `StudentName`, `Email`, `Phone`, `Country`, `CurrentEducation`, `MessageToUniversity`, `IsSubscribed`, `RegisteredAt`, `UnsubscribeToken`) VALUES
(5, 3, 'Mohamed Hassan', 'mohamed.hassan@example.com', '+20 100 123 4567', 'Egypt', 'High school graduate with strong mathematics background', 'Excited about AI and machine learning career opportunities.', 1, '2025-07-25 08:50:10', '5f75e8e6-6934-11f0-95af-80e82c50e457'),
(6, 7, 'Sarah Johnson', 'sarah.johnson@example.com', '+61 400 123 456', 'Australia', 'BSc Information Systems with cybersecurity focus', 'Want to advance my career in digital forensics.', 1, '2025-07-25 08:50:10', '5f75e94d-6934-11f0-95af-80e82c50e457');

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `LevelID` int(11) NOT NULL,
  `LevelName` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL,
  `SortOrder` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`LevelID`, `LevelName`, `Description`, `SortOrder`) VALUES
(1, 'Undergraduate', 'Bachelor degree programmes (3-4 years)', 1),
(2, 'Postgraduate', 'Master degree programmes (1-2 years)', 2),
(3, 'PhD', 'Doctoral research programmes (3-4 years)', 3);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `ModuleID` int(11) NOT NULL,
  `ModuleCode` varchar(20) NOT NULL,
  `ModuleName` varchar(200) NOT NULL,
  `ModuleLeaderID` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `LearningOutcomes` text DEFAULT NULL,
  `AssessmentMethods` text DEFAULT NULL,
  `Credits` int(11) DEFAULT 20,
  `Image` varchar(255) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`ModuleID`, `ModuleCode`, `ModuleName`, `ModuleLeaderID`, `Description`, `LearningOutcomes`, `AssessmentMethods`, `Credits`, `Image`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'CS101', 'Introduction to Programming', 1, 'Covers the fundamentals of programming using Python and Java, including basic algorithms and problem-solving techniques.', 'Understand programming concepts, write basic programs, debug code effectively', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(2, 'MA101', 'Mathematics for Computer Science', 2, 'Teaches discrete mathematics, linear algebra, and probability theory essential for computer science.', 'Apply mathematical concepts to computing problems, understand algorithmic complexity', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(3, 'CS102', 'Computer Systems & Architecture', 3, 'Explores CPU design, memory management, assembly language, and computer organization.', 'Understand computer hardware, write assembly code, optimize system performance', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(4, 'CS103', 'Databases', 4, 'Covers SQL, relational database design, normalization, and introduction to NoSQL systems.', 'Design efficient databases, write complex SQL queries, understand data modeling', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(5, 'CS201', 'Software Engineering', 4, 'Focuses on agile development, design patterns, testing methodologies, and project management.', 'Apply software engineering principles, work in teams, manage software projects', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:34:20'),
(6, 'CS202', 'Algorithms & Data Structures', 3, 'Examines sorting, searching, graph algorithms, and complexity analysis.', 'Implement efficient algorithms, analyze computational complexity, solve optimization problems', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:34:16'),
(7, 'CY101', 'Cyber Security Fundamentals', 3, 'Provides introduction to network security, cryptography, and vulnerability assessment.', 'Identify security threats, implement basic security measures, understand encryption', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:34:11'),
(8, 'AI101', 'Artificial Intelligence', 5, 'Introduces AI concepts including neural networks, expert systems, and robotics.', 'Understand AI principles, implement basic AI algorithms, analyze AI applications', 'exams', 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 10:09:08'),
(9, 'AI201', 'Machine Learning', 4, 'Explores supervised and unsupervised learning, including decision trees and clustering algorithms.', 'Apply machine learning techniques, evaluate model performance, handle real-world data', 'exams', 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:34:02'),
(10, 'CY201', 'Ethical Hacking', 12, 'Covers penetration testing, security assessments, and cybersecurity laws and ethics.', 'Perform ethical penetration testing, understand legal frameworks, assess security risks', 'exams', 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:35:54'),
(11, 'CS301', 'Computer Networks', 1, 'Teaches TCP/IP, network protocols, wireless communication, and network security.', 'Configure networks, troubleshoot connectivity issues, implement network security', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(12, 'CS302', 'Software Testing & Quality Assurance', 2, 'Focuses on automated testing, debugging techniques, and code reliability assessment.', 'Design comprehensive test suites, automate testing processes, ensure software quality', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(13, 'CS303', 'Embedded Systems', 3, 'Examines microcontrollers, real-time operating systems, and IoT applications.', 'Program embedded devices, design IoT solutions, understand real-time constraints', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(14, 'CS304', 'Human-Computer Interaction', 4, 'Studies UI/UX design principles, usability testing, and accessibility guidelines.', 'Design user-friendly interfaces, conduct usability studies, ensure accessibility compliance', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(15, 'CS305', 'Blockchain Technologies', 5, 'Covers distributed ledgers, consensus mechanisms, smart contracts, and cryptocurrency.', 'Understand blockchain principles, develop smart contracts, analyze decentralized systems', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(16, 'CS306', 'Cloud Computing', 6, 'Introduces cloud services, virtualization, distributed systems, and scalability.', 'Deploy cloud applications, manage cloud resources, design scalable systems', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(17, 'CY301', 'Digital Forensics', 3, 'Teaches forensic investigation techniques, evidence handling, and cybercrime analysis.', 'Conduct digital investigations, preserve evidence, analyze cyber incidents', 'exams', 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:33:50'),
(18, 'CS401', 'Final Year Project', 8, 'Independent project where students develop a comprehensive software solution.', 'Apply learned skills, manage a complex project, present technical solutions', NULL, 40, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(19, 'AI301', 'Advanced Machine Learning', 1, 'Covers deep learning, reinforcement learning, and cutting-edge AI techniques.', 'Implement advanced ML algorithms, understand deep learning architectures, research AI applications', 'exams', 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:30:06'),
(20, 'CY401', 'Cyber Threat Intelligence', 4, 'Focuses on cybersecurity risk analysis, malware detection, and threat mitigation strategies.', 'Analyze cyber threats, develop mitigation strategies, implement threat intelligence frameworks', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:33:45'),
(21, 'DS301', 'Big Data Analytics', 2, 'Explores data mining techniques, distributed computing, and AI-driven insights.', 'Process large datasets, apply data mining techniques, derive business insights', 'exams', 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:31:18'),
(22, 'CS501', 'Cloud & Edge Computing', 3, 'Examines scalable cloud platforms, serverless computing, and edge network technologies.', 'Design cloud architectures, implement edge computing solutions, optimize distributed systems', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:33:38'),
(23, 'CS502', 'Blockchain & Cryptography', 2, 'Advanced coverage of decentralized applications, consensus algorithms, and security measures.', 'Develop blockchain applications, implement cryptographic protocols, ensure system security', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:33:33'),
(24, 'AI401', 'AI Ethics &amp; Society', 3, 'Analyzes ethical dilemmas in AI, fairness, bias detection, and regulatory considerations.', 'Evaluate AI ethics, identify bias in algorithms, understand AI governance frameworks', 'exams', 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:33:26'),
(25, 'CS503', 'Quantum Computing', 4, 'Introduces quantum algorithms, qubit manipulation, and cryptographic applications.', 'Understand quantum principles, implement quantum algorithms, explore quantum cryptography', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:33:19'),
(26, 'CY501', 'Cybersecurity Law &amp; Policy', 1, 'Explores digital privacy laws, GDPR compliance, and international cyber law frameworks.', 'Understand legal frameworks, ensure compliance, develop security policies', 'exams', 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:31:33'),
(27, 'AI501', 'Neural Networks &amp; Deep Learning', 1, 'Advanced study of convolutional networks, GANs, and latest AI advancements.', 'Design neural architectures, implement advanced models, research deep learning applications', 'exams', 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:30:40'),
(28, 'AI502', 'Human-AI Interaction', 3, 'Studies AI usability, natural language processing systems, and social robotics.', 'Design AI interfaces, implement NLP systems, understand human-AI collaboration', 'exams', 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 09:30:56'),
(29, 'AI503', 'Autonomous Systems', 11, 'Focuses on self-driving technology, robotics control, and intelligent agent design.', 'Design autonomous systems, implement control algorithms, understand safety considerations', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(30, 'CY502', 'Digital Forensics & Incident Response', 12, 'Advanced forensic analysis, evidence gathering, and comprehensive threat mitigation.', 'Lead forensic investigations, coordinate incident response, develop security procedures', NULL, 20, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10'),
(31, 'CS601', 'Postgraduate Dissertation', 13, 'Major research project exploring advanced topics in computing and related fields.', 'Conduct independent research, contribute to knowledge, present research findings', NULL, 60, NULL, 1, '2025-07-25 08:50:10', '2025-07-25 08:50:10');

-- --------------------------------------------------------

--
-- Table structure for table `programmemodules`
--

CREATE TABLE `programmemodules` (
  `ProgrammeModuleID` int(11) NOT NULL,
  `ProgrammeID` int(11) NOT NULL,
  `ModuleID` int(11) NOT NULL,
  `Year` int(11) NOT NULL,
  `Semester` varchar(20) DEFAULT 'Full Year',
  `IsCore` tinyint(1) DEFAULT 1,
  `SortOrder` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programmemodules`
--

INSERT INTO `programmemodules` (`ProgrammeModuleID`, `ProgrammeID`, `ModuleID`, `Year`, `Semester`, `IsCore`, `SortOrder`) VALUES
(13, 2, 1, 1, 'Semester 1', 1, 0),
(14, 2, 2, 1, 'Semester 1', 1, 0),
(15, 2, 3, 1, 'Semester 2', 1, 0),
(16, 2, 4, 1, 'Semester 2', 1, 0),
(17, 2, 5, 2, 'Semester 1', 1, 0),
(18, 2, 6, 2, 'Semester 1', 1, 0),
(19, 2, 12, 2, 'Semester 2', 1, 0),
(20, 2, 14, 2, 'Semester 2', 1, 0),
(21, 2, 13, 3, 'Semester 1', 1, 0),
(22, 2, 15, 3, 'Semester 1', 1, 0),
(23, 2, 16, 3, 'Semester 2', 1, 0),
(24, 2, 18, 3, 'Full Year', 1, 0),
(25, 3, 1, 1, 'Semester 1', 1, 0),
(26, 3, 2, 1, 'Semester 1', 1, 0),
(27, 3, 3, 1, 'Semester 2', 1, 0),
(28, 3, 4, 1, 'Semester 2', 1, 0),
(29, 3, 5, 2, 'Semester 1', 1, 0),
(30, 3, 9, 2, 'Semester 1', 1, 0),
(31, 3, 8, 2, 'Semester 2', 1, 0),
(32, 3, 10, 2, 'Semester 2', 0, 0),
(33, 3, 19, 3, 'Semester 1', 1, 0),
(34, 3, 24, 3, 'Semester 1', 1, 0),
(35, 3, 27, 3, 'Semester 2', 1, 0),
(36, 3, 18, 3, 'Full Year', 1, 0),
(49, 5, 1, 1, 'Semester 1', 1, 0),
(50, 5, 2, 1, 'Semester 1', 1, 0),
(51, 5, 3, 1, 'Semester 2', 1, 0),
(52, 5, 4, 1, 'Semester 2', 1, 0),
(53, 5, 5, 2, 'Semester 1', 1, 0),
(54, 5, 6, 2, 'Semester 1', 1, 0),
(55, 5, 9, 2, 'Semester 2', 1, 0),
(56, 5, 16, 2, 'Semester 2', 1, 0),
(57, 5, 21, 3, 'Semester 1', 1, 0),
(58, 5, 14, 3, 'Semester 1', 0, 0),
(59, 5, 16, 3, 'Semester 2', 1, 0),
(60, 5, 18, 3, 'Full Year', 1, 0),
(66, 7, 20, 1, 'Semester 1', 1, 0),
(67, 7, 26, 1, 'Semester 1', 1, 0),
(68, 7, 30, 1, 'Semester 2', 1, 0),
(69, 7, 23, 1, 'Semester 2', 1, 0),
(70, 7, 31, 1, 'Full Year', 1, 0),
(71, 8, 21, 1, 'Semester 1', 1, 0),
(72, 8, 22, 1, 'Semester 1', 1, 0),
(73, 8, 27, 1, 'Semester 2', 1, 0),
(74, 8, 28, 1, 'Semester 2', 1, 0),
(75, 8, 31, 1, 'Full Year', 1, 0),
(81, 10, 23, 1, 'Semester 1', 1, 0),
(82, 10, 22, 1, 'Semester 1', 1, 0),
(83, 10, 25, 1, 'Semester 2', 1, 0),
(84, 10, 26, 1, 'Semester 2', 0, 0),
(85, 10, 31, 1, 'Full Year', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `programmes`
--

CREATE TABLE `programmes` (
  `ProgrammeID` int(11) NOT NULL,
  `ProgrammeCode` varchar(20) NOT NULL,
  `ProgrammeName` varchar(200) NOT NULL,
  `LevelID` int(11) NOT NULL,
  `ProgrammeLeaderID` int(11) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `EntryRequirements` text DEFAULT NULL,
  `CareerProspects` text DEFAULT NULL,
  `Duration` varchar(50) DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `IsPublished` tinyint(1) DEFAULT 0,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `programmes`
--

INSERT INTO `programmes` (`ProgrammeID`, `ProgrammeCode`, `ProgrammeName`, `LevelID`, `ProgrammeLeaderID`, `Description`, `EntryRequirements`, `CareerProspects`, `Duration`, `Image`, `IsPublished`, `IsActive`, `CreatedAt`, `UpdatedAt`) VALUES
(2, 'BSC-SE', 'BSc Software Engineering', 1, 2, 'Specialized degree focusing on the complete software development lifecycle, including requirements analysis, design, implementation, testing, and maintenance of large-scale software systems.', 'A-levels: Mathematics (A), plus one from Physics, Computer Science, or related technical subjects (B or above). GCSE: English and Mathematics (grade 6 or above).', 'Software Engineer, DevOps Engineer, Technical Lead, Software Architect, Quality Assurance Manager, Product Manager', '3 years full-time', 'uploads/programmes/prog_688352c2193a3.png', 1, 1, '2025-07-25 08:50:10', '2025-07-25 10:00:49'),
(3, 'BSC-AI', 'BSc Artificial Intelligence', 1, 3, 'Cutting-edge programme focusing on machine learning, deep learning, neural networks, and AI applications in various domains including robotics and data science.', 'A-levels: Mathematics (A), plus one from Physics, Computer Science, Further Mathematics (A or B). GCSE: English and Mathematics (grade 6 or above).', 'AI Engineer, Machine Learning Specialist, Data Scientist, Research Scientist, AI Product Manager, Robotics Engineer', '3 years full-time', 'uploads/programmes/prog_688353de66c0a.jpg', 1, 1, '2025-07-25 08:50:10', '2025-07-25 10:10:34'),
(5, 'BSC-DS', 'BSc Data Science', 1, 5, 'Interdisciplinary programme combining statistics, machine learning, big data technologies, and domain expertise to extract insights from complex datasets.', 'A-levels: Mathematics (A), plus one from Physics, Computer Science, Economics, or Psychology (B or above). GCSE: English and Mathematics (grade 6 or above).', 'Data Scientist, Business Intelligence Analyst, Data Engineer, Quantitative Analyst, Market Research Analyst, Statistical Consultant', '3 years full-time', 'uploads/programmes/prog_6883545d315cf.jpg', 1, 1, '2025-07-25 08:50:10', '2025-07-25 09:54:37'),
(7, 'MSC-CY', 'MSc Cyber Security', 2, 4, 'Specialized programme covering advanced digital forensics, cyber threat intelligence, security policy development, and enterprise security management.', 'First or upper second class honours degree in Computer Science, Information Systems, or related field. Professional experience in IT recommended.', 'Senior Security Analyst, CISO, Security Architect, Incident Response Manager, Compliance Manager, Security Researcher', '1 year full-time', 'uploads/programmes/prog_688352cca3eee.jpg', 1, 1, '2025-07-25 08:50:10', '2025-07-25 09:47:56'),
(8, 'MSC-DS', 'MSc Data Science', 2, 1, 'Advanced programme focusing on big data analytics, cloud computing platforms, statistical modeling, and AI-driven business intelligence.', 'First or upper second class honours degree in Computer Science, Mathematics, Statistics, Engineering, or related quantitative discipline.', 'Lead Data Scientist, Head of Analytics, Data Science Manager, Principal Data Engineer, Research Data Scientist', '1 year full-time', 'uploads/programmes/prog_688352d8254da.png', 1, 1, '2025-07-25 08:50:10', '2025-07-25 09:48:08'),
(10, 'MSC-SE', 'MSc Software Engineering', 2, 6, 'Advanced programme emphasizing enterprise software design, blockchain applications, cloud architecture, and modern development methodologies.', 'First or upper second class honours degree in Computer Science, Software Engineering, or related field. Professional development experience preferred.', 'Senior Software Engineer, Solutions Architect, Technical Director, Engineering Manager, Principal Engineer, Technology Consultant', '1 year full-time', 'uploads/programmes/prog_688352e24dda1.jpg', 1, 1, '2025-07-25 08:50:10', '2025-07-25 09:48:18');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `StaffID` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Name` varchar(200) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `Department` varchar(100) DEFAULT NULL,
  `Title` varchar(100) DEFAULT NULL,
  `Bio` text DEFAULT NULL,
  `ProfileImage` varchar(255) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`StaffID`, `Username`, `PasswordHash`, `Name`, `Email`, `Phone`, `Department`, `Title`, `Bio`, `ProfileImage`, `IsActive`, `CreatedAt`) VALUES
(1, 'alice', '$2y$10$SEULrJ/LDu2XqAJOmthqge4k2x1zoOoC/MAZ7KmLQi9PvFTrEl5XC', 'Dr. Alice Johnsonss', 'alice.johnson@university.ac.uk', '254794349788', 'Computer Science', 'Professor', 'Expert in programming languages and software engineering with 15+ years of experience.', 'uploads/staff/6883513e8e80e.jpg', 1, '2025-07-25 08:50:10'),
(2, 'brian.lee', '$2y$10$NDqH8Y4mV0.YqroR7wFrHOe3sGmPgLLOctfu6rMIeqRijvAYP7UQy', 'Dr. Brian Lee', 'brian.lee@university.ac.uk', '', 'Mathematics', 'Senior Lecturer', 'Specialist in discrete mathematics and computational theory.', 'uploads/staff/68835153b7e9c.jpg', 1, '2025-07-25 08:50:10'),
(3, 'carol.white', '$2y$10$NDqH8Y4mV0.YqroR7wFrHOe3sGmPgLLOctfu6rMIeqRijvAYP7UQy', 'Dr. Carol White', 'carol.white@university.ac.uk', '', 'Computer Science', 'Associate Professor', 'Research interests include computer architecture and embedded systems.', 'uploads/staff/688351686ae86.jpg', 1, '2025-07-25 08:50:10'),
(4, 'david.green', '$2y$10$NDqH8Y4mV0.YqroR7wFrHOe3sGmPgLLOctfu6rMIeqRijvAYP7UQy', 'Dr. David Green', 'david.green@university.ac.uk', '', 'Computer Science', 'Professor', 'Database systems expert with extensive industry experience.', 'uploads/staff/68835181bfa3c.jpg', 1, '2025-07-25 08:50:10'),
(5, 'emma.scott', '$2y$10$NDqH8Y4mV0.YqroR7wFrHOe3sGmPgLLOctfu6rMIeqRijvAYP7UQy', 'Dr. Emma Scott', 'emma.scott@university.ac.uk', '', 'Computer Science', 'Senior Lecturer', 'Software engineering methodologies and project management specialist.', 'uploads/staff/6883518e552ba.jpg', 1, '2025-07-25 08:50:10'),
(6, 'frank.moore', '$2y$10$NDqH8Y4mV0.YqroR7wFrHOe3sGmPgLLOctfu6rMIeqRijvAYP7UQy', 'Dr. Frank Moore', 'frank.moore@university.ac.uk', '', 'Computer Science', 'Professor', 'Algorithms and data structures researcher.', 'uploads/staff/68835198d75e3.jpg', 1, '2025-07-25 08:50:10'),
(8, 'henry.clark', '$2y$10$NDqH8Y4mV0.YqroR7wFrHOe3sGmPgLLOctfu6rMIeqRijvAYP7UQy', 'Dr. Henry Clark', 'henry.clark@university.ac.uk', '', 'AI Research', 'Professor', 'Leading researcher in artificial intelligence and machine learning.', 'uploads/staff/688351a3847f0.jpg', 1, '2025-07-25 08:50:10'),
(11, 'sophia.miller', '$2y$10$NDqH8Y4mV0.YqroR7wFrHOe3sGmPgLLOctfu6rMIeqRijvAYP7UQy', 'Dr. Sophia Miller', 'sophia.miller@university.ac.uk', '', 'AI Research', 'Professor', 'Advanced machine learning and neural networks researcher.', 'uploads/staff/688351ae0051d.jpg', 1, '2025-07-25 08:50:10'),
(12, 'benjamin.carter', '$2y$10$NDqH8Y4mV0.YqroR7wFrHOe3sGmPgLLOctfu6rMIeqRijvAYP7UQy', 'Dr. Benjamin Carter', 'benjamin.carter@university.ac.uk', '', 'Cybersecurity', 'Senior Lecturer', 'Cyber threat intelligence and security policy expert.', 'uploads/staff/688351482f4a0.jpg', 1, '2025-07-25 08:50:10'),
(13, 'chloe.thompson', '$2y$10$NDqH8Y4mV0.YqroR7wFrHOe3sGmPgLLOctfu6rMIeqRijvAYP7UQy', 'Dr. Chloe Thompson', 'chloe.thompson@university.ac.uk', '', 'Data Science', 'Associate Professor', 'Big data analytics and cloud computing specialist.', 'uploads/staff/68835174e8d2d.jpg', 1, '2025-07-25 08:50:10');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `StudentID` int(11) NOT NULL,
  `StudentNumber` varchar(20) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Country` varchar(100) DEFAULT NULL,
  `PostalCode` varchar(20) DEFAULT NULL,
  `ProgrammeID` int(11) DEFAULT NULL,
  `EnrollmentDate` date NOT NULL,
  `Status` enum('Active','Inactive','Graduated','Withdrawn') DEFAULT 'Active',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`StudentID`, `StudentNumber`, `FirstName`, `LastName`, `Email`, `Phone`, `DateOfBirth`, `Address`, `City`, `Country`, `PostalCode`, `ProgrammeID`, `EnrollmentDate`, `Status`, `CreatedAt`, `UpdatedAt`) VALUES
(1, '25000000000001', 'test', 'test', 'test@gmail.com', '+44 123456789', '2001-02-06', 'sw6 london', 'London', 'Uk', '00511', 3, '2025-07-25', 'Active', '2025-07-25 10:33:27', '2025-07-25 10:33:27');

-- --------------------------------------------------------

--
-- Table structure for table `userroles`
--

CREATE TABLE `userroles` (
  `RoleID` int(11) NOT NULL,
  `RoleName` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `userroles`
--

INSERT INTO `userroles` (`RoleID`, `RoleName`, `Description`) VALUES
(1, 'Super Admin', 'Full system access and user management'),
(2, 'Admin', 'Programme and module management'),
(3, 'Staff', 'View own modules and programmes'),
(4, 'Viewer', 'Read-only access');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `RoleID` int(11) NOT NULL,
  `FirstName` varchar(100) DEFAULT NULL,
  `LastName` varchar(100) DEFAULT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `LastLogin` timestamp NULL DEFAULT NULL,
  `ResetToken` varchar(255) DEFAULT NULL,
  `ResetTokenExpiry` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `Email`, `PasswordHash`, `RoleID`, `FirstName`, `LastName`, `IsActive`, `CreatedAt`, `LastLogin`, `ResetToken`, `ResetTokenExpiry`) VALUES
(1, 'admin', 'admin@university.ac.uk', '$2y$10$AUJRPm8FiE7Ud2U273lJkOu1Kid5TwNrxE.BcYqM3npPWmSA.CPZe', 2, 'System', 'Administrator', 1, '2025-07-25 08:50:10', '2025-07-25 11:42:29', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activitylog`
--
ALTER TABLE `activitylog`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `interestedstudents`
--
ALTER TABLE `interestedstudents`
  ADD PRIMARY KEY (`InterestID`),
  ADD UNIQUE KEY `UnsubscribeToken` (`UnsubscribeToken`),
  ADD KEY `idx_email` (`Email`),
  ADD KEY `idx_programme` (`ProgrammeID`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`LevelID`),
  ADD UNIQUE KEY `LevelName` (`LevelName`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`ModuleID`),
  ADD UNIQUE KEY `ModuleCode` (`ModuleCode`),
  ADD KEY `ModuleLeaderID` (`ModuleLeaderID`);

--
-- Indexes for table `programmemodules`
--
ALTER TABLE `programmemodules`
  ADD PRIMARY KEY (`ProgrammeModuleID`),
  ADD UNIQUE KEY `unique_programme_module_year` (`ProgrammeID`,`ModuleID`,`Year`),
  ADD KEY `ModuleID` (`ModuleID`);

--
-- Indexes for table `programmes`
--
ALTER TABLE `programmes`
  ADD PRIMARY KEY (`ProgrammeID`),
  ADD UNIQUE KEY `ProgrammeCode` (`ProgrammeCode`),
  ADD KEY `LevelID` (`LevelID`),
  ADD KEY `ProgrammeLeaderID` (`ProgrammeLeaderID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`StaffID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`StudentID`),
  ADD UNIQUE KEY `StudentNumber` (`StudentNumber`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `ProgrammeID` (`ProgrammeID`);

--
-- Indexes for table `userroles`
--
ALTER TABLE `userroles`
  ADD PRIMARY KEY (`RoleID`),
  ADD UNIQUE KEY `RoleName` (`RoleName`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `RoleID` (`RoleID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activitylog`
--
ALTER TABLE `activitylog`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `interestedstudents`
--
ALTER TABLE `interestedstudents`
  MODIFY `InterestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `LevelID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `ModuleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `programmemodules`
--
ALTER TABLE `programmemodules`
  MODIFY `ProgrammeModuleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `programmes`
--
ALTER TABLE `programmes`
  MODIFY `ProgrammeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `StaffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `StudentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `userroles`
--
ALTER TABLE `userroles`
  MODIFY `RoleID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activitylog`
--
ALTER TABLE `activitylog`
  ADD CONSTRAINT `activitylog_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL;

--
-- Constraints for table `interestedstudents`
--
ALTER TABLE `interestedstudents`
  ADD CONSTRAINT `interestedstudents_ibfk_1` FOREIGN KEY (`ProgrammeID`) REFERENCES `programmes` (`ProgrammeID`) ON DELETE CASCADE;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`ModuleLeaderID`) REFERENCES `staff` (`StaffID`);

--
-- Constraints for table `programmemodules`
--
ALTER TABLE `programmemodules`
  ADD CONSTRAINT `programmemodules_ibfk_1` FOREIGN KEY (`ProgrammeID`) REFERENCES `programmes` (`ProgrammeID`) ON DELETE CASCADE,
  ADD CONSTRAINT `programmemodules_ibfk_2` FOREIGN KEY (`ModuleID`) REFERENCES `modules` (`ModuleID`) ON DELETE CASCADE;

--
-- Constraints for table `programmes`
--
ALTER TABLE `programmes`
  ADD CONSTRAINT `programmes_ibfk_1` FOREIGN KEY (`LevelID`) REFERENCES `levels` (`LevelID`),
  ADD CONSTRAINT `programmes_ibfk_2` FOREIGN KEY (`ProgrammeLeaderID`) REFERENCES `staff` (`StaffID`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`ProgrammeID`) REFERENCES `programmes` (`ProgrammeID`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`RoleID`) REFERENCES `userroles` (`RoleID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
