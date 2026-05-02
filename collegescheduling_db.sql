-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2026 at 10:28 AM
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

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` int(11) NOT NULL,
  `student_no` varchar(50) NOT NULL,
  `fn` varchar(100) NOT NULL,
  `mn` varchar(100) DEFAULT NULL,
  `ln` varchar(100) NOT NULL,
  `program_id` int(11) NOT NULL,
  `curriculum_id` int(11) NOT NULL,
  `gwa` decimal(6,5) DEFAULT NULL,
  `schoolyear_id` int(11) DEFAULT NULL,
  `criteria_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicants`
--

INSERT INTO `applicants` (`id`, `student_no`, `fn`, `mn`, `ln`, `program_id`, `curriculum_id`, `gwa`, `schoolyear_id`, `criteria_id`, `created_at`) VALUES
(9, '005', 'ALLEN', NULL, 'TAN', 1, 6, 1.59603, 4, 1, '2026-05-02 06:17:37'),
(10, '005', 'ALLEN', NULL, 'TAN', 1, 6, 1.65534, 4, 2, '2026-05-02 06:17:37'),
(11, '006', 'KYLE', NULL, 'ALAS-AS', 1, 6, 1.39570, 4, 1, '2026-05-02 07:01:06'),
(12, '006', 'KYLE', NULL, 'ALAS-AS', 1, 6, 1.40534, 4, 2, '2026-05-02 07:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `applicant_grades`
--

CREATE TABLE `applicant_grades` (
  `id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `grade` varchar(10) DEFAULT NULL,
  `remarks` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applicant_grades`
--

INSERT INTO `applicant_grades` (`id`, `applicant_id`, `subject_id`, `grade`, `remarks`, `created_at`) VALUES
(1, 9, 207, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(2, 9, 208, '2.00', 'PASSED', '2026-05-02 06:40:11'),
(3, 9, 209, '2.00', 'PASSED', '2026-05-02 06:40:11'),
(4, 9, 210, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(5, 9, 211, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(6, 9, 212, '1.50', 'PASSED', '2026-05-02 06:40:11'),
(7, 9, 213, '1.00', 'PASSED', '2026-05-02 06:40:11'),
(8, 9, 214, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(9, 9, 215, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(10, 9, 216, '1.75', 'PASSED', '2026-05-02 06:40:11'),
(11, 9, 217, '2.25', 'PASSED', '2026-05-02 06:40:11'),
(12, 9, 218, '2.00', 'PASSED', '2026-05-02 06:40:11'),
(13, 9, 219, '1.50', 'PASSED', '2026-05-02 06:40:11'),
(14, 9, 220, '1.50', 'PASSED', '2026-05-02 06:40:11'),
(15, 9, 221, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(16, 9, 222, '1.00', 'PASSED', '2026-05-02 06:40:11'),
(17, 9, 223, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(18, 9, 224, '1.75', 'PASSED', '2026-05-02 06:40:11'),
(19, 9, 225, '2.00', 'PASSED', '2026-05-02 06:40:11'),
(20, 9, 226, '1.50', 'PASSED', '2026-05-02 06:40:11'),
(21, 9, 227, '1.75', 'PASSED', '2026-05-02 06:40:11'),
(22, 9, 228, '2.00', 'PASSED', '2026-05-02 06:40:11'),
(23, 9, 229, '1.00', 'PASSED', '2026-05-02 06:40:11'),
(24, 9, 230, '1.75', 'PASSED', '2026-05-02 06:40:11'),
(25, 9, 231, '1.50', 'PASSED', '2026-05-02 06:40:11'),
(26, 9, 232, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(27, 9, 233, '1.50', 'PASSED', '2026-05-02 06:40:11'),
(28, 9, 234, '1.75', 'PASSED', '2026-05-02 06:40:11'),
(29, 9, 235, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(30, 9, 236, '1.75', 'PASSED', '2026-05-02 06:40:11'),
(31, 9, 237, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(32, 9, 238, '1.75', 'PASSED', '2026-05-02 06:40:11'),
(33, 9, 239, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(34, 9, 240, '2.50', 'PASSED', '2026-05-02 06:40:11'),
(35, 9, 241, '1.50', 'PASSED', '2026-05-02 06:40:11'),
(36, 9, 242, '1.75', 'PASSED', '2026-05-02 06:40:11'),
(37, 9, 243, '1.25', 'PASSED', '2026-05-02 06:40:11'),
(38, 9, 244, '2.00', 'PASSED', '2026-05-02 06:40:12'),
(39, 9, 245, '1.75', 'PASSED', '2026-05-02 06:40:12'),
(40, 9, 247, '2.25', 'PASSED', '2026-05-02 06:40:12'),
(41, 9, 246, '1.75', 'PASSED', '2026-05-02 06:40:12'),
(42, 9, 248, '1.75', 'PASSED', '2026-05-02 06:40:12'),
(43, 9, 249, '1.00', 'PASSED', '2026-05-02 06:40:12'),
(44, 9, 250, '1.75', 'PASSED', '2026-05-02 06:40:12'),
(45, 9, 251, '1.75', 'PASSED', '2026-05-02 06:40:12'),
(46, 9, 252, '1.50', 'PASSED', '2026-05-02 06:40:12'),
(47, 9, 253, '1.00', 'PASSED', '2026-05-02 06:40:12'),
(48, 9, 254, '1.00', 'PASSED', '2026-05-02 06:40:12'),
(49, 9, 255, '2.25', 'PASSED', '2026-05-02 06:40:12'),
(50, 9, 256, '1.50', 'PASSED', '2026-05-02 06:40:12'),
(51, 9, 257, '1.50', 'PASSED', '2026-05-02 06:40:12'),
(52, 9, 258, '1.25', 'PASSED', '2026-05-02 06:40:12'),
(521, 10, 207, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(522, 10, 208, '2.00', 'PASSED', '2026-05-02 06:56:40'),
(523, 10, 209, '2.00', 'PASSED', '2026-05-02 06:56:40'),
(524, 10, 210, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(525, 10, 211, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(526, 10, 212, '1.50', 'PASSED', '2026-05-02 06:56:40'),
(527, 10, 213, '1.00', 'PASSED', '2026-05-02 06:56:40'),
(528, 10, 214, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(529, 10, 215, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(530, 10, 216, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(531, 10, 217, '2.25', 'PASSED', '2026-05-02 06:56:40'),
(532, 10, 218, '2.00', 'PASSED', '2026-05-02 06:56:40'),
(533, 10, 219, '1.50', 'PASSED', '2026-05-02 06:56:40'),
(534, 10, 220, '1.50', 'PASSED', '2026-05-02 06:56:40'),
(535, 10, 221, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(536, 10, 222, '1.00', 'PASSED', '2026-05-02 06:56:40'),
(537, 10, 223, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(538, 10, 224, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(539, 10, 225, '2.00', 'PASSED', '2026-05-02 06:56:40'),
(540, 10, 226, '1.50', 'PASSED', '2026-05-02 06:56:40'),
(541, 10, 227, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(542, 10, 228, '2.00', 'PASSED', '2026-05-02 06:56:40'),
(543, 10, 229, '1.00', 'PASSED', '2026-05-02 06:56:40'),
(544, 10, 230, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(545, 10, 231, '1.50', 'PASSED', '2026-05-02 06:56:40'),
(546, 10, 232, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(547, 10, 233, '1.50', 'PASSED', '2026-05-02 06:56:40'),
(548, 10, 234, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(549, 10, 235, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(550, 10, 236, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(551, 10, 237, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(552, 10, 238, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(553, 10, 239, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(554, 10, 240, '2.50', 'PASSED', '2026-05-02 06:56:40'),
(555, 10, 241, '1.50', 'PASSED', '2026-05-02 06:56:40'),
(556, 10, 242, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(557, 10, 243, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(558, 10, 244, '2.00', 'PASSED', '2026-05-02 06:56:40'),
(559, 10, 245, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(560, 10, 247, '2.25', 'PASSED', '2026-05-02 06:56:40'),
(561, 10, 246, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(562, 10, 248, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(563, 10, 249, '1.00', 'PASSED', '2026-05-02 06:56:40'),
(564, 10, 250, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(565, 10, 251, '1.75', 'PASSED', '2026-05-02 06:56:40'),
(566, 10, 252, '1.50', 'PASSED', '2026-05-02 06:56:40'),
(567, 10, 253, '1.00', 'PASSED', '2026-05-02 06:56:40'),
(568, 10, 254, '1.00', 'PASSED', '2026-05-02 06:56:40'),
(569, 10, 255, '2.25', 'PASSED', '2026-05-02 06:56:40'),
(570, 10, 256, '1.50', 'PASSED', '2026-05-02 06:56:40'),
(571, 10, 257, '1.50', 'PASSED', '2026-05-02 06:56:40'),
(572, 10, 258, '1.25', 'PASSED', '2026-05-02 06:56:40'),
(573, 11, 207, '1.75', 'PASSED', '2026-05-02 07:04:25'),
(574, 11, 208, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(575, 11, 209, '1.75', 'PASSED', '2026-05-02 07:04:25'),
(576, 11, 210, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(577, 11, 211, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(578, 11, 212, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(579, 11, 213, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(580, 11, 214, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(581, 11, 215, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(582, 11, 216, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(583, 11, 217, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(584, 11, 218, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(585, 11, 219, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(586, 11, 220, '1.50', 'PASSED', '2026-05-02 07:04:25'),
(587, 11, 221, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(588, 11, 222, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(589, 11, 223, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(590, 11, 224, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(591, 11, 225, '1.50', 'PASSED', '2026-05-02 07:04:25'),
(592, 11, 226, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(593, 11, 227, '1.75', 'PASSED', '2026-05-02 07:04:25'),
(594, 11, 228, '2.00', 'PASSED', '2026-05-02 07:04:25'),
(595, 11, 229, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(596, 11, 230, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(597, 11, 231, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(598, 11, 232, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(599, 11, 233, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(600, 11, 234, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(601, 11, 235, '1.50', 'PASSED', '2026-05-02 07:04:25'),
(602, 11, 236, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(603, 11, 237, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(604, 11, 238, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(605, 11, 239, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(606, 11, 240, '2.00', 'PASSED', '2026-05-02 07:04:25'),
(607, 11, 241, '1.50', 'PASSED', '2026-05-02 07:04:25'),
(608, 11, 242, '1.50', 'PASSED', '2026-05-02 07:04:25'),
(609, 11, 243, '1.50', 'PASSED', '2026-05-02 07:04:25'),
(610, 11, 244, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(611, 11, 245, '1.50', 'PASSED', '2026-05-02 07:04:25'),
(612, 11, 247, '1.50', 'PASSED', '2026-05-02 07:04:25'),
(613, 11, 246, '2.75', 'PASSED', '2026-05-02 07:04:25'),
(614, 11, 248, '1.75', 'PASSED', '2026-05-02 07:04:25'),
(615, 11, 249, '2.00', 'PASSED', '2026-05-02 07:04:25'),
(616, 11, 250, '1.75', 'PASSED', '2026-05-02 07:04:25'),
(617, 11, 251, '1.75', 'PASSED', '2026-05-02 07:04:25'),
(618, 11, 252, '1.50', 'PASSED', '2026-05-02 07:04:25'),
(619, 11, 253, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(620, 11, 254, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(621, 11, 255, '2.00', 'PASSED', '2026-05-02 07:04:25'),
(622, 11, 256, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(623, 11, 257, '1.25', 'PASSED', '2026-05-02 07:04:25'),
(624, 11, 258, '1.00', 'PASSED', '2026-05-02 07:04:25'),
(625, 12, 207, '1.75', 'PASSED', '2026-05-02 07:04:41'),
(626, 12, 208, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(627, 12, 209, '1.75', 'PASSED', '2026-05-02 07:04:41'),
(628, 12, 210, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(629, 12, 211, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(630, 12, 212, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(631, 12, 213, '1.00', 'PASSED', '2026-05-02 07:04:41'),
(632, 12, 214, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(633, 12, 215, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(634, 12, 216, '1.00', 'PASSED', '2026-05-02 07:04:41'),
(635, 12, 217, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(636, 12, 218, '1.00', 'PASSED', '2026-05-02 07:04:41'),
(637, 12, 219, '1.00', 'PASSED', '2026-05-02 07:04:41'),
(638, 12, 220, '1.50', 'PASSED', '2026-05-02 07:04:41'),
(639, 12, 221, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(640, 12, 222, '1.00', 'PASSED', '2026-05-02 07:04:41'),
(641, 12, 223, '1.00', 'PASSED', '2026-05-02 07:04:41'),
(642, 12, 224, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(643, 12, 225, '1.50', 'PASSED', '2026-05-02 07:04:41'),
(644, 12, 226, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(645, 12, 227, '1.75', 'PASSED', '2026-05-02 07:04:41'),
(646, 12, 228, '2.00', 'PASSED', '2026-05-02 07:04:41'),
(647, 12, 229, '1.00', 'PASSED', '2026-05-02 07:04:41'),
(648, 12, 230, '1.00', 'PASSED', '2026-05-02 07:04:41'),
(649, 12, 231, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(650, 12, 232, '1.00', 'PASSED', '2026-05-02 07:04:41'),
(651, 12, 233, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(652, 12, 234, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(653, 12, 235, '1.50', 'PASSED', '2026-05-02 07:04:41'),
(654, 12, 236, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(655, 12, 237, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(656, 12, 238, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(657, 12, 239, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(658, 12, 240, '2.00', 'PASSED', '2026-05-02 07:04:41'),
(659, 12, 241, '1.50', 'PASSED', '2026-05-02 07:04:41'),
(660, 12, 242, '1.50', 'PASSED', '2026-05-02 07:04:41'),
(661, 12, 243, '1.50', 'PASSED', '2026-05-02 07:04:41'),
(662, 12, 244, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(663, 12, 245, '1.50', 'PASSED', '2026-05-02 07:04:41'),
(664, 12, 247, '1.50', 'PASSED', '2026-05-02 07:04:41'),
(665, 12, 246, '2.75', 'PASSED', '2026-05-02 07:04:41'),
(666, 12, 248, '1.75', 'PASSED', '2026-05-02 07:04:41'),
(667, 12, 249, '2.00', 'PASSED', '2026-05-02 07:04:41'),
(668, 12, 250, '1.75', 'PASSED', '2026-05-02 07:04:41'),
(669, 12, 251, '1.75', 'PASSED', '2026-05-02 07:04:41'),
(670, 12, 252, '1.50', 'PASSED', '2026-05-02 07:04:41'),
(671, 12, 253, '1.00', 'PASSED', '2026-05-02 07:04:41'),
(672, 12, 254, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(673, 12, 255, '2.00', 'PASSED', '2026-05-02 07:04:41'),
(674, 12, 256, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(675, 12, 257, '1.25', 'PASSED', '2026-05-02 07:04:41'),
(676, 12, 258, '1.00', 'PASSED', '2026-05-02 07:04:41');

-- --------------------------------------------------------

--
-- Table structure for table `awards_criteria`
--

CREATE TABLE `awards_criteria` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `schoolyear_id` int(11) NOT NULL,
  `excluded_subjects` varchar(255) DEFAULT NULL,
  `gwa_cutoff` decimal(6,5) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `awards_criteria`
--

INSERT INTO `awards_criteria` (`id`, `title`, `schoolyear_id`, `excluded_subjects`, `gwa_cutoff`, `created_at`) VALUES
(1, 'Academic Achiever Awards', 4, 'NSTP 1,NSTP 2', 1.90000, '2026-05-02 05:54:55'),
(2, 'Program Academic Excellence Achiever Awards', 4, 'CAS 101,MATH 100,US 101,FIL 101,PE 101,NSTP 1,EUTH A,CW 101,STS 100,FIL 102,PE 102,NSTP 2,EUTH B,LIT 101,PE 103,PE 104,ETHICS 101,HIST 100,HIST 101,A&H 100', 1.60000, '2026-05-02 06:01:13');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` int(11) NOT NULL,
  `schoolyear_id` int(11) NOT NULL,
  `curriculum_id` int(11) DEFAULT NULL,
  `section_name` varchar(50) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `schoolyear_id`, `curriculum_id`, `section_name`, `year_level`, `created_at`) VALUES
