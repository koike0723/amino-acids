-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2026-04-13 07:43:33
-- サーバのバージョン： 10.4.32-MariaDB
-- PHP のバージョン: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `career_consultant`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `m_admins`
--

CREATE TABLE `m_admins` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_admins`
--

INSERT INTO `m_admins` (`id`, `first_name`, `last_name`, `password`, `created_at`, `updated_at`) VALUES
(1, '管理', '太郎', 'password', '2026-04-13 11:12:42', '2026-04-13 11:24:55');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_consultants`
--

CREATE TABLE `m_consultants` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_consultants`
--

INSERT INTO `m_consultants` (`id`, `first_name`, `last_name`, `created_at`, `updated_at`) VALUES
(1, 'キャリア', 'コンサルタント1', '2026-04-13 13:30:51', '2026-04-13 13:30:51'),
(2, 'キャリア', 'コンサルタント2', '2026-04-13 13:30:51', '2026-04-13 13:30:51'),
(3, 'キャリア', 'コンサルタント3', '2026-04-13 13:31:16', '2026-04-13 13:31:16'),
(4, 'キャリア', 'コンサルタント4', '2026-04-13 13:31:16', '2026-04-13 13:31:16');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_courses`
--

CREATE TABLE `m_courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `room_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_courses`
--

INSERT INTO `m_courses` (`id`, `name`, `start_date`, `end_date`, `room_id`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'これから始める！Web・SNS動画編集＆ホームページデザイン科', '2025-11-21', '2026-05-20', 2, 1, '2026-04-13 05:31:25', '2026-04-13 05:31:25'),
(2, '基礎から学ぶJava＋Pythonプログラマー養成科', '2026-02-25', '2026-08-21', 4, 1, '2026-02-25 12:31:25', '2026-04-13 05:31:25'),
(3, 'AI活用で学ぶWeb制作・デザイン＆アプリ開発科', '2026-01-21', '2026-07-18', 5, 1, '2026-04-13 05:37:14', '2026-04-13 05:37:14'),
(4, '初歩から学ぶグラフィック・Webデザイナー養成所\r\n', '2025-10-21', '2026-04-20', 6, 1, '2026-04-13 05:37:14', '2026-04-13 05:37:14'),
(5, '初歩から学ぶグラフィック・Webデザイナー養成所', '2026-04-21', '2026-10-20', 6, 1, '2026-04-13 05:39:37', '2026-04-13 05:39:37'),
(6, 'ここから始める！グラフィック・広告クリエイト科', '2025-12-23', '2026-06-22', 8, 1, '2026-04-13 05:41:14', '2026-04-13 05:41:14'),
(7, 'これから始める！Web・SNS動画編集＆ホームページデザイン科', '2026-03-19', '2026-09-18', 9, 1, '2026-04-13 05:42:31', '2026-04-13 05:42:31'),
(8, 'これから始める！Web・SNS動画編集＆ホームページデザイン科', '2026-01-21', '2026-07-18', 10, 1, '2026-04-13 05:42:31', '2026-04-13 05:42:31'),
(9, 'これから始める！Web・SNS動画編集＆ホームページデザイン科', '2025-12-23', '2026-06-22', 11, 1, '2026-04-13 05:44:45', '2026-04-13 05:44:45'),
(10, '基礎から始めるフロントエンジニア養成科', '2026-03-19', '2026-09-18', 12, 1, '2026-04-13 05:46:51', '2026-04-13 05:46:51'),
(11, 'Webプログラミング科', '2025-11-05', '2026-04-30', 3, 2, '2026-04-13 05:47:31', '2026-04-13 05:47:31');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_courses_categories`
--

CREATE TABLE `m_courses_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_courses_categories`
--

