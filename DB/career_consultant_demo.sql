-- ============================================================
-- プレゼン用デモDB: career_consultant
-- 作成日: 2026-04-26
-- ※新規サーバーへの一発インポート用（ALTER TABLE不使用）
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `career_consultant` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `career_consultant`;

-- ============================================================
-- テーブル定義
-- ============================================================

CREATE TABLE `m_admins` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `login_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `m_consultants` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `m_courses_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `m_meating_styles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `m_request_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `m_request_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `m_rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `m_student_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `m_times` (
  `id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `m_courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `room_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `m_students` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `number` int(11) NOT NULL,
  `login_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `t_cc_slots` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `is_cc_plus` tinyint(1) NOT NULL COMMENT 'デフォルト:0、1ならキャリコンプラス用',
  `consultant_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `t_cc_bookings` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `cc_slot_id` int(11) NOT NULL,
  `time_id` int(11) NOT NULL,
  `style_id` int(11) NOT NULL,
  `cc_plus_booking_id` int(11) DEFAULT NULL COMMENT 'CC+仮予約から確定した通常予約の場合、元CC+予約のID。通常予約起源の場合はNULL',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `t_cc_requests` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `booking_id_a` int(11) DEFAULT NULL,
  `booking_id_b` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `t_course_cc_schedules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `cc_count` int(11) NOT NULL,
  `date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `t_reset_requests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- マスタデータ
-- ============================================================

-- m_admins（パスワード 'password' のbcryptハッシュ）
INSERT INTO `m_admins` (`id`, `first_name`, `last_name`, `login_id`, `password`, `created_at`, `updated_at`) VALUES
(1, '太郎', '管理', 'admin001', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', '2026-04-26 09:00:00', '2026-04-26 09:00:00');

-- m_consultants
INSERT INTO `m_consultants` (`id`, `first_name`, `last_name`, `created_at`, `updated_at`) VALUES
(1, '信彦', '田中', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(2, '花子', '山田', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(3, '一郎', '鈴木', '2026-04-26 09:00:00', '2026-04-26 09:00:00');

-- m_courses_categories
INSERT INTO `m_courses_categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '求職者支援訓練', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(2, '公共職業訓練', '2026-04-26 09:00:00', '2026-04-26 09:00:00');

