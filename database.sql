DROP DATABASE IF EXISTS bngrc_dons;
CREATE DATABASE bngrc_dons;
USE bngrc_dons;


CREATE TABLE ville (
    id_ville INT PRIMARY KEY AUTO_INCREMENT,
    nom_ville VARCHAR(100) NOT NULL,
    region VARCHAR(100),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


    id_categorie INT PRIMARY KEY AUTO_INCREMENT,
    nom_categorie VARCHAR(50) NOT NULL,
    description TEXT
);


CREATE TABLE type_besoin (
    id_type_besoin INT PRIMARY KEY AUTO_INCREMENT,
    id_categorie INT NOT NULL,
    nom VARCHAR(100) NOT NULL,
    unite VARCHAR(20) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_categorie) REFERENCES categorie_besoin(id_categorie)
);


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


CREATE TABLE don (
    id_don INT PRIMARY KEY AUTO_INCREMENT,
    id_donateur INT,
    id_type_besoin INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    date_saisie TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_donateur) REFERENCES donateur(id_donateur),
    FOREIGN KEY (id_type_besoin) REFERENCES type_besoin(id_type_besoin)
);


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


-- Les 3 categories
INSERT INTO categorie_besoin (nom_categorie, description) VALUES
('nature', 'Produits alimentaires'),
('materiaux', 'Materiaux de construction'),
('argent', 'Aide financiere');


INSERT INTO ville (nom_ville, region) VALUES
('Antananarivo', 'Analamanga'),
('Toamasina', 'Atsinanana'),
('Antsirabe', 'Vakinankaratra'),
('Mahajanga', 'Boeny'),
('Fianarantsoa', 'Haute Matsiatra');


INSERT INTO type_besoin (id_categorie, nom, unite, prix_unitaire) VALUES
(1, 'Riz', 'kg', 2000.00),
(1, 'Huile', 'litre', 8000.00),
(1, 'Sucre', 'kg', 3000.00),
(1, 'Haricots', 'kg', 4000.00),
(1, 'Eau potable', 'litre', 500.00);


INSERT INTO type_besoin (id_categorie, nom, unite, prix_unitaire) VALUES
(2, 'Tole', 'piece', 35000.00),
(2, 'Clou', 'kg', 8000.00),
(2, 'Ciment', 'sac', 25000.00),
(2, 'Bache', 'piece', 12000.00),
(2, 'Couverture', 'piece', 20000.00);


INSERT INTO type_besoin (id_categorie, nom, unite, prix_unitaire) VALUES
(3, 'Aide financiere', 'Ar', 1.00);


INSERT INTO donateur (nom, prenom, telephone, type_donateur) VALUES
('RASOANAIVO', 'Jean', '0331234567', 'particulier'),
('RAKOTO', 'Marie', '0341234567', 'particulier'),
(NULL, NULL, '0201234567', 'entreprise'),
('RANDRIA', 'Paul', '0341112233', 'particulier');

UPDATE donateur SET organisation = 'Entreprise TechMada' WHERE id_donateur = 3;


INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee) VALUES
(1, 1, 5000.00),  
(1, 2, 500.00),   
(1, 6, 200.00),   
(1, 8, 100.00);   


INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee) VALUES
(2, 1, 3000.00),  
(2, 5, 2000.00),  
(2, 9, 150.00),   
(2, 11, 5000000.00); 


INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee) VALUES
(3, 1, 2000.00),  
(3, 4, 800.00),   
(3, 10, 100.00),  
(3, 2, 300.00),   
(3, 5, 1000.00);  

INSERT INTO don (id_donateur, id_type_besoin, quantite) VALUES
(1, 1, 1000.00),  
(2, 2, 200.00),   
(3, 6, 100.00),   
(4, 1, 500.00),  
(1, 10, 30.00);   


-- V2 : Ajout de la table de configuration des frais et des tables pour les achats et simulations
CREATE TABLE IF NOT EXISTS config_frais (
    id_config INT PRIMARY KEY AUTO_INCREMENT,
    nom_config VARCHAR(100) NOT NULL UNIQUE,
    valeur DECIMAL(5,2) NOT NULL,
    description TEXT,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insérer le frais d'achat par défaut (10%)
INSERT INTO config_frais (nom_config, valeur, description) VALUES
('frais_achat_pourcentage', 10.00, 'Frais appliqués lors de l''achat de besoins avec de l''argent (en pourcentage)')
ON DUPLICATE KEY UPDATE valeur = 10.00;




CREATE TABLE IF NOT EXISTS achat (
    id_achat INT PRIMARY KEY AUTO_INCREMENT,
    id_don_argent INT NOT NULL,
    id_type_besoin INT NOT NULL,
    quantite_achetee DECIMAL(10,2) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    frais_pourcentage DECIMAL(5,2) NOT NULL,
    montant_total DECIMAL(12,2) NOT NULL,
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_don_argent) REFERENCES don(id_don),
    FOREIGN KEY (id_type_besoin) REFERENCES type_besoin(id_type_besoin)
);



CREATE TABLE IF NOT EXISTS simulation (
    id_simulation INT PRIMARY KEY AUTO_INCREMENT,
    id_don_argent INT NOT NULL,
    details_json TEXT NOT NULL,
    montant_total DECIMAL(12,2) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_cours', 'validee', 'annulee') DEFAULT 'en_cours',
    FOREIGN KEY (id_don_argent) REFERENCES don(id_don)
);



SELECT 'Vérification des besoins existants :' AS info;
SELECT v.nom_ville, tb.nom AS type_besoin, bv.quantite_demandee, bv.quantite_recue,
       (bv.quantite_demandee - bv.quantite_recue) AS manquant
FROM besoin_ville bv
JOIN ville v ON bv.id_ville = v.id_ville
JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
WHERE bv.quantite_recue < bv.quantite_demandee
ORDER BY v.nom_ville, tb.nom;



INSERT INTO don (id_donateur, id_type_besoin, quantite) VALUES
(1, 11, 10000000.00),  
(2, 11, 5000000.00),   
(3, 11, 20000000.00)   
ON DUPLICATE KEY UPDATE id_don = id_don;
