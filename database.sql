DROP DATABASE IF EXISTS bngrc_dons;
CREATE DATABASE bngrc_dons;
USE bngrc_dons;


-- Table: ville
CREATE TABLE ville (
    id_ville INT PRIMARY KEY AUTO_INCREMENT,
    nom_ville VARCHAR(100) NOT NULL,
    region VARCHAR(100),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: categorie_besoin
CREATE TABLE categorie_besoin (
    id_categorie INT PRIMARY KEY AUTO_INCREMENT,
    nom_categorie VARCHAR(50) NOT NULL,
    description TEXT
);

-- Table: type_besoin
CREATE TABLE type_besoin (
    id_type_besoin INT PRIMARY KEY AUTO_INCREMENT,
    id_categorie INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    unite VARCHAR(20) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_categorie) REFERENCES categorie_besoin(id_categorie)
);

-- Table: besoin_ville
CREATE TABLE besoin_ville (
    id_besoin INT PRIMARY KEY AUTO_INCREMENT,
    id_ville INT NOT NULL,
    id_type_besoin INT NOT NULL,
    quantite_demandee DECIMAL(15,2) NOT NULL,
    quantite_recue DECIMAL(15,2) DEFAULT 0,
    date_saisie TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville),
    FOREIGN KEY (id_type_besoin) REFERENCES type_besoin(id_type_besoin)
);

-- Table: donateur
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
CREATE TABLE don (
    id_don INT PRIMARY KEY AUTO_INCREMENT,
    id_donateur INT,
    id_type_besoin INT NOT NULL,
    quantite DECIMAL(15,2) NOT NULL,
    date_saisie TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_donateur) REFERENCES donateur(id_donateur),
    FOREIGN KEY (id_type_besoin) REFERENCES type_besoin(id_type_besoin)
);

-- Table: distribution
CREATE TABLE distribution (
    id_distribution INT PRIMARY KEY AUTO_INCREMENT,
    id_don INT NOT NULL,
    id_ville INT NOT NULL,
    id_besoin INT NOT NULL,
    quantite_attribuee DECIMAL(15,2) NOT NULL,
    date_distribution TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_don) REFERENCES don(id_don),
    FOREIGN KEY (id_ville) REFERENCES ville(id_ville),
    FOREIGN KEY (id_besoin) REFERENCES besoin_ville(id_besoin)
);


-- Table: config_frais
CREATE TABLE config_frais (
    id_config INT PRIMARY KEY AUTO_INCREMENT,
    nom_config VARCHAR(100) NOT NULL UNIQUE,
    valeur DECIMAL(5,2) NOT NULL,
    description TEXT,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table: achat
CREATE TABLE achat (
    id_achat INT PRIMARY KEY AUTO_INCREMENT,
    id_don_argent INT NOT NULL,
    id_type_besoin INT NOT NULL,
    quantite_achetee DECIMAL(15,2) NOT NULL,
    prix_unitaire DECIMAL(15,2) NOT NULL,
    frais_pourcentage DECIMAL(5,2) NOT NULL,
    montant_total DECIMAL(15,2) NOT NULL,
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_don_argent) REFERENCES don(id_don),
    FOREIGN KEY (id_type_besoin) REFERENCES type_besoin(id_type_besoin)
);

-- Table: simulation
CREATE TABLE simulation (
    id_simulation INT PRIMARY KEY AUTO_INCREMENT,
    id_don_argent INT NOT NULL,
    details_json TEXT NOT NULL,
    montant_total DECIMAL(15,2) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_cours', 'validee', 'annulee') DEFAULT 'en_cours',
    FOREIGN KEY (id_don_argent) REFERENCES don(id_don)
);



-- Les 3 catégories
INSERT INTO categorie_besoin (nom_categorie, description) VALUES
('nature', 'Produits alimentaires'),
('materiaux', 'Materiaux de construction'),
('argent', 'Aide financiere');