(1, 1, 3, 'ACT-AD 1A', 1, '2026-04-18 09:30:40'),
(2, 1, 3, 'ACT-AD 2A', 2, '2026-04-18 09:31:38'),
(3, 3, 1, 'BSCS 2A', 2, '2026-04-18 09:34:12'),
(4, 3, 1, 'BSCS 3A', 3, '2026-04-18 09:35:11'),
(5, 3, 1, 'BSCS 3B', 3, '2026-04-18 09:36:22'),
(6, 3, 5, 'MIT 1A', 1, '2026-04-18 10:12:39'),
(7, 3, 2, 'BSIT 3A', 3, '2026-04-18 10:12:51'),
(8, 3, 2, 'BSIT 3B', 3, '2026-04-18 10:13:01'),
(9, 3, 2, 'BSIT 3C', 3, '2026-04-18 10:13:10'),
(11, 1, 1, 'BSCS 1A', 1, '2026-04-29 14:34:52'),
(12, 1, 1, 'BSCS 1B', 1, '2026-04-29 14:35:00'),
(13, 1, 1, 'BSCS 1C', 1, '2026-04-29 14:35:07'),
(14, 1, 1, 'BSCS 2A', 2, '2026-04-29 14:35:17'),
(15, 1, 1, 'BSCS 2B', 2, '2026-04-29 14:35:26'),
(16, 1, 1, 'BSCS 3A', 3, '2026-04-29 14:35:39'),
(17, 1, 1, 'BSCS 4A', 4, '2026-04-29 14:35:49'),
(18, 1, 1, 'BSCS 4B', 4, '2026-04-29 14:35:57'),
(19, 1, 4, 'ACT-NT 1A', 1, '2026-04-29 14:36:47'),
(20, 1, 4, 'ACT-NT 2A', 2, '2026-04-29 14:36:59'),
(21, 1, 4, 'ACT-NT 2B', 2, '2026-04-29 14:37:07'),
(22, 1, 2, 'BSIT 1A', 1, '2026-04-29 14:37:23'),
(23, 1, 2, 'BSIT 1B', 1, '2026-04-29 14:37:32'),
(24, 1, 2, 'BSIT 1C', 1, '2026-04-29 14:37:56'),
(25, 1, 2, 'BSIT 2A', 2, '2026-04-29 14:38:27'),
(26, 1, 2, 'BSIT 2B', 2, '2026-04-29 14:38:35'),
(27, 1, 2, 'BSIT 3A', 3, '2026-04-29 14:38:44'),
(28, 1, 2, 'BSIT 4A', 4, '2026-04-29 14:38:52'),
(29, 1, 2, 'BSIT 4B', 4, '2026-04-29 14:39:00'),
(30, 1, 2, 'BSIT 4C', 4, '2026-04-29 14:39:09'),
(31, 1, 5, 'MIT 1A', 1, '2026-04-29 14:39:24'),
(32, 1, 5, 'MIT 2A', 2, '2026-04-29 14:39:32'),
(33, 3, NULL, 'BS BIO 4A', NULL, '2026-05-01 09:37:14'),
(34, 3, NULL, 'BS BIO 4B', NULL, '2026-05-01 10:11:45');

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
  `is_vpaa` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `college_officials`
