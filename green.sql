-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2025 at 05:49 PM
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
-- Database: `green`
--

-- --------------------------------------------------------

--
-- Table structure for table `footprint`
--

CREATE TABLE `footprint` (
  `rec` int(3) NOT NULL,
  `uid` varchar(100) NOT NULL,
  `trans` varchar(200) NOT NULL,
  `tt` varchar(200) NOT NULL,
  `ec` varchar(100) NOT NULL,
  `es` varchar(100) NOT NULL,
  `wg` varchar(20) NOT NULL,
  `wr` varchar(20) NOT NULL,
  `dt` varchar(100) NOT NULL,
  `date` varchar(10) NOT NULL,
  `te` varchar(10) NOT NULL,
  `ee` varchar(10) NOT NULL,
  `we` varchar(10) NOT NULL,
  `de` varchar(10) NOT NULL,
  `fe` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `footprint`
--

INSERT INTO `footprint` (`rec`, `uid`, `trans`, `tt`, `ec`, `es`, `wg`, `wr`, `dt`, `date`, `te`, `ee`, `we`, `de`, `fe`) VALUES
(0, '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `name` varchar(150) NOT NULL,
  `email` varchar(100) NOT NULL,
  `uid` varchar(15) NOT NULL,
  `pwd` varchar(150) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `age` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `footprint`
--
ALTER TABLE `footprint`
  ADD UNIQUE KEY `rec` (`rec`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `uid` (`uid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