INSERT INTO `m_courses_categories` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '求職者支援訓練', '2026-04-13 12:10:52', '2026-04-13 12:10:52'),
(2, '公共職業訓練', '2026-04-13 12:10:52', '2026-04-13 12:10:52');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_meating_styles`
--

CREATE TABLE `m_meating_styles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_meating_styles`
--

INSERT INTO `m_meating_styles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'ZOOM', '2026-04-13 12:21:16', '2026-04-13 12:21:16'),
(2, '対面', '2026-04-13 12:21:16', '2026-04-13 12:21:16');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_request_status`
--

CREATE TABLE `m_request_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_request_status`
--

INSERT INTO `m_request_status` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '新規', '2026-04-13 12:24:52', '2026-04-13 12:24:52'),
(2, '未対応', '2026-04-13 12:24:52', '2026-04-13 12:24:52'),
(3, '承認', '2026-04-13 12:25:05', '2026-04-13 12:25:05'),
(4, '却下', '2026-04-13 12:25:05', '2026-04-13 12:25:05');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_request_types`
--

CREATE TABLE `m_request_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_request_types`
--

INSERT INTO `m_request_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'cc+予約', '2026-04-13 12:23:01', '2026-04-13 12:23:01'),
(2, 'cc+変更', '2026-04-13 12:23:01', '2026-04-13 12:23:01'),
(3, 'cc+キャンセル', '2026-04-13 12:23:11', '2026-04-13 12:23:11'),
(4, 'cc変更', '2026-04-13 12:23:11', '2026-04-13 12:23:11');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_reset_requests`
--

