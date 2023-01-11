-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           5.7.33 - MySQL Community Server (GPL)
-- SE du serveur:                Win64
-- HeidiSQL Version:             11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour projetsessionsm
CREATE DATABASE IF NOT EXISTS `projetsessionsm` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `projetsessionsm`;

-- Listage de la structure de la table projetsessionsm. categorie
CREATE TABLE IF NOT EXISTS `categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetsessionsm.categorie : ~4 rows (environ)
/*!40000 ALTER TABLE `categorie` DISABLE KEYS */;
INSERT INTO `categorie` (`id`, `intitule`) VALUES
	(1, 'Dev web'),
	(2, 'Bureautique'),
	(3, 'Infographie'),
	(4, 'Design');
/*!40000 ALTER TABLE `categorie` ENABLE KEYS */;

-- Listage de la structure de la table projetsessionsm. doctrine_migration_versions
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Listage des données de la table projetsessionsm.doctrine_migration_versions : ~0 rows (environ)
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20230103104722', '2023-01-10 07:45:26', 1184);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;

-- Listage de la structure de la table projetsessionsm. formateur
CREATE TABLE IF NOT EXISTS `formateur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetsessionsm.formateur : ~2 rows (environ)
/*!40000 ALTER TABLE `formateur` DISABLE KEYS */;
INSERT INTO `formateur` (`id`, `nom`, `prenom`) VALUES
	(1, 'Omeyer', 'Balthazar'),
	(2, 'Murmann', 'Mickaël');
/*!40000 ALTER TABLE `formateur` ENABLE KEYS */;

-- Listage de la structure de la table projetsessionsm. formation
CREATE TABLE IF NOT EXISTS `formation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetsessionsm.formation : ~2 rows (environ)
/*!40000 ALTER TABLE `formation` DISABLE KEYS */;
INSERT INTO `formation` (`id`, `intitule`) VALUES
	(1, 'Developpement web'),
	(2, 'Design Web'),
	(3, 'Game design');
/*!40000 ALTER TABLE `formation` ENABLE KEYS */;

-- Listage de la structure de la table projetsessionsm. messenger_messages
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetsessionsm.messenger_messages : ~0 rows (environ)
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;

-- Listage de la structure de la table projetsessionsm. module
CREATE TABLE IF NOT EXISTS `module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `intitule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `categorie_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C242628BCF5E72D` (`categorie_id`),
  CONSTRAINT `FK_C242628BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetsessionsm.module : ~11 rows (environ)
/*!40000 ALTER TABLE `module` DISABLE KEYS */;
INSERT INTO `module` (`id`, `intitule`, `categorie_id`) VALUES
	(1, 'PHP', 1),
	(2, 'SQL', 1),
	(3, 'CSS', 1),
	(4, 'HTML', 1),
	(5, 'Symfony', 1),
	(6, 'Laravel', 1),
	(7, 'Word', 2),
	(8, 'Excel', 2),
	(9, 'Outlook', 2),
	(10, 'Photoshop', 3),
	(11, 'Illustrator', 3),
	(12, 'InDesign', 3),
	(13, 'Godot', 4);
/*!40000 ALTER TABLE `module` ENABLE KEYS */;

-- Listage de la structure de la table projetsessionsm. programme
CREATE TABLE IF NOT EXISTS `programme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `duree` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_3DDCB9FF613FECDF` (`session_id`),
  KEY `IDX_3DDCB9FFAFC2B591` (`module_id`),
  CONSTRAINT `FK_3DDCB9FF613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`),
  CONSTRAINT `FK_3DDCB9FFAFC2B591` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetsessionsm.programme : ~12 rows (environ)
/*!40000 ALTER TABLE `programme` DISABLE KEYS */;
INSERT INTO `programme` (`id`, `session_id`, `module_id`, `duree`) VALUES
	(2, 1, 2, 8),
	(3, 2, 3, 15),
	(4, 2, 4, 4),
	(5, 3, 6, 8),
	(6, 3, 5, 12),
	(7, 4, 7, 5),
	(8, 4, 8, 7),
	(9, 4, 9, 8),
	(10, 4, 10, 7),
	(11, 4, 11, 10),
	(12, 4, 12, 6),
	(14, 1, 1, 7);
