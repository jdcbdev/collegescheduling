-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2026 at 01:30 AM
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
-- Database: `collegescheduling_db`
--
CREATE DATABASE IF NOT EXISTS `collegescheduling_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `collegescheduling_db`;

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` int(11) NOT NULL,
  `schoolyear_id` int(11) NOT NULL,
  `curriculum_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `year_level` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `schoolyear_id`, `curriculum_id`, `section_name`, `year_level`, `created_at`) VALUES
(1, 1, 3, 'ACT-AD1A', 1, '2026-04-18 09:30:40'),
(2, 1, 3, 'ACT-AD2A', 2, '2026-04-18 09:31:38'),
(3, 3, 1, 'BSCS2A', 2, '2026-04-18 09:34:12'),
(4, 3, 1, 'BSCS3A', 3, '2026-04-18 09:35:11'),
(5, 3, 1, 'BSCS3B', 3, '2026-04-18 09:36:22'),
(6, 3, 5, 'MIT1A', 1, '2026-04-18 10:12:39'),
(7, 3, 2, 'BSIT3A', 3, '2026-04-18 10:12:51'),
(8, 3, 2, 'BSIT3B', 3, '2026-04-18 10:13:01'),
(9, 3, 2, 'BSIT3C', 3, '2026-04-18 10:13:10');

-- --------------------------------------------------------

--
-- Table structure for table `college_officials`
--

CREATE TABLE `college_officials` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `title` varchar(150) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `is_dean` tinyint(1) NOT NULL DEFAULT 0,
  `is_secretary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college_officials`
--

INSERT INTO `college_officials` (`id`, `name`, `title`, `department_id`, `is_dean`, `is_secretary`, `created_at`) VALUES
(1, 'Jaydee C. Ballaho, MIT', 'Head, Computer Science Department', 1, 0, 0, '2026-04-19 11:30:52'),
(2, 'Jason A. Catadman, MIT', 'Head, Information Technology Department', 2, 0, 0, '2026-04-19 11:31:29'),
(3, 'Lucy F. Felix-Sadiwa, MSCS', 'Head, MIT Department', 4, 0, 0, '2026-04-19 11:31:58'),
(4, 'Ceed Jezreel B. Lorenzo, MIT', 'Head, ACT Department', 3, 0, 0, '2026-04-19 11:32:20'),
(5, 'Mark L. Flores, PhD', 'Dean, College of Computing Studies', NULL, 1, 0, '2026-04-19 11:32:48'),
(6, 'Jaydee C. Ballaho, MIT', 'College Secretary', NULL, 0, 1, '2026-04-19 12:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `curriculum`
--

CREATE TABLE `curriculum` (
  `id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `effective_start_year` int(11) NOT NULL,
  `effective_end_year` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum`
--

INSERT INTO `curriculum` (`id`, `program_id`, `effective_start_year`, `effective_end_year`, `created_at`) VALUES
(1, 1, 2023, 2024, '2026-04-17 12:18:34'),
(2, 2, 2023, 2024, '2026-04-17 12:21:03'),
(3, 3, 2023, 2024, '2026-04-17 12:21:09'),
(4, 4, 2023, 2024, '2026-04-17 12:21:15'),
(5, 5, 2023, 2024, '2026-04-17 12:21:30');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_code` varchar(50) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_code`, `department_name`, `created_at`) VALUES
(1, 'BSCS', 'Department of Computer Science', '2026-04-17 11:29:37'),
(2, 'BSIT', 'Department of Information Technology', '2026-04-17 11:29:54'),
(3, 'ACT', 'Department of Computer Technology', '2026-04-17 11:30:06'),
(4, 'Graduate', 'Department of Graduate Studies', '2026-04-17 12:06:56');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `id` int(11) NOT NULL,
  `instructor_code` varchar(50) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `specialization` varchar(200) DEFAULT NULL,
  `active_status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`id`, `instructor_code`, `firstname`, `middlename`, `lastname`, `email`, `phone`, `department_id`, `specialization`, `active_status`, `created_at`) VALUES
(1, '001', 'Jaydee', '', 'Ballaho', '', '', 1, '', 1, '2026-04-17 14:33:38'),
(2, '002', 'Rhamirl', '', 'Jaafar', '', '', 1, '', 1, '2026-04-17 14:33:47'),
(3, '003', 'Salimar', '', 'Tahil', '', '', 1, '', 1, '2026-04-17 14:33:59'),
(4, '004', 'Vin Czar', '', 'Jailani', '', '', 3, '', 1, '2026-04-17 14:34:23'),
(5, '005', 'Lucy', '', 'Felix-Sadiwa', '', '', 4, '', 1, '2026-04-17 14:34:43'),
(6, '006', 'Odon', '', 'Maravillas', '', '', 1, '', 1, '2026-04-17 14:35:53'),
(7, '007', 'Edwin', '', 'Arip', '', '', 1, '', 1, '2026-04-17 14:36:26'),
(8, '008', 'Jason', '', 'Catadman', '', '', 2, '', 1, '2026-04-17 14:36:41'),
(9, '009', 'Sherard Chris', '', 'Banquerigo', '', '', 2, '', 1, '2026-04-17 14:37:04'),
(10, '0010', 'Jhon Paul', '', 'Arip', '', '', 2, '', 1, '2026-04-17 14:37:25');

-- --------------------------------------------------------

--
-- Table structure for table `programs`
--

