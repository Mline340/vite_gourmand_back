# Vite & Gourmand 
Site vitrine 

- ![Trello](https://trello.com/b/P9JLcw4y/projet-studi-vite-et-gourmand).
- ![Mailtrap](https://mailtrap.io/).

# Présentation du projet

Pour la réalisation de l'ECF Studi, j'ai réalisé un site web de commande de menus en ligne avec livraison à domicile.

L'entreprise "Vite & Gourmand", installée à Bordeaux depuis plus de 25 ans, souhaite se moderniser et augmenter sa visibilité en proposant ses services de manière plus simple et dynamique via une plateforme web. L'objectif est de permettre aux clients de consulter les menus et de passer commande, ce qui facilitera la gestion des commandes et des livraisons ainsi qu'une image plus moderne de l'entreprise. 

# Architecture du projet 

J'ai séparé le front-end (HTML/CSS/JS) du back-end (Symfony) avec une API REST comme interface de communication. Cette architecture permet une meilleure maintenance et renforce la sécurité en isolant la logique métier et la base de données côté serveur.
- Repository Front-end : Interface utilisateur (HTML/CSS/JS/Bootstrap)
- Repository Back-end : API REST Symfony + base de données

# Technologies utilisées 

## Front-end 
- HTML 5 / CSS3 / JavaScript
- Bootstrap 5 + Bootstrap Icons
- Chart.js (graphiques) 

## Back-end 
- PHP 8.2.12
- Symfony 7.4.2
- Doctrine ORM
- MySQL
- Apache 2
- API Platform 4 
- Security Bundle 
- CROS (nelmio/cros-bundle) voir le fichier : config/packages/nelmio_cors.yaml

## Installation base de données 
- MySQL (Adminer):
    $ php bin/console doctrine:database:create
    $ php bin/console doctrine:migrations:migrate
- MongoDB (données statistiques)
    - Télécharger: https://www.mongodb.com/try/download/community

# Outils de développement 
- Visual Studio Code :
    -Extension front-end  : PHP server + Live SASS Compiler + intelephense
    -Extension back-end : PHP et Symfony
- Git / Github
- XAMPP
- Composer (API- zircote/swagger-php - orm-fixtures)
- Symfony CLI
- npm

# Installation de l'environnement de développement 

## Prérequis
- VSCode : Editeur de texte léger avec extensions pour PHP et Symfony.

- PHP 8.2.12 : Langage de programmation pour le développement web.

- XAMPP  : Package incluant Apache, MySQL et PHP. Simplifie l'installation et la configuration de l'environnement local sans avoir à installer chaque composant séparément. 

- Git/ Github : Contrôle de version pour suivre les modifications et sauvegarde du code.

- Node.js et npm : Gestionnaire de paquets JavaScript pour installer les dépendances front-end.

- Composer : Gestionnaire de dépendances PHP indispensable pour installer les bundles Symfony (Doctrine, Sécurity, API Platform...).

- Symfony CLI : Outil officiel Symfony pour créer des projets, gérer les dépendances et lancer le serveur de développement. Facilite le workflow quotidien.


## Installation des outils de base sur Windows

**Télécharger et installer :**

- PHP : https://www.php.net/
- XAMPP 3.3.0 : https://www.apachefriends.org
- Git : https://git-scm.com/install/windows
- Node.js : https://nodejs.org/fr/download
- npm en ligne de commande  $ npm install -g npm
- Composer : https://getcomposer.org/download/
- Symfony CLI : https://symfony.com/download
- Docker : 

# Création et connexion des repositories Github 

**Méthode** 

- Création des dossiers projet sur mon ordinateur (Vite-Gourmand-front et vite_gourmand_back)
- Ouvrir chaque dossier avec Visual Studio Code :
    - Se rendre sur l'onglet Contrôle de code source
    - Sélectionner : Publish to Github
    - Choisir  : Public 
    - Puis valider 
- VS Code gère automatiquement : 
    - L'initialisation Git 
    - La création du repository sur Github
    - La connexion entre le projet local et Github
    - Le premier commit et push 

# Installation du projet côté Back-end

## Création du projet Symfony
symfony new vite_gourmand_back --version=7.4

## Installation des dépendances :

- Doctrine : 
```bash
composer require symfony/orm-pack
```
- Sécurité : 
```bash
composer require symfony/security-bundle
```
- API REST : 
```bash
composer require api-platform/core
```
- CROS :  
```bash
composer require nelmio/cors-bundle
```
- Doc API :   
```bash
composer require zircote/swagger-php
```
- Fixtures :  
```bash
composer require --dev orm-fixtures
```
 

## Configuration de la base de données : 

Création du fichier .env.local 
   DATABASE_URL="mysql://root:*******@127.0.0.1:3306/bdd?serverVersion=10.4.32-MariaDB&charset=utf8mb4"

Création de la base de données : 
    ```bash
    php bin/console doctrine:database:create
    ```

Exécuter les migrations :
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

## Lancement du serveur : 
Démarer le serveur Symfony : 
- symfony server:start 
    http://localhost:8000

# Installations du projet côté Front-end 

Installation des dépendances :
 ```bash
 npm install
 ```

Ouvrir l'application :
- PHP Server
    http://localhost:3000

# Fichier SQL   

Import des fichiers SQL depuis Adminer : 
- Fichier SQL de création de la srtucture [Tables](BDD/tables.sql) 
- Fichier SQL d'intégration de données [Intégration](BDD/integration.sql)

Exemple de requêt SQL : 
- Retrouver toutes les commandes rattachées à un utilisateur :
 'SELECT * FROM commande WHERE user_id = 11;'

- Calculer le prix par personne : 
'SELECT (prix_menu / nombre_personne) as total 
FROM commande 
WHERE id = 1;'

- Calculer le nombre de menu commander 
'SELECT (menu_id),
    COUNT(commande_id)
FROM commande_menu
GROUP BY menu_id;'

# Manuel d'utilisation 
[Manuel d'utilisation](PDF/Manuel-d'utilisation.pdf).

# Charte 
[Charte graphique](PDF/Charte-graphique.pdf).

# Gestion de projet 
[Gestion de projet](PDF/Gestion-de-projet.pdf).