--

INSERT INTO `college_officials` (`id`, `name`, `title`, `department_id`, `is_dean`, `is_secretary`, `is_vpaa`, `created_at`) VALUES
(1, 'JAYDEE C. BALLAHO, MIT', 'Head, Computer Science Department', 1, 0, 0, 0, '2026-04-19 11:30:52'),
(2, 'JASON A. CATADMAN, MIT', 'Head, Information Technology Department', 2, 0, 0, 0, '2026-04-19 11:31:29'),
(3, 'LUCY F. FELIX-SADIWA, MSCS', 'Head, MIT Department', 4, 0, 0, 0, '2026-04-19 11:31:58'),
(4, 'CEED JEZREEL B. LORENZO, MIT', 'Head, ACT Department', 3, 0, 0, 0, '2026-04-19 11:32:20'),
(5, 'MARK L. FLORES, PhD', 'Dean, College of Computing Studies', NULL, 1, 0, 0, '2026-04-19 11:32:48'),
(6, 'JAYDEE C. BALLAHO, MIT', 'College Secretary', NULL, 0, 1, 0, '2026-04-19 12:34:58'),
(7, 'BERHANA I. FLORES, EdD', 'OIC-Vice President for Academic Affairs', NULL, 0, 0, 1, '2026-04-30 13:51:31');

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
(5, 5, 2023, 2024, '2026-04-17 12:21:30'),
(6, 1, 2018, 2019, '2026-05-02 03:40:45'),
(7, 2, 2018, 2019, '2026-05-02 03:41:14');

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
(4, 'MIT', 'Department of Master in Information Technology', '2026-04-17 12:06:56');

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
(6, '006', 'Odon', '', 'Maravillas Jr.', '', '', 1, '', 1, '2026-04-17 14:35:53'),
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
(5, 'MIT', 'Master in Information Technology', 4, '2026-04-17 12:08:15'),
(6, 'SERVICE', 'Service Program', NULL, '2026-05-01 09:42:37');

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
(7, 'GYM', 50, '2026-04-26 05:07:36'),
(8, 'FIELD', 50, '2026-04-26 05:07:41'),
(9, 'CSM 205', 50, '2026-05-01 09:37:14');

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
  `class_size` int(11) NOT NULL DEFAULT 40,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `schoolyear_id`, `class_id`, `subject_id`, `class_mode`, `instructor_id`, `room_id`, `day_of_week`, `start_time`, `end_time`, `class_size`, `created_at`) VALUES
