<?php

declare(strict_types=1);

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $key, string $message): void
{
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $message;
}

function current_language(): string
{
    $lang = (string) ($_SESSION['preferred_language'] ?? 'en');
    return in_array($lang, ['en', 'fr'], true) ? $lang : 'en';
}

function translate_output_to_english(string $content): string
{
    static $map = null;

    if ($map === null) {
        $map = [
            '<html lang="fr">' => '<html lang="en">',
            'Plateforme d\'apprentissage' => 'Learning Platform',
            'Plateforme E-learning' => 'E-learning Platform',
            'Navigation principale' => 'Main navigation',
            'Accueil Enjah' => 'Enjah Home',
            'Parcours' => 'Tracks',
            'Methode' => 'Method',
            'Avis' => 'Reviews',
            'Connexion' => 'Login',
            'Essai gratuit' => 'Free trial',
            'Commencer maintenant' => 'Start now',
            'J\'ai deja un compte' => 'I already have an account',
            'Apprenants' => 'Learners',
            'Taux de satisfaction' => 'Satisfaction rate',
            'Heures de contenu' => 'Hours of content',
            'Voir mes cours' => 'View my courses',
            'Parcours populaires' => 'Popular tracks',
            'Methode Enjah' => 'Enjah method',
            'Retours d\'apprenants' => 'Learner feedback',
            'Pret a transformer ton apprentissage ?' => 'Ready to transform your learning?',
            'Demarrer avec Enjah' => 'Start with Enjah',
            'Tous droits reserves.' => 'All rights reserved.',
            'Mes Cours' => 'My Courses',
            'Mes Taches' => 'My Tasks',
            'Mes T&acirc;ches' => 'My Tasks',
            'Choisir une offre' => 'Choose a plan',
            'Reclamation' => 'Support',
            'R&eacute;clamation' => 'Support',
            'Mon Profil' => 'My Profile',
            'Tableau des taches' => 'Task board',
            'Organisez votre travail en 3 etapes simples : A faire, En cours, Terminee.' => 'Organize your work in 3 simple stages: To do, In progress, Done.',
            'Ajouter une tache' => 'Add task',
            'Titre' => 'Title',
            'Priorite' => 'Priority',
            'Haute' => 'High',
            'Moyenne' => 'Medium',
            'Basse' => 'Low',
            'Date limite (optionnel)' => 'Due date (optional)',
            'Ajouter la tache' => 'Add task',
            'A faire' => 'To do',
            'En cours' => 'In progress',
            'Terminee' => 'Done',
            'Aucune tache.' => 'No tasks.',
            'Rechercher une tache' => 'Search a task',
            'Toutes priorites' => 'All priorities',
            'Filtrer par priorite' => 'Filter by priority',
            'Filtrer par echeance' => 'Filter by deadline',
            'Toutes echeances' => 'All deadlines',
            'Avec date limite' => 'With due date',
            'Sans date limite' => 'Without due date',
            'En retard' => 'Overdue',
            'A rendre aujourd\'hui' => 'Due today',
            'Cette semaine' => 'This week',
            'Trier les taches' => 'Sort tasks',
            'Tri par defaut' => 'Default sort',
            'Date limite proche' => 'Closest due date',
            'Date limite lointaine' => 'Farthest due date',
            'Priorite haute d\'abord' => 'High priority first',
            'Priorite basse d\'abord' => 'Low priority first',
            'Titre A-Z' => 'Title A-Z',
            'Reinitialiser' => 'Reset',
            'Sans date limite' => 'No due date',
            'Date limite:' => 'Due date:',
            'Mettre a jour' => 'Update',
            'Supprimer la tache' => 'Delete task',
            'Aucune tache ne correspond aux filtres.' => 'No task matches the filters.',
            'Catalogue des Cours' => 'Course Catalog',
            'Rechercher un cours...' => 'Search for a course...',
            'Tous' => 'All',
            'D&eacute;veloppement' => 'Development',
            'Design' => 'Design',
            'Business' => 'Business',
            'Compl&eacute;t&eacute;' => 'Completed',
            'Continuer' => 'Continue',
            'Offres Sp&eacute;ciales' => 'Special Offers',
            'Investissez dans votre avenir avec nos cours premium.' => 'Invest in your future with our premium courses.',
            'Choisir ce cours' => 'Choose this course',
            '&Eacute;tape 1 : Choisir la date' => 'Step 1: Choose the date',
            'Date de d&eacute;but du cours' => 'Course start date',
            'Cr&eacute;neau horaire' => 'Time slot',
            'Matin (09:00 - 12:00)' => 'Morning (09:00 - 12:00)',
            'Apr&egrave;s-midi (14:00 - 17:00)' => 'Afternoon (14:00 - 17:00)',
            'Soir (19:00 - 22:00)' => 'Evening (19:00 - 22:00)',
            'Continuer vers le paiement' => 'Continue to payment',
            '&Eacute;tape 2 : Paiement s&eacute;curis&eacute;' => 'Step 2: Secure payment',
            'Nom sur la carte' => 'Name on card',
            'Num&eacute;ro de carte' => 'Card number',
            'Valider le paiement' => 'Confirm payment',
            'Paiement valid&eacute; !' => 'Payment confirmed!',
            'Acc&eacute;der &agrave; mes cours' => 'Go to my courses',
            'Envoyer une reclamation' => 'Send a support request',
            'Un probleme ? Notre equipe vous repondra sous 24h.' => 'Having an issue? Our team will reply within 24h.',
            'Support Etudiant' => 'Student Support',
            'Reponse sous 24h ouvrables' => 'Reply within 24 business hours',
            'Suivi personnalise de votre demande' => 'Personalized follow-up',
            'Confidentialite de vos informations' => 'Your information stays confidential',
            'Sujet de la reclamation' => 'Request subject',
            'Description detaillee' => 'Detailed description',
            'Pieces jointes (facultatif)' => 'Attachments (optional)',
            'Envoyer la reclamation' => 'Send request',
            'Mon Profil - Enjah' => 'My Profile - Enjah',
            'Mon Profil' => 'My Profile',
            'Gerez vos informations personnelles.' => 'Manage your personal information.',
            'Compte actif' => 'Active account',
            'Membre depuis' => 'Member since',
            'Informations du compte' => 'Account information',
            'Prenom' => 'First name',
            'Nom' => 'Last name',
            'Adresse email' => 'Email address',
            'Langue' => 'Language',
            'Parametres' => 'Settings',
            'Langue du site' => 'Website language',
            'English (default)' => 'English (default)',
            'Francais' => 'French',
            'Enregistrer les parametres' => 'Save settings',
            'Se deconnecter' => 'Log out',
            'Supprimer le profil' => 'Delete profile',
            'Vous avez deja un compte ?' => 'Already have an account?',
            'Pas encore de compte ?' => 'No account yet?',
            'S\'inscrire gratuitement' => 'Sign up for free',
            'S\'inscrire' => 'Sign up',
            'Adresse Email' => 'Email address',
            'Mot de passe' => 'Password',
            'Confirmer le mot de passe' => 'Confirm password',
            'Inscription' => 'Sign up',
            'Creez votre compte pour commencer votre parcours d\'apprentissage.' => 'Create your account to start your learning journey.',
            'Connectez-vous pour acceder a votre espace d\'apprentissage.' => 'Log in to access your learning space.',
            'Le prenom et le nom sont obligatoires.' => 'First name and last name are required.',
            'Profil mis a jour avec succes.' => 'Profile updated successfully.',
            'Utilisateur introuvable.' => 'User not found.',
            'Votre reclamation a ete envoyee.' => 'Your request has been sent.',
            'Le titre de la tache est obligatoire.' => 'Task title is required.',
            'La date limite est invalide.' => 'The due date is invalid.',
            'Nouvelle tache ajoutee.' => 'New task added.',
            'Tache invalide.' => 'Invalid task.',
            'Tache introuvable.' => 'Task not found.',
            'Tache terminee supprimee.' => 'Completed task deleted.',
            'Tache deja terminee.' => 'Task already completed.',
            'Veuillez vous connecter pour continuer.' => 'Please log in to continue.',
            'Connexion reussie.' => 'Login successful.',
            'Identifiants invalides.' => 'Invalid credentials.',
            'Compte cree avec succes.' => 'Account created successfully.',
            'Tous les champs sont obligatoires.' => 'All fields are required.',
            'Adresse email invalide.' => 'Invalid email address.',
            'Le mot de passe doit contenir au moins 8 caracteres.' => 'Password must contain at least 8 characters.',
            'Les mots de passe ne correspondent pas.' => 'Passwords do not match.',
            'Cet email est deja utilise.' => 'This email is already used.',
        ];
    }

    return strtr($content, $map);
}