CREATE TABLE `programs` (
  `id` int(11) NOT NULL,
  `program_code` varchar(50) NOT NULL,
  `program_name` varchar(200) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `programs`
--

INSERT INTO `programs` (`id`, `program_code`, `program_name`, `department_id`, `created_at`) VALUES
(1, 'BSCS', 'Bachelor of Science in Computer Science', 1, '2026-04-17 12:06:34'),
(2, 'BSIT', 'Bachelor of Science in Information Technology', 2, '2026-04-17 12:07:18'),
(3, 'ACT-AD', 'Associate in Computer Technology - Applications Development', 3, '2026-04-17 12:07:47'),
(4, 'ACT-NT', 'Associate in Computer Technology - Networking', 3, '2026-04-17 12:08:01'),
(5, 'MIT', 'Master in Information Technology', 4, '2026-04-17 12:08:15');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(50) NOT NULL,
  `capacity` int(11) DEFAULT 50,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `room_name`, `capacity`, `created_at`) VALUES
(1, 'LR 1', 50, '2026-04-17 11:47:20'),
(2, 'LR 2', 50, '2026-04-17 11:47:26'),
(3, 'LR 3', 50, '2026-04-17 11:47:31'),
(4, 'LR 4', 50, '2026-04-17 11:47:36'),
(5, 'LAB 1', 40, '2026-04-17 11:47:40'),
(6, 'LAB 2', 40, '2026-04-17 11:47:45'),
(7, 'Gym', 50, '2026-04-26 05:07:36'),
(8, 'Field', 50, '2026-04-26 05:07:41');

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `schoolyear_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `class_mode` varchar(10) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `day_of_week` varchar(20) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `schoolyear_id`, `class_id`, `subject_id`, `class_mode`, `instructor_id`, `room_id`, `day_of_week`, `start_time`, `end_time`, `created_at`) VALUES
(4, 3, 6, 95, 'LEC', 6, NULL, 'Wednesday', '17:30:00', '20:30:00', '2026-04-18 13:07:21'),
(5, 3, 6, 95, 'LEC', 6, NULL, 'Monday', '17:30:00', '20:30:00', '2026-04-18 13:07:21'),
(6, 3, 6, 95, 'LEC', 6, NULL, 'Saturday', '13:00:00', '16:00:00', '2026-04-18 13:09:36'),
(7, 3, 6, 96, 'LEC', 5, NULL, 'Thursday', '17:30:00', '20:30:00', '2026-04-18 13:10:25'),
(8, 3, 6, 96, 'LEC', 5, NULL, 'Tuesday', '17:30:00', '20:30:00', '2026-04-18 13:10:25'),
(9, 3, 6, 96, 'LEC', 5, NULL, 'Saturday', '09:00:00', '12:00:00', '2026-04-18 13:12:56'),
(17, 3, 3, 34, 'LEC', 7, 1, 'Monday', '16:00:00', '19:00:00', '2026-04-18 13:31:05'),
(21, 3, 3, 34, 'LEC', 7, 1, 'Wednesday', '16:00:00', '19:00:00', '2026-04-18 13:42:15'),
(22, 3, 3, 34, 'LEC', 7, 1, 'Friday', '16:00:00', '19:00:00', '2026-04-18 13:42:15'),
(23, 3, 3, 32, 'LEC', 2, 2, 'Wednesday', '09:00:00', '12:00:00', '2026-04-18 13:44:53'),
(24, 3, 3, 32, 'LEC', 2, 2, 'Monday', '09:00:00', '12:00:00', '2026-04-18 13:44:53'),
(25, 3, 3, 32, 'LEC', 2, 2, 'Friday', '09:00:00', '12:00:00', '2026-04-18 13:44:53'),
(26, 3, 3, 33, 'LEC', 10, 1, 'Wednesday', '13:00:00', '16:00:00', '2026-04-18 13:55:11'),
(27, 3, 3, 33, 'LEC', 10, 1, 'Monday', '13:00:00', '16:00:00', '2026-04-18 13:55:11'),
(28, 3, 3, 33, 'LAB', 3, 7, 'Tuesday', '09:00:00', '12:00:00', '2026-04-18 13:55:50'),
(29, 3, 3, 33, 'LAB', 3, 5, 'Tuesday', '13:00:00', '16:00:00', '2026-04-18 13:55:50'),
(30, 3, 4, 50, 'LEC', 1, 2, 'Monday', '13:00:00', '16:00:00', '2026-04-18 14:33:56'),
(31, 3, 4, 50, 'LEC', 1, 2, 'Wednesday', '13:00:00', '16:00:00', '2026-04-18 14:33:56'),
(32, 3, 4, 50, 'LEC', 1, 2, 'Friday', '13:00:00', '16:00:00', '2026-04-18 14:33:57'),
(34, 3, 5, 50, 'LEC', 1, 2, 'Monday', '16:00:00', '19:00:00', '2026-04-18 14:34:57'),
(35, 3, 5, 50, 'LEC', 1, 2, 'Friday', '16:00:00', '19:00:00', '2026-04-18 14:34:57'),
(36, 3, 5, 50, 'LEC', 1, 2, 'Wednesday', '16:00:00', '19:00:00', '2026-04-18 14:34:57'),
(37, 3, 7, 145, 'LAB', 2, 6, 'Monday', '16:00:00', '19:00:00', '2026-04-18 14:44:39'),
(38, 3, 7, 145, 'LAB', 2, 6, 'Wednesday', '16:00:00', '19:00:00', '2026-04-18 14:44:39'),
(39, 3, 7, 145, 'LEC', 4, 4, 'Monday', '13:00:00', '16:00:00', '2026-04-18 14:48:11'),
(40, 3, 7, 145, 'LEC', 4, 4, 'Wednesday', '13:00:00', '16:00:00', '2026-04-18 14:48:11'),
(41, 3, 7, 144, 'LEC', 8, 1, 'Monday', '09:00:00', '12:00:00', '2026-04-18 14:49:36'),
(42, 3, 7, 144, 'LEC', 8, 1, 'Wednesday', '09:00:00', '12:00:00', '2026-04-18 14:49:36'),
(43, 3, 7, 144, 'LEC', 8, 1, 'Friday', '09:00:00', '12:00:00', '2026-04-18 14:49:36'),
(44, 3, 8, 144, 'LEC', 8, 3, 'Monday', '13:00:00', '16:00:00', '2026-04-18 14:53:17'),
(45, 3, 8, 144, 'LEC', 8, 3, 'Friday', '13:00:00', '16:00:00', '2026-04-18 14:53:17'),
(46, 3, 8, 144, 'LEC', 8, 3, 'Wednesday', '13:00:00', '16:00:00', '2026-04-18 14:53:17'),
(47, 3, 7, 145, 'LAB', 2, 6, 'Friday', '16:00:00', '19:00:00', '2026-04-18 15:22:32'),
(48, 3, 3, 33, 'LAB', 3, 5, 'Friday', '13:00:00', '16:00:00', '2026-04-18 15:37:48'),
(49, 3, 8, 145, 'LEC', 4, 3, 'Wednesday', '09:00:00', '12:00:00', '2026-04-18 15:43:05'),
(50, 3, 8, 145, 'LEC', 4, 3, 'Monday', '09:00:00', '12:00:00', '2026-04-18 15:43:05'),
(51, 3, 8, 145, 'LAB', 9, 5, 'Friday', '16:00:00', '19:00:00', '2026-04-18 15:44:02'),
(52, 3, 8, 145, 'LAB', 9, 5, 'Monday', '16:00:00', '19:00:00', '2026-04-18 15:44:02'),
(53, 3, 8, 145, 'LAB', 9, 5, 'Wednesday', '16:00:00', '19:00:00', '2026-04-18 15:44:02'),
(54, 3, 9, 144, 'LEC', 10, 4, 'Wednesday', '09:00:00', '12:00:00', '2026-04-18 15:46:25'),
(55, 3, 9, 144, 'LEC', 10, 4, 'Monday', '09:00:00', '12:00:00', '2026-04-18 15:46:25'),
(56, 3, 9, 144, 'LEC', 10, 4, 'Friday', '09:00:00', '12:00:00', '2026-04-18 15:46:25'),
(57, 3, 9, 145, 'LAB', 9, 5, 'Wednesday', '13:00:00', '16:00:00', '2026-04-18 15:52:40'),
(58, 3, 9, 145, 'LAB', 9, 5, 'Monday', '13:00:00', '16:00:00', '2026-04-18 15:52:40'),
(59, 3, 9, 145, 'LAB', 9, 6, 'Friday', '13:00:00', '16:00:00', '2026-04-18 15:53:37'),
(60, 3, 9, 145, 'LEC', 4, 4, 'Wednesday', '16:00:00', '19:00:00', '2026-04-18 15:56:55'),
(61, 3, 9, 145, 'LEC', 4, 4, 'Monday', '16:00:00', '19:00:00', '2026-04-18 15:56:55');

-- --------------------------------------------------------

--
-- Table structure for table `schoolyear`
--

CREATE TABLE `schoolyear` (
  `id` int(11) NOT NULL,
  `start_year` int(11) NOT NULL,
  `end_year` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schoolyear`
