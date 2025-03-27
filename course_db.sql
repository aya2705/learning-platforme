
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



CREATE DATABASE course_db;
USE course_db;

CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `Announcements` (
  `announcement_id` int(11) NOT NULL AUTO_INCREMENT,
  `tutor_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'deactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`announcement_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `bookmark` (
  `user_id` varchar(20) NOT NULL,
  `playlist_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `comments` (
  `id` varchar(20) NOT NULL,
  `content_id` varchar(20) NOT NULL,
  `user_id` varchar(20) DEFAULT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `comment` varchar(1000) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_id` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `content` (
  `id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `playlist_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `prerequisites` text NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `video` varchar(100) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `deletion_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `deletion_tutors` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `tutor_id` VARCHAR(20) NOT NULL,
  `tutor_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`request_id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `playlist` (
  `id` varchar(20) NOT NULL,
  `tutor_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `thumb` varchar(100) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'deactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `tutors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `image` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



INSERT INTO `admin` (`id`, `name`, `email`, `password`, `image`) VALUES
(1, 'Admin', 'chioua.hiba1@gmail.com', 'ab28cfc74820d6462adabc4f2c4221b803a83507', 'cDM2EJ51so4lEcXg3O0M.avif');

INSERT INTO `users` (`id`, `name`, `email`, `password`, `image`) VALUES
(1, 'Ahmed Jaber', 'ahmed.jaber@gmail.com', 'c38ae1eb311400460911a30ad83ae2513ba00cad', 'ahmedjaber.jpg');

INSERT INTO `tutors` (`id`, `name`, `email`, `password`, `image`) VALUES
(1, 'Khalid Manssouri', 'khalid@gmail.com', '188a381a68579ab6419f6d0d1be2d01eb3158b32', 'teacher.jpg');


INSERT INTO `playlist` (`id`, `tutor_id`, `title`, `description`, `thumb`, `date`, `status`) VALUES
('X4fKXwWjtOO7rV3iZ6Om', '1', 'C Avancé', 'Dans ce cours, vous serez capables de comprendre tous ce qui est en relation avec le langage de programmation C', 'cJz0NyuulJnFrzCiVzAE.png', '2024-05-11', 'active');


INSERT INTO `content` (`id`, `tutor_id`, `playlist_id`, `title`, `description`, `prerequisites`, `keywords`, `video`, `thumb`, `date`, `status`) VALUES
('MNTBzCBOoGfP7ii5HRoG', '1', 'X4fKXwWjtOO7rV3iZ6Om', 'Chapitre 1: Les pointeurs', 'Après ce cours,vous devez être capables de travailler avec les pointeurs.', 'Vous devez avoir des notions sur les tableaux.', 'C avancé,Tableaux,Pointeurs', 'PvIdyaLxSvxE2GG0GjS3.pdf', 'Yh0hRQdXuksPSXUnNMl9.png', '2024-05-11', 'active'),
('XJrs1cZCXMSyEgLMMY5y', '1', 'X4fKXwWjtOO7rV3iZ6Om', 'Chapitre 2:  Les structures ', 'Ce cours est pour apprendre tous ce qui est en relation avec les structures', 'Vous devez avoir des notions sur les variables et leurs types', 'C avancé,Structures,Pointeurs,Variables', 'eUQT5OMWW0Da883xkOEB.pdf', 'ZWSf5RGLkwsHPNBcUBoF.png', '2024-05-11', 'active');


COMMIT;