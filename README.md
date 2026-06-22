# Plateforme E-learning Enjah

Bienvenue sur le dépôt de la plateforme **Enjah**, une solution e-learning moderne, permettant aux étudiants de suivre des cours, suivre leur progression et accomplir leurs tâches, le tout dans une interface élégante et performante.

## 🚀 Fonctionnalités
- **Espace Étudiant** : Tableau de bord, consultation de cours, suivi de la progression, gestion des tâches, demande de certificats.
- **Espace Professeur** : Gestion des cours, création de leçons, validation de l'avancement des étudiants.
- **Espace Administrateur** : Vue globale sur la plateforme, gestion des utilisateurs et validation financière.
- **Multilingue** : Interface disponible en Français et en Anglais.

## 🛠️ Stack Technique
- **Backend** : PHP 8+ avec architecture MVC légère, PDO pour l'interaction avec la base de données.
- **Frontend** : HTML5, CSS3 vanilla (design moderne, glassmorphism), JavaScript (Fetch API, DOM manipulation).
- **Base de données** : MySQL / MariaDB (schéma relationnel avec clés étrangères).

## 📁 Structure du Projet
Le projet est divisé en trois applications principales :
- `/Projet/student` : Application Étudiant (point d'entrée principal pour les apprenants).
- `/Projet/professeur` : Application Professeur (gestion du contenu).
- `/Projet/admin` : Application Administrateur (gestion globale).

## ⚙️ Installation & Démarrage (Local)

1. **Cloner le dépôt :**
   ```bash
   git clone https://github.com/votre-nom/e-learning-project.git
   cd e-learning-project/Projet/student
   ```

2. **Configurer l'environnement :**
   Copiez `.env.example` en `.env` et remplissez les variables de connexion à votre base de données MySQL :
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=elearning
   DB_USER=root
   DB_PASS=
   ```

3. **Base de données :**
   Importez le fichier `elearning_unified.sql` (situé dans `database/`) dans votre serveur MySQL pour créer la structure et les tables.

4. **Démarrer le serveur PHP :**
   ```bash
   php -S localhost:8000
   ```
   Rendez-vous sur `http://localhost:8000` pour accéder à la plateforme étudiant.

## ☁️ Déploiement en Production

L'application étant développée en PHP, les plateformes purement "serverless" (comme Vercel ou Netlify) ne sont pas optimales pour héberger le backend complet sans configurations Docker complexes. 

**Recommandations d'hébergement :**
- **Railway.app / Render** : Excellent choix pour héberger du PHP + MySQL. Connectez simplement votre dépôt GitHub, configurez les variables d'environnement, et la plateforme gère le reste.
- **Hébergement mutualisé classique** (Hostinger, O2Switch, OVH) : Transférez les fichiers via FTP et importez la base de données via phpMyAdmin.

---

*Développé avec passion pour rendre l'apprentissage plus accessible et organisé.*
