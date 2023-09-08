-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 09, 2023 at 12:04 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tpay`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_ID` int(225) NOT NULL,
  `admin_name` varchar(25) NOT NULL,
  `admin_username` varchar(25) NOT NULL,
  `admin_email` varchar(255) NOT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_profile` varchar(255) NOT NULL,
  `Balance` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_ID`, `admin_name`, `admin_username`, `admin_email`, `admin_password`, `admin_profile`, `Balance`) VALUES
(1, 'Ir Diane', 'Diane', 'ireneudiane@gmail.com', '1dc8ed480f98d79c8938a45efd7d759a', 'WhatsApp Image 2023-08-07 at 21.27.01.jpeg', 667600);

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `created_at` datetime NOT NULL,
  `aID` int(10) NOT NULL,
  `agent_name` varchar(255) NOT NULL,
  `agent_username` varchar(255) NOT NULL,
  `agent_mail` varchar(255) NOT NULL,
  `agent_password` varchar(255) NOT NULL,
  `agent_pin` int(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `agent_balance` int(220) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `agent`
--

INSERT INTO `agent` (`created_at`, `aID`, `agent_name`, `agent_username`, `agent_mail`, `agent_password`, `agent_pin`, `photo`, `agent_balance`, `status`) VALUES
('2023-06-06 02:22:03', 36, 'RUMUMBA Patrice', 'Patrice', 'patrice@gmail.com', '597673be8ea7215c682c809347ba60ec', 5502, 'download.jpg', 23000, 'active'),
('2023-09-01 17:02:12', 46, 'NYIRAMINANI Maria', 'Maria', 'maria@gmail.com', '202cb962ac59075b964b07152d234b70', 5617, 're.PNG', 51300, 'active'),
('2023-09-04 09:06:27', 49, 'NSHUTI Yves', 'Yves', 'nshuti@gmail.com', '202cb962ac59075b964b07152d234b70', 2703, 'NSHUTI.PNG', 5000, 'active'),
('2023-09-02 03:10:09', 50, 'KAGABO Yvette', 'kagabo', 'kagabo@gmail.com', '202cb962ac59075b964b07152d234b70', 2307, 'mik.PNG', 290000, 'active'),
('2023-09-05 16:14:12', 51, 'MINANI Hadjat', 'hadjat', 'minani@gmail.com', '00e3f19606e9e1a82a2a7d1c1050b75e', 5234, 'NSHUTI.PNG', 6000, 'sleep'),
('2023-09-06 10:00:08', 52, 'BATAMURIZA Sarah', 'Sarah', 'sarah@gmail.com', '9d86928442689ed3f4de9af89e0fda95', 6501, 'mik.PNG', 0, 'sleep'),
('2023-09-08 06:50:58', 53, 'Danny', 'Idt', 'idtdanny@gmail.com', 'f085f7015901c1aec68db7d612796317', 5931, 'WhatsApp Image 2023-08-07 at 21.27.01.jpeg', 0, 'active'),
('2023-09-08 06:51:37', 54, 'Danny', 'Idt', 'idtdanny@gmail.com', '8e3d2c544f6b844fd04fc162a217fc0a', 5253, 'WhatsApp Image 2023-08-07 at 21.27.01.jpeg', 0, 'active'),
('2023-09-08 07:03:59', 62, 'Diane', 'Ir', 'ireneudiane@gmail.com', '233a9c2c82c2d3b5a3ead9410958e4e5', 8679, 'WhatsApp Image 2023-08-31 at 8.31.31 PM.jpeg', 5000, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `agent_location`
--

CREATE TABLE `agent_location` (
  `no` int(255) NOT NULL,
  `aID` int(255) NOT NULL,
  `agent_name` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `sector` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business`
--

CREATE TABLE `business` (
  `Date` datetime NOT NULL,
  `bID` int(255) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_tin` int(9) NOT NULL,
  `business_mail` varchar(255) NOT NULL,
  `business_password` varchar(255) NOT NULL,
  `business_pin` int(255) NOT NULL,
  `business_type` varchar(255) NOT NULL,
  `balance` int(255) DEFAULT NULL,
  `status` text DEFAULT NULL,
  `photo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business`
--

INSERT INTO `business` (`Date`, `bID`, `business_name`, `business_tin`, `business_mail`, `business_password`, `business_pin`, `business_type`, `balance`, `status`, `photo`) VALUES
('2022-12-27 01:08:06', 11, 'IDA TECHNOLOGY', 120582059, 'ida@gmail.com', 'e7e158399a1fe6378cf2dcc1996b1848', 1748, 'others', 4100, 'Active', 'IDA.PNG'),
('2023-01-28 09:34:12', 12, 'ENGEN RDA', 100800300, 'engen@gmail.com', 'e2a01a3c474b5068e68073afe5669468', 1496, 'gas', 800, 'Active', 'ENGEN.PNG'),
('2022-12-28 20:29:19', 13, 'GOOD WAY DIRECTION (GWD)', 100000009, 'gway@gmail.com', '6848b18148ff3b0e3f07c4e297e23a4e', 3596, 'others', 0, 'Active', 'mik.PNG'),
('2022-12-22 01:32:16', 14, 'Quincallelie', 100999777, 'quin@gmail.com', '5524e1290a1549764984c32c23b06938', 8491, 'others', 300000, 'Active', 'NSHUTI.PNG'),
('2023-06-28 12:15:40', 15, 'Simba Super market', 1122234455, 'sims@gmail.com', '4ccdfb391d83cd329540406ab246c4a9', 8777, 'others', 0, 'Sleep', 'ENGEN.PNG'),
('2023-09-08 11:34:04', 47, 'Biryogo Food', 105688367, 'idtbusy@gmail.com', 'be64fb58b646850bba20fe1b2962361a', 9399, 'gas', 0, 'Active', 'WhatsApp Image 2023-08-07 at 21.27.01.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `business_location`
--

CREATE TABLE `business_location` (
  `no` int(255) NOT NULL,
  `bID` int(255) NOT NULL,
  `business_tin` int(9) NOT NULL,
  `district` varchar(255) NOT NULL,
  `sector` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_location`
--

INSERT INTO `business_location` (`no`, `bID`, `business_tin`, `district`, `sector`) VALUES
(5, 47, 105688367, 'Rwamagana', 'Nyagasambu');

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `created_at` datetime DEFAULT NULL,
  `cID` int(255) NOT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `client_tel` varchar(12) NOT NULL,
  `client_mail` varchar(25) NOT NULL,
  `client_balance` int(255) NOT NULL,
  `referral_agent` varchar(255) NOT NULL,
  `status` text DEFAULT NULL,
  `approve` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`created_at`, `cID`, `client_id`, `client_name`, `client_tel`, `client_mail`, `client_balance`, `referral_agent`, `status`, `approve`) VALUES
('2022-12-28 00:00:00', 16, '0000695191', 'HAMZA', '250788778999', 'divinirakoze10@gmail.com', 1900, 'kagabo', '', 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `nID` int(25) NOT NULL,
  `recieverid` int(255) NOT NULL,
  `message` longtext NOT NULL,
  `date_sent` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `target` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`nID`, `recieverid`, `message`, `date_sent`, `status`, `target`) VALUES
(14, 8, 'You have a Successful Payment of500 From: 5<br> Done:30/05/2020, 11:05', '2020-05-30', 'unread', 'agent'),
(15, 5, 'You have Payed 500 To: alpha_boutique<br> Done:31/05/2020, 12:05', '2020-05-30', 'read', 'client'),
(16, 8, 'You have a Successful Payment of500 From: 5<br> Done:31/05/2020, 12:05', '2020-05-30', 'read', 'agent'),
(20, 8, 'You have Recharged 2000 To: obed2<br> Done:16/06/2020, 05:06', '2020-06-15', 'read', 'agent'),
(21, 5, 'Your account has been Recharged 2000 From: alpha_boutique<br> Done:16/06/2020, 05:06', '2020-06-15', 'read', 'client'),
(24, 0, 'Recharge Successful , Amount: 1000<br> Done:12/07/2020, 06:07', '2020-07-12', 'unread', 'agent'),
(26, 8, 'Recharge Successful , Amount: 5000<br> Done:12/07/2020, 06:07', '2020-07-12', 'unread', 'agent'),
(55, 5502, 'Recharge Successful , Amount: 5000<br/> Done:30/11/2022,09:12', '2022-11-30', 'unread', 'agent'),
(56, 5502, 'Recharge Successful , Amount: 1000<br/> Done:01/12/2022,09:12', '2022-12-01', 'read', 'agent'),
(60, 4317, 'Recharge Successful , Amount: 10000<br/> Done:01/12/2022,02:12', '2022-12-01', 'read', 'agent'),
(67, 5617, 'Recharge Successful , Amount: 100000<br/> Done:01/12/2022,08:12', '2022-12-01', 'unread', 'agent'),
(69, 111189338, 'Withdraw Successful , Amount: 200<br/> Done:02/12/2022,11:12', '2022-12-02', 'read', 'business'),
(70, 111189338, 'Withdraw Successful , Amount: 500<br/> Done:02/12/2022,02:12', '2022-12-02', 'read', 'business'),
(74, 111189338, 'You have been paid by <b>MURIGANDE</b> , Amount: 2000.0<br/> Done:2022-12-03,01:12', '2022-12-03', 'unread', 'business'),
(75, 111189338, 'You have been paid by <b>MURIGANDE</b> , Amount: 5000.0<br/> Done:2022-12-03,01:12', '2022-12-03', 'unread', 'business'),
(76, 111189338, 'You have been paid by <b>MURIGANDE</b> , Amount: 2000.0<br/> Done: 2022-12-03,13:11', '2022-12-03', 'unread', 'business'),
(77, 111189338, 'You have been paid by <b>MURIGANDE</b> , Amount: 3000.0<br/> Done: 2022-12-03,01:12', '2022-12-03', 'unread', 'business'),
(78, 5502, 'Recharge Successful , Amount: 1000<br/> Done:03/12/2022,01:12', '2022-12-03', 'unread', 'agent'),
(79, 5502, 'You have been recharged , Amount: 2,000<br/> Done: 03/12/2022, 01:12', '2022-12-03', 'unread', 'agent'),
(80, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 2000.0<br/> Done: 2022-12-20,10:08', '2022-12-20', 'unread', 'business'),
(81, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,10:10', '2022-12-20', 'unread', 'business'),
(82, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,10:21', '2022-12-20', 'unread', 'business'),
(83, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,10:31', '2022-12-20', 'unread', 'business'),
(84, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,10:38', '2022-12-20', 'unread', 'business'),
(85, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,10:41', '2022-12-20', 'unread', 'business'),
(86, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,10:45', '2022-12-20', 'unread', 'business'),
(87, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,11:10', '2022-12-20', 'unread', 'business'),
(88, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,12:10', '2022-12-20', 'unread', 'business'),
(89, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,12:18', '2022-12-20', 'unread', 'business'),
(90, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,12:19', '2022-12-20', 'unread', 'business'),
(91, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,12:21', '2022-12-20', 'unread', 'business'),
(92, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,12:27', '2022-12-20', 'unread', 'business'),
(93, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,12:28', '2022-12-20', 'unread', 'business'),
(94, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:18', '2022-12-20', 'unread', 'business'),
(95, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:21', '2022-12-20', 'unread', 'business'),
(96, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:22', '2022-12-20', 'unread', 'business'),
(97, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:23', '2022-12-20', 'unread', 'business'),
(98, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:24', '2022-12-20', 'unread', 'business'),
(99, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:30', '2022-12-20', 'unread', 'business'),
(100, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:34', '2022-12-20', 'unread', 'business'),
(101, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:54', '2022-12-20', 'unread', 'business'),
(102, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:55', '2022-12-20', 'unread', 'business'),
(103, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:56', '2022-12-20', 'unread', 'business'),
(104, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,02:58', '2022-12-20', 'unread', 'business'),
(105, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,03:02', '2022-12-20', 'unread', 'business'),
(106, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,03:16', '2022-12-20', 'unread', 'business'),
(107, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,03:22', '2022-12-20', 'unread', 'business'),
(108, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,03:26', '2022-12-20', 'unread', 'business'),
(109, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,03:33', '2022-12-20', 'unread', 'business'),
(110, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,03:34', '2022-12-20', 'unread', 'business'),
(111, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,03:38', '2022-12-20', 'unread', 'business'),
(112, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,03:42', '2022-12-20', 'unread', 'business'),
(113, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 1000.0<br/> Done: 2022-12-20,03:45', '2022-12-20', 'unread', 'business'),
(114, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 1000.0<br/> Done: 2022-12-20,04:41', '2022-12-20', 'unread', 'business'),
(115, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 1000.0<br/> Done: 2022-12-20,04:46', '2022-12-20', 'unread', 'business'),
(116, 111189338, 'You have been paid by <b>MUBERA</b> , Amount: 100.0<br/> Done: 2022-12-20,04:47', '2022-12-20', 'unread', 'business'),
(117, 111189338, 'You have been paid by <b></b> , Amount: 1000.0<br/> Done: 2022-12-20,05:49', '2022-12-20', 'unread', 'business'),
(118, 5617, 'You have been recharged , Amount: 1,000<br/> Done: 21/12/2022, 10:12', '2022-12-21', 'unread', 'agent'),
(119, 5617, 'You have been recharged , Amount: 300<br/> Done: 21/12/2022, 10:12', '2022-12-21', 'unread', 'agent'),
(120, 4205, 'You have been recharged , Amount: 4,000<br/> Done: 21/12/2022, 10:12', '2022-12-21', 'read', 'agent'),
(121, 5502, 'You have Recharged a Card with  1000 <br/> Done:2022-12-27,09:12', '0000-00-00', 'unread', 'agent'),
(122, 5502, 'You have Recharged a Card with  3000 <br/> Done:2022-12-27,09:12', '0000-00-00', 'unread', 'agent'),
(123, 111189338, 'Withdraw Successful , Amount: 20000<br/> Done:27/12/2022,09:12', '2022-12-27', 'unread', 'business'),
(124, 5502, 'You have been recharged , Amount: 10,000<br/> Done: 27/12/2022, 09:12', '2022-12-27', 'unread', 'agent'),
(125, 5502, 'You have been recharged , Amount: 10,000<br/> Done: 27/12/2022, 09:12', '2022-12-27', 'unread', 'agent'),
(126, 111189338, 'Withdraw Successful , Amount: 500<br/> Done:27/12/2022,09:12', '2022-12-27', 'unread', 'business'),
(127, 111189338, 'Withdraw Successful , Amount: 100<br/> Done:27/12/2022,09:12', '2022-12-27', 'unread', 'business'),
(128, 111189338, 'Withdraw Successful , Amount: 300<br/> Done:27/12/2022,10:12', '2022-12-27', 'unread', 'business'),
(129, 5502, 'Card Top-up to <b>gHHGH</b> Successful , Amount: 1000<br/> Done:2022-12-27,10:12', '2022-12-27', 'unread', 'agent'),
(130, 111189338, 'You have been paid by <b>gHHGH</b> , Amount: 300.0<br/> Done: 2022-12-27,10:32', '2022-12-27', 'unread', 'business'),
(131, 111189338, 'You have been paid by <b>gHHGH</b> , Amount: 500.0<br/> Done: 2022-12-27,10:33', '2022-12-27', 'unread', 'business'),
(132, 111189338, 'You have been paid by <b>gHHGH</b> , Amount: 200.0<br/> Done: 2022-12-27,10:36', '2022-12-27', 'unread', 'business'),
(133, 120582059, 'You have been paid by <b>gHHGH</b> , Amount: 450.0<br/> Done: 2022-12-27,10:42', '2022-12-27', 'unread', 'business'),
(134, 5502, 'You have been recharged , Amount: 10,000<br/> Done: 27/12/2022, 10:12', '2022-12-27', 'unread', 'agent'),
(135, 5502, 'You have been recharged , Amount: 600<br/> Done: 27/12/2022, 10:12', '2022-12-27', 'unread', 'agent'),
(136, 5502, 'You have been recharged , Amount: 1,400<br/> Done: 27/12/2022, 10:12', '2022-12-27', 'unread', 'agent'),
(137, 0, 'You have been paid by <b>gHHGH</b> , Amount: 100.0<br/> Done: 2022-12-27,10:55', '2022-12-27', 'unread', 'business'),
(138, 5617, 'Withdraw Successful , Amount: 50000 Rwf<br/> Done:27/12/2022,10:12', '2022-12-27', 'unread', 'agent'),
(139, 5502, 'You have Recharged a Card with  1000 <br/> Done:2022-12-28,07:12', '0000-00-00', 'unread', 'agent'),
(140, 120582059, 'You have been paid by <b>Bokasa</b> , Amount: 500.0<br/> Done: 2022-12-28,07:28', '2022-12-28', 'unread', 'business'),
(141, 111189338, 'You have been paid by <b>Bokasa</b> , Amount: 100.0<br/> Done: 2022-12-28,07:29', '2022-12-28', 'unread', 'business'),
(142, 111189338, 'You have been paid by <b>Bokasa</b> , Amount: 100.0<br/> Done: 2022-12-28,07:29', '2022-12-28', 'unread', 'business'),
(143, 5502, 'You have Recharged a Card with  15000 <br/> Done:2022-12-28,07:12', '0000-00-00', 'unread', 'agent'),
(144, 111189338, 'You have been paid by <b>Divin</b> , Amount: 200.0<br/> Done: 2022-12-28,07:40', '2022-12-28', 'unread', 'business'),
(145, 111189338, 'You have been paid by <b>Divin</b> , Amount: 500.0<br/> Done: 2022-12-28,07:41', '2022-12-28', 'unread', 'business'),
(146, 0, 'You have been paid by <b>Divin</b> , Amount: 100.0<br/> Done: 2022-12-28,07:42', '2022-12-28', 'unread', 'business'),
(147, 111189338, 'You have been paid by <b>Divin</b> , Amount: 4000.0<br/> Done: 2022-12-28,07:45', '2022-12-28', 'unread', 'business'),
(148, 120582059, 'You have been paid by <b>Divin</b> , Amount: 1050.0<br/> Done: 2022-12-28,07:48', '2022-12-28', 'unread', 'business'),
(149, 2703, 'You have been recharged , Amount: 30,000<br/> Done: 28/12/2022, 07:12', '2022-12-28', 'unread', 'agent'),
(150, 2703, 'Withdraw Successful , Amount: 15000 Rwf<br/> Done:28/12/2022,07:12', '2022-12-28', 'unread', 'agent'),
(151, 100800300, 'You have been paid by <b>Divin</b> , Amount: 800.0<br/> Done: 2022-12-28,08:03', '2022-12-28', 'unread', 'business'),
(152, 2703, 'You have Recharged a Card with  10000 <br/> Done:2022-12-28,08:12', '0000-00-00', 'unread', 'agent'),
(153, 100800300, 'You have been paid by <b>Divin</b> , Amount: 200.0<br/> Done: 2022-12-28,08:34', '2022-12-28', 'unread', 'business'),
(154, 100800300, 'Withdraw Successful , Amount: 200<br/> Done:28/12/2022,08:12', '2022-12-28', 'unread', 'business'),
(155, 2307, 'You have been recharged , Amount: 340,000<br/> Done: 28/12/2022, 11:12', '2022-12-28', 'unread', 'agent'),
(156, 2307, 'You have Recharged a Card with  50000 <br/> Done:2022-12-28,11:12', '0000-00-00', 'unread', 'agent'),
(157, 100999777, 'You have been paid by <b>HAMZA</b> , Amount: 300000.0<br/> Done: 2022-12-28,12:03', '2022-12-28', 'unread', 'business'),
(158, 120582059, 'You have been paid by <b>HAMZA</b> , Amount: 2000.0<br/> Done: 2022-12-28,01:43', '2022-12-28', 'unread', 'business'),
(159, 120582059, 'You have been paid by <b>HAMZA</b> , Amount: 100.0<br/> Done: 2022-12-28,01:44', '2022-12-28', 'unread', 'business'),
(160, 5502, 'You have Recharged a Card with  2000 <br/> Done:2022-12-28,01:12', '0000-00-00', 'unread', 'agent'),
(161, 5234, 'You have been recharged , Amount: 3,000<br/> Done: 08/09/2023, 08:09', '2023-09-08', 'unread', 'agent'),
(162, 5234, 'You have been recharged , Amount: 3,000<br/> Done: 08/09/2023, 08:09', '2023-09-08', 'unread', 'agent');

-- --------------------------------------------------------

--
-- Table structure for table `notification_admin`
--

CREATE TABLE `notification_admin` (
  `nID` int(25) NOT NULL,
  `recieverid` int(255) NOT NULL,
  `message` longtext NOT NULL,
  `date_sent` date NOT NULL,
  `status` varchar(255) NOT NULL,
  `target` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_admin`
--

INSERT INTO `notification_admin` (`nID`, `recieverid`, `message`, `date_sent`, `status`, `target`) VALUES
(20, 8, 'You have Recharged 2000 To: obed2<br> Done:16/06/2020, 05:06', '2020-06-15', 'read', 'agent'),
(21, 5, 'Your account has been Recharged 2000 From: alpha_boutique<br> Done:16/06/2020, 05:06', '2020-06-15', 'read', 'client'),
(24, 0, 'Recharge Successful , Amount: 1000<br> Done:12/07/2020, 06:07', '2020-07-12', 'unread', 'agent'),
(26, 8, 'Recharge Successful , Amount: 5000<br> Done:12/07/2020, 06:07', '2020-07-12', 'unread', 'agent'),
(55, 5502, 'Recharge Successful , Amount: 5000<br/> Done:30/11/2022,09:12', '2022-11-30', 'unread', 'agent'),
(56, 5502, 'Recharge Successful , Amount: 1000<br/> Done:01/12/2022,09:12', '2022-12-01', 'read', 'agent'),
(60, 4317, 'Recharge Successful , Amount: 10000<br/> Done:01/12/2022,02:12', '2022-12-01', 'read', 'agent'),
(67, 5617, 'Recharge Successful , Amount: 100000<br/> Done:01/12/2022,08:12', '2022-12-01', 'unread', 'agent'),
(69, 111189338, 'Withdraw Successful , Amount: 200<br/> Done:02/12/2022,11:12', '2022-12-02', 'read', 'business'),
(70, 111189338, 'Withdraw Successful , Amount: 500<br/> Done:02/12/2022,02:12', '2022-12-02', 'read', 'business'),
(0, 5502, 'Recharge Successfully , Amount: 2,000<br/> To: Idt Dann<br/> Done: 03/12/2022, 01:12', '2022-12-03', 'unread', 'agent'),
(0, 5617, 'Recharge Successfully , Amount: 1,000<br/> To: Muta<br/> Done: 21/12/2022, 10:12', '2022-12-21', 'unread', 'agent'),
(0, 5617, 'Recharge Successfully , Amount: 300<br/> To: Muta<br/> Done: 21/12/2022, 10:12', '2022-12-21', 'unread', 'agent'),
(0, 4205, 'Recharge Successfully , Amount: 4,000<br/> To: Hamza<br/> Done: 21/12/2022, 10:12', '2022-12-21', 'unread', 'agent'),
(0, 5502, 'Recharge Successfully , Amount: 10,000<br/> To: Idt Dann<br/> Done: 27/12/2022, 09:12', '2022-12-27', 'unread', 'agent'),
(0, 5502, 'Recharge Successfully , Amount: 10,000<br/> To: Idt Dann<br/> Done: 27/12/2022, 09:12', '2022-12-27', 'unread', 'agent'),
(0, 5502, 'Recharge Successfully , Amount: 10,000<br/> To: Idt Dann<br/> Done: 27/12/2022, 10:12', '2022-12-27', 'unread', 'agent'),
(0, 5502, 'Recharge Successfully , Amount: 600<br/> To: Idt Dann<br/> Done: 27/12/2022, 10:12', '2022-12-27', 'unread', 'agent'),
(0, 5502, 'Recharge Successfully , Amount: 1,400<br/> To: Idt Dann<br/> Done: 27/12/2022, 10:12', '2022-12-27', 'unread', 'agent'),
(0, 5617, 'Recharged with , Amount: 50000 Rwf fromMuta<br/> Done:27/12/2022,10:12', '2022-12-27', 'unread', 'agent'),
(0, 2703, 'Recharge Successfully , Amount: 30,000<br/> To: NSHUTI<br/> Done: 28/12/2022, 07:12', '2022-12-28', 'unread', 'agent'),
(0, 2703, 'Recharged with , Amount: 15000 Rwf fromNSHUTI<br/> Done:28/12/2022,07:12', '2022-12-28', 'unread', 'agent'),
(0, 2307, 'Recharge Successfully , Amount: 340,000<br/> To: kagabo<br/> Done: 28/12/2022, 11:12', '2022-12-28', 'unread', 'agent');

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `Date` date DEFAULT NULL,
  `rID` int(9) NOT NULL,
  `Card_id` varchar(255) DEFAULT NULL,
  `Card_holder` varchar(255) DEFAULT NULL,
  `Amount_paid` int(255) DEFAULT NULL,
  `Status` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`Date`, `rID`, `Card_id`, `Card_holder`, `Amount_paid`, `Status`) VALUES
('2022-12-03', 111189338, 'FEE0A71E', '0', 2000, 'Paid'),
('2022-12-03', 111189338, 'FEE0A71E', 'MURIGANDE', 5000, 'Paid'),
('2022-12-03', 111189338, 'FEE0A71E', 'MURIGANDE', 2000, 'Paid'),
('2022-12-03', 111189338, 'FEE0A71E', 'MURIGANDE', 3000, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 2000, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 1000, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 1000, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 1000, 'Paid'),
('2022-12-20', 111189338, 'FA77C584', 'MUBERA', 100, 'Paid'),
('2022-12-20', 111189338, 'D43FD503', '', 1000, 'Paid'),
('2022-12-27', 111189338, 'FA77C584', 'gHHGH', 300, 'Paid'),
('2022-12-27', 111189338, 'FA77C584', 'gHHGH', 500, 'Paid'),
('2022-12-27', 111189338, 'FA77C584', 'gHHGH', 200, 'Paid'),
('2022-12-27', 120582059, 'FA77C584', 'gHHGH', 450, 'Paid'),
('2022-12-27', 0, 'FA77C584', 'gHHGH', 100, 'Paid'),
('2022-12-28', 120582059, 'D01FB53B', 'Bokasa', 500, 'Paid'),
('2022-12-28', 111189338, 'D01FB53B', 'Bokasa', 100, 'Paid'),
('2022-12-28', 111189338, 'D01FB53B', 'Bokasa', 100, 'Paid'),
('2022-12-28', 111189338, 'D01FB53B', 'Divin', 200, 'Paid'),
('2022-12-28', 111189338, 'D01FB53B', 'Divin', 500, 'Paid'),
('2022-12-28', 0, 'D01FB53B', 'Divin', 100, 'Paid'),
('2022-12-28', 111189338, 'D01FB53B', 'Divin', 4000, 'Paid'),
('2022-12-28', 120582059, 'D01FB53B', 'Divin', 1050, 'Paid'),
('2022-12-28', 100800300, 'D01FB53B', 'Divin', 800, 'Paid'),
('2022-12-28', 100800300, 'D01FB53B', 'Divin', 200, 'Paid'),
('2022-12-28', 100999777, '2C28570C', 'HAMZA', 300000, 'Paid'),
('2022-12-28', 120582059, '2C28570C', 'HAMZA', 2000, 'Paid'),
('2022-12-28', 120582059, '2C28570C', 'HAMZA', 100, 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `reference`
--

CREATE TABLE `reference` (
  `id` int(11) NOT NULL,
  `amount` varchar(100) NOT NULL DEFAULT '0',
  `business_pin` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `topup_ref`
--

CREATE TABLE `topup_ref` (
  `id` int(25) NOT NULL,
  `Amount` int(255) NOT NULL,
  `agent_pin` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `topup_ref`
--

INSERT INTO `topup_ref` (`id`, `Amount`, `agent_pin`) VALUES
(1, 1000, 5502);

-- --------------------------------------------------------

--
-- Table structure for table `transfering`
--

CREATE TABLE `transfering` (
  `tID` int(10) NOT NULL,
  `sender_ID` int(10) NOT NULL,
  `reciever_ID` varchar(25) NOT NULL,
  `amount` int(255) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `transfering`
--

INSERT INTO `transfering` (`tID`, `sender_ID`, `reciever_ID`, `amount`, `date`) VALUES
(16, 8, 'FEE0A71E', 2000, '2022-12-01'),
(17, 35, 'FEE0A71E', 3450, '2022-12-01'),
(18, 35, 'FEE0A71E', 2000, '2022-12-01'),
(19, 35, 'FEE0A71E', 2000, '2022-12-01'),
(20, 35, 'FEE0A71E', 1000, '2022-12-01'),
(21, 35, 'FEE0A71E', 50, '2022-12-01'),
(22, 35, 'FEE0A71E', 50, '0000-00-00'),
(23, 35, 'FEE0A71E', 300, '2022-12-01'),
(24, 36, 'FA77C584', 1000, '2022-12-27'),
(25, 36, 'D43FD503', 3000, '2022-12-27'),
(26, 36, 'D01FB53B', 1000, '2022-12-28'),
(27, 36, 'D01FB53B', 15000, '2022-12-28'),
(28, 49, '2C28570C', 10000, '2022-12-28'),
(29, 50, '2C28570C', 50000, '2022-12-28'),
(30, 36, 'FEE0A71E', 2000, '2022-12-28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_ID`);

--
-- Indexes for table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`aID`);

--
-- Indexes for table `agent_location`
--
ALTER TABLE `agent_location`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `business`
--
ALTER TABLE `business`
  ADD PRIMARY KEY (`bID`),
  ADD UNIQUE KEY `business_tin` (`business_tin`);

--
-- Indexes for table `business_location`
--
ALTER TABLE `business_location`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`cID`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`nID`);

--
-- Indexes for table `topup_ref`
--
ALTER TABLE `topup_ref`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transfering`
--
ALTER TABLE `transfering`
  ADD PRIMARY KEY (`tID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_ID` int(225) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `agent`
--
ALTER TABLE `agent`
  MODIFY `aID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `agent_location`
--
ALTER TABLE `agent_location`
  MODIFY `no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `business`
--
ALTER TABLE `business`
  MODIFY `bID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `business_location`
--
ALTER TABLE `business_location`
  MODIFY `no` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `cID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `nID` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `topup_ref`
--
ALTER TABLE `topup_ref`
  MODIFY `id` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `transfering`
--
ALTER TABLE `transfering`
  MODIFY `tID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
