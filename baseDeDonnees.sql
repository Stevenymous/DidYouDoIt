-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Dim 05 Juillet 2015 à 18:07
-- Version du serveur :  5.6.24-0ubuntu2
-- Version de PHP :  5.6.4-4ubuntu6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `sf2`
--

-- --------------------------------------------------------

--
-- Structure de la table `task`
--

CREATE TABLE IF NOT EXISTS `task` (
`task_id` int(11) NOT NULL,
  `tasklist_id_fk` int(11) DEFAULT NULL,
  `libelle` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `checked` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `task`
--

INSERT INTO `task` (`task_id`, `tasklist_id_fk`, `libelle`, `checked`) VALUES
(15, 5, 'ISSUE : Double tâche à la création', 1),
(18, 5, 'Ajouter icone checked', 1),
(19, 6, 'Acheter des quiches', 0),
(20, 6, 'Acheter du canard WC', 1),
(23, 5, 'teste', 1),
(24, 5, 'Télécharger le bundle google-api pour se connecter en OAuth', 1),
(26, 6, 'sqd', 0);

-- --------------------------------------------------------

--
-- Structure de la table `tasklist`
--

CREATE TABLE IF NOT EXISTS `tasklist` (
`tasklist_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `tasklist`
--

INSERT INTO `tasklist` (`tasklist_id`, `name`) VALUES
(5, 'TODO sur l''application'),
(6, 'Liste des courses');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `task`
--
ALTER TABLE `task`
 ADD PRIMARY KEY (`task_id`), ADD KEY `IDX_527EDB2583466746` (`tasklist_id_fk`);

--
-- Index pour la table `tasklist`
--
ALTER TABLE `tasklist`
 ADD PRIMARY KEY (`tasklist_id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `task`
--
ALTER TABLE `task`
MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT pour la table `tasklist`
--
ALTER TABLE `tasklist`
MODIFY `tasklist_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `task`
--
ALTER TABLE `task`
ADD CONSTRAINT `FK_527EDB2583466746` FOREIGN KEY (`tasklist_id_fk`) REFERENCES `tasklist` (`tasklist_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