/*!40000 ALTER TABLE `programme` ENABLE KEYS */;

-- Listage de la structure de la table projetsessionsm. session
CREATE TABLE IF NOT EXISTS `session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `referent_id` int(11) DEFAULT NULL,
  `formation_id` int(11) NOT NULL,
  `intitule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `nb_places` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D044D5D435E47E35` (`referent_id`),
  KEY `IDX_D044D5D45200282E` (`formation_id`),
  CONSTRAINT `FK_D044D5D435E47E35` FOREIGN KEY (`referent_id`) REFERENCES `formateur` (`id`),
  CONSTRAINT `FK_D044D5D45200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetsessionsm.session : ~4 rows (environ)
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
INSERT INTO `session` (`id`, `referent_id`, `formation_id`, `intitule`, `date_debut`, `date_fin`, `nb_places`) VALUES
	(1, NULL, 1, 'Initiation PHP / SQL', '2023-01-09', '2023-01-19', 8),
	(2, NULL, 1, 'Initiation front-end', '2023-01-18', '2023-02-15', 10),
	(3, NULL, 1, 'Initiation frameworks connus', '2022-12-03', '2022-12-24', 5),
	(4, NULL, 2, 'Initiation bureautique et Infographie', '2023-01-06', '2023-01-30', 15),
	(5, NULL, 1, 'Initiation RGPD / sécurité des applications web', '2023-01-05', '2023-02-14', 14);
/*!40000 ALTER TABLE `session` ENABLE KEYS */;

-- Listage de la structure de la table projetsessionsm. session_stagiaire
CREATE TABLE IF NOT EXISTS `session_stagiaire` (
  `session_id` int(11) NOT NULL,
  `stagiaire_id` int(11) NOT NULL,
  PRIMARY KEY (`session_id`,`stagiaire_id`),
  KEY `IDX_C80B23B613FECDF` (`session_id`),
  KEY `IDX_C80B23BBBA93DD6` (`stagiaire_id`),
  CONSTRAINT `FK_C80B23B613FECDF` FOREIGN KEY (`session_id`) REFERENCES `session` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_C80B23BBBA93DD6` FOREIGN KEY (`stagiaire_id`) REFERENCES `stagiaire` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetsessionsm.session_stagiaire : ~2 rows (environ)
/*!40000 ALTER TABLE `session_stagiaire` DISABLE KEYS */;
INSERT INTO `session_stagiaire` (`session_id`, `stagiaire_id`) VALUES
	(1, 1),
	(4, 3);
/*!40000 ALTER TABLE `session_stagiaire` ENABLE KEYS */;

-- Listage de la structure de la table projetsessionsm. stagiaire
CREATE TABLE IF NOT EXISTS `stagiaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexe` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_naissance` date NOT NULL,
  `ville` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Listage des données de la table projetsessionsm.stagiaire : ~4 rows (environ)
/*!40000 ALTER TABLE `stagiaire` DISABLE KEYS */;
INSERT INTO `stagiaire` (`id`, `nom`, `prenom`, `sexe`, `date_naissance`, `ville`, `mail`, `telephone`) VALUES
	(1, 'Dos', 'Marianne', 'Femme', '2000-01-20', 'Strasbourg', 'marianne@gmail.com', 611223344),
	(2, 'Da Silva', 'Manuel', 'Homme', '1998-04-05', 'Strasbourg', 'manuel@gmail.com', 622334455),
	(3, 'Dubois', 'Julie', 'Femme', '1999-08-28', 'Obernai', 'julie@gmail.com', 633445566),
	(8, 'Hoquet', 'Guy', 'homme', '1995-09-07', 'Molsheim', 'guy@gmail.com', 655889977);
/*!40000 ALTER TABLE `stagiaire` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