-- Configuration des frais d'achat (10%)
INSERT INTO config_frais (nom_config, valeur, description) VALUES
('frais_achat_pourcentage', 10.00, 'Frais appliques lors de l achat de besoins avec de l argent (en pourcentage)');


INSERT INTO ville (nom_ville, region) VALUES
('Toamasina', 'Atsinanana'),
('Mananjary', 'Vatovavy-Fitovinany'),
('Farafangana', 'Atsimo-Atsinanana'),
('Nosy Be', 'Diana'),
('Morondava', 'Menabe');



-- Types de besoins - Nature
INSERT INTO type_besoin (id_categorie, nom, unite, prix_unitaire) VALUES
(1, 'Riz', 'kg', 3000.00),
(1, 'Huile', 'litre', 6000.00),
(1, 'Eau potable', 'litre', 1000.00),
(1, 'Haricots', 'kg', 4000.00);

-- Types de besoins - Matériaux
INSERT INTO type_besoin (id_categorie, nom, unite, prix_unitaire) VALUES
(2, 'Tole', 'piece', 25000.00),
(2, 'Clou', 'kg', 8000.00),
(2, 'Bache', 'piece', 15000.00),
(2, 'Bois', 'm3', 10000.00),
(2, 'Groupe electrogene', 'piece', 6750000.00);

-- Types de besoins - Argent
INSERT INTO type_besoin (id_categorie, nom, unite, prix_unitaire) VALUES
(3, 'Aide financiere', 'Ar', 1.00);



-- TOAMASINA (id_ville = 1)
INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee, date_saisie) VALUES
(1, 1, 800, '2026-02-16'),          -- Riz 800 kg
(1, 3, 1500, '2026-02-15'),         -- Eau 1500 L
(1, 5, 120, '2026-02-16'),          -- Tôle 120 pièces
(1, 7, 200, '2026-02-15'),          -- Bâche 200 pièces
(1, 10, 120000000, '2026-02-16'),   -- Argent 120M Ar
(1, 9, 3, '2026-02-15');            -- Groupe électrogène 3 pièces

-- MANANJARY (id_ville = 2)
INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee, date_saisie) VALUES
(2, 1, 500, '2026-02-15'),          -- Riz 500 kg
(2, 2, 120, '2026-02-16'),          -- Huile 120 L
(2, 5, 80, '2026-02-15'),           -- Tôle 80 pièces
(2, 6, 60, '2026-02-16'),           -- Clous 60 kg
(2, 10, 60000000, '2026-02-15');    -- Argent 60M Ar

-- FARAFANGANA (id_ville = 3)
INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee, date_saisie) VALUES
(3, 1, 600, '2026-02-16'),          -- Riz 600 kg
(3, 3, 1000, '2026-02-15'),         -- Eau 1000 L
(3, 7, 150, '2026-02-16'),          -- Bâche 150 pièces
(3, 8, 100, '2026-02-15'),          -- Bois 100 m3
(3, 10, 80000000, '2026-02-16');    -- Argent 80M Ar

-- NOSY BE (id_ville = 4)
INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee, date_saisie) VALUES
(4, 1, 300, '2026-02-15'),          -- Riz 300 kg
(4, 4, 200, '2026-02-16'),          -- Haricots 200 kg
(4, 5, 40, '2026-02-15'),           -- Tôle 40 pièces
(4, 6, 30, '2026-02-16'),           -- Clous 30 kg
(4, 10, 40000000, '2026-02-15');    -- Argent 40M Ar

-- MORONDAVA (id_ville = 5)
INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee, date_saisie) VALUES
(5, 1, 700, '2026-02-16'),          -- Riz 700 kg
(5, 3, 1200, '2026-02-15'),         -- Eau 1200 L
(5, 7, 180, '2026-02-16'),          -- Bâche 180 pièces
(5, 8, 150, '2026-02-15'),          -- Bois 150 m3
(5, 10, 100000000, '2026-02-16');   -- Argent 100M Ar



