-- ============================================================================
-- BASE DE DONNEES PROJET BNGRC - Suivi des Dons pour Sinistres
-- Fevrier 2026
-- ============================================================================

DROP DATABASE IF EXISTS bngrc_dons;
CREATE DATABASE bngrc_dons;
USE bngrc_dons;

-- ============================================================================
-- TABLES PRINCIPALES
-- ============================================================================

-- Table: ville
-- Les villes sinistrees
CREATE TABLE ville (
    id_ville INT PRIMARY KEY AUTO_INCREMENT,
    nom_ville VARCHAR(100) NOT NULL,
    region VARCHAR(100),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: categorie_besoin
-- Les 3 categories: nature, materiaux, argent
CREATE TABLE categorie_besoin (
    id_categorie INT PRIMARY KEY AUTO_INCREMENT,
    nom_categorie VARCHAR(50) NOT NULL,
    description TEXT
);

-- Table: type_besoin
-- Les differents types de besoins avec leur prix
CREATE TABLE type_besoin (
    id_type_besoin INT PRIMARY KEY AUTO_INCREMENT,
    id_categorie INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    unite VARCHAR(20) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_categorie) REFERENCES categorie_besoin(id_categorie)
);

-- Table: besoin_ville
-- Les besoins de chaque ville
CREATE TABLE besoin_ville (
    id_besoin INT PRIMARY KEY AUTO_INCREMENT,
    id_ville INT NOT NULL,
    id_type_besoin INT NOT NULL,
    quantite_demandee DECIMAL(10,2) NOT NULL,
    quantite_recue DECIMAL(10,2) DEFAULT 0,
    date_saisie TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville),
    FOREIGN KEY (id_type_besoin) REFERENCES type_besoin(id_type_besoin)
);

-- Table: donateur
-- Informations sur les donateurs
CREATE TABLE donateur (
    id_donateur INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    organisation VARCHAR(150),
    telephone VARCHAR(20),
    email VARCHAR(100),
    type_donateur VARCHAR(50) DEFAULT 'particulier',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: don
-- Les dons recus
CREATE TABLE don (
    id_don INT PRIMARY KEY AUTO_INCREMENT,
    id_donateur INT,
    id_type_besoin INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    date_saisie TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_donateur) REFERENCES donateur(id_donateur),
    FOREIGN KEY (id_type_besoin) REFERENCES type_besoin(id_type_besoin)
);

-- Table: distribution
-- Attribution des dons aux villes
CREATE TABLE distribution (
    id_distribution INT PRIMARY KEY AUTO_INCREMENT,
    id_don INT NOT NULL,
    id_ville INT NOT NULL,
    id_besoin INT NOT NULL,
    quantite_attribuee DECIMAL(10,2) NOT NULL,
    date_distribution TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_don) REFERENCES don(id_don),
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville),
    FOREIGN KEY (id_besoin) REFERENCES besoin_ville(id_besoin)
);


-- ============================================================================
-- DONNEES DE TEST
-- ============================================================================

-- Les 3 categories
INSERT INTO categorie_besoin (nom_categorie, description) VALUES
('nature', 'Produits alimentaires'),
('materiaux', 'Materiaux de construction'),
('argent', 'Aide financiere');

-- Quelques villes
INSERT INTO ville (nom_ville, region) VALUES
('Antananarivo', 'Analamanga'),
('Toamasina', 'Atsinanana'),
('Antsirabe', 'Vakinankaratra'),
('Mahajanga', 'Boeny'),
('Fianarantsoa', 'Haute Matsiatra');

-- Types de besoins - Nature
INSERT INTO type_besoin (id_categorie, nom, unite, prix_unitaire) VALUES
(1, 'Riz', 'kg', 2000.00),
(1, 'Huile', 'litre', 8000.00),
(1, 'Sucre', 'kg', 3000.00),
(1, 'Haricots', 'kg', 4000.00),
(1, 'Eau potable', 'litre', 500.00);

-- Types de besoins - Materiaux
INSERT INTO type_besoin (id_categorie, nom, unite, prix_unitaire) VALUES
(2, 'Tole', 'piece', 35000.00),
(2, 'Clou', 'kg', 8000.00),
(2, 'Ciment', 'sac', 25000.00),
(2, 'Bache', 'piece', 12000.00),
(2, 'Couverture', 'piece', 20000.00);

