# 🥬 Projet_EcoRide - Platteforme de covoiturage 
Il s'agit d'une plateforme web de covoiturage écologique permettant de partager des trajets en favorisant la mobilité durable.
Projet réalisé par FONTAINE Jérôme dans le cadre du TP Développeur Web & Web Mobile (TP DWWM).

---

## 📋 Sommaire
1. Présentation de l'application 
2. Accès à l’application  
3. L'applications en fonctionnement (preuves à l'appuie)  
4. Difficultés rencontrées  
5. Ressources utilisées  
6. Installation en mode local  
7. Comment accéder à la plateforme en mode local  
8. Comptes utilisateurs permettant de tester  
9. Problèmes identifés / Limitations de la plateforme  
10. Arboresence du projet  
11. Documentations  
12. A Propos  

---

## 🕐. Présentation de l'application  
l'application EcoRide favorise la mobilité responsable en simplifiant le covoiturage entre particuliers.   
Les utilisateurs pourront explorer, proposer ou réserver des trajets en tenant compte de critères écologiques et/ou sociaux :  
- la présence d’animaux,
- le tabagisme,
- le type de véhicule.

---

## 🕑. Accès à l’application  

---

## 🕒. L'application en fonctionnement (preuves à l'appuie)  

Toutes les fonctionnalités principales sont démontrées dans le PDF “preuves du fonctionnement de l'application” (/docs/Preuves du fonctionnement de l'application.pdf).
Ce document fait foi en cas d’impossibilité.

---

## 🕓. Difficultés rencontrées  

Les principales difficultés sont apparues au fil du développement avec :
- le modèle de Base De Données (BDD) fouurnis qui n'est pas complet : 
    - Ajout de table -> CREATE TABLE ...  
    - Ajout de champs -> ALTER TABLE ...
- la gestion d'un utilisateur connecté utilisable sur les différentes pages
- la gestion des erreurs

---

## 🕔. Ressources utilisées 

- PHP 8.x (PDO orienté objet)
- HTML5, CSS3 (Bootstrap)
- JavaScript 
- MySQL / MariaDB
- Chart.js (statistiques)
- SVG icon library
- Serveur local : XAMPP (Windows)

---

## 🕕. Installation en mode local  

### Prérequis  
- XAMPP (Apache, PHP, MySQL)  
- Git  
### Étapes    
1. Cloner le dépôt
    ```  
    git clone https://github.com/jfontaine01/Projet_EcoRide.git
    ```  
2. Placer le dossier dans XAMPP  
    > Copier le dossier dans C:\xampp\htdocs\Projet_EcoRide  
3. Créer la base de données  
    > Ouvrir phpMyAdmin  
    > Importer SQL/ecoride_db.sql  
4. Configurer la connexion  
    >Modifier config.php (ou backend/config/database.php) avec vos identifiants MySQL locaux :  
    ```  
    $host = 'localhost';
    $dbname = 'ecoride_db';
    $username = 'root';
    $password = '';
    ```
5. Lancer le serveur  
    >Démarrer Apache et MySQL via XAMPP  
    >Accéder à l’application via

---

## 🕖. Comment accéder à la plateforme en mode local   

   L’application est accessible à l’adresse suivante :
   ```  
   http://localhost/Projet_EcoRide/frontend/pages/index.php
   ```  
    
---

## 🕗. Comptes utilisateurs permettant de tester  

Utilisez les identifiants suivants pour tester les différents rôles dans l’application :

- Utilisateur Standard

  Email : utilisateur@ecoride.fr  
  Mot de passe : password  
  Rôle : Utilisateur  

- Modérateur (Employé)

  Email : employe@ecoride.fr  
  Mot de passe : password  
  Rôle : Employé  

- Administrateur  

  Email : admin@ecoride.fr  
  Mot de passe : password  
  Rôle : Administrateur  

Ces identifiants sont ceux de la base locale de développement 

---

## 🕘. Problèmes identifés / Limitations de la plateforme  

Certaines erreurs liés au mauvaise manipulation uutilisateur ne sont pas gérées.  

---

## 🕙. Arboresence du projet  

   ```  
Projet_EcoRide/
├── backend/
│   ├── config/           # Connexion à la base de données, config SQL
│   ├── controllers/      # Logique métier (gestion utilisateurs, etc.)
│   ├── logs/             # Fichiers de logs (accès, erreurs, debug)
├── frontend/
│   ├── pages/            # Pages accessibles (index.php, etc.)
│   ├── assets/           # Ressources statiques
│   │   ├── css/          # Feuilles de style CSS
│   │   ├── img/          # Images métier (illustrations, icônes)
│   │   └── js/           # Scripts JavaScript (interactions, formulaires)
│   └── includes/         # Templates réutilisables (navbar/header/footer)
├── vendors/              # Dépendances installées via Composer
│   ├── composer/         # Fichiers liés au gestionnaire Composer
│   ├── graham-campbell/  # Package utilitaire (ex: configuration, helpers)
│   ├── phpoption/        # Package pour la gestion d’options PHP
│   ├── symfony/          # Composants Symfony (dotenv, routing, etc.)
│   ├── vlucas/           # Package vlucas/phpdotenv gérer variables d’env.
├── .env                  # Fichier d’environnement (variables sensibles)
├── composer.json         # Fichier de config Composer (dépendances)
├── composer.lock         # Fichier de verrouillage des versions packages
└── README.md             # Documentation du projet 
   ```  
  
---

## 🕚. Documentations  

- Gestion de projet : docs/Gestion de Projet.pdf
- Charte graphique : docs/Charte graphique.pdf
- Documentation technique : docs/Documentations_Technique.pdf
- Preuce de bon fonctionnement : docs/Preuve de bon fonctionnement.pdf  
- Manuel utilisateur : docs/Manuel_Utilisateur.pdf  

---

## 🕛. A Propos  

Auteur : FONTAINE Jérôme  
Formation : TP Développeur Web & Web Mobile  

---
