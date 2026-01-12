-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.40 - MySQL Community Server - GPL
-- Server OS:                    Linux
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


-- Dumping database structure for quota
CREATE DATABASE IF NOT EXISTS `quota` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `quota`;

-- Dumping structure for table quota.plans
CREATE TABLE IF NOT EXISTS `plans` (
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT (0),
  `name` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL,
  `min_GPAX` float NOT NULL DEFAULT '0',
  `min_GPA_MAT` float NOT NULL DEFAULT '0',
  `min_GPA_SCI` float NOT NULL DEFAULT '0',
  `allow_ungrade` tinyint(1) NOT NULL DEFAULT '0',
  `allow_not_meet_req` tinyint(1) NOT NULL DEFAULT '0',
  `allow_behavior_fail` tinyint(1) NOT NULL DEFAULT '0',
  `img_cover` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default%20image.jpg',
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`code`) USING BTREE,
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table quota.students
CREATE TABLE IF NOT EXISTS `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `student_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `GPAX` float NOT NULL,
  `GPA_MAT` float NOT NULL,
  `GPA_SCI` float NOT NULL,
  `GPA_ENG` float DEFAULT NULL,
  `GPA_Fail` int NOT NULL,
  `behavior_pass` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=472 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table quota.system_settings
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `registration_enabled` tinyint NOT NULL DEFAULT (0),
  `registration_start_date` datetime DEFAULT NULL,
  `registration_end_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

INSERT INTO `plans` (`code`, `name`, `min_GPAX`, `min_GPA_MAT`, `min_GPA_SCI`, `allow_ungrade`, `allow_not_meet_req`, `allow_behavior_fail`, `img_cover`, `color`, `order`) VALUES ('eng', 'ภาษาอังกฤษ – คณิตศาสตร์', 0, 0, 0, 0, 0, 0, 'eng.gif', 0, 2);
INSERT INTO `plans` (`code`, `name`, `min_GPAX`, `min_GPA_MAT`, `min_GPA_SCI`, `allow_ungrade`, `allow_not_meet_req`, `allow_behavior_fail`, `img_cover`, `color`, `order`) VALUES ('mou', 'การจัดการธุรกิจการค้าสมัยใหม่ : MOU CP ALL', 0, 0, 0, 0, 0, 0, 'mou.gif', 0, 3);
INSERT INTO `plans` (`code`, `name`, `min_GPAX`, `min_GPA_MAT`, `min_GPA_SCI`, `allow_ungrade`, `allow_not_meet_req`, `allow_behavior_fail`, `img_cover`, `color`, `order`) VALUES ('sci', 'วิทยาศาสตร์ – คณิตศาสตร์', 2.75, 2.5, 2.5, 0, 0, 0, 'sci.gif', 0, 1);

INSERT INTO `system_settings` (`id`, `registration_enabled`, `registration_start_date`, `registration_end_date`) VALUES (1, 1, '2024-12-09 08:00:00', '2024-12-11 16:00:00');

-- Dumping structure for table quota.plan_questions
CREATE TABLE IF NOT EXISTS `plan_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `plan_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int NOT NULL DEFAULT '0',
  `required` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `plan_code` (`plan_code`),
  CONSTRAINT `fk_plan_questions_plan` FOREIGN KEY (`plan_code`) REFERENCES `plans` (`code`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table quota.student_question_answers
CREATE TABLE IF NOT EXISTS `student_question_answers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `question_id` int NOT NULL,
  `answer` tinyint(1) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `fk_student_answers_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_student_answers_question` FOREIGN KEY (`question_id`) REFERENCES `plan_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Insert data for plan questions
-- Example questions for different plans (you can customize these)
-- INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) VALUES ('sci', 'คุณมีความสนใจในการเรียนวิทยาศาสตร์และคณิตศาสตร์หรือไม่?', 1, 1);
-- INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) VALUES ('sci', 'คุณพร้อมที่จะเรียนเนื้อหาที่ท้าทายมากขึ้นหรือไม่?', 2, 1);
-- INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) VALUES ('eng', 'คุณมีพื้นฐานภาษาอังกฤษที่ดีหรือไม่?', 1, 1);
-- INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) VALUES ('mou', 'คุณสนใจทำงานในธุรกิจค้าปลีกหรือไม่?', 1, 1);

-- Active question for sci plan
INSERT INTO `plan_questions` (`plan_code`, `question`, `order`, `required`) VALUES ('sci', 'คุณสนใจเข้าเรียนห้องวิทยาศาสตร์พลังสิบ (ห้อง 3) หรือไม่', 1, 1);