-- Créer des donateurs
INSERT INTO donateur (nom, prenom, type_donateur) VALUES
('Donateur', 'Anonyme', 'particulier');

-- Dons en argent
INSERT INTO don (id_donateur, id_type_besoin, quantite, date_saisie) VALUES
(1, 10, 5000000, '2026-02-16'),     -- Argent 5M Ar
(1, 10, 3000000, '2026-02-16'),     -- Argent 3M Ar
(1, 10, 4000000, '2026-02-17'),     -- Argent 4M Ar
(1, 10, 1500000, '2026-02-17'),     -- Argent 1.5M Ar
(1, 10, 6000000, '2026-02-17'),     -- Argent 6M Ar

-- Dons en nature
(1, 1, 400, '2026-02-16'),          -- Riz 400 kg
(1, 3, 600, '2026-02-16'),          -- Eau 600 L

-- Dons en matériaux
(1, 5, 50, '2026-02-17'),           -- Tôle 50 pièces
(1, 7, 70, '2026-02-17'),           -- Bâche 70 pièces

-- Dons en nature (suite)
(1, 4, 100, '2026-02-17'),          -- Haricots 100 kg
(1, 1, 2000, '2026-02-18'),         -- Riz 2000 kg

-- Dons en matériaux (suite)
(1, 5, 300, '2026-02-18'),          -- Tôle 300 pièces
(1, 3, 5000, '2026-02-18'),         -- Eau 5000 L

-- Dons en argent (suite)
(1, 10, 20000000, '2026-02-19'),    -- Argent 20M Ar

-- Dons en matériaux (suite)
(1, 7, 500, '2026-02-19'),          -- Bâche 500 pièces
(1, 4, 88, '2026-02-17');           -- Haricots 88 kg


-- Afficher tous les besoins
SELECT 
    v.nom_ville,
    tb.nom AS type_besoin,
    bv.quantite_demandee,
    tb.unite,
    tb.prix_unitaire,
    (bv.quantite_demandee * tb.prix_unitaire) AS valeur,
    bv.date_saisie
FROM besoin_ville bv
JOIN ville v ON bv.id_ville = v.id_ville
JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
ORDER BY v.nom_ville, bv.date_saisie;

-- Statistiques par ville
SELECT 
    v.nom_ville,
    COUNT(*) AS nb_besoins,
    SUM(bv.quantite_demandee * tb.prix_unitaire) AS valeur_totale_besoins
FROM besoin_ville bv
JOIN ville v ON bv.id_ville = v.id_ville
JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
GROUP BY v.nom_ville
ORDER BY v.nom_ville;

-- Afficher tous les dons
SELECT 
    dn.id_don,
    tb.nom AS type_don,
    dn.quantite,
    tb.unite,
    (dn.quantite * tb.prix_unitaire) AS valeur,
    dn.date_saisie
FROM don dn
JOIN type_besoin tb ON dn.id_type_besoin = tb.id_type_besoin
ORDER BY dn.date_saisie, tb.nom;

-- Statistiques des dons par type
SELECT 
    cb.nom_categorie,
    tb.nom AS type_don,
    SUM(dn.quantite) AS quantite_totale,
    tb.unite,
    SUM(dn.quantite * tb.prix_unitaire) AS valeur_totale
FROM don dn
JOIN type_besoin tb ON dn.id_type_besoin = tb.id_type_besoin
JOIN categorie_besoin cb ON tb.id_categorie = cb.id_categorie
GROUP BY cb.nom_categorie, tb.nom, tb.unite
ORDER BY cb.nom_categorie, tb.nom;



SELECT '=== RÉSUMÉ ===' AS info;

SELECT 
    'Villes' AS type,
    COUNT(*) AS total
FROM ville
UNION ALL
SELECT 
    'Types de besoins',
    COUNT(*)
FROM type_besoin
UNION ALL
SELECT 
    'Besoins enregistrés',
    COUNT(*)
FROM besoin_ville
UNION ALL
SELECT 
    'Dons reçus',
    COUNT(*)
FROM don;