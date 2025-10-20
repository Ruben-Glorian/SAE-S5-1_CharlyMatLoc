-- Données d'exemple pour les catégories
INSERT INTO categories (nom, description) VALUES
('Petit outillage', 'Outils pour les petits travaux de bricolage'),
('Menuiserie', 'Outils pour travailler le bois et la menuiserie'),
('Peinture', 'Matériel pour peinture et décoration'),
('Nettoyage', 'Outils pour le nettoyage et l’entretien');

-- Données d'exemple pour les outils
INSERT INTO outils (nom, description, categorie_id, tarif) VALUES
('Perceuse', 'Perceuse sans fil 18V', 1, 15.00),
('Scie sauteuse', 'Scie sauteuse électrique', 2, 12.50),
('Peinture blanche', 'Seau de peinture 10L', 3, 8.00),
('Aspirateur', 'Aspirateur industriel', 4, 20.00);

-- Données d'exemple pour les images
INSERT INTO images_outils (outil_id, url, description) VALUES
(1, 'https://exemple.com/images/perceuse.jpg', 'Perceuse vue de face'),
(2, 'https://exemple.com/images/scie.jpg', 'Scie sauteuse en action'),
(3, 'https://exemple.com/images/peinture.jpg', 'Seau de peinture blanche 10L'),
(4, 'https://exemple.com/images/aspirateur.jpg', 'Aspirateur industriel');

-- Exemple d'ajout au panier
INSERT INTO panier (outil_id, date_location) VALUES
(1, '2025-10-21'),
(3, '2025-10-22');