(4, 3, 6, 95, 'LEC', 6, NULL, 'Wednesday', '17:30:00', '20:30:00', 40, '2026-04-18 13:07:21'),
(5, 3, 6, 95, 'LEC', 6, NULL, 'Monday', '17:30:00', '20:30:00', 40, '2026-04-18 13:07:21'),
(6, 3, 6, 95, 'LEC', 6, NULL, 'Saturday', '13:00:00', '16:00:00', 40, '2026-04-18 13:09:36'),
(7, 3, 6, 96, 'LEC', 5, NULL, 'Thursday', '17:30:00', '20:30:00', 40, '2026-04-18 13:10:25'),
(8, 3, 6, 96, 'LEC', 5, NULL, 'Tuesday', '17:30:00', '20:30:00', 40, '2026-04-18 13:10:25'),
(9, 3, 6, 96, 'LEC', 5, NULL, 'Saturday', '09:00:00', '12:00:00', 40, '2026-04-18 13:12:56'),
(17, 3, 3, 34, 'LEC', 7, 1, 'Monday', '17:30:00', '19:00:00', 40, '2026-04-18 13:31:05'),
(21, 3, 3, 34, 'LEC', 7, 1, 'Wednesday', '17:30:00', '19:00:00', 40, '2026-04-18 13:42:15'),
(22, 3, 3, 34, 'LEC', 7, 1, 'Friday', '17:30:00', '19:00:00', 40, '2026-04-18 13:42:15'),
(23, 3, 3, 32, 'LEC', 2, 2, 'Wednesday', '09:00:00', '12:00:00', 50, '2026-04-18 13:44:53'),
(24, 3, 3, 32, 'LEC', 2, 2, 'Monday', '09:00:00', '12:00:00', 50, '2026-04-18 13:44:53'),
(25, 3, 3, 32, 'LEC', 2, 2, 'Friday', '09:00:00', '12:00:00', 50, '2026-04-18 13:44:53'),
(26, 3, 3, 33, 'LEC', 10, 1, 'Wednesday', '13:00:00', '16:00:00', 40, '2026-04-18 13:55:11'),
(27, 3, 3, 33, 'LEC', 10, 1, 'Monday', '13:00:00', '16:00:00', 40, '2026-04-18 13:55:11'),
(28, 3, 3, 33, 'LAB', 3, 5, 'Tuesday', '09:00:00', '12:00:00', 40, '2026-04-18 13:55:50'),
(29, 3, 3, 33, 'LAB', 3, 5, 'Tuesday', '13:00:00', '16:00:00', 40, '2026-04-18 13:55:50'),
(30, 3, 4, 50, 'LEC', 1, 2, 'Monday', '13:00:00', '16:00:00', 40, '2026-04-18 14:33:56'),
(31, 3, 4, 50, 'LEC', 1, 2, 'Wednesday', '13:00:00', '16:00:00', 40, '2026-04-18 14:33:56'),
(32, 3, 4, 50, 'LEC', 1, 2, 'Friday', '13:00:00', '16:00:00', 40, '2026-04-18 14:33:57'),
(34, 3, 5, 50, 'LEC', 1, 2, 'Monday', '16:00:00', '19:00:00', 40, '2026-04-18 14:34:57'),
(35, 3, 5, 50, 'LEC', 1, 2, 'Friday', '16:00:00', '19:00:00', 40, '2026-04-18 14:34:57'),
(36, 3, 5, 50, 'LEC', 1, 2, 'Wednesday', '16:00:00', '19:00:00', 40, '2026-04-18 14:34:57'),
(37, 3, 7, 145, 'LAB', 2, 6, 'Monday', '16:00:00', '19:00:00', 35, '2026-04-18 14:44:39'),
(38, 3, 7, 145, 'LAB', 2, 6, 'Wednesday', '16:00:00', '19:00:00', 35, '2026-04-18 14:44:39'),
(39, 3, 7, 145, 'LEC', 4, 4, 'Monday', '13:00:00', '16:00:00', 35, '2026-04-18 14:48:11'),
(40, 3, 7, 145, 'LEC', 4, 4, 'Wednesday', '13:00:00', '16:00:00', 35, '2026-04-18 14:48:11'),
(41, 3, 7, 144, 'LEC', 8, 1, 'Monday', '09:00:00', '12:00:00', 35, '2026-04-18 14:49:36'),
(42, 3, 7, 144, 'LEC', 8, 1, 'Wednesday', '09:00:00', '12:00:00', 35, '2026-04-18 14:49:36'),
(43, 3, 7, 144, 'LEC', 8, 1, 'Friday', '09:00:00', '12:00:00', 35, '2026-04-18 14:49:36'),
(44, 3, 8, 144, 'LEC', 8, 3, 'Monday', '13:00:00', '16:00:00', 35, '2026-04-18 14:53:17'),
(45, 3, 8, 144, 'LEC', 8, 3, 'Friday', '13:00:00', '16:00:00', 35, '2026-04-18 14:53:17'),
(46, 3, 8, 144, 'LEC', 8, 3, 'Wednesday', '13:00:00', '16:00:00', 35, '2026-04-18 14:53:17'),
(47, 3, 7, 145, 'LAB', 2, 6, 'Friday', '16:00:00', '19:00:00', 35, '2026-04-18 15:22:32'),
(48, 3, 3, 33, 'LAB', 3, 5, 'Friday', '13:00:00', '16:00:00', 40, '2026-04-18 15:37:48'),
(49, 3, 8, 145, 'LEC', 4, 3, 'Wednesday', '09:00:00', '12:00:00', 35, '2026-04-18 15:43:05'),
(50, 3, 8, 145, 'LEC', 4, 3, 'Monday', '09:00:00', '12:00:00', 35, '2026-04-18 15:43:05'),
(51, 3, 8, 145, 'LAB', 9, 5, 'Friday', '16:00:00', '19:00:00', 35, '2026-04-18 15:44:02'),
(52, 3, 8, 145, 'LAB', 9, 5, 'Monday', '16:00:00', '19:00:00', 35, '2026-04-18 15:44:02'),
(53, 3, 8, 145, 'LAB', 9, 5, 'Wednesday', '16:00:00', '19:00:00', 35, '2026-04-18 15:44:02'),
(54, 3, 9, 144, 'LEC', 10, 4, 'Wednesday', '09:00:00', '12:00:00', 35, '2026-04-18 15:46:25'),
(55, 3, 9, 144, 'LEC', 10, 4, 'Monday', '09:00:00', '12:00:00', 35, '2026-04-18 15:46:25'),
(56, 3, 9, 144, 'LEC', 10, 4, 'Friday', '09:00:00', '12:00:00', 35, '2026-04-18 15:46:25'),
(57, 3, 9, 145, 'LAB', 9, 6, 'Wednesday', '13:00:00', '16:00:00', 35, '2026-04-18 15:52:40'),
(58, 3, 9, 145, 'LAB', 9, 6, 'Monday', '13:00:00', '16:00:00', 35, '2026-04-18 15:52:40'),
(59, 3, 9, 145, 'LAB', 9, 6, 'Friday', '13:00:00', '16:00:00', 35, '2026-04-18 15:53:37'),
(60, 3, 9, 145, 'LEC', 4, 4, 'Wednesday', '16:00:00', '19:00:00', 35, '2026-04-18 15:56:55'),
(61, 3, 9, 145, 'LEC', 4, 4, 'Monday', '16:00:00', '19:00:00', 35, '2026-04-18 15:56:55'),
(73, 3, 3, 34, 'LEC', 7, 1, 'Saturday', '09:00:00', '12:00:00', 40, '2026-04-30 13:39:24'),
(74, 3, 3, 34, 'LEC', 7, 1, 'Saturday', '14:30:00', '16:00:00', 40, '2026-04-30 13:40:00');

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
(3, 2025, 2026, 3, 0, '2026-04-17 11:15:15'),
(4, 2025, 2026, 2, 1, '2026-05-02 03:51:48');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `subject_name` varchar(200) DEFAULT NULL,
  `lec_credits` int(11) DEFAULT 0,
  `lab_credits` int(11) DEFAULT 0,
  `total_credits` int(11) GENERATED ALWAYS AS (`lec_credits` + `lab_credits`) STORED,
  `curriculum_id` int(11) DEFAULT NULL,
  `year_level` int(11) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
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
(88, 'MIT 201', 'Advanced Operating Systems and Networking', 3, 0, 5, 1, 1, '2026-04-18 09:40:53'),
(89, 'MIT 202', 'Advanced System Design and Implementation', 3, 0, 5, 1, 1, '2026-04-18 09:40:53'),
(90, 'MIT 203', 'Advanced Database Systems', 3, 0, 5, 1, 1, '2026-04-18 09:40:53'),
(91, 'MIT 204', 'Technology and Project Management', 3, 0, 5, 1, 1, '2026-04-18 09:40:53'),
(92, 'MIT 205', 'Systems Development', 3, 0, 5, 1, 2, '2026-04-18 09:40:53'),
(93, 'MIT 206', 'Information Management', 3, 0, 5, 1, 2, '2026-04-18 09:40:53'),
(94, 'MIT 207', 'Interactive Systems', 3, 0, 5, 1, 2, '2026-04-18 09:40:53'),
(95, 'MIT 208', 'Intelligent Systems', 3, 0, 5, 1, 3, '2026-04-18 09:40:53'),
(96, 'MIT 209', 'Data Analytics', 3, 0, 5, 1, 3, '2026-04-18 09:40:53'),
(97, 'CAPSTONE 1', 'Capstone Project 1', 3, 0, 5, 2, 1, '2026-04-18 09:40:53'),
(98, 'CAPSTONE 2', 'Capstone Project 2', 3, 0, 5, 2, 2, '2026-04-18 09:40:53'),
(99, 'MIT CC1', 'Cognate', 3, 0, 5, 1, 2, '2026-04-18 09:42:24'),
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
(205, 'IT422', 'Practicum / Industry Immersion', 0, 6, 4, 4, 2, '2026-04-18 10:10:17'),
(206, 'MST303', 'MST 303', 0, 0, NULL, NULL, NULL, '2026-05-01 09:37:14'),
(207, 'CC 100', 'Introduction to Computing', 2, 1, 6, 1, 1, '2026-05-02 03:57:37'),
(208, 'CC 101', 'Computer Programming 1', 3, 1, 6, 1, 1, '2026-05-02 03:57:37'),
(209, 'CAS 101', 'Purposive Communication', 3, 0, 6, 1, 1, '2026-05-02 03:57:37'),
(210, 'MATH 100', 'Mathematics in the Modern World', 3, 0, 6, 1, 1, '2026-05-02 03:57:37'),
(211, 'US 101', 'Understanding the Self', 3, 0, 6, 1, 1, '2026-05-02 03:57:37'),
(212, 'FIL 101', 'Komunikasyon sa Akademikong Filipino', 3, 0, 6, 1, 1, '2026-05-02 03:57:37'),
(213, 'PE 101', 'Physical Education 1', 2, 0, 6, 1, 1, '2026-05-02 03:57:37'),
(214, 'NSTP 1', 'National Service Training Program 1', 3, 0, 6, 1, 1, '2026-05-02 03:57:37'),
(215, 'EUTH A', 'Euthenics A', 2, 0, 6, 1, 1, '2026-05-02 03:57:37'),
(216, 'CS 111', 'Discrete Structures 1', 3, 0, 6, 1, 2, '2026-05-02 03:57:37'),
(217, 'CC 102', 'Computer Programming 2', 3, 1, 6, 1, 2, '2026-05-02 03:57:37'),
(218, 'MATH 103', 'Calculus 1', 3, 0, 6, 1, 2, '2026-05-02 03:57:37'),
(219, 'CW 101', 'The Contemporary World', 3, 0, 6, 1, 2, '2026-05-02 03:57:37'),
(220, 'STS 100', 'Science, Technology and Society', 3, 0, 6, 1, 2, '2026-05-02 03:57:37'),
(221, 'FIL 102', 'Retorika', 3, 0, 6, 1, 2, '2026-05-02 03:57:37'),
(222, 'PE 102', 'Physical Education 2', 2, 0, 6, 1, 2, '2026-05-02 03:57:37'),
(223, 'NSTP 2', 'National Service Training Program 2', 3, 0, 6, 1, 2, '2026-05-02 03:57:37'),
(224, 'EUTH B', 'Euthenics B', 2, 0, 6, 1, 2, '2026-05-02 03:57:37'),
(225, 'CS 121', 'Object-Oriented Programming', 3, 1, 6, 2, 1, '2026-05-02 03:57:37'),
(226, 'CS 123', 'Discrete Structures 2', 3, 0, 6, 2, 1, '2026-05-02 03:57:37'),
(227, 'CS 125', 'Digital Design', 3, 1, 6, 2, 1, '2026-05-02 03:57:37'),
(228, 'CS 127', 'Human-Computer Interaction', 0, 1, 6, 2, 1, '2026-05-02 03:57:37'),
(229, 'CC 103', 'Data Structures and Algorithms', 3, 1, 6, 2, 1, '2026-05-02 03:57:37'),
(230, 'MATH 104', 'Calculus 2', 3, 0, 6, 2, 1, '2026-05-02 03:57:37'),
(231, 'LIT 101', 'Philippine Literature', 3, 0, 6, 2, 1, '2026-05-02 03:57:37'),
(232, 'PE 103', 'Physical Education 3', 2, 0, 6, 2, 1, '2026-05-02 03:57:37'),
(233, 'CS 120', 'Architecture and Organization', 3, 1, 6, 2, 2, '2026-05-02 03:57:37'),
(234, 'CS 122', 'Design and Analysis of Algorithms', 3, 0, 6, 2, 2, '2026-05-02 03:57:37'),
(235, 'CS 124', 'Programming Languages', 2, 1, 6, 2, 2, '2026-05-02 03:57:37'),
(236, 'CS 126', 'Networks and Communications', 2, 1, 6, 2, 2, '2026-05-02 03:57:37'),
(237, 'CS 128', 'CS Elective 1', 2, 1, 6, 2, 2, '2026-05-02 03:57:37'),
(238, 'CC 104', 'Information Management', 3, 1, 6, 2, 2, '2026-05-02 03:57:37'),
(239, 'PE 104', 'Physical Education 4', 2, 0, 6, 2, 2, '2026-05-02 03:57:37'),
(240, 'CS 131', 'Automata Theory and Formal Languages', 3, 0, 6, 3, 1, '2026-05-02 03:57:37'),
(241, 'CS 133', 'Information Assurance and Security', 2, 0, 6, 3, 1, '2026-05-02 03:57:37'),
(242, 'CS 135', 'Advanced Database Systems', 3, 1, 6, 3, 1, '2026-05-02 03:57:37'),
(243, 'CS 137', 'Software Engineering 1', 2, 1, 6, 3, 1, '2026-05-02 03:57:37'),
(244, 'CS 139', 'Web Programming and Development', 3, 1, 6, 3, 1, '2026-05-02 03:57:37'),
(245, 'CS 140', 'CS Elective 2', 2, 1, 6, 3, 1, '2026-05-02 03:57:37'),
(246, 'ETHICS 101', 'Ethics', 3, 0, 6, 3, 2, '2026-05-02 03:57:37'),
(247, 'CC 105', 'Application Development and Emerging Technologies', 2, 1, 6, 3, 1, '2026-05-02 03:57:37'),
(248, 'CS 130', 'CS Thesis 1', 3, 0, 6, 3, 2, '2026-05-02 03:57:37'),
(249, 'CS 132', 'Software Engineering 2', 2, 1, 6, 3, 2, '2026-05-02 03:57:37'),
(250, 'CS 134', 'Operating Systems', 3, 1, 6, 3, 2, '2026-05-02 03:57:37'),
(251, 'CS 136', 'Modeling and Simulation', 2, 1, 6, 3, 2, '2026-05-02 03:57:37'),
(252, 'CS 138', 'CS Elective 3', 2, 1, 6, 3, 2, '2026-05-02 03:57:37'),
(253, 'CS 141', 'Practicum / Industry Immersion', 0, 3, 6, 3, 3, '2026-05-02 03:57:37'),
(254, 'CS 143', 'Thesis 2', 3, 0, 6, 4, 1, '2026-05-02 03:57:37'),
(255, 'HIST 100', 'Life and Works of Rizal', 3, 0, 6, 4, 1, '2026-05-02 03:57:37'),
(256, 'CS 142', 'Social Issues and Professional Practice', 3, 0, 6, 4, 2, '2026-05-02 03:57:37'),
(257, 'HIST 101', 'Readings in Philippine History', 3, 0, 6, 4, 2, '2026-05-02 03:57:37'),
(258, 'A&H 100', 'Art Appreciation', 3, 0, 6, 4, 2, '2026-05-02 03:57:37'),
(259, 'CC 100', 'Introduction to Computing', 2, 1, 7, 1, 1, '2026-05-02 04:07:46'),
(260, 'CC 101', 'Computer Programming 1', 3, 1, 7, 1, 1, '2026-05-02 04:07:46'),
(261, 'CAS 101', 'Purposive Communication', 3, 0, 7, 1, 1, '2026-05-02 04:07:46'),
(262, 'MATH 100', 'Mathematics in the Modern World', 3, 0, 7, 1, 1, '2026-05-02 04:07:46'),
(263, 'US 101', 'Understanding the Self', 3, 0, 7, 1, 1, '2026-05-02 04:07:46'),
(264, 'FIL 101', 'Komunikasyon sa Akademikong Filipino', 3, 0, 7, 1, 1, '2026-05-02 04:07:46'),
(265, 'PE 101', 'Physical Education 1', 2, 0, 7, 1, 1, '2026-05-02 04:07:46'),
(266, 'NSTP 1', 'National Service Training Program 1', 3, 0, 7, 1, 1, '2026-05-02 04:07:46'),
(267, 'EUTH A', 'Euthenics A', 2, 0, 7, 1, 1, '2026-05-02 04:07:46'),
(268, 'IT 112', 'Discrete Mathematics', 3, 0, 7, 1, 2, '2026-05-02 04:07:46'),
(269, 'IT 114', 'Operating Systems', 3, 1, 7, 1, 2, '2026-05-02 04:07:46'),
(270, 'CC 102', 'Computer Programming 2', 3, 1, 7, 1, 2, '2026-05-02 04:07:46'),
(271, 'HIST 100', 'Life and Works of Rizal', 3, 0, 7, 1, 2, '2026-05-02 04:07:46'),
(272, 'A&H 100', 'Art Appreciation', 3, 0, 7, 1, 2, '2026-05-02 04:07:46'),
(273, 'FIL 102', 'Retorika', 3, 0, 7, 1, 2, '2026-05-02 04:07:46'),
(274, 'PE 102', 'Physical Education 2', 2, 0, 7, 1, 2, '2026-05-02 04:07:46'),
(275, 'NSTP 2', 'National Service Training Program 2', 3, 0, 7, 1, 2, '2026-05-02 04:07:46'),
(276, 'EUTH B', 'Euthenics B', 2, 0, 7, 1, 2, '2026-05-02 04:07:46'),
(277, 'IT 121', 'Object-Oriented Programming', 3, 1, 7, 2, 1, '2026-05-02 04:07:46'),
(278, 'IT 123', 'Platform Technologies', 2, 1, 7, 2, 1, '2026-05-02 04:07:46'),
(279, 'IT 125', 'Human-Computer Interaction', 2, 1, 7, 2, 1, '2026-05-02 04:07:46'),
(280, 'CC 103', 'Data Structures and Algorithms', 3, 1, 7, 2, 1, '2026-05-02 04:07:46'),
(281, 'HIST 101', 'Readings in Philippine History', 3, 0, 7, 2, 1, '2026-05-02 04:07:46'),
(282, 'STS 100', 'Science, Technology and Society', 3, 0, 7, 2, 1, '2026-05-02 04:07:46'),
(283, 'LIT 101', 'Philippine Literature', 3, 0, 7, 2, 1, '2026-05-02 04:07:46'),
(284, 'PE 103', 'Physical Education 3', 2, 0, 7, 2, 1, '2026-05-02 04:07:46'),
(285, 'IT 122', 'Integrative Programming and Technologies', 2, 1, 7, 2, 2, '2026-05-02 04:07:46'),
(286, 'IT 124', 'Networking 1', 2, 1, 7, 2, 2, '2026-05-02 04:07:46'),
(287, 'IT 126', 'Quantitative Methods (including Modeling and Simulation)', 3, 0, 7, 2, 2, '2026-05-02 04:07:46'),
(288, 'CC 104', 'Information Management', 3, 1, 7, 2, 2, '2026-05-02 04:07:46'),
(289, 'CW 101', 'The Contemporary World', 3, 0, 7, 2, 2, '2026-05-02 04:07:46'),
(290, 'ETHICS 101', 'Ethics', 3, 0, 7, 2, 2, '2026-05-02 04:07:46'),
(291, 'PE 104', 'Physical Education 4', 2, 0, 7, 2, 2, '2026-05-02 04:07:46'),
(292, 'IT 131', 'Advanced Database Systems', 3, 1, 7, 3, 1, '2026-05-02 04:07:46'),
(293, 'IT 133', 'Networking 2', 2, 1, 7, 3, 1, '2026-05-02 04:07:46'),
(294, 'IT 135', 'Systems Integration and Architecture', 2, 1, 7, 3, 1, '2026-05-02 04:07:46'),
(295, 'IT 137', 'Web Systems and Technologies', 3, 1, 7, 3, 1, '2026-05-02 04:07:46'),
(296, 'IT 139', 'IT Elective 1', 2, 1, 7, 3, 1, '2026-05-02 04:07:46'),
(297, 'CC 105', 'Application Development and Emerging Technologies', 2, 1, 7, 3, 1, '2026-05-02 04:07:46'),
(298, 'IT 130', 'Information Assurance and Security', 2, 1, 7, 3, 2, '2026-05-02 04:07:46'),
(299, 'IT 132', 'Software Engineering', 3, 1, 7, 3, 2, '2026-05-02 04:07:46'),
(300, 'IT 134', 'Social and Professional Practice', 3, 0, 7, 3, 2, '2026-05-02 04:07:46'),
(301, 'IT 136', 'IT Elective 2', 2, 1, 7, 3, 2, '2026-05-02 04:07:46'),
(302, 'IT 138', 'IT Elective 3', 2, 1, 7, 3, 2, '2026-05-02 04:07:46'),
(303, 'IT 140', 'Capstone Project and Research 1', 3, 0, 7, 3, 3, '2026-05-02 04:07:46'),
(304, 'IT 141', 'Capstone Project and Research 2', 3, 0, 7, 4, 1, '2026-05-02 04:07:46'),
(305, 'IT 142', 'Systems Administration and Maintenance', 2, 1, 7, 4, 2, '2026-05-02 04:07:46'),
(306, 'IT 143', 'Information Assurance and Security', 2, 1, 7, 4, 1, '2026-05-02 04:07:46'),
(307, 'IT 144', 'Practicum / Industry Immersion', 0, 9, 7, 4, 2, '2026-05-02 04:07:46'),
(308, 'IT 145', 'IT Elective 4', 2, 1, 7, 4, 1, '2026-05-02 04:07:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_applicant_program` (`program_id`),
  ADD KEY `fk_applicant_curriculum` (`curriculum_id`),
  ADD KEY `fk_applicant_schoolyear` (`schoolyear_id`);

--
-- Indexes for table `applicant_grades`
--
ALTER TABLE `applicant_grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_applicant_subject` (`applicant_id`,`subject_id`),
  ADD KEY `fk_ag_subject` (`subject_id`);

--
-- Indexes for table `awards_criteria`
--
ALTER TABLE `awards_criteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_awards_criteria_schoolyear` (`schoolyear_id`);

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
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `applicant_grades`
--
ALTER TABLE `applicant_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=677;

--
-- AUTO_INCREMENT for table `awards_criteria`
--
ALTER TABLE `awards_criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `college_officials`
--
ALTER TABLE `college_officials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `curriculum`
--
ALTER TABLE `curriculum`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `schoolyear`
--
ALTER TABLE `schoolyear`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=309;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicants`
--
ALTER TABLE `applicants`
  ADD CONSTRAINT `fk_applicant_curriculum` FOREIGN KEY (`curriculum_id`) REFERENCES `curriculum` (`id`),
  ADD CONSTRAINT `fk_applicant_program` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`),
  ADD CONSTRAINT `fk_applicant_schoolyear` FOREIGN KEY (`schoolyear_id`) REFERENCES `schoolyear` (`id`);

--
-- Constraints for table `applicant_grades`
--
ALTER TABLE `applicant_grades`
  ADD CONSTRAINT `fk_ag_applicant` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ag_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `awards_criteria`
--
ALTER TABLE `awards_criteria`
  ADD CONSTRAINT `fk_awards_criteria_schoolyear` FOREIGN KEY (`schoolyear_id`) REFERENCES `schoolyear` (`id`) ON UPDATE CASCADE;

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
