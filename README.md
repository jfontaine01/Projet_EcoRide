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

## 🕒. L'applications en fonctionnement (preuves à l'appuie)  

---

## 🕓. Difficultés rencontrées  

---

## 🕔. Ressources utilisées  

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
   http://localhost/ecoride/frontend/public/pages/index.php
   ```  
    
---

## 🕗. Comptes utilisateurs permettant de tester  

Utilisez les identifiants suivants pour tester les différents rôles dans l’application :

Utilisateur Standard

Email : user@ecoride.fr
Mot de passe : password
Rôle : Utilisateur

Administrateur

Email : admin@ecoride.fr
Mot de passe : password
Rôle : Administrateur

Modérateur

Email : modo@ecoride.fr
Mot de passe : password
Rôle : Modérateur

Ces identifiants sont ceux de la base locale de développement 

---

## 🕘. Problèmes identifés / Limitations de la plateforme  

---

## 🕙. Arboresence du projet  

---

## 🕚. Documentations  

---

## 🕛. A Propos  

---
