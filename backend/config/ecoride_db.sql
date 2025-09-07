-- Création de la base
CREATE DATABASE IF NOT EXISTS ecoride_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ecoride_db;

-- D'abord : suppression si existant (ordre contraintes OK)
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS role;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS status;
DROP TABLE IF EXISTS marque;
DROP TABLE IF EXISTS voiture;
DROP TABLE IF EXISTS covoiturage;
DROP TABLE IF EXISTS covoiturage_participants;
DROP TABLE IF EXISTS avis;
DROP TABLE IF EXISTS preferences_conducteur;
DROP TABLE IF EXISTS preferences_trajet;
DROP TABLE IF EXISTS parametre;
DROP TABLE IF EXISTS configuration;
DROP TABLE IF EXISTS signalements;
DROP TABLE IF EXISTS logs_activite;

SET FOREIGN_KEY_CHECKS = 1;


-- Table des rôles
CREATE TABLE role (
  role_id INT AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(50) NOT NULL
);

-- Table des utilisateurs
CREATE TABLE utilisateur (
  utilisateur_id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(50),
  prenom VARCHAR(50),
  email VARCHAR(100) UNIQUE,
  password VARCHAR(255),
  telephone VARCHAR(20),
  adresse VARCHAR(100),
  date_naissance DATE,
  photo BLOB,
  pseudo VARCHAR(50),
  role_id INT,
  status_id INT, 
  cree_le DATETIME DEFAULT CURRENT_TIMESTAMP, 
  maj_le DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table status utilisateurs
CREATE TABLE status (
  status_id INT AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(50)
);

-- Table des marques de véhicule
CREATE TABLE marque (
  marque_id INT AUTO_INCREMENT PRIMARY KEY,
  libelle VARCHAR(50) NOT NULL
);

-- Table des véhicules
CREATE TABLE voiture (
  voiture_id INT AUTO_INCREMENT PRIMARY KEY,
  modele VARCHAR(50),
  immatriculation VARCHAR(20) UNIQUE,
  energie VARCHAR(50),
  couleur VARCHAR(50),
  date_premiere_immatriculation DATE,
  utilisateur_id INT,
  marque_id INT
);

-- Table des covoiturages
CREATE TABLE covoiturage (
  covoiturage_id INT AUTO_INCREMENT PRIMARY KEY,
  date_depart DATE,
  heure_depart TIME,
  lieu_depart VARCHAR(100),
  date_arrivee DATE,
  heure_arrivee TIME,
  lieu_arrivee VARCHAR(100),
  statut VARCHAR(50),
  nb_place INT,
  prix_personne DECIMAL(6,2),
  voiture_id INT,
  conducteur_id INT
);

/* Anomalie détecté lors du déploiements
-- Table des participants à un covoiturage
CREATE TABLE covoiturage_participants (
  covoiturage_participants_id INT AUTO_INCREMENT PRIMARY KEY,
  covoiturage_id INT PRIMARY KEY,
  utilisateur_id INT,
  statut_participation VARCHAR(50)
);
*/

-- Table des participants à un covoiturage
CREATE TABLE covoiturage_participants (
  covoiturage_participants_id INT AUTO_INCREMENT PRIMARY KEY,
  covoiturage_id INT NOT NULL,
  utilisateur_id INT NOT NULL,
  statut_participation VARCHAR(50),

  -- Clés étrangères
  FOREIGN KEY (covoiturage_id) REFERENCES covoiturage(covoiturage_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  FOREIGN KEY (utilisateur_id) REFERENCES utilisateur(utilisateur_id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- Table des avis
CREATE TABLE avis (
  avis_id INT AUTO_INCREMENT PRIMARY KEY,
  commentaire TEXT,
  note INT CHECK (note BETWEEN 1 AND 5),
  statut VARCHAR(50),
  conducteur_id INT,
  passager_id INT,
  covoiturage_id INT,
  cree_le DATETIME DEFAULT CURRENT_TIMESTAMP,
  valide_id INT,
  valide_le DATETIME
);

-- Table des préférences du conducteur
CREATE TABLE preferences_conducteur (
  preferences_conducteur_id INT AUTO_INCREMENT PRIMARY KEY,
  utilisateur_id INT,
  fumeur TEXT DEFAULT NULL,
  animaux TEXT DEFAULT NULL,
  autres TEXT
);

-- Table des préférences spécifiques au trajet
CREATE TABLE preferences_trajet (
  preferences_trajet_id INT AUTO_INCREMENT PRIMARY KEY,
  covoiturage_id INT,
  fumeur BOOLEAN DEFAULT FALSE,
  animaux BOOLEAN DEFAULT FALSE,
  autres TEXT
);

-- Table des paramètres techniques
CREATE TABLE parametre (
  parametre_id INT AUTO_INCREMENT PRIMARY KEY,
  propriete VARCHAR(50),
  valeur VARCHAR(50)
);

-- Table de configuration
CREATE TABLE configuration (
  id_configuration INT AUTO_INCREMENT PRIMARY KEY
);

-- Table des signalements
CREATE TABLE signalements (
  signalement_id INT AUTO_INCREMENT PRIMARY KEY,
  covoiturage_id INT,
  motif TEXT,
  signale_id INT,
  cree_le DATETIME DEFAULT CURRENT_TIMESTAMP,
  statut VARCHAR(50) DEFAULT 'ouvert'
);

-- Table des logs d'activités 
CREATE TABLE logs_activite (
  logs_activite_id INT AUTO_INCREMENT PRIMARY KEY,
  admin_id INT,
  action TEXT,
  ip_address VARCHAR(45),
  cree_le DATETIME DEFAULT CURRENT_TIMESTAMP
);


-- Données de démonstration 
    -- Table paramétrages 
        -- Table status
        INSERT INTO status (status_id, libelle) 
        VALUES
        (1, 'compte actif'),
        (2, 'compte suspendu'),
        (3, 'compte désactivé');

        -- Table role
        INSERT INTO role (role_id, libelle) 
        VALUES
        (1, 'Administrateur'),
        (2, 'Employé'),
        (3, 'Utilisateur');

        -- Table parametre
        INSERT INTO parametre (parametre_id,propriete,valeur) 
        VALUES
        (1, 'Crédit initial', 20 );

        -- Table marque
        INSERT INTO marque ( marque_id, libelle) 
        VALUES
        (1,'Abarth'),
        (2,'Aiways'),
        (3,'Alfa Romeo'),
        (4,'Alpine'),
        (5,'Aston Martin'),
        (6,'Audi'),
        (7,'Austin'),
        (8,'Bentley'),
        (9,'BMW'),
        (10,'Bugatti'),
        (11,'Buick'),
        (12,'Cadillac'),
        (13,'Carver'),
        (14,'Chevrolet'),
        (15,'Chrysler'),
        (16,'Citroën'),
        (17,'Corvette'),
        (18,'Cupra'),
        (19,'Dacia'),
        (20,'Daewoo'),
        (21,'Daihatsu'),
        (22,'Daimler'),
        (23,'Datsun'),
        (24,'Dodge'),
        (25,'DS'),
        (26,'Ferrari'),
        (27,'Fiat'),
        (28,'Fisker'),
        (29,'Ford'),
        (30,'Honda'),
        (31,'Hummer'),
        (32,'Jaguar'),
        (33,'Jeep'),
        (34,'Kia'),
        (35,'Lada'),
        (36,'Lamborghini'),
        (37,'Lancia'),
        (38,'Land Rover'),
        (39,'Lexus'),
        (40,'Lincoln'),
        (41,'Lotus'),
        (42,'Maserati'),
        (43,'Mazda'),
        (44,'McLaren'),
        (45,'Mercedes-Benz'),
        (46,'MG'),
        (47,'Mini'),
        (48,'Mitsubishi'),
        (49,'Nissan'),
        (50,'Opel'),
        (51,'Peugeot'),
        (52,'Pontiac'),
        (53,'Porsche'),
        (54,'Renault'),
        (55,'Rolls-Royce'),
        (56,'Rover'),
        (57,'Saab'),
        (58,'Seat'),
        (59,'Skoda'),
        (60,'Subaru'),
        (61,'Suzuki'),
        (62,'Talbot'),
        (63,'Tesla'),
        (64,'Toyota'),
        (65,'Volkswagen'),
        (66,'Volvo');


        -- Table logs_activités
        INSERT INTO logs_activite (logs_activite_id, admin_id, action, ip_address, cree_le) 
        VALUES
        (1,1,'Connexion' ,'localhost','2025-08-15 16:40:38'),
        (2,1,'Deconnexion', 'localhost', '2025-08-15 16:50:32'),
        (3,1,'Connexion' ,'localhost','2025-08-16 13:40:38'),
        (4,1,'Connexion' ,'localhost','2025-08-16 14:00:13'),
        (5,1,'Deconnexion', 'localhost', '2025-08-15 14:02:57');

    -- Table fonctionelles
      -- Table utilisateur
      INSERT INTO utilisateur (utilisateur_id, nom, prenom, email, password, telephone, adresse, date_naissance, photo, pseudo, role_id, status_id, cree_le, maj_le) 
      VALUES 
      (1, 'Administrateur', '', 'admin@ecoride.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '06.02.xx.35.88', 'Place de la bourse 69000 Lyon','1970-06-18', '', 'Adm', 1, 1, '2025-08-15 18:40:38', '2025-08-15 18:40:38'), 
      (2, 'Employé', '', 'employe@ecoride.fr',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '07.22.68.xx.78', 'Avenue E. Zola 13000 Marseille', '1990-12-15', '', 'Mod', 2, 1, '2025-08-15 18:40:38', '2025-08-15 18:40:38'),
      (3, 'Utilisateur', '', 'utilisateur@ecoride.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '06.12.xx.57.44', 'Rue du centre 71000 Macon', '2001-02-03', '', 'Util', 3, 1, '2025-08-15 18:40:38', '2025-08-15 18:40:38');

      ALTER TABLE utilisateur
      ADD credit INT DEFAULT 0,
      ADD conducteur ENUM('O','N') DEFAULT 'N',
      ADD passager ENUM('O','N') DEFAULT 'O';

      -- Table des véhicules
      INSERT INTO voiture (voiture_id, modele, immatriculation, energie, couleur, date_premiere_immatriculation, utilisateur_id, marque_id) 
      VALUES
      (1,'3008', 'AG-358-TY', 'Hybride', 'Bleue','2021-01-13',2,1),
      (2,'A3 Sportback', 'BJ-123-DF', 'Essence', 'Grise','2018-03-16',1,2),
      (3,'DOLPHIN', 'DR-621-RT', 'Electrique', 'Violette', '2024-08-25',3,3), 
      (4,'Model S', 'JD-456-TG', 'Electrique', 'Bordeaux','2023-12-09',3,4);

      ALTER TABLE voiture
      ADD nb_places_dispo VARCHAR(2) DEFAULT '1';

      -- Table covoiturage
      INSERT INTO covoiturage (covoiturage_id, date_depart, heure_depart, lieu_depart, date_arrivee, heure_arrivee, lieu_arrivee, statut, nb_place, prix_personne, voiture_id, conducteur_id ) 
      VALUES
      (1,'2025-07-11','09:30','Lyon 7','2025-07-11','12:30','Marseille Vieux Port','planifié','3','15 €',2,2),
      (2,'2025-07-17','18:15','Marseille St Charles','2025-07-17','22:30','Nice','planifié','2','18 €',3,3),
      (3,'2025-07-11','09:30','Lyon 7','2025-07-11','12:30','Marseille Vieux Port','planifié','1','38 €',4,3),
      (4,'2025-08-14','16:30','Bordeaux','2025-08-14','22:30','Lyon','planifié','4','35 €',1,1);

      -- Table covoiturage_participants
      INSERT INTO covoiturage_participants (covoiturage_participants_id, covoiturage_id, utilisateur_id, statut_participation)
      VALUES
      (1, 1, 3, 'Présent'),
      (2, 3, 1, 'A confirmer'),
      (3, 4, 2, 'Présent');

      -- Table avis
      INSERT INTO avis (avis_id, commentaire, note, statut, conducteur_id, passager_id, covoiturage_id , cree_le, valide_id, valide_le) 
      VALUES 
      (1,'Excellent chauffeur, très ponctuel et sympatique', 5, 'à modérer', 1, 3, 2, '2025-07-18', NULL, NULL),
      (2,'Passager agréable et ouvert aux éxhanges', 4, 'validé', 3, 3, 3, '2025-07-12', 2, '2025-07-13'),
      (3,'Excellent', 4, 'à moderer', 3, 1, 2, '2025-07-18', NULL, NULL);

      -- Table preferences_conducteur
      INSERT INTO preferences_conducteur (preferences_conducteur_id, utilisateur_id, fumeur, animaux, autres)
      VALUES
      (1,2,'N', 'O', 'Passager ouvert pour discuter'),
      (2,1,'O', 'N', 'Aime le musique rock');
 
      -- Table preferences_trajet
      INSERT INTO preferences_trajet (preferences_trajet_id, covoiturage_id, fumeur, animaux, autres) 
      VALUES
      (1, 2, NULL, NULL, 'voyage climatisé');

      -- Table signalements
      INSERT INTO signalements (signalement_id, covoiturage_id, motif, signale_id, cree_le, statut) 
      VALUES
      (1, 3, 'Trop bavard sur des sujets sans connaitre les passagers', 2, '2025-07-20', 'ouvert');

