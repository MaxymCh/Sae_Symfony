Symfony Application Web Quizz
========================

## Membres
### Charpentier Maxym
### Jory Jonathan

------------
## Présentation

L'application web quizz est une application composé de 2 modules.
Module 1: Création de questionnaires, composé de questions et réponses
Module 2: Module permettant de répondre aux questionnaires.

Requirements
------------

  * PHP 8.1.0 or higher;
  * PDO-SQLite PHP extension enabled;
  * and the [usual Symfony application requirements][2].

Installation
------------
 Installer symfony et composer.
 
 Crée un fichier .env.local contenant la connexion à la base de donée mysql.
 Exemple à l'iut DATABASE_URL="mysql://nom_utilisateur:mdp_utilisateur@servinfo-mariadb:3306/nom_de_la_BD"
 Se placer dans le répertoire racine et executer la commande composer update.
 Puis executer la commande symfony console doctrine:migrations:migrate pour la création de la BDD.
  
 
Usage
-----

 Se placer dans le répertoire racine.
 Executer la commande symfony server:start
