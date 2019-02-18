-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 18, 2019 at 01:20 AM
-- Server version: 5.7.23
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quizapp_local`
--

-- --------------------------------------------------------

--
-- Table structure for table `qzh_answer`
--

DROP TABLE IF EXISTS `qzh_answer`;
CREATE TABLE `qzh_answer` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` varchar(256) NOT NULL,
  `answer_right` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `qzh_question`
--

DROP TABLE IF EXISTS `qzh_question`;
CREATE TABLE `qzh_question` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `qzh_quiz`
--

DROP TABLE IF EXISTS `qzh_quiz`;
CREATE TABLE `qzh_quiz` (
  `id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `qzh_user`
--

DROP TABLE IF EXISTS `qzh_user`;
CREATE TABLE `qzh_user` (
  `id` int(11) NOT NULL,
  `u_name` varchar(255) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `correct_answers` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `qzh_answer`
--
ALTER TABLE `qzh_answer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qzh_question`
--
ALTER TABLE `qzh_question`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qzh_quiz`
--
ALTER TABLE `qzh_quiz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qzh_user`
--
ALTER TABLE `qzh_user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `qzh_answer`
--
ALTER TABLE `qzh_answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `qzh_question`
--
ALTER TABLE `qzh_question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `qzh_quiz`
--
ALTER TABLE `qzh_quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `qzh_user`
--
ALTER TABLE `qzh_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