-- Types de besoins - Argent
INSERT INTO type_besoin (id_categorie, nom, unite, prix_unitaire) VALUES
(3, 'Aide financiere', 'Ar', 1.00);

-- Quelques donateurs
INSERT INTO donateur (nom, prenom, telephone, type_donateur) VALUES
('RASOANAIVO', 'Jean', '0331234567', 'particulier'),
('RAKOTO', 'Marie', '0341234567', 'particulier'),
(NULL, NULL, '0201234567', 'entreprise'),
('RANDRIA', 'Paul', '0341112233', 'particulier');

UPDATE donateur SET organisation = 'Entreprise TechMada' WHERE id_donateur = 3;

-- Besoins pour Antananarivo
INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee) VALUES
(1, 1, 5000.00),  -- 5000 kg de riz
(1, 2, 500.00),   -- 500 litres huile
(1, 6, 200.00),   -- 200 toles
(1, 8, 100.00);   -- 100 sacs ciment

-- Besoins pour Toamasina
INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee) VALUES
(2, 1, 3000.00),  -- 3000 kg riz
(2, 5, 2000.00),  -- 2000 litres eau
(2, 9, 150.00),   -- 150 baches
(2, 11, 5000000.00); -- 5M Ar

-- Besoins pour Antsirabe
INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee) VALUES
(3, 1, 2000.00),  -- 2000 kg riz
(3, 4, 800.00),   -- 800 kg haricots
(3, 10, 100.00);  -- 100 couvertures

-- Quelques dons
INSERT INTO don (id_donateur, id_type_besoin, quantite) VALUES
(1, 1, 1000.00),  -- 1000 kg riz
(2, 2, 200.00),   -- 200 litres huile
(3, 6, 100.00),   -- 100 toles
(4, 1, 500.00),   -- 500 kg riz
(1, 10, 30.00);   -- 30 couvertures


-- ============================================================================
-- TABLES PEUT-ETRE UTILES PLUS TARD (commentees)
-- ============================================================================

/*
-- Table: utilisateur
-- Pour gerer les acces et les droits
CREATE TABLE utilisateur (
    id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
    nom_utilisateur VARCHAR(50) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    role VARCHAR(50) DEFAULT 'operateur',
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
*/

/*
-- Table: sinistre
-- Pour suivre les evenements (cyclone, inondation, etc)
CREATE TABLE sinistre (
    id_sinistre INT PRIMARY KEY AUTO_INCREMENT,
    nom_sinistre VARCHAR(150) NOT NULL,
    type_sinistre VARCHAR(50),
    date_debut DATE,
    date_fin DATE,
    description TEXT
);

-- Lier les villes aux sinistres
CREATE TABLE ville_sinistre (
    id_ville INT,
    id_sinistre INT,
    nombre_sinistres INT,
    PRIMARY KEY (id_ville, id_sinistre),
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville),
    FOREIGN KEY (id_sinistre) REFERENCES sinistre(id_sinistre)
);
*/

/*
-- Table: entrepot
-- Pour gerer un stock intermediaire
CREATE TABLE entrepot (
    id_entrepot INT PRIMARY KEY AUTO_INCREMENT,
    nom_entrepot VARCHAR(100),
    adresse TEXT,
    capacite_max DECIMAL(10,2)
);

CREATE TABLE stock (
    id_stock INT PRIMARY KEY AUTO_INCREMENT,
    id_entrepot INT,
    id_type_besoin INT,
    quantite_disponible DECIMAL(10,2),
    date_maj TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_entrepot) REFERENCES entrepot(id_entrepot),
    FOREIGN KEY (id_type_besoin) REFERENCES type_besoin(id_type_besoin)
);
*/

/*
-- Table: historique
-- Pour suivre les modifications
CREATE TABLE historique (
    id_historique INT PRIMARY KEY AUTO_INCREMENT,
    table_concernee VARCHAR(50),
    id_enregistrement INT,
    action VARCHAR(20),
    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    details TEXT
);
*/