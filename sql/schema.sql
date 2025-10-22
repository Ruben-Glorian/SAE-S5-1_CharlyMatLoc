-- Table des catégories d'outils
CREATE TABLE categories (
id SERIAL PRIMARY KEY,
nom VARCHAR(50) NOT NULL,
description TEXT
);

-- Table des outils
CREATE TABLE outils (
id SERIAL PRIMARY KEY,
nom VARCHAR(100) NOT NULL,
description TEXT,
categorie_id INT REFERENCES categories(id),
tarif NUMERIC(10,2) NOT NULL
);

-- Table des images d'outils
CREATE TABLE images_outils (
id SERIAL PRIMARY KEY,
outil_id INT REFERENCES outils(id) ON DELETE CASCADE,
url VARCHAR(255) NOT NULL,
description TEXT
);

-- Table du panier
CREATE TABLE panier (
id SERIAL PRIMARY KEY,
outil_id INT REFERENCES outils(id),
date_location DATE NOT NULL,
date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des utilisateurs
DROP TABLE IF EXISTS "users";
CREATE TABLE "public"."users" (
    "id" SERIAL PRIMARY KEY,
    "email" character varying(128) NOT NULL,
    "password" character varying(256) NOT NULL,
    CONSTRAINT "users_email" UNIQUE ("email")
) WITH (oids = false);

CREATE TABLE reservations (
id SERIAL PRIMARY KEY,
user_id UUID REFERENCES users(id) ON DELETE CASCADE,
outil_id INTEGER REFERENCES outils(id) ON DELETE SET NULL,
date_location DATE NOT NULL,
date_reservation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
