<div align="center">
  <img src="Projet/student/media/logo.jpg" alt="Logo Enjah" width="350" style="border-radius: 20px;"/>
  <h1>Enjah - Plateforme E-learning Moderne</h1>
</div>

Bienvenue sur le dépôt du projet **Enjah**. Ce document est divisé en deux parties : une présentation du produit pour les clients et partenaires, et une documentation technique pour les développeurs.

---

## 🎯 1. Pour les Clients & Partenaires (Présentation du Produit)

### Qu'est-ce qu'Enjah ?
**Enjah** est une plateforme d'apprentissage en ligne (e-learning) moderne, conçue pour centraliser et simplifier la gestion des cours, des tâches et du suivi des apprenants. L'objectif d'Enjah est de supprimer les frictions technologiques pour se concentrer sur l'essentiel : **apprendre et progresser efficacement**.

### Pourquoi choisir Enjah ?
- **Suivi visuel de la progression** : Chaque étudiant peut voir exactement où il en est dans sa formation grâce à des barres de progression claires et motivantes.
- **Gestion des tâches intégrée** : Un tableau de bord façon "Kanban" (À faire, En cours, Terminé) permet aux apprenants d'organiser leur travail directement sur la plateforme.
- **Système de certification** : Une fois un cours validé à 100%, l'apprenant reçoit un certificat officiel généré automatiquement.
- **Multilingue** : L'interface est entièrement traduite en Français et en Anglais pour s'adapter à une audience internationale.
- **Design élégant et ergonomique** : L'interface utilise les codes modernes du web (glassmorphism, couleurs dynamiques) pour offrir une expérience utilisateur (UX) haut de gamme, que ce soit sur ordinateur ou sur mobile.

### Les Différents Espaces
- **Espace Apprenant** : Un tableau de bord pour suivre ses cours, télécharger ses certificats, gérer ses to-do lists et envoyer des requêtes de support.
- **Espace Formateur** : Un espace dédié pour publier des leçons, suivre l'avancement global des élèves, et valider l'obtention des diplômes.
- **Espace Administrateur** : Un tableau de contrôle central pour la gestion des utilisateurs, la modération et la supervision des paiements.

---

## 💻 2. Pour les Développeurs (Documentation Technique)

Cette section détaille l'architecture et les instructions de déploiement de l'application.

### Stack Technique
- **Backend** : PHP 8+ (Architecture Model-View-Controller allégée).
- **Frontend** : HTML5, CSS3 natif (variables CSS, flexbox/grid), JavaScript (manipulation du DOM).
- **Base de données** : MySQL / MariaDB utilisant l'API `PDO` pour la sécurité (Prepared Statements).
- **Architecture** : Hébergement de fichiers isolés par type de compte (Étudiant, Professeur, Admin) pour une sécurité et une modularité accrues.

### Installation en Local

1. **Clonage du dépôt** :
   ```bash
   git clone https://github.com/votre-compte/e-learning-project.git
   cd e-learning-project
   ```

2. **Base de données** :
   - Importez le schéma unifié disponible dans `Projet/student/database/elearning_unified.sql` dans votre instance MySQL.

3. **Variables d'environnement** :
   - Allez dans `Projet/student/`.
   - Copiez le fichier `.env.example` en `.env`.
   - Remplissez les informations de votre base de données :
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=elearning
   DB_USER=root
   DB_PASS=votre_mot_de_passe
   ```

4. **Démarrer le serveur** :
   ```bash
   cd Projet/student
   php -S localhost:8000
   ```
   L'application est maintenant disponible sur `http://localhost:8000`.

### Déploiement en Production

L'application requiert un environnement supportant PHP et MySQL.

- **Option A : Railway.app / Render (Recommandé)**  
  Connectez simplement le dépôt GitHub à Railway, ajoutez une ressource MySQL, injectez les variables d'environnement (`DB_HOST`, etc.) dans les paramètres du projet, et le déploiement se fera automatiquement et nativement.

- **Option B : Hébergement mutualisé (O2Switch, Hostinger, OVH)**  
  Envoyez les fichiers du dossier `Projet/` par FTP. Importez la base de données via phpMyAdmin et ajustez le fichier `.env` ou les variables du serveur.

- **Option C : Vercel**  
  Bien que Vercel soit orienté Node.js/Statique, un fichier `vercel.json` est inclus à la racine pour forcer l'exécution de PHP via le builder communautaire (`vercel-php@0.6.2`). **Note :** Vous devrez impérativement connecter une base de données MySQL externe (ex: Aiven, PlanetScale ou Railway) dans les variables d'environnement de Vercel.

---
*Projet développé pour repousser les limites de l'expérience éducative en ligne.*
