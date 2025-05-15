-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for edu_users
CREATE DATABASE
IF NOT EXISTS `edu_users` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `edu_users`;

-- Dumping structure for table edu_users.parents_students
CREATE TABLE IF NOT EXISTS `parents_students` (
  `student_id` int NOT NULL AUTO_INCREMENT,
  `parent_pin` int NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `classes` int DEFAULT NULL CHECK (`classes` >= 1 AND `classes` <= 12),
  PRIMARY KEY (`student_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping structure for table edu_users.teachers
CREATE TABLE IF NOT EXISTS `teachers` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Sample insert for a teacher user (password is 'password' hashed)
INSERT INTO `teachers` (`name`, `email`, `password`) VALUES ('Sample Teacher', 'teacher@example.com', '$2y$10$eImiTXuWVxfM37uY4JANjQ==');

-- Dumping data for table edu_users.parents_students: ~0 rows (approximately)

-- Dumping structure for table edu_users.report_history
CREATE TABLE
IF NOT EXISTS `report_history`
(
  `report_id` int NOT NULL AUTO_INCREMENT,
  `class` varchar
(50) DEFAULT NULL,
  `std_name` varchar
(100) DEFAULT NULL,
  `participation` int DEFAULT NULL,
  `understanding` int DEFAULT NULL,
  `behavior` int DEFAULT NULL,
  `emotional` int DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `student_id` int DEFAULT NULL,
  `teacher_id` int DEFAULT NULL,
  PRIMARY KEY
(`report_id`),
  KEY `student_id`
(`student_id`),
  KEY `teacher_id`
(`teacher_id`),
  CONSTRAINT `report_history_ibfk_1` FOREIGN KEY
(`student_id`) REFERENCES `parents_students`
(`student_id`),
  CONSTRAINT `report_history_ibfk_2` FOREIGN KEY
(`teacher_id`) REFERENCES `teachers`
(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Table for teacher-student assignment requests
CREATE TABLE IF NOT EXISTS `teacher_student_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `teacher_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `approved` TINYINT(1) DEFAULT 0,
  FOREIGN KEY (`teacher_id`) REFERENCES `teachers`(`user_id`),
  FOREIGN KEY (`student_id`) REFERENCES `parents_students`(`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table edu_users.report_history: ~0 rows (approximately)

-- Dumping structure for table edu_users.teachers
CREATE TABLE
IF NOT EXISTS `teachers`
(
  `name` varchar
(50) DEFAULT NULL,
  `email` varchar
(50) DEFAULT NULL,
  `password` varchar
(255) DEFAULT NULL,
  `user_id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY
(`user_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table edu_users.teachers: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