CREATE TABLE `m_reset_requests` (
  `id` int(11) NOT NULL,
  `sutudent_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_reset_requests`
--

INSERT INTO `m_reset_requests` (`id`, `sutudent_id`, `status_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-04-13 14:41:55', '2026-04-13 14:41:55'),
(2, 2, 2, '2026-04-13 14:41:55', '2026-04-13 14:41:55'),
(3, 3, 3, '2026-04-13 14:42:20', '2026-04-13 14:42:20'),
(4, 4, 4, '2026-04-13 14:42:20', '2026-04-13 14:42:20');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_rooms`
--

CREATE TABLE `m_rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_rooms`
--

INSERT INTO `m_rooms` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '6A', '2026-04-13 04:54:03', '2026-04-13 04:54:03'),
(2, '6B', '2026-04-13 04:54:22', '2026-04-13 04:54:22'),
(3, '6C', '2026-04-13 04:54:29', '2026-04-13 04:54:29'),
(4, '6D', '2026-04-13 04:54:46', '2026-04-13 04:54:46'),
(5, '6E', '2026-04-13 04:54:46', '2026-04-13 04:54:46'),
(6, '6F', '2026-04-13 04:55:30', '2026-04-13 04:55:30'),
(7, '6G', '2026-04-13 04:55:30', '2026-04-13 04:55:30'),
(8, '6H', '2026-04-13 04:56:22', '2026-04-13 04:56:22'),
(9, '7A', '2026-04-13 04:54:46', '2026-04-13 04:54:46'),
(10, '7B', '2026-04-13 04:56:35', '2026-04-13 04:56:35'),
(11, '7C', '2026-04-13 04:54:46', '2026-04-13 04:54:46'),
(12, '7D', '2026-04-13 04:57:06', '2026-04-13 04:57:06'),
(13, 'CC', '2026-04-13 04:57:19', '2026-04-13 04:57:19');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_students`
--

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

--
-- テーブルのデータのダンプ `m_students`
--

INSERT INTO `m_students` (`id`, `first_name`, `last_name`, `number`, `login_id`, `password`, `status_id`, `course_id`, `created_at`, `updated_at`) VALUES
(1, '梅崎', '竜之介', 1, 'umesaki@id', 'umesaki@pass', 1, 11, '2026-04-13 12:52:10', '2026-04-13 12:52:10'),
(2, '江原', '実里', 2, 'ehara@id', 'ehara@pass', 3, 11, '2026-04-13 12:52:10', '2026-04-13 12:52:10'),
(3, '大古場', '佐和子', 3, 'ookoba@id', 'ookoba@pass', 3, 11, '2026-04-13 12:53:20', '2026-04-13 12:53:20'),
(4, '小倉', '啓太郎', 4, 'ogura@id', 'ogura@pass', 1, 11, '2026-04-13 12:53:20', '2026-04-13 12:53:20'),
(5, '岸本', '恵美子', 5, 'kisimoto@id', 'kisimoto@pass', 1, 11, '2026-04-13 12:54:03', '2026-04-13 12:54:03'),
(6, '小池', '義明', 6, 'koike@id', 'koike@pass', 1, 11, '2026-04-13 12:54:47', '2026-04-13 12:54:47'),
(7, '小松', '喜徳', 7, 'komatu@id', 'komatu@pass', 1, 11, '2026-04-13 12:56:11', '2026-04-13 12:56:11'),
(8, '環', '怜汰郎', 8, 'tamaki@id', 'tamaki@pass', 1, 11, '2026-04-13 12:56:11', '2026-04-13 12:56:11'),
(9, '友原', '豪', 9, 'tomohara@id', 'tomohara@pass', 2, 11, '2026-04-13 12:56:46', '2026-04-13 12:56:46'),
(10, '永田', '貴久', 10, 'nagata@id', 'nagata@pass', 1, 11, '2026-04-13 12:58:20', '2026-04-13 12:58:20'),
(11, '兵藤', '優翔', 11, 'hyoudou@id', 'hyoudou@pass', 1, 11, '2026-04-13 12:58:20', '2026-04-13 12:58:20'),
(12, 'リカレント', '1郎', 1, '1rou@id', '1rou@pass', 1, 1, '2026-04-13 12:59:27', '2026-04-13 13:23:14'),
(13, 'リカレント', '2郎', 2, '2rou@id', '2rou@pass', 2, 1, '2026-04-13 13:22:39', '2026-04-13 13:25:32'),
(14, 'リカレント', '3郎', 3, '3rou@id', '3rou@pass', 3, 1, '2026-04-13 13:25:02', '2026-04-13 13:25:02'),
(15, 'リカレント', '4郎', 4, '4rou@id', '4rou@pass', 4, 1, '2026-04-13 13:25:02', '2026-04-13 13:25:02'),
(16, 'リカレント', '5郎', 5, '5rou@id', '5rou@pass', 1, 1, '2026-04-13 13:26:38', '2026-04-13 13:26:38'),
(17, 'リカレント', '6郎', 6, '6rou@id', '6rou@pass', 1, 1, '2026-04-13 13:26:38', '2026-04-13 13:26:38'),
(18, 'リカレント', '7郎', 7, '7rou@id', '7rou@pass', 1, 1, '2026-04-13 13:28:10', '2026-04-13 13:28:10'),
(19, 'リカレント', '8郎', 8, '8rou@id', '8rou@pass', 1, 1, '2026-04-13 13:28:10', '2026-04-13 13:28:10'),
(20, 'リカレント', '9郎', 9, '9rou@id', '9rou@pass', 1, 1, '2026-04-13 13:29:32', '2026-04-13 13:29:32'),
(21, 'リカレント', '10郎', 10, '10rou@id', '10rou@pass', 1, 1, '2026-04-13 13:29:32', '2026-04-13 13:29:32');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_student_status`
--

CREATE TABLE `m_student_status` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_student_status`
--

INSERT INTO `m_student_status` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, '在校中', '2026-04-13 11:43:32', '2026-04-13 11:43:32'),
(2, '退校', '2026-04-13 11:43:49', '2026-04-13 11:43:49'),
(3, '就職', '2026-04-13 11:44:18', '2026-04-13 11:44:18'),
(4, '修了', '2026-04-13 11:44:25', '2026-04-13 11:58:34');

-- --------------------------------------------------------

--
-- テーブルの構造 `m_times`
--

CREATE TABLE `m_times` (
  `id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `m_times`
--

INSERT INTO `m_times` (`id`, `start_time`, `display_name`, `created_at`, `updated_at`) VALUES
(1, '10:00:00', '10時～', '2026-04-13 05:16:34', '2026-04-13 05:16:34'),
(2, '11:00:00', '11時～', '2026-04-13 05:16:34', '2026-04-13 05:16:34'),
(3, '12:00:00', '12時～', '2026-04-13 05:17:37', '2026-04-13 05:17:37'),
(4, '14:00:00', '14時～', '2026-04-13 05:17:37', '2026-04-13 05:17:37'),
(5, '15:00:00', '15時～', '2026-04-13 05:18:08', '2026-04-13 05:18:08'),
(6, '16:00:00', '16時～', '2026-04-13 05:18:08', '2026-04-13 05:18:08');

-- --------------------------------------------------------

--
-- テーブルの構造 `t_cc_bookings`
--

CREATE TABLE `t_cc_bookings` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `cc_slot_id` int(11) NOT NULL,
  `time_id` int(11) NOT NULL,
  `style_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `t_cc_bookings`
--

INSERT INTO `t_cc_bookings` (`id`, `student_id`, `cc_slot_id`, `time_id`, `style_id`, `created_at`, `updated_at`) VALUES
(1, 12, 3, 1, 2, '2026-04-13 13:51:08', '2026-04-13 13:51:08'),
(2, 13, 3, 2, 2, '2026-04-13 13:51:08', '2026-04-13 13:51:08'),
(3, 14, 3, 3, 1, '2026-04-13 13:51:51', '2026-04-13 13:51:51'),
(4, 15, 3, 4, 1, '2026-04-13 13:51:51', '2026-04-13 13:51:51'),
(5, 16, 3, 5, 1, '2026-04-13 13:54:56', '2026-04-13 13:54:56'),
(6, 17, 3, 6, 1, '2026-04-13 13:54:56', '2026-04-13 13:54:56'),
(7, 18, 4, 1, 1, '2026-04-13 13:55:49', '2026-04-13 13:55:49'),
(8, 19, 4, 2, 2, '2026-04-13 13:55:49', '2026-04-13 13:55:49'),
(9, 20, 4, 3, 1, '2026-04-13 13:56:20', '2026-04-13 13:56:20'),
(10, 21, 4, 4, 2, '2026-04-13 13:56:20', '2026-04-13 13:56:20'),
(11, 1, 1, 1, 1, '2026-04-13 14:26:20', '2026-04-13 14:26:20'),
(12, 4, 1, 2, 1, '2026-04-13 14:26:20', '2026-04-13 14:26:20'),
(13, 5, 1, 5, 1, '2026-04-13 14:27:16', '2026-04-13 14:28:15'),
(14, 6, 1, 6, 2, '2026-04-13 14:27:16', '2026-04-13 14:28:41'),
(15, 8, 2, 1, 2, '2026-04-13 14:29:50', '2026-04-13 14:29:50'),
(16, 10, 2, 2, 1, '2026-04-13 14:29:50', '2026-04-13 14:29:50');

-- --------------------------------------------------------

--
-- テーブルの構造 `t_cc_requests`
--

CREATE TABLE `t_cc_requests` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `t_cc_requests`
--

INSERT INTO `t_cc_requests` (`id`, `type_id`, `student_id`, `status_id`, `message`, `created_at`, `updated_at`) VALUES
(5, 1, 11, 1, 'キャリコンプラスの予約を入れたいです', '2026-04-13 14:30:59', '2026-04-13 14:30:59'),
(6, 2, 8, 2, 'キャリコンプラスの変更をしたいです', '2026-04-13 14:31:39', '2026-04-13 14:31:39'),
(7, 3, 11, 3, 'キャリコンプラスのキャンセルをしたいです', '2026-04-13 14:33:09', '2026-04-13 14:34:23'),
(8, 4, 8, 4, 'キャリコンの日程を変更（2026/4月25日10:00:00~から16:00:00~）したいです。', '2026-04-13 14:36:07', '2026-04-13 14:36:07');

-- --------------------------------------------------------

--
-- テーブルの構造 `t_cc_slots`
--

CREATE TABLE `t_cc_slots` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `is_cc_plus` tinyint(1) NOT NULL COMMENT 'デフォルト:0、1ならキャリコンプラス用',
  `consultant_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `t_cc_slots`
--

INSERT INTO `t_cc_slots` (`id`, `date`, `is_cc_plus`, `consultant_id`, `room_id`, `created_at`, `updated_at`) VALUES
(1, '2026-04-25', 1, NULL, NULL, '2026-04-13 06:36:47', '2026-04-13 06:36:47'),
(2, '2026-04-25', 1, NULL, NULL, '2026-04-13 06:36:47', '2026-04-13 06:36:47'),
(3, '2026-04-25', 0, NULL, NULL, '2026-04-13 06:37:58', '2026-04-13 06:37:58'),
(4, '2026-04-25', 0, NULL, NULL, '2026-04-13 06:37:58', '2026-04-13 06:37:58');

-- --------------------------------------------------------

--
-- テーブルの構造 `t_course_cc_schedules`
--

CREATE TABLE `t_course_cc_schedules` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `cc_count` int(11) NOT NULL,
  `date` date NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `t_course_cc_schedules`
--

INSERT INTO `t_course_cc_schedules` (`id`, `course_id`, `cc_count`, `date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-12-20', '2026-04-13 14:12:22', '2026-04-13 14:14:44'),
(2, 1, 2, '2026-02-21', '2026-04-13 14:12:22', '2026-04-13 14:14:58'),
(3, 1, 3, '2026-04-18', '2026-04-13 14:12:53', '2026-04-13 14:15:10');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `m_admins`
--
ALTER TABLE `m_admins`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_consultants`
--
ALTER TABLE `m_consultants`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_courses`
--
ALTER TABLE `m_courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_room` (`room_id`),
  ADD KEY `course_category` (`category_id`);

--
-- テーブルのインデックス `m_courses_categories`
--
ALTER TABLE `m_courses_categories`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_meating_styles`
--
ALTER TABLE `m_meating_styles`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_request_status`
--
ALTER TABLE `m_request_status`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_request_types`
--
ALTER TABLE `m_request_types`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_reset_requests`
--
ALTER TABLE `m_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reset_request_student` (`sutudent_id`),
  ADD KEY `reset_request_status` (`status_id`);

--
-- テーブルのインデックス `m_rooms`
--
ALTER TABLE `m_rooms`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_students`
--
ALTER TABLE `m_students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_status` (`status_id`),
  ADD KEY `student_course` (`course_id`);

--
-- テーブルのインデックス `m_student_status`
--
ALTER TABLE `m_student_status`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `m_times`
--
ALTER TABLE `m_times`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `t_cc_bookings`
--
ALTER TABLE `t_cc_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_student` (`student_id`),
  ADD KEY `booking_slot` (`cc_slot_id`),
  ADD KEY `booking_time` (`time_id`),
  ADD KEY `booking_style` (`style_id`);

--
-- テーブルのインデックス `t_cc_requests`
--
ALTER TABLE `t_cc_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cc_request_type` (`type_id`),
  ADD KEY `cc_request_student` (`student_id`),
  ADD KEY `cc_request_status` (`status_id`);

--
-- テーブルのインデックス `t_cc_slots`
--
ALTER TABLE `t_cc_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `slot_consultant` (`consultant_id`),
  ADD KEY `slot_room` (`room_id`);

--
-- テーブルのインデックス `t_course_cc_schedules`
--
ALTER TABLE `t_course_cc_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_cc_schedule_course` (`course_id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `m_admins`
--
ALTER TABLE `m_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `m_consultants`
--
ALTER TABLE `m_consultants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `m_courses`
--
ALTER TABLE `m_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- テーブルの AUTO_INCREMENT `m_courses_categories`
--
ALTER TABLE `m_courses_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルの AUTO_INCREMENT `m_meating_styles`
--
ALTER TABLE `m_meating_styles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- テーブルの AUTO_INCREMENT `m_request_status`
--
ALTER TABLE `m_request_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `m_request_types`
--
ALTER TABLE `m_request_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `m_reset_requests`
--
ALTER TABLE `m_reset_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `m_rooms`
--
ALTER TABLE `m_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- テーブルの AUTO_INCREMENT `m_students`
--
ALTER TABLE `m_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- テーブルの AUTO_INCREMENT `m_student_status`
--
ALTER TABLE `m_student_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `m_times`
--
ALTER TABLE `m_times`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- テーブルの AUTO_INCREMENT `t_cc_bookings`
--
ALTER TABLE `t_cc_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- テーブルの AUTO_INCREMENT `t_cc_requests`
--
ALTER TABLE `t_cc_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `t_cc_slots`
--
ALTER TABLE `t_cc_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `t_course_cc_schedules`
--
ALTER TABLE `t_course_cc_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `m_courses`
--
ALTER TABLE `m_courses`
  ADD CONSTRAINT `course_category` FOREIGN KEY (`category_id`) REFERENCES `m_courses_categories` (`id`),
  ADD CONSTRAINT `course_room` FOREIGN KEY (`room_id`) REFERENCES `m_rooms` (`id`);

--
-- テーブルの制約 `m_reset_requests`
--
ALTER TABLE `m_reset_requests`
  ADD CONSTRAINT `reset_request_status` FOREIGN KEY (`status_id`) REFERENCES `m_request_status` (`id`),
  ADD CONSTRAINT `reset_request_student` FOREIGN KEY (`sutudent_id`) REFERENCES `m_students` (`id`);

--
-- テーブルの制約 `m_students`
--
ALTER TABLE `m_students`
  ADD CONSTRAINT `student_course` FOREIGN KEY (`course_id`) REFERENCES `m_courses` (`id`),
  ADD CONSTRAINT `student_status` FOREIGN KEY (`status_id`) REFERENCES `m_student_status` (`id`);

--
-- テーブルの制約 `t_cc_bookings`
--
ALTER TABLE `t_cc_bookings`
  ADD CONSTRAINT `booking_slot` FOREIGN KEY (`cc_slot_id`) REFERENCES `t_cc_slots` (`id`),
  ADD CONSTRAINT `booking_student` FOREIGN KEY (`student_id`) REFERENCES `m_students` (`id`),
  ADD CONSTRAINT `booking_style` FOREIGN KEY (`style_id`) REFERENCES `m_meating_styles` (`id`),
  ADD CONSTRAINT `booking_time` FOREIGN KEY (`time_id`) REFERENCES `m_times` (`id`);

--
-- テーブルの制約 `t_cc_requests`
--
ALTER TABLE `t_cc_requests`
  ADD CONSTRAINT `cc_request_status` FOREIGN KEY (`status_id`) REFERENCES `m_request_status` (`id`),
  ADD CONSTRAINT `cc_request_student` FOREIGN KEY (`student_id`) REFERENCES `m_students` (`id`),
  ADD CONSTRAINT `cc_request_type` FOREIGN KEY (`type_id`) REFERENCES `m_request_types` (`id`);

--
-- テーブルの制約 `t_cc_slots`
--
ALTER TABLE `t_cc_slots`
  ADD CONSTRAINT `slot_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `m_consultants` (`id`),
  ADD CONSTRAINT `slot_room` FOREIGN KEY (`room_id`) REFERENCES `m_rooms` (`id`);

--
-- テーブルの制約 `t_course_cc_schedules`
--
ALTER TABLE `t_course_cc_schedules`
  ADD CONSTRAINT `course_cc_schedule_course` FOREIGN KEY (`course_id`) REFERENCES `m_courses` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
