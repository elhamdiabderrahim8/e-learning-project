Installer xampp via https://www.apachefriends.org/fr/index.html

🚀 Étapes d'installation
1. Récupérer le projet (Pull/Clone)
Ouvrez un terminal dans votre dossier C:/xampp/htdocs/ et tapez :


git clone git@github.com:elhamdiabderrahim8/e-learning-project.git

git checkout -b abderrahim-attempt origin/abderrahim-attempt

2. Configurer XAMPP
Lancez le XAMPP Control Panel.

Cliquez sur Start pour les modules Apache et MySQL.

3. Importer la Base de Données (elearning)
C'est l'étape la plus importante pour que les certificats et les cours fonctionnent :

Allez sur http://localhost/phpmyadmin/.

Cliquez sur "Nouvelle" dans la barre latérale gauche.

Nommez la base de données : elearning et cliquez sur Créer.

Sélectionnez la base elearning, puis cliquez sur l'onglet "Importer" en haut.

Choisissez le fichier SQL situé dans le dossier :projet/taki/database/elearning.sql 

Cliquez sur "Importer" en bas de la page.

4. Appliquer la migration Support / suppression de `users`
Après l'import, exécutez le script SQL :
projet/taki/database/migrations/2026-04-04_support_and_remove_users.sql
dans phpMyAdmin (onglet SQL). Cela supprime la table `users` et ajoute la table `support_messages`.

5. Vérification de la Connexion
Assurez-vous que votre fichier de configuration (ex: backend/config/database.php) utilise ces paramètres par défaut :

Serveur : localhost

Utilisateur : root

Mot de passe : "" (vide)

Base de données : elearning

🖥️ Utilisation
Une fois installé, accédez au projet via votre navigateur :
http://localhost/projet/

