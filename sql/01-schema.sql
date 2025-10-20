SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `images_evenements`;
DROP TABLE IF EXISTS `evenements`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `utilisateurs`;

SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE `utilisateurs` (
  `id_utilisateur` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(255) NOT NULL,
  `mot_de_passe_hash` VARCHAR(255) NOT NULL, 
  `role` INT(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE INDEX `email_UNIQUE` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `id_categorie` INT NOT NULL AUTO_INCREMENT,
  `libelle` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id_categorie`),
  UNIQUE INDEX `libelle_UNIQUE` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `evenements` (
  `id_evenement` INT NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(255) NOT NULL,
  `description` TEXT NULL, 
  `tarif` VARCHAR(100) NULL, 
  `date_debut` DATETIME NOT NULL, 
  `date_fin` DATETIME NULL,
  `id_categorie` INT NOT NULL,
  `est_publie` INT(1) NOT NULL DEFAULT 0, 
  `id_utilisateur_creation` INT NOT NULL, 
  `date_creation` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_evenement`),
  CONSTRAINT `fk_evenements_categories`
    FOREIGN KEY (`id_categorie`)
    REFERENCES `categories` (`id_categorie`),
  CONSTRAINT `fk_evenements_utilisateurs`
    FOREIGN KEY (`id_utilisateur_creation`)
    REFERENCES `utilisateurs` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `images_evenements` (
  `id_image` INT NOT NULL AUTO_INCREMENT,
  `id_evenement` INT NOT NULL,
  `url_image` VARCHAR(2048) NOT NULL, 
  `legende` VARCHAR(255) NULL,
  `ordre_affichage` INT NULL DEFAULT 0,
  `date_ajout` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_image`),
  CONSTRAINT `fk_images_evenements_evenements`
    FOREIGN KEY (`id_evenement`)
    REFERENCES `evenements` (`id_evenement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
