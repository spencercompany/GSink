-- phpMyAdmin SQL Dump
-- version 3.3.0-beta1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Nov 01, 2011 at 09:06 PM
-- Server version: 5.1.53
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `project_gsink`
--

-- --------------------------------------------------------

--
-- Table structure for table `ContactManager_Companies`
--

DROP TABLE IF EXISTS `ContactManager_Companies`;
CREATE TABLE `ContactManager_Companies` (
  `Id` bigint(5) NOT NULL AUTO_INCREMENT,
  `SignUp_Date` datetime NOT NULL,
  `Last_Login_Date` datetime NOT NULL,
  `Status` enum('Inactive','Active') NOT NULL,
  `Company_Name` varchar(255) NOT NULL,
  `Admin_Login` varchar(255) NOT NULL,
  `Admin_Password_Hash` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Admin_Login` (`Admin_Login`),
  UNIQUE KEY `Email` (`Email`),
  KEY `SignUp_Date` (`SignUp_Date`),
  KEY `Last_Login_Date` (`Last_Login_Date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ContactManager_Companies`
--


-- --------------------------------------------------------

--
-- Table structure for table `ContactManager_Contacts`
--

DROP TABLE IF EXISTS `ContactManager_Contacts`;
CREATE TABLE `ContactManager_Contacts` (
  `Id` bigint(5) NOT NULL AUTO_INCREMENT,
  `GoogleAccount` varchar(255) NOT NULL,
  `GooglePassword` varchar(255) NOT NULL,
  `Company_Id` bigint(5) NOT NULL,
  `Account_Type` enum('Authoritative','Non-Authoritative') NOT NULL,
  `Last_Update` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Company_Id` (`Company_Id`),
  KEY `Account_Type` (`Account_Type`),
  KEY `Last_Update` (`Last_Update`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `ContactManager_Contacts`
--


-- --------------------------------------------------------

--
-- Table structure for table `ContactManager_Emails`
--

DROP TABLE IF EXISTS `ContactManager_Emails`;
CREATE TABLE `ContactManager_Emails` (
  `Company_Id` bigint(5) NOT NULL,
  `Email_Id` varchar(40) NOT NULL,
  `From` varchar(40) NOT NULL,
  `Subject` varchar(255) NOT NULL,
  `Message` text NOT NULL,
  PRIMARY KEY (`Company_Id`,`Email_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ContactManager_Emails`
--


-- --------------------------------------------------------

--
-- Table structure for table `ContactManager_ListEntries`
--

DROP TABLE IF EXISTS `ContactManager_ListEntries`;
CREATE TABLE `ContactManager_ListEntries` (
  `ContactManager_Contacts_Id` bigint(5) NOT NULL,
  `Address` varchar(50) NOT NULL,
  `Original_Account_Id` varchar(30) NOT NULL,
  `Serialized_Entry` text NOT NULL,
  `Entry_HTML` text NOT NULL,
  PRIMARY KEY (`ContactManager_Contacts_Id`,`Address`),
  KEY `ContactManager_Contacts_Id` (`ContactManager_Contacts_Id`),
  KEY `Original_Account_Id` (`Original_Account_Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ContactManager_ListEntries`
--


-- --------------------------------------------------------

--
-- Table structure for table `ContactManager_Logs`
--

DROP TABLE IF EXISTS `ContactManager_Logs`;
CREATE TABLE `ContactManager_Logs` (
  `Code` int(11) NOT NULL,
  `Response` text NOT NULL,
  `Type` enum('add','update') NOT NULL,
  `Account` varchar(255) NOT NULL,
  `Status` enum('success','failure') NOT NULL,
  `Address` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ContactManager_Logs`
--


-- --------------------------------------------------------

--
-- Table structure for table `ContactManager_PasswordResetCodes`
--

DROP TABLE IF EXISTS `ContactManager_PasswordResetCodes`;
CREATE TABLE `ContactManager_PasswordResetCodes` (
  `ResetCode` varchar(40) NOT NULL,
  `Company_Id` bigint(5) NOT NULL,
  `Expiration` datetime NOT NULL,
  PRIMARY KEY (`ResetCode`),
  KEY `Expiration` (`Expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ContactManager_PasswordResetCodes`
--