--

INSERT INTO `schoolyear` (`id`, `start_year`, `end_year`, `semester`, `is_active`, `created_at`) VALUES
(1, 2026, 2027, 1, 0, '2026-04-17 11:11:56'),
(2, 2026, 2027, 2, 0, '2026-04-17 11:12:11'),
(3, 2025, 2026, 3, 1, '2026-04-17 11:15:15');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `lec_credits` int(11) DEFAULT 0,
  `lab_credits` int(11) DEFAULT 0,
  `total_credits` int(11) GENERATED ALWAYS AS (`lec_credits` + `lab_credits`) STORED,
  `curriculum_id` int(11) DEFAULT NULL,
  `year_level` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `lec_credits`, `lab_credits`, `curriculum_id`, `year_level`, `semester`, `created_at`) VALUES
(1, 'CC 100', 'Introduction to Computing', 2, 1, 1, 1, 1, '2026-04-17 12:42:09'),
(2, 'CC 101', 'Computer Programming 1 (Fundamentals of Programming)', 3, 1, 1, 1, 1, '2026-04-17 12:42:09'),
(3, 'DS 111', 'Discrete Structures 1', 3, 0, 1, 1, 1, '2026-04-17 12:42:09'),
(4, 'CAS 101', 'Purposive Communication', 3, 0, 1, 1, 1, '2026-04-17 12:42:09'),
(5, 'MATH 100', 'Mathematics in the Modern World', 3, 0, 1, 1, 1, '2026-04-17 12:42:09'),
(6, 'HIST 100', 'Life and Works of Rizal', 3, 0, 1, 1, 1, '2026-04-17 12:42:09'),
(7, 'US 101', 'Understanding the Self', 3, 0, 1, 1, 1, '2026-04-17 12:42:09'),
(8, 'PATHFIT 1', 'Movement Competency Training', 2, 0, 1, 1, 1, '2026-04-17 12:42:09'),
(9, 'NSTP 1', 'NSTP 1', 3, 0, 1, 1, 1, '2026-04-17 12:42:09'),
(10, 'CC 102', 'Computer Programming 2 (Intermediate Programming)', 3, 1, 1, 1, 2, '2026-04-17 12:42:09'),
(11, 'OOP 112', 'Object-Oriented Programming', 2, 1, 1, 1, 2, '2026-04-17 12:42:09'),
(12, 'WD 114', 'Web Development 1', 2, 1, 1, 1, 2, '2026-04-17 12:42:09'),
(13, 'HCI 116', 'Human Computer Interaction', 3, 0, 1, 1, 2, '2026-04-17 12:42:09'),
(14, 'DS 118', 'Discrete Structures 2', 3, 0, 1, 1, 2, '2026-04-17 12:42:09'),
(15, 'HIST 101', 'Readings in Philippine History', 3, 0, 1, 1, 2, '2026-04-17 12:42:09'),
(16, 'STS 100', 'Science, Technology and Society', 3, 0, 1, 1, 2, '2026-04-17 12:42:09'),
(17, 'PATHFIT 2', 'Exercise-Based Fitness Activities', 2, 0, 1, 1, 2, '2026-04-17 12:42:09'),
(18, 'NSTP 2', 'NSTP 2', 3, 0, 1, 1, 2, '2026-04-17 12:42:09'),
(19, 'CC 103', 'Data Structures and Algorithms', 2, 1, 1, 2, 1, '2026-04-17 12:42:09'),
(20, 'CC 104', 'Information Management', 2, 1, 1, 2, 1, '2026-04-17 12:42:09'),
(21, 'MAD 121', 'Mobile Application Development', 2, 1, 1, 2, 1, '2026-04-17 12:42:09'),
(22, 'WD 123', 'Web Development 2', 2, 1, 1, 2, 1, '2026-04-17 12:42:09'),
(23, 'SIPP 125', 'Social Issues and Professional Practice', 3, 0, 1, 2, 1, '2026-04-17 12:42:09'),
(24, 'NC 127', 'Networks and Communications', 2, 1, 1, 2, 1, '2026-04-17 12:42:09'),
(25, 'PATHFIT 3', 'Sports', 2, 0, 1, 2, 1, '2026-04-17 12:42:09'),
(26, 'CC 105', 'Applications Development and Emerging Technologies', 2, 1, 1, 2, 2, '2026-04-17 12:42:09'),
(27, 'ACTINT 122', 'ACT Internship (320 hours)', 0, 6, 1, 2, 2, '2026-04-17 12:42:09'),
(28, 'AO 124', 'Architecture and Organization', 2, 1, 1, 2, 2, '2026-04-17 12:42:09'),
(29, 'PHILCON', 'Philippine Constitution', 3, 0, 1, 2, 2, '2026-04-17 12:42:09'),
(30, 'CW 101', 'The Contemporary World', 3, 0, 1, 2, 2, '2026-04-17 12:42:09'),
(31, 'PATHFIT 4', 'Outdoor and Adventure activities', 2, 0, 1, 2, 2, '2026-04-17 12:42:09'),
(32, 'AC 128', 'Algorithms and Complexity', 3, 0, 1, 2, 3, '2026-04-17 12:42:09'),
(33, 'PL 129', 'Programming Languages', 2, 1, 1, 2, 3, '2026-04-17 12:42:09'),
(34, 'STAT 120', 'Statistics for Computer Science', 3, 0, 1, 2, 3, '2026-04-17 12:42:09'),
(35, 'SE 131', 'Software Engineering 1', 2, 1, 1, 3, 1, '2026-04-17 12:42:09'),
(36, 'ADS 133', 'Advanced Database Systems', 2, 1, 1, 3, 1, '2026-04-17 12:42:09'),
(37, 'ATPL 135', 'Automata Theory and Formal Languages', 3, 0, 1, 3, 1, '2026-04-17 12:42:09'),
(38, 'OS 137', 'Operating Systems', 2, 1, 1, 3, 1, '2026-04-17 12:42:09'),
(39, 'CALC 139', 'Calculus for Computer Science', 3, 0, 1, 3, 1, '2026-04-17 12:42:09'),
(40, 'CSE 131', 'CS Elective 1', 2, 1, 1, 3, 1, '2026-04-17 12:42:09'),
(41, 'CSE 133', 'CS Elective 2', 2, 1, 1, 3, 1, '2026-04-17 12:42:09'),
(43, 'ES 130', 'Embedded Systems', 2, 1, 1, 3, 2, '2026-04-17 12:42:09'),
(44, 'SE 132', 'Software Engineering 2', 2, 1, 1, 3, 2, '2026-04-17 12:42:09'),
(45, 'IAS 138', 'Information Assurance and Security', 2, 0, 1, 3, 2, '2026-04-17 12:42:09'),
(46, 'TW 136', 'Technical Writing for Computer Science', 0, 1, 1, 3, 2, '2026-04-17 12:42:09'),
(47, 'CSE 132', 'CS Elective 3', 2, 1, 1, 3, 2, '2026-04-17 12:42:09'),
(48, 'SSP 103', 'Gender and Society', 3, 0, 1, 3, 2, '2026-04-17 12:42:09'),
(49, 'SSP 104', 'The Entrepreneurial Mind', 3, 0, 1, 3, 2, '2026-04-17 12:42:09'),
(50, 'THESIS 139', 'CS Thesis 1', 3, 0, 1, 3, 3, '2026-04-17 12:42:09'),
(51, 'THESIS 141', 'CS Thesis 2', 3, 0, 1, 4, 1, '2026-04-17 12:42:09'),
(52, 'A&H 100', 'Art Appreciation', 3, 0, 1, 4, 1, '2026-04-17 12:42:09'),
(53, 'CSPRAC 142', 'Industry Immersion / Practicum', 0, 3, 1, 4, 2, '2026-04-17 12:42:09'),
(55, 'MST 101', 'Environmental Science', 3, 0, 1, 3, 2, '2026-04-17 13:14:47'),
(56, 'CC 100', 'Introduction to Computing', 2, 1, 3, 1, 1, '2026-04-17 13:20:26'),
(57, 'CC 101', 'Computer Programming 1 (Fundamentals of Programming)', 3, 1, 3, 1, 1, '2026-04-17 13:20:26'),
(58, 'DS 111', 'Discrete Structures 1', 3, 0, 3, 1, 1, '2026-04-17 13:20:26'),
(59, 'CAS 101', 'Purposive Communication', 3, 0, 3, 1, 1, '2026-04-17 13:20:26'),
(60, 'MATH 100', 'Mathematics in the Modern World', 3, 0, 3, 1, 1, '2026-04-17 13:20:26'),
(61, 'HIST 100', 'Life and Works of Rizal', 3, 0, 3, 1, 1, '2026-04-17 13:20:26'),
(62, 'US 101', 'Understanding the Self', 3, 0, 3, 1, 1, '2026-04-17 13:20:26'),
(63, 'PATHFIT 1', 'Movement Competency Training', 2, 0, 3, 1, 1, '2026-04-17 13:20:26'),
(64, 'NSTP 1', 'NSTP 1', 3, 0, 3, 1, 1, '2026-04-17 13:20:26'),
(65, 'CC 102', 'Computer Programming 2 (Intermediate Programming)', 3, 1, 3, 1, 2, '2026-04-17 13:20:26'),
(66, 'OOP 112', 'Object-Oriented Programming', 2, 1, 3, 1, 2, '2026-04-17 13:20:26'),
(67, 'WD 114', 'Web Development 1', 2, 1, 3, 1, 2, '2026-04-17 13:20:26'),
(68, 'HCI 116', 'Human Computer Interaction', 3, 0, 3, 1, 2, '2026-04-17 13:20:26'),
(69, 'DS 118', 'Discrete Structures 2', 3, 0, 3, 1, 2, '2026-04-17 13:20:26'),
(70, 'HIST 101', 'Readings in Philippine History', 3, 0, 3, 1, 2, '2026-04-17 13:20:26'),
(71, 'STS 100', 'Science, Technology and Society', 3, 0, 3, 1, 2, '2026-04-17 13:20:26'),
(72, 'PATHFIT 2', 'Exercise-Based Fitness Activities', 2, 0, 3, 1, 2, '2026-04-17 13:20:26'),
(73, 'NSTP 2', 'NSTP 2', 3, 0, 3, 1, 2, '2026-04-17 13:20:26'),
(74, 'CC 103', 'Data Structures and Algorithms', 2, 1, 3, 2, 1, '2026-04-17 13:20:26'),
(75, 'CC 104', 'Information Management', 2, 1, 3, 2, 1, '2026-04-17 13:20:26'),
(76, 'MAD 121', 'Mobile Application Development', 2, 1, 3, 2, 1, '2026-04-17 13:20:26'),
(77, 'WD 123', 'Web Development 2', 2, 1, 3, 2, 1, '2026-04-17 13:20:26'),
(78, 'SIPP 125', 'Social Issues and Professional Practice', 3, 0, 3, 2, 1, '2026-04-17 13:20:26'),
(79, 'NC 127', 'Networks and Communications', 2, 1, 3, 2, 1, '2026-04-17 13:20:26'),
(80, 'PATHFIT 3', 'Sports', 2, 0, 3, 2, 1, '2026-04-17 13:20:26'),
(81, 'CC 105', 'Applications Development and Emerging Technologies', 2, 1, 3, 2, 2, '2026-04-17 13:20:26'),
(82, 'ACTINT 122', 'ACT Internship (320 hours)', 0, 6, 3, 2, 2, '2026-04-17 13:20:26'),
(83, 'AO 124', 'Architecture and Organization', 2, 1, 3, 2, 2, '2026-04-17 13:20:26'),
(84, 'PHILCON', 'Philippine Constitution', 3, 0, 3, 2, 2, '2026-04-17 13:20:26'),
(85, 'CW 101', 'The Contemporary World', 3, 0, 3, 2, 2, '2026-04-17 13:20:26'),
(86, 'PATHFIT 4', 'Outdoor and Adventure activities', 2, 0, 3, 2, 2, '2026-04-17 13:20:26'),
(87, 'ETHICS 101', 'Ethics', 3, 0, 1, 4, 2, '2026-04-17 13:46:48'),
(88, 'MIT201', 'Advanced Operating Systems and Networking', 3, 0, 5, 1, 1, '2026-04-18 09:40:53'),
(89, 'MIT202', 'Advanced System Design and Implementation', 3, 0, 5, 1, 1, '2026-04-18 09:40:53'),
(90, 'MIT203', 'Advanced Database Systems', 3, 0, 5, 1, 1, '2026-04-18 09:40:53'),
(91, 'MIT204', 'Technology and Project Management', 3, 0, 5, 1, 1, '2026-04-18 09:40:53'),
(92, 'MIT205', 'Systems Development', 3, 0, 5, 1, 2, '2026-04-18 09:40:53'),
(93, 'MIT206', 'Information Management', 3, 0, 5, 1, 2, '2026-04-18 09:40:53'),
(94, 'MIT207', 'Interactive Systems', 3, 0, 5, 1, 2, '2026-04-18 09:40:53'),
(95, 'MIT208', 'Intelligent Systems', 3, 0, 5, 1, 3, '2026-04-18 09:40:53'),
(96, 'MIT209', 'Data Analytics', 3, 0, 5, 1, 3, '2026-04-18 09:40:53'),
(97, 'CAPSTONE1', 'Capstone Project 1', 3, 0, 5, 2, 1, '2026-04-18 09:40:53'),
(98, 'CAPSTONE2', 'Capstone Project 2', 3, 0, 5, 2, 2, '2026-04-18 09:40:53'),
(99, 'MITCC1', 'Cognate', 3, 0, 5, 1, 2, '2026-04-18 09:42:24'),
(100, 'CC100', 'Introduction to Computing', 2, 1, 2, 1, 1, '2026-04-18 09:45:12'),
(101, 'CC101', 'Computer Programming 1 (Fundamentals of Programming)', 2, 1, 2, 1, 1, '2026-04-18 09:45:12'),
(102, 'CAS101', 'Purposive Communication', 3, 0, 2, 1, 1, '2026-04-18 09:45:12'),
(103, 'MATH100', 'Mathematics in the Modern World', 3, 0, 2, 1, 1, '2026-04-18 09:45:12'),
(104, 'US101', 'Understanding the Self', 3, 0, 2, 1, 1, '2026-04-18 09:45:12'),
(105, 'GE1', 'GE Elective 1', 3, 0, 2, 1, 1, '2026-04-18 09:45:12'),
(106, 'PATHFIT1', 'Movement Competency Training', 2, 0, 2, 1, 1, '2026-04-18 09:45:12'),
(107, 'NSTP1', 'NSTP 1', 3, 0, 2, 1, 1, '2026-04-18 09:45:12'),
(108, 'FIL101', 'Wika, Kultura, at Mapayapang Lipunan', 3, 0, 2, 1, 1, '2026-04-18 09:45:12'),
(109, 'IT121', 'Discrete Mathematics', 3, 0, 2, 1, 2, '2026-04-18 09:45:12'),
(110, 'IT122', 'Introduction to Human-Computer Interaction', 2, 1, 2, 1, 2, '2026-04-18 09:45:12'),
(111, 'CC102', 'Computer Programming 2 (Intermediate Programming)', 2, 1, 2, 1, 2, '2026-04-18 09:45:12'),
(112, 'HIST100', 'Life and Works of Rizal', 3, 0, 2, 1, 2, '2026-04-18 09:45:12'),
(113, 'AH100', 'Art Appreciation', 3, 0, 2, 1, 2, '2026-04-18 09:45:12'),
(114, 'GE2', 'GE Elective 2', 3, 0, 2, 1, 2, '2026-04-18 09:45:12'),
(115, 'PATHFIT2', 'Exercise-based Fitness Activities', 2, 0, 2, 1, 2, '2026-04-18 09:45:12'),
(116, 'NSTP2', 'NSTP 2', 3, 0, 2, 1, 2, '2026-04-18 09:45:12'),
(117, 'PHILCON', 'Philippine Constitution', 3, 0, 2, 1, 2, '2026-04-18 09:45:12'),
(118, 'ITE1', 'IT Elective 1', 2, 1, 2, 2, 1, '2026-04-18 09:45:12'),
(119, 'IT212', 'Quantitative Methods', 3, 0, 2, 2, 1, '2026-04-18 09:45:12'),
(120, 'IT213', 'Social and Professional Practice', 3, 0, 2, 2, 1, '2026-04-18 09:45:12'),
(121, 'CC103', 'Data Structures and Algorithms', 2, 1, 2, 2, 1, '2026-04-18 09:45:12'),
(122, 'CC104', 'Information Management', 2, 1, 2, 2, 1, '2026-04-18 09:45:12'),
(123, 'HIST101', 'Readings in Philippine History', 3, 0, 2, 2, 1, '2026-04-18 09:45:12'),
(124, 'STS100', 'Science, Technology and Society', 3, 0, 2, 2, 1, '2026-04-18 09:45:12'),
(125, 'PATHFIT3', 'PATHFit 3', 2, 0, 2, 2, 1, '2026-04-18 09:45:12'),
(126, 'IT221', 'Integrative Programming and Technologies 1', 2, 1, 2, 2, 2, '2026-04-18 09:45:12'),
(127, 'IT222', 'Networking 1', 2, 1, 2, 2, 2, '2026-04-18 09:45:12'),
(128, 'ITE2', 'IT Elective 2', 2, 1, 2, 2, 2, '2026-04-18 09:45:12'),
(129, 'IT224', 'Mobile Computing', 2, 1, 2, 2, 2, '2026-04-18 09:45:12'),
(130, 'IT225', 'Advanced Database Systems', 2, 1, 2, 2, 2, '2026-04-18 09:45:12'),
(131, 'CW101', 'The Contemporary World', 3, 0, 2, 2, 2, '2026-04-18 09:45:12'),
(132, 'ETHICS101', 'Ethics', 3, 0, 2, 2, 2, '2026-04-18 09:45:12'),
(133, 'PATHFIT4', 'PATHFit 4', 2, 0, 2, 2, 2, '2026-04-18 09:45:12'),
(134, 'CC105', 'Application Development and Emerging Technologies', 2, 1, 2, 3, 1, '2026-04-18 09:45:12'),
(135, 'IT311', 'Networking 2', 2, 1, 2, 3, 1, '2026-04-18 09:45:12'),
(136, 'IT312', 'Systems Integration and Architecture 1', 2, 1, 2, 3, 1, '2026-04-18 09:45:12'),
(137, 'ITE3', 'IT Elective 3', 2, 1, 2, 3, 1, '2026-04-18 09:45:12'),
(138, 'IT314', 'Data Analytics', 2, 1, 2, 3, 1, '2026-04-18 09:45:12'),
(139, 'IT321', 'Internet of Things', 2, 1, 2, 3, 2, '2026-04-18 09:45:12'),
(140, 'IT322', 'Machine Learning', 2, 1, 2, 3, 2, '2026-04-18 09:45:12'),
(141, 'IT323', 'Information Assurance and Security 1', 2, 1, 2, 3, 2, '2026-04-18 09:45:12'),
(142, 'ITE4', 'IT Elective 4', 2, 1, 2, 3, 2, '2026-04-18 09:45:12'),
(143, 'GE3', 'GE Elective 3', 3, 0, 2, 3, 2, '2026-04-18 09:45:12'),
(144, 'IT331', 'Capstone Project and Research 1', 3, 0, 2, 3, 3, '2026-04-18 09:45:12'),
(145, 'IT332', 'Information Assurance and Security 2', 2, 1, 2, 3, 3, '2026-04-18 09:45:12'),
(146, 'IT411', 'Capstone Project and Research 2', 3, 0, 2, 4, 1, '2026-04-18 09:45:12'),
(147, 'IT412', 'Systems Administration and Maintenance', 2, 1, 2, 4, 1, '2026-04-18 09:45:12'),
(148, 'IT413', 'Cybersecurity', 2, 1, 2, 4, 1, '2026-04-18 09:45:12'),
(149, 'IT421', 'Cloud Computing', 2, 1, 2, 4, 2, '2026-04-18 09:45:12'),
(150, 'IT422', 'Practicum / Industry Immersion', 0, 6, 2, 4, 2, '2026-04-18 09:45:12'),
(151, 'CC100', 'Introduction to Computing', 2, 1, 4, 1, 1, '2026-04-18 10:10:17'),
(152, 'CC101', 'Computer Programming 1', 3, 1, 4, 1, 1, '2026-04-18 10:10:17'),
(153, 'ACT115', 'Platform Technologies', 2, 1, 4, 1, 1, '2026-04-18 10:10:17'),
(154, 'ACT116', 'Human Computer Interaction', 2, 1, 4, 1, 1, '2026-04-18 10:10:17'),
(155, 'CAS101', 'Purposive Communication', 3, 0, 4, 1, 1, '2026-04-18 10:10:17'),
(156, 'MATH100', 'Mathematics in the Modern World', 3, 0, 4, 1, 1, '2026-04-18 10:10:17'),
(157, 'NSTP1', 'NSTP 1', 3, 0, 4, 1, 1, '2026-04-18 10:10:17'),
(158, 'PATHFIT1', 'Movement and Competency Training', 2, 0, 4, 1, 1, '2026-04-18 10:10:17'),
(159, 'CC102', 'Computer Programming 2', 3, 1, 4, 1, 2, '2026-04-18 10:10:17'),
(160, 'ACT122', 'Data Communications and Networking 1', 2, 1, 4, 1, 2, '2026-04-18 10:10:17'),
(161, 'ACT123', 'Systems Administration', 2, 1, 4, 1, 2, '2026-04-18 10:10:17'),
(162, 'ACT124', 'Object-Oriented Programming', 2, 1, 4, 1, 2, '2026-04-18 10:10:17'),
(163, 'STS100', 'Science, Technology and Society', 3, 0, 4, 1, 2, '2026-04-18 10:10:17'),
(164, 'ETHICS101', 'Ethics', 3, 0, 4, 1, 2, '2026-04-18 10:10:17'),
(165, 'NSTP2', 'NSTP 2', 3, 0, 4, 1, 2, '2026-04-18 10:10:17'),
(166, 'PATHFIT2', 'Exercise-based Fitness Activities', 2, 0, 4, 1, 2, '2026-04-18 10:10:17'),
(167, 'CC103', 'Data Structures and Algorithms', 2, 1, 4, 2, 1, '2026-04-18 10:10:17'),
(168, 'CC104', 'Information Management 1', 2, 1, 4, 2, 1, '2026-04-18 10:10:17'),
(169, 'ACT213', 'Professional Issues in Computing', 3, 0, 4, 2, 1, '2026-04-18 10:10:17'),
(170, 'ACT214', 'Data Communications and Networking 2', 2, 1, 4, 2, 1, '2026-04-18 10:10:17'),
(171, 'ACT215', 'Network Administration', 2, 1, 4, 2, 1, '2026-04-18 10:10:17'),
(172, 'HIST100', 'Life and Works of Rizal', 3, 0, 4, 2, 1, '2026-04-18 10:10:17'),
(173, 'PHILCON', 'Philippine Constitution', 3, 0, 4, 2, 1, '2026-04-18 10:10:17'),
(174, 'PATHFIT3', 'PATHFit 3', 2, 0, 4, 2, 1, '2026-04-18 10:10:17'),
(175, 'ACT221', 'Internship', 0, 6, 4, 2, 2, '2026-04-18 10:10:17'),
(176, 'ACT222', 'Network Security', 2, 1, 4, 2, 2, '2026-04-18 10:10:17'),
(177, 'PATHFIT4', 'PATHFit 4', 2, 0, 4, 2, 2, '2026-04-18 10:10:17'),
(178, 'US101', 'Understanding the Self', 3, 0, 4, 3, 1, '2026-04-18 10:10:17'),
(179, 'GE1', 'GE Elective 1', 3, 0, 4, 3, 1, '2026-04-18 10:10:17'),
(180, 'FIL101', 'Wika, Kultura, at Mapayapang Lipunan', 3, 0, 4, 3, 1, '2026-04-18 10:10:17'),
(181, 'ITE1', 'IT Elective 1', 2, 1, 4, 3, 1, '2026-04-18 10:10:17'),
(182, 'HIST101', 'Readings in Philippine History', 3, 0, 4, 3, 1, '2026-04-18 10:10:17'),
(183, 'IT121', 'Discrete Mathematics', 3, 0, 4, 3, 2, '2026-04-18 10:10:17'),
(184, 'AH100', 'Art Appreciation', 3, 0, 4, 3, 2, '2026-04-18 10:10:17'),
(185, 'GE2', 'GE Elective 2', 2, 1, 4, 3, 2, '2026-04-18 10:10:17'),
(186, 'IT221', 'Integrative Programming and Technologies 1', 2, 1, 4, 3, 2, '2026-04-18 10:10:17'),
(187, 'ITE2', 'IT Elective 2', 2, 1, 4, 3, 2, '2026-04-18 10:10:17'),
(188, 'IT224', 'Mobile Computing', 2, 1, 4, 3, 2, '2026-04-18 10:10:17'),
(189, 'IT225', 'Advanced Database Systems', 2, 1, 4, 3, 2, '2026-04-18 10:10:17'),
(190, 'CW101', 'Contemporary World', 3, 0, 4, 3, 2, '2026-04-18 10:10:17'),
(191, 'IT331', 'Capstone Project and Research 1', 3, 0, 4, 3, 3, '2026-04-18 10:10:17'),
(192, 'IT332', 'Information Assurance and Security 2', 2, 1, 4, 3, 3, '2026-04-18 10:10:17'),
(193, 'IT212', 'Quantitative Methods', 3, 0, 4, 4, 1, '2026-04-18 10:10:17'),
(194, 'CC105', 'Applications Development and Emerging Technologies', 2, 1, 4, 4, 1, '2026-04-18 10:10:17'),
(195, 'IT312', 'Systems Integration and Architecture 1', 2, 1, 4, 4, 1, '2026-04-18 10:10:17'),
(196, 'ITE3', 'IT Elective 3', 2, 1, 4, 4, 1, '2026-04-18 10:10:17'),
(197, 'IT314', 'Data Analytics', 2, 1, 4, 4, 1, '2026-04-18 10:10:17'),
(198, 'IT411', 'Capstone Project and Research 2', 3, 0, 4, 4, 1, '2026-04-18 10:10:17'),
(199, 'IT413', 'Cybersecurity', 2, 1, 4, 4, 1, '2026-04-18 10:10:17'),
(200, 'IT321', 'Internet of Things', 2, 1, 4, 4, 2, '2026-04-18 10:10:17'),
(201, 'IT322', 'Machine Learning', 2, 1, 4, 4, 2, '2026-04-18 10:10:17'),
(202, 'ITE4', 'IT Elective 4', 2, 1, 4, 4, 2, '2026-04-18 10:10:17'),
(203, 'GE3', 'GE Elective 3', 3, 0, 4, 4, 2, '2026-04-18 10:10:17'),
(204, 'IT421', 'Cloud Computing', 2, 1, 4, 4, 2, '2026-04-18 10:10:17'),
(205, 'IT422', 'Practicum / Industry Immersion', 0, 6, 4, 4, 2, '2026-04-18 10:10:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schoolyear_id` (`schoolyear_id`),
  ADD KEY `curriculum_id` (`curriculum_id`);

--
-- Indexes for table `college_officials`
--
ALTER TABLE `college_officials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_college_officials_department` (`department_id`);

--
-- Indexes for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department_code` (`department_code`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `instructor_code` (`instructor_code`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `programs`
--
ALTER TABLE `programs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `program_code` (`program_code`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_name` (`room_name`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schoolyear_id` (`schoolyear_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `instructor_id` (`instructor_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `schoolyear`
--
ALTER TABLE `schoolyear`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_curriculum_subject_code` (`curriculum_id`,`subject_code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `college_officials`
--
ALTER TABLE `college_officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `curriculum`
--
ALTER TABLE `curriculum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `programs`
--
ALTER TABLE `programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `schoolyear`
--
ALTER TABLE `schoolyear`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `class_ibfk_1` FOREIGN KEY (`schoolyear_id`) REFERENCES `schoolyear` (`id`),
  ADD CONSTRAINT `class_ibfk_2` FOREIGN KEY (`curriculum_id`) REFERENCES `curriculum` (`id`);

--
-- Constraints for table `college_officials`
--
ALTER TABLE `college_officials`
  ADD CONSTRAINT `fk_college_officials_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD CONSTRAINT `curriculum_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`);

--
-- Constraints for table `instructors`
--
ALTER TABLE `instructors`
  ADD CONSTRAINT `instructors_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `programs`
--
ALTER TABLE `programs`
  ADD CONSTRAINT `programs_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`);

--
-- Constraints for table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`schoolyear_id`) REFERENCES `schoolyear` (`id`),
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`),
  ADD CONSTRAINT `schedules_ibfk_4` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `schedules_ibfk_5` FOREIGN KEY (`class_id`) REFERENCES `class` (`id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`curriculum_id`) REFERENCES `curriculum` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
