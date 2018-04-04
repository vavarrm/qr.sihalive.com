-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 04, 2018 at 05:04 AM
-- Server version: 10.1.30-MariaDB
-- PHP Version: 7.2.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `casino`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_account`
--

CREATE TABLE `admin_account` (
  `Acc_ID` int(11) NOT NULL,
  `Acc_Name` int(11) NOT NULL,
  `Acc_Password` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `Admin_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `table_admin`
--

CREATE TABLE `table_admin` (
  `Admin_ID` int(11) NOT NULL,
  `Frist_Name` int(11) NOT NULL,
  `Last_Name` int(11) NOT NULL,
  `Gender` int(11) NOT NULL,
  `Phone` int(11) NOT NULL,
  `Create_Date` int(11) NOT NULL,
  `About` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `table_comapany`
--

CREATE TABLE `table_comapany` (
  `Com_Name` int(11) NOT NULL,
  `Com_ID` int(11) NOT NULL,
  `Com_Address` int(11) NOT NULL,
  `Phone` int(11) NOT NULL,
  `Website` int(11) NOT NULL,
  `lat` int(11) NOT NULL,
  `lang` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `table_log`
--

CREATE TABLE `table_log` (
  `Log_ID` int(11) NOT NULL,
  `Date_Time_In` int(11) NOT NULL,
  `Date_Time_Out` int(11) NOT NULL,
  `Acc_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `table_message`
--

CREATE TABLE `table_message` (
  `message` int(11) NOT NULL,
  `date_time` int(11) NOT NULL,
  `message_Id` int(11) NOT NULL,
  `Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `table_qrcode`
--

CREATE TABLE `table_qrcode` (
  `QR_ID` int(11) NOT NULL,
  `WebSite` int(11) NOT NULL,
  `Com_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `table_tuktuk`
--

CREATE TABLE `table_tuktuk` (
  `Tuktuk_ID` int(11) NOT NULL,
  `Phone` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `Name` int(11) NOT NULL,
  `message_Id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `table_user`
--

CREATE TABLE `table_user` (
  `Id` int(11) NOT NULL,
  `Last_Name` varchar(200) NOT NULL,
  `Gender` varchar(100) NOT NULL,
  `First_Name` varchar(200) NOT NULL,
  `Phone` varchar(100) NOT NULL,
  `lat` varchar(100) NOT NULL,
  `lang` varchar(100) NOT NULL,
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_account`
--
ALTER TABLE `admin_account`
  ADD PRIMARY KEY (`Acc_ID`),
  ADD KEY `Admin_ID` (`Admin_ID`);

--
-- Indexes for table `table_admin`
--
ALTER TABLE `table_admin`
  ADD PRIMARY KEY (`Admin_ID`);

--
-- Indexes for table `table_comapany`
--
ALTER TABLE `table_comapany`
  ADD PRIMARY KEY (`Com_ID`);

--
-- Indexes for table `table_log`
--
ALTER TABLE `table_log`
  ADD PRIMARY KEY (`Log_ID`),
  ADD KEY `Acc_ID` (`Acc_ID`);

--
-- Indexes for table `table_message`
--
ALTER TABLE `table_message`
  ADD PRIMARY KEY (`message_Id`),
  ADD KEY `Id` (`Id`);

--
-- Indexes for table `table_qrcode`
--
ALTER TABLE `table_qrcode`
  ADD PRIMARY KEY (`QR_ID`),
  ADD KEY `Com_ID` (`Com_ID`);

--
-- Indexes for table `table_tuktuk`
--
ALTER TABLE `table_tuktuk`
  ADD KEY `message_Id` (`message_Id`);

--
-- Indexes for table `table_user`
--
ALTER TABLE `table_user`
  ADD PRIMARY KEY (`Id`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_account`
--
ALTER TABLE `admin_account`
  ADD CONSTRAINT `admin_account_ibfk_1` FOREIGN KEY (`Admin_ID`) REFERENCES `table_admin` (`Admin_ID`);

--
-- Constraints for table `table_log`
--
ALTER TABLE `table_log`
  ADD CONSTRAINT `table_log_ibfk_1` FOREIGN KEY (`Acc_ID`) REFERENCES `admin_account` (`Acc_ID`);

--
-- Constraints for table `table_message`
--
ALTER TABLE `table_message`
  ADD CONSTRAINT `table_message_ibfk_1` FOREIGN KEY (`Id`) REFERENCES `table_user` (`Id`);

--
-- Constraints for table `table_qrcode`
--
ALTER TABLE `table_qrcode`
  ADD CONSTRAINT `table_qrcode_ibfk_1` FOREIGN KEY (`Com_ID`) REFERENCES `table_comapany` (`Com_ID`);

--
-- Constraints for table `table_tuktuk`
--
ALTER TABLE `table_tuktuk`
  ADD CONSTRAINT `table_tuktuk_ibfk_1` FOREIGN KEY (`message_Id`) REFERENCES `table_message` (`message_Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