-- m_meating_styles
INSERT INTO `m_meating_styles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'ZOOM', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(2, '対面', '2026-04-26 09:00:00', '2026-04-26 09:00:00');

-- m_request_status
INSERT INTO `m_request_status` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '新規', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(2, '未対応', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(3, '承認', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(4, '却下', '2026-04-26 09:00:00', '2026-04-26 09:00:00');

-- m_request_types
INSERT INTO `m_request_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'cc+予約', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(2, 'cc+変更', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(3, 'cc+キャンセル', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(4, 'cc変更', '2026-04-26 09:00:00', '2026-04-26 09:00:00');

-- m_rooms
INSERT INTO `m_rooms` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1,  '6A', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(2,  '6B', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(3,  '6C', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(4,  '6D', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(5,  '6E', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(6,  '6F', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(7,  '6G', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(8,  '6H', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(9,  '7A', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(10, '7B', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(11, '7C', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(12, '7D', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(13, 'CC', '2026-04-26 09:00:00', '2026-04-26 09:00:00');

-- m_student_status
INSERT INTO `m_student_status` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '在校中', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(2, '退校',   '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(3, '就職',   '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(4, '修了',   '2026-04-26 09:00:00', '2026-04-26 09:00:00');

-- m_times
INSERT INTO `m_times` (`id`, `start_time`, `display_name`, `created_at`, `updated_at`) VALUES
(1, '10:00:00', '10時～', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(2, '11:00:00', '11時～', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(3, '12:00:00', '12時～', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(4, '14:00:00', '14時～', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(5, '15:00:00', '15時～', '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(6, '16:00:00', '16時～', '2026-04-26 09:00:00', '2026-04-26 09:00:00');

-- ============================================================
-- コース・生徒
-- ============================================================

-- m_courses（3コース）
INSERT INTO `m_courses` (`id`, `name`, `start_date`, `end_date`, `room_id`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'Web・SNS動画編集＆ホームページデザイン科',    '2025-11-21', '2026-05-20', 1, 1, '2025-11-01 09:00:00', '2025-11-01 09:00:00'),
(2, '基礎から学ぶJava＋Pythonプログラマー養成科', '2026-01-21', '2026-07-18', 2, 1, '2026-01-05 09:00:00', '2026-01-05 09:00:00'),
(3, '基礎から学ぶOffice実務科',                   '2026-02-02', '2026-07-31', 3, 2, '2026-01-15 09:00:00', '2026-01-15 09:00:00');

-- m_students（29名、パスワードはすべて 'password' のbcryptハッシュ）
-- コース1（求職者・5名: s1〜s5）在校中4名, 退校1名(s5)
-- コース2（求職者・20名: s6〜s25）在校中18名, 就職1名(s24), 退校1名(s25)
-- コース3（公共・4名: s26〜s29）在校中4名
INSERT INTO `m_students` (`id`, `first_name`, `last_name`, `number`, `login_id`, `password`, `status_id`, `course_id`, `created_at`, `updated_at`) VALUES
(1,  '花子',   '山田',   1,  '202511_6A01', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 1, '2025-11-20 10:00:00', '2025-11-20 10:00:00'),
(2,  '一郎',   '田中',   2,  '202511_6A02', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 1, '2025-11-20 10:00:00', '2025-11-20 10:00:00'),
(3,  '幸子',   '鈴木',   3,  '202511_6A03', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 1, '2025-11-20 10:00:00', '2025-11-20 10:00:00'),
(4,  '健一',   '佐藤',   4,  '202511_6A04', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 1, '2025-11-20 10:00:00', '2025-11-20 10:00:00'),
(5,  '美咲',   '高橋',   5,  '202511_6A05', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 2, 1, '2025-11-20 10:00:00', '2026-02-10 10:00:00'),
(6,  '拓也',   '伊藤',   1,  '202601_6B01', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(7,  '麻衣',   '中村',   2,  '202601_6B02', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(8,  '隆',     '小林',   3,  '202601_6B03', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(9,  'さくら', '加藤',   4,  '202601_6B04', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(10, '浩',     '吉田',   5,  '202601_6B05', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(11, '千夏',   '渡辺',   6,  '202601_6B06', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(12, '直樹',   '斎藤',   7,  '202601_6B07', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(13, '愛',     '松本',   8,  '202601_6B08', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(14, '勇',     '井上',   9,  '202601_6B09', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(15, '恵',     '木村',   10, '202601_6B10', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(16, '正樹',   '林',     11, '202601_6B11', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(17, '奈緒',   '清水',   12, '202601_6B12', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(18, '大介',   '山口',   13, '202601_6B13', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(19, '静',     '池田',   14, '202601_6B14', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(20, '裕',     '橋本',   15, '202601_6B15', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(21, '由美子', '阿部',   16, '202601_6B16', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(22, '翔',     '石田',   17, '202601_6B17', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(23, '晴美',   '前田',   18, '202601_6B18', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 2, '2026-01-20 10:00:00', '2026-01-20 10:00:00'),
(24, '誠',     '藤田',   19, '202601_6B19', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 3, 2, '2026-01-20 10:00:00', '2026-03-15 10:00:00'),
(25, '明子',   '近藤',   20, '202601_6B20', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 2, 2, '2026-01-20 10:00:00', '2026-03-01 10:00:00'),
(26, '春樹',   '後藤',   1,  '202602_6C01', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 3, '2026-02-01 10:00:00', '2026-02-01 10:00:00'),
(27, '恵子',   '村上',   2,  '202602_6C02', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 3, '2026-02-01 10:00:00', '2026-02-01 10:00:00'),
(28, '義彦',   '長谷川', 3,  '202602_6C03', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 3, '2026-02-01 10:00:00', '2026-02-01 10:00:00'),
(29, 'さやか', '坂本',   4,  '202602_6C04', '$2y$10$GY7eTaGqtBEc9P.ofQkNJes6xAjtgSflhv1vHiyJXAkPJWDplGRui', 1, 3, '2026-02-01 10:00:00', '2026-02-01 10:00:00');

-- ============================================================
-- トランザクションデータ
-- ============================================================

-- t_course_cc_schedules（1回につき2日制）
-- コース1: 第1〜3回 × 2日 = 6件
-- コース2: 第1〜3回 × 2日 = 6件
INSERT INTO `t_course_cc_schedules` (`id`, `course_id`, `cc_count`, `date`, `created_at`, `updated_at`) VALUES
(1,  1, 1, '2026-01-17', '2025-11-01 10:00:00', '2025-11-01 10:00:00'),
(2,  1, 1, '2026-01-31', '2025-11-01 10:00:00', '2025-11-01 10:00:00'),
(3,  1, 2, '2026-02-21', '2025-11-01 10:00:00', '2025-11-01 10:00:00'),
(4,  1, 2, '2026-03-07', '2025-11-01 10:00:00', '2025-11-01 10:00:00'),
(5,  1, 3, '2026-04-11', '2025-11-01 10:00:00', '2025-11-01 10:00:00'),
(6,  1, 3, '2026-04-25', '2025-11-01 10:00:00', '2025-11-01 10:00:00'),
(7,  2, 1, '2026-03-14', '2026-01-05 10:00:00', '2026-01-05 10:00:00'),
(8,  2, 1, '2026-03-28', '2026-01-05 10:00:00', '2026-01-05 10:00:00'),
(9,  2, 2, '2026-04-11', '2026-01-05 10:00:00', '2026-01-05 10:00:00'),
(10, 2, 2, '2026-04-25', '2026-01-05 10:00:00', '2026-01-05 10:00:00'),
(11, 2, 3, '2026-05-23', '2026-01-05 10:00:00', '2026-01-05 10:00:00'),
(12, 2, 3, '2026-06-06', '2026-01-05 10:00:00', '2026-01-05 10:00:00');

-- t_cc_slots（22枠）
-- ・コース1（5名）: 各日1スロット
-- ・コース2（20名）: 各日2スロット（6名+4名 or 6名+6名）
-- ・CC+枠: 2026-04-11 に2枠、2026-05-09 に2枠（未来）
-- ・consultant/roomは過去分のみ設定、未来分はNULL
INSERT INTO `t_cc_slots` (`id`, `date`, `is_cc_plus`, `consultant_id`, `room_id`, `created_at`, `updated_at`) VALUES
-- コース1 第1回 (2日制)
(1,  '2026-01-17', 0, 1, 13, '2026-01-10 10:00:00', '2026-01-10 10:00:00'),
(2,  '2026-01-31', 0, 1, 13, '2026-01-10 10:00:00', '2026-01-10 10:00:00'),
-- コース1 第2回 (2日制)
(3,  '2026-02-21', 0, 1, 13, '2026-02-15 10:00:00', '2026-02-15 10:00:00'),
(4,  '2026-03-07', 0, 1, 13, '2026-02-15 10:00:00', '2026-02-15 10:00:00'),
-- コース2 第1回-day1 (2026-03-14, slot×2)
(5,  '2026-03-14', 0, 1, 13, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(6,  '2026-03-14', 0, 2, NULL,'2026-03-07 10:00:00', '2026-03-07 10:00:00'),
-- コース2 第1回-day2 (2026-03-28, slot×2)
(7,  '2026-03-28', 0, 3, 13, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(8,  '2026-03-28', 0, NULL,NULL,'2026-03-07 10:00:00', '2026-03-07 10:00:00'),
-- 2026-04-11: コース1 第3回-day1 + コース2 第2回-day1(slot×2) + CC+(slot×2)
(9,  '2026-04-11', 0, 1, 13, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(10, '2026-04-11', 0, 2, NULL,'2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(11, '2026-04-11', 0, 3, NULL,'2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(12, '2026-04-11', 1, 2, 13, '2026-03-11 10:00:00', '2026-03-11 10:00:00'),
(13, '2026-04-11', 1, 3, NULL,'2026-03-11 10:00:00', '2026-03-11 10:00:00'),
-- 2026-04-25: コース1 第3回-day2 + コース2 第2回-day2(slot×2)
(14, '2026-04-25', 0, 1, 13, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(15, '2026-04-25', 0, 2, 13, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(16, '2026-04-25', 0, 3, NULL,'2026-04-05 10:00:00', '2026-04-05 10:00:00'),
-- CC+枠 (2026-05-09, 未来)
(17, '2026-05-23', 1, NULL,NULL,'2026-04-09 10:00:00', '2026-04-09 10:00:00'),
(18, '2026-05-23', 1, NULL,NULL,'2026-04-09 10:00:00', '2026-04-09 10:00:00'),
-- コース2 第3回-day1 (2026-05-23, 未来, slot×2)
(19, '2026-05-23', 0, NULL,NULL,'2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(20, '2026-05-23', 0, NULL,NULL,'2026-04-26 09:00:00', '2026-04-26 09:00:00'),
-- コース2 第3回-day2 (2026-06-06, 未来, slot×2)
(21, '2026-06-06', 0, NULL,NULL,'2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(22, '2026-06-06', 0, NULL,NULL,'2026-04-26 09:00:00', '2026-04-26 09:00:00');

-- t_cc_bookings（75件）
-- slot9 = コース1第3回-day1、slot10,11 = コース2第2回-day1
-- slot12,13 = CC+枠（2026-04-11）
-- CC+予約: id=44〜48（cc_plus_booking_id=NULL）
-- ユニーク制約: (cc_slot_id, time_id)
INSERT INTO `t_cc_bookings` (`id`, `student_id`, `cc_slot_id`, `time_id`, `style_id`, `cc_plus_booking_id`, `created_at`, `updated_at`) VALUES
-- コース1 第1回-day1 (slot1)
(1,  1, 1, 1, 2, NULL, '2026-01-10 10:00:00', '2026-01-10 10:00:00'),
(2,  2, 1, 2, 2, NULL, '2026-01-10 10:00:00', '2026-01-10 10:00:00'),
(3,  3, 1, 3, 2, NULL, '2026-01-10 10:00:00', '2026-01-10 10:00:00'),
-- コース1 第1回-day2 (slot2)
(4,  4, 2, 1, 2, NULL, '2026-01-10 10:00:00', '2026-01-10 10:00:00'),
(5,  5, 2, 2, 2, NULL, '2026-01-10 10:00:00', '2026-01-10 10:00:00'),
-- コース1 第2回-day1 (slot3)
(6,  1, 3, 1, 2, NULL, '2026-02-15 10:00:00', '2026-02-15 10:00:00'),
(7,  2, 3, 2, 2, NULL, '2026-02-15 10:00:00', '2026-02-15 10:00:00'),
(8,  3, 3, 3, 2, NULL, '2026-02-15 10:00:00', '2026-02-15 10:00:00'),
-- コース1 第2回-day2 (slot4)
(9,  4, 4, 1, 2, NULL, '2026-02-15 10:00:00', '2026-02-15 10:00:00'),
(10, 5, 4, 2, 2, NULL, '2026-02-15 10:00:00', '2026-02-15 10:00:00'),
-- コース2 第1回-day1 slot5 (s6〜s11)
(11, 6,  5, 1, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(12, 7,  5, 2, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(13, 8,  5, 3, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(14, 9,  5, 4, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(15, 10, 5, 5, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(16, 11, 5, 6, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
-- コース2 第1回-day1 slot6 (s12〜s15)
(17, 12, 6, 1, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(18, 13, 6, 2, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(19, 14, 6, 3, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(20, 15, 6, 4, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
-- コース2 第1回-day2 slot7 (s16〜s21)
(21, 16, 7, 1, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(22, 17, 7, 2, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(23, 18, 7, 3, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(24, 19, 7, 4, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(25, 20, 7, 5, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(26, 21, 7, 6, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
-- コース2 第1回-day2 slot8 (s22〜s25)
(27, 22, 8, 1, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(28, 23, 8, 2, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(29, 24, 8, 3, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
(30, 25, 8, 4, 2, NULL, '2026-03-07 10:00:00', '2026-03-07 10:00:00'),
-- コース1 第3回-day1 (slot9, 2026-04-11)
(31, 1, 9, 1, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(32, 2, 9, 2, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(33, 3, 9, 3, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
-- コース2 第2回-day1 slot10 (s6〜s11)
(34, 6,  10, 1, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(35, 7,  10, 2, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(36, 8,  10, 3, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(37, 9,  10, 4, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(38, 10, 10, 5, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(39, 11, 10, 6, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
-- コース2 第2回-day1 slot11 (s12〜s15)
(40, 12, 11, 1, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(41, 13, 11, 2, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(42, 14, 11, 3, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(43, 15, 11, 4, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
-- CC+予約 slot12 (s6,s7,s8) - cc_plus_booking_id=NULL
(44, 6,  12, 1, 1, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(45, 7,  12, 2, 1, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(46, 8,  12, 3, 1, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
-- CC+予約 slot13 (s9,s10)
(47, 9,  13, 1, 1, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(48, 10, 13, 2, 1, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
-- コース1 第3回-day2 (slot14, 2026-04-25) s5退校のためs4のみ
(49, 4, 14, 1, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
-- コース2 第2回-day2 slot15 (s16〜s21)
(50, 16, 15, 1, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(51, 17, 15, 2, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(52, 18, 15, 3, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(53, 19, 15, 4, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(54, 20, 15, 5, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(55, 21, 15, 6, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
-- コース2 第2回-day2 slot16 (s22,s23 ※s24就職・s25退校除外)
(56, 22, 16, 1, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
(57, 23, 16, 2, 2, NULL, '2026-04-05 10:00:00', '2026-04-05 10:00:00'),
-- コース2 第3回-day1 slot19 (s6〜s11, 未来)
(58, 6,  19, 1, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(59, 7,  19, 2, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(60, 8,  19, 3, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(61, 9,  19, 4, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(62, 10, 19, 5, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(63, 11, 19, 6, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
-- コース2 第3回-day1 slot20 (s12〜s14)
(64, 12, 20, 1, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(65, 13, 20, 2, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(66, 14, 20, 3, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
-- コース2 第3回-day2 slot21 (s15〜s20)
(67, 15, 21, 1, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(68, 16, 21, 2, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(69, 17, 21, 3, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(70, 18, 21, 4, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(71, 19, 21, 5, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(72, 20, 21, 6, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
-- コース2 第3回-day2 slot22 (s21〜s23)
(73, 21, 22, 1, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(74, 22, 22, 2, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
(75, 23, 22, 3, 2, NULL, '2026-04-26 09:00:00', '2026-04-26 09:00:00'),
-- CC+予約申請用 (s26,s27 → slot17=2026-05-09)
(76, 26, 17, 1, 2, NULL, '2026-04-20 10:00:00', '2026-04-20 10:00:00'),
(77, 27, 17, 2, 1, NULL, '2026-04-21 10:00:00', '2026-04-21 10:00:00'),
-- CC+変更申請用変更先 (s6,s7 → slot18=2026-05-09)
(78, 6,  18, 1, 1, NULL, '2026-04-12 10:00:00', '2026-04-12 10:00:00'),
(79, 7,  18, 2, 1, NULL, '2026-04-12 11:00:00', '2026-04-12 11:00:00');

-- t_cc_requests（各タイプ2件 = 計8件）
-- type1 CC+予約:  booking_id_a=申請時に作成した仮予約(76,77)
-- type2 CC+変更:  booking_id_a=変更元CC+予約(44,45), booking_id_b=変更先CC+予約(78,79)
-- type3 CC+キャンセル: booking_id_a=キャンセル対象CC+予約(46,47)
-- type4 CC変更:   booking_id_a=自分の必須CC予約(38,40), booking_id_b=相手の必須CC予約(50,51)
INSERT INTO `t_cc_requests` (`id`, `type_id`, `student_id`, `status_id`, `message`, `booking_id_a`, `booking_id_b`, `created_at`, `updated_at`) VALUES
(1, 1, 26, 1, 'キャリコンプラスの予約を希望します',             76,   NULL, '2026-04-20 10:00:00', '2026-04-20 10:00:00'),
(2, 1, 27, 2, 'キャリコンプラスに参加したいです',               77,   NULL, '2026-04-21 10:00:00', '2026-04-21 10:00:00'),
(3, 2,  6, 1, 'CC+の日程変更をお願いします',                   44,   78,   '2026-04-12 10:00:00', '2026-04-12 10:00:00'),
(4, 2,  7, 2, '予定が変わりましたので変更希望です',             45,   79,   '2026-04-12 11:00:00', '2026-04-12 11:00:00'),
(5, 3,  8, 1, '都合が悪くなりましたのでCC+をキャンセルします', 46,   NULL, '2026-04-12 12:00:00', '2026-04-12 12:00:00'),
(6, 3,  9, 2, '体調不良のためCC+のキャンセルをお願いします',   47,   NULL, '2026-04-12 13:00:00', '2026-04-12 13:00:00'),
(7, 4, 10, 1, '仕事の都合でCC日程の変更をお願いします',        38,   50,   '2026-04-18 10:00:00', '2026-04-18 10:00:00'),
(8, 4, 12, 2, '急用が入ったため日程変更したいです',             40,   51,   '2026-04-18 11:00:00', '2026-04-18 11:00:00');

-- ============================================================
-- インデックス・PRIMARY KEY
-- ============================================================

ALTER TABLE `m_admins`           ADD PRIMARY KEY (`id`);
ALTER TABLE `m_consultants`      ADD PRIMARY KEY (`id`);
ALTER TABLE `m_courses_categories` ADD PRIMARY KEY (`id`);
ALTER TABLE `m_meating_styles`   ADD PRIMARY KEY (`id`);
ALTER TABLE `m_request_status`   ADD PRIMARY KEY (`id`);
ALTER TABLE `m_request_types`    ADD PRIMARY KEY (`id`);
ALTER TABLE `m_rooms`            ADD PRIMARY KEY (`id`);
ALTER TABLE `m_student_status`   ADD PRIMARY KEY (`id`);
ALTER TABLE `m_times`            ADD PRIMARY KEY (`id`);
ALTER TABLE `m_courses`          ADD PRIMARY KEY (`id`), ADD KEY `course_room` (`room_id`), ADD KEY `course_category` (`category_id`);
ALTER TABLE `m_students`         ADD PRIMARY KEY (`id`), ADD KEY `student_status` (`status_id`), ADD KEY `student_course` (`course_id`);
ALTER TABLE `t_cc_slots`         ADD PRIMARY KEY (`id`), ADD KEY `slot_consultant` (`consultant_id`), ADD KEY `slot_room` (`room_id`);
ALTER TABLE `t_cc_bookings`      ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `uq_slot_time` (`cc_slot_id`, `time_id`), ADD KEY `booking_student` (`student_id`), ADD KEY `booking_slot` (`cc_slot_id`), ADD KEY `booking_time` (`time_id`), ADD KEY `booking_style` (`style_id`), ADD KEY `booking_cc_plus` (`cc_plus_booking_id`);
ALTER TABLE `t_cc_requests`      ADD PRIMARY KEY (`id`), ADD KEY `cc_request_type` (`type_id`), ADD KEY `cc_request_student` (`student_id`), ADD KEY `cc_request_status` (`status_id`), ADD KEY `cc_request_booking_a` (`booking_id_a`), ADD KEY `cc_request_booking_b` (`booking_id_b`);
ALTER TABLE `t_course_cc_schedules` ADD PRIMARY KEY (`id`), ADD KEY `course_cc_schedule_course` (`course_id`);
ALTER TABLE `t_reset_requests`   ADD PRIMARY KEY (`id`), ADD KEY `reset_request_student` (`student_id`), ADD KEY `reset_request_status` (`status_id`);

-- ============================================================
-- AUTO_INCREMENT
-- ============================================================

ALTER TABLE `m_admins`              MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `m_consultants`         MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `m_courses_categories`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `m_meating_styles`      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `m_request_status`      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `m_request_types`       MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `m_rooms`               MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
ALTER TABLE `m_student_status`      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
ALTER TABLE `m_times`               MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `m_courses`             MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `m_students`            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
ALTER TABLE `t_cc_slots`            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
ALTER TABLE `t_cc_bookings`         MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;
ALTER TABLE `t_cc_requests`         MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `t_course_cc_schedules` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `t_reset_requests`      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- ============================================================
-- 外部キー制約
-- ============================================================

ALTER TABLE `m_courses`
  ADD CONSTRAINT `course_category` FOREIGN KEY (`category_id`) REFERENCES `m_courses_categories` (`id`),
  ADD CONSTRAINT `course_room`     FOREIGN KEY (`room_id`)     REFERENCES `m_rooms` (`id`);

ALTER TABLE `m_students`
  ADD CONSTRAINT `student_status` FOREIGN KEY (`status_id`)  REFERENCES `m_student_status` (`id`),
  ADD CONSTRAINT `student_course` FOREIGN KEY (`course_id`)  REFERENCES `m_courses` (`id`);

ALTER TABLE `t_cc_slots`
  ADD CONSTRAINT `slot_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `m_consultants` (`id`),
  ADD CONSTRAINT `slot_room`       FOREIGN KEY (`room_id`)       REFERENCES `m_rooms` (`id`);

ALTER TABLE `t_cc_bookings`
  ADD CONSTRAINT `booking_student`  FOREIGN KEY (`student_id`)        REFERENCES `m_students` (`id`),
  ADD CONSTRAINT `booking_slot`     FOREIGN KEY (`cc_slot_id`)        REFERENCES `t_cc_slots` (`id`),
  ADD CONSTRAINT `booking_time`     FOREIGN KEY (`time_id`)           REFERENCES `m_times` (`id`),
  ADD CONSTRAINT `booking_style`    FOREIGN KEY (`style_id`)          REFERENCES `m_meating_styles` (`id`),
  ADD CONSTRAINT `fk_cc_bookings_cc_plus` FOREIGN KEY (`cc_plus_booking_id`) REFERENCES `t_cc_bookings` (`id`) ON DELETE SET NULL;

ALTER TABLE `t_cc_requests`
  ADD CONSTRAINT `cc_request_type`    FOREIGN KEY (`type_id`)    REFERENCES `m_request_types` (`id`),
  ADD CONSTRAINT `cc_request_student` FOREIGN KEY (`student_id`) REFERENCES `m_students` (`id`),
  ADD CONSTRAINT `cc_request_status`  FOREIGN KEY (`status_id`)  REFERENCES `m_request_status` (`id`),
  ADD CONSTRAINT `t_cc_requests_booking_a_fk` FOREIGN KEY (`booking_id_a`) REFERENCES `t_cc_bookings` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `t_cc_requests_booking_b_fk` FOREIGN KEY (`booking_id_b`) REFERENCES `t_cc_bookings` (`id`) ON DELETE SET NULL;

ALTER TABLE `t_course_cc_schedules`
  ADD CONSTRAINT `course_cc_schedule_course` FOREIGN KEY (`course_id`) REFERENCES `m_courses` (`id`);

ALTER TABLE `t_reset_requests`
  ADD CONSTRAINT `reset_request_student` FOREIGN KEY (`student_id`) REFERENCES `m_students` (`id`),
  ADD CONSTRAINT `reset_request_status`  FOREIGN KEY (`status_id`)  REFERENCES `m_request_status` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
