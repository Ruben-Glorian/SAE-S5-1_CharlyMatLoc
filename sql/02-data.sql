SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

TRUNCATE TABLE `images_evenements`;
TRUNCATE TABLE `evenements`;
TRUNCATE TABLE `categories`;
TRUNCATE TABLE `utilisateurs`;

-- Pour 'superadmin@lachaudiere.fr', le mot de passe en clair était 'superadmin_secret'
-- Pour 'admin.concerts@lachaudiere.fr', le mot de passe en clair était 'admin123'
-- Pour 'admin.expos@lachaudiere.fr', le mot de passe en clair était 'moderator_pass'
INSERT INTO `utilisateurs` (`email`, `mot_de_passe_hash`, `role`) VALUES
('superadmin@lachaudiere.fr', '$2y$10$nBTMnREmyY.TQc2afKiC/OoImjzJP802fmOHQgABZcxTTPkNN5xh.', 0),
('admin.concerts@lachaudiere.fr', '$2y$10$Ay9w0Q.Sn5HeyCCrfMn2feWCvuckl7h2BJTqdpLtmuuKeFoHp2y.K', 1),
('admin.expos@lachaudiere.fr', '$2y$10$JcCuQA2pKF/bb6TkTTzhH./bgm27qdhFuO4P84EakML0aaOxuGV2K', 1);



INSERT INTO `categories` (`id_categorie`, `libelle`, `description`) VALUES
(1, 'Concert', 'Performances musicales live de tous genres. Rock, pop, jazz, classique, électro et plus encore.'),
(2, 'Spectacle', 'Théâtre, danse, humour, cirque, magie... Des performances artistiques captivantes pour tous les âges.'),
(3, 'Exposition', 'Arts visuels, photographie, sculpture, installations. Explorez des univers créatifs variés.'),
(4, 'Conférence & Débat', 'Rencontres, discussions et présentations sur des sujets culturels, scientifiques ou de société.'),
(5, 'Festival', 'Événements thématiques sur plusieurs jours, combinant différentes formes d''art et d''expression.'),
(6, 'Atelier & Stage', 'Apprentissage et pratique artistique ou culturelle pour amateurs et passionnés.');


INSERT INTO `evenements` (`titre`, `description`, `tarif`, `date_debut`, `date_fin`, `id_categorie`, `est_publie`, `id_utilisateur_creation`) VALUES
('Nuit Électro à La Chaudière', '## Vibrez au son des meilleurs DJs !\n\nLine-up exceptionnel avec DJ Spark et MC Flow. Show lumière et ambiance survoltée garantie jusqu''au petit matin.', '25€ en prévente, 30€ sur place', '2025-07-12 22:00:00', '2025-07-13 05:00:00', 1, 1, 2), -- Créé par admin.concerts@lachaudiere.fr (supposé ID 2)
('Exposition "Regards Urbains"', 'Collection de photographies capturant l''essence des métropoles modernes. Par l''artiste visionnaire Alex Lens.', 'Entrée libre', '2025-08-01 10:00:00', '2025-09-15 19:00:00', 3, 1, 3), -- Créé par admin.expos@lachaudiere.fr (supposé ID 3)
('Théâtre : "Le Songe d''une Nuit d''Été"', 'Une adaptation contemporaine et magique de la célèbre pièce de Shakespeare. Mise en scène par la troupe "Les Illuminés".', '18€ tarif plein, 12€ tarif réduit', '2025-09-05 20:30:00', '2025-09-10 12:00:00', 2, 0, 2), -- Non publié, créé par admin.concerts
('Conférence : "L''Avenir de l''Art Numérique"', 'Animée par Dr. Pixel, pionnier de l''art génératif. Quelles sont les prochaines révolutions créatives ?', 'Gratuit sur réservation', '2025-06-28 18:30:00', '2025-07-05 12:00:00', 4, 1, 1), -- Créé par superadmin@lachaudiere.fr (supposé ID 1)
('Festival des Arts de la Rue', '**Édition 2025 !** Jongleurs, cracheurs de feu, musiciens et acrobates envahissent le parvis de La Chaudière pour 3 jours de fête.', 'Accès libre aux animations extérieures', '2025-07-25 14:00:00', '2025-07-27 23:00:00', 5, 1, 1), -- Créé par superadmin
('Atelier d''Écriture Créative', 'Libérez votre imagination et apprenez les techniques de base pour écrire nouvelles, poèmes ou scénarios. Animé par Jeanne Plume.', '35€ pour la session de 3h (matériel inclus)', '2025-07-19 09:30:00', '2025-07-19 12:30:00', 6, 1, 3), -- Créé par admin.expos
('Concert Gims Nancy', 'Gims vous fait l honneur de passer à Nancy pour sa tournée en France', '18€ tarif plein, 12€ tarif réduit', '2025-05-01 10:00:00', '2025-05-01 12:00:00', 1, 1, 1),-- Événement passé caté 1
('Concert Angel Nancy', 'Angel vous fait l honneur de passer à Nancy pour sa tournée en France', '18€ tarif plein, 12€ tarif réduit', '2025-06-06 08:00:00', '2025-06-06 22:00:00', 1, 1, 1);-- Événement courant caté 1


INSERT INTO `images_evenements` (`id_evenement`, `url_image`, `legende`, `ordre_affichage`) VALUES
(1, 'https://picsum.photos/seed/electro1/800/450', 'DJ Spark aux platines', 0),
(1, 'https://picsum.photos/seed/electro2/800/450', 'Ambiance lumineuse', 1);

INSERT INTO `images_evenements` (`id_evenement`, `url_image`, `legende`) VALUES
(2, 'https://picsum.photos/seed/urbain1/700/500', 'Perspective architecturale'),
(2, 'https://picsum.photos/seed/urbain2/700/500', 'Scène de rue captivante');

INSERT INTO `images_evenements` (`id_evenement`, `url_image`) VALUES
(5, 'https://picsum.photos/seed/festivalrue/900/400');

SET FOREIGN_KEY_CHECKS=1;
