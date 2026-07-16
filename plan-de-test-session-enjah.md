# Plan de test — Sécurité de la gestion de session
### Projet Enjah (e-learning-project)

**Périmètre :** authentification, cookies de session, contrôle d'accès par rôle (étudiant / formateur / admin)
**Approche :** test basé sur les risques — la session conditionne l'accès à 3 niveaux de privilège, donc un défaut ici a un impact démultiplié
**Base :** revue statique du dépôt GitHub (`php.ini`, `.user.ini`, `composer.json`, `index.php`) + hypothèses à confirmer par tests dynamiques
**Date :** 16 juillet 2026

---

## 1. Constats confirmés (revue statique du dépôt)

| # | Constat | Où | Sévérité |
|---|---------|-----|----------|
| C1 | `session.cookie_secure` n'est défini nulle part → le cookie de session peut circuler en clair (HTTP) | `php.ini` et `.user.ini` | **Critique** |
| C2 | `php.ini` et `.user.ini` sont strictement identiques (10 lignes dupliquées) → double maintenance, risque de divergence future, et incertitude sur lequel des deux est réellement pris en compte par le builder de Railway | racine du dépôt | Moyenne |
| C3 | Aucune dépendance de sécurité ni de test dans `composer.json` (uniquement les extensions PDO/MySQLi) : pas de PHPUnit, pas de protection CSRF packagée | `composer.json` | Moyenne |
| C4 | Architecture à fichiers séparés par rôle (étudiant/formateur/admin) → la logique de session est probablement dupliquée 3 fois, donc 3 fois le risque d'oubli lors d'un correctif | README (architecture) | Moyenne |

**Ce qui est déjà bien fait**, pour être honnête : `session.cookie_httponly=1`, `session.cookie_samesite=Lax` et `session.use_strict_mode=1` sont corrects. Ce n'est pas un projet mal sécurisé de zéro — il manque une pièce précise, pas toute la structure.

---

## 2. Cas de test

### TC-SES-01 — Attribut `Secure` du cookie
- **Priorité :** Critique
- **Préconditions :** application accessible en HTTPS
- **Étapes :** se connecter → DevTools > Application > Cookies (ou `curl -v`) → inspecter l'en-tête `Set-Cookie`
- **Résultat attendu :** le flag `Secure` est présent
- **Estimation :** échec probable (voir C1)

### TC-SES-02 — Attribut `HttpOnly`
- **Priorité :** Élevée
- **Étapes :** se connecter → dans la console JS du navigateur, exécuter `document.cookie`
- **Résultat attendu :** le cookie de session n'apparaît pas dans le résultat
- **Estimation :** devrait passer — à confirmer en production, pas seulement en local

### TC-SES-03 — Attribut `SameSite`
- **Priorité :** Moyenne
- **Étapes :** inspecter l'en-tête `Set-Cookie`
- **Résultat attendu :** `SameSite=Lax` présent (envisager `Strict` pour les routes `/admin/`)

### TC-SES-04 — Régénération de l'ID après connexion (anti-fixation de session)
- **Priorité :** Critique
- **Technique :** test de transition d'état (anonyme → authentifié)
- **Étapes :** noter la valeur du cookie avant connexion → se connecter avec des identifiants valides → comparer la valeur du cookie après
- **Résultat attendu :** l'ID de session change après authentification
- **À vérifier dans le code :** présence de `session_regenerate_id(true)` juste après la vérification du mot de passe

### TC-SES-05 — Invalidation complète à la déconnexion
- **Priorité :** Critique
- **Étapes :** se connecter, copier la valeur du cookie → se déconnecter via le bouton logout → réinjecter manuellement l'ancien cookie et tenter d'accéder à une page protégée
- **Résultat attendu :** accès refusé, redirection vers la connexion

### TC-SES-06 — Expiration après inactivité
- **Priorité :** Moyenne
- **Étapes :** rester inactif au-delà de 7200s (`gc_maxlifetime`) → tenter une action
- **Résultat attendu :** session expirée, reconnexion demandée
- **Remarque :** 2h est long pour un espace admin — envisager un timeout applicatif plus court, spécifique à `/admin/`

### TC-SES-07 — Accès à une ressource protégée sans session
- **Priorité :** Critique
- **Étapes :** en navigation privée, accéder directement à une URL de type dashboard (étudiant, formateur ou admin)
- **Résultat attendu :** redirection vers le login ; aucune fuite de contenu ni de message d'erreur révélant la structure serveur

### TC-SES-08 — Cloisonnement vertical entre rôles
- **Priorité :** Critique
- **Étapes :** connecté en tant qu'étudiant, tenter d'accéder directement à une URL du dossier admin ou formateur
- **Résultat attendu :** refus côté serveur — pas seulement un lien caché côté interface

### TC-SES-09 — Contrôle d'accès horizontal (IDOR)
- **Priorité :** Critique
- **Étapes :** connecté en tant qu'étudiant A, modifier un identifiant (`?id=`, `?user=`...) dans l'URL pour viser les données d'un étudiant B (certificat, notes, tâches)
- **Résultat attendu :** refus — le serveur doit croiser l'ID demandé avec `$_SESSION['user_id']`, jamais faire confiance au paramètre reçu tel quel

### TC-SES-10 — Sessions concurrentes / multi-appareils
- **Priorité :** Basse
- **Étapes :** se connecter avec le même compte depuis deux navigateurs différents
- **Résultat attendu :** comportement cohérent et voulu (coexistence ou invalidation de la première session) — un choix conscient, pas un accident

### TC-SES-11 — Protection CSRF
- **Priorité :** Élevée
- **Étapes :** en étant connecté, soumettre une action sensible (changement de mot de passe, tâche) depuis un formulaire HTML hébergé ailleurs que sur Enjah
- **Résultat attendu :** requête rejetée faute de jeton CSRF valide
- **Estimation :** échec probable — rien dans `composer.json` ne suggère de protection CSRF

### TC-SES-12 — Cohérence entre environnements de déploiement
- **Priorité :** Moyenne
- **Étapes :** comparer les en-têtes `Set-Cookie` obtenus sur local, Railway et Vercel pour le même parcours de connexion
- **Résultat attendu :** mêmes flags partout — un écart confirmerait que ni `php.ini` ni `.user.ini` n'est fiable sur l'un des environnements (C1/C2)

### TC-SES-13 — Résilience aux redémarrages du conteneur
- **Priorité :** Moyenne
- **Étapes :** se connecter → déclencher un redéploiement sur Railway → rafraîchir la page
- **Résultat attendu à documenter :** une déconnexion après redéploiement est acceptable tant qu'il n'y a qu'une seule instance (sessions en fichiers locaux). Mais si tu actives un jour plusieurs instances Railway, ce même mécanisme causera des déconnexions **aléatoires en usage normal**, pas seulement au redéploiement — signal qu'il faudra migrer vers des sessions stockées en base de données (via `SessionHandlerInterface`, en réutilisant ton PDO déjà en place)

---

## 3. Correctifs de code recommandés

### 3.1 Initialisation sécurisée (avant tout `session_start()`)
```php
<?php
// Railway termine le HTTPS au niveau du proxy : $_SERVER['HTTPS'] peut
// être vide même si le visiteur est bien en HTTPS. On vérifie donc aussi
// l'en-tête transmis par le proxy.
$isSecure = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
);

session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $isSecure,
    'httponly' => true,
    'samesite' => 'Lax', // 'Strict' conseillé pour les routes /admin/
]);

session_start();
```
Ce correctif fonctionne quel que soit l'hébergeur (Railway, Vercel, mutualisé), sans dépendre de `php.ini` ni `.user.ini`.

### 3.2 Après authentification réussie
```php
session_regenerate_id(true); // anti-fixation
$_SESSION['user_id']       = $user['id'];
$_SESSION['role']          = $user['role']; // 'student' | 'formateur' | 'admin'
$_SESSION['last_activity'] = time();
```

### 3.3 Déconnexion propre
```php
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}
session_destroy();
```

### 3.4 Garde d'accès à centraliser
À placer dans **un seul fichier** inclus par les trois espaces, plutôt que dupliqué dans `/student/`, `/formateur/`, `/admin/` :
```php
if (empty($_SESSION['user_id'])) {
    header('Location: /Projet/student/login.php');
    exit();
}
if ($_SESSION['role'] !== 'admin') { // adapter selon la page protégée
    http_response_code(403);
    exit('Accès refusé.');
}
if (time() - ($_SESSION['last_activity'] ?? 0) > 7200) {
    session_unset();
    session_destroy();
    header('Location: /Projet/student/login.php?expired=1');
    exit();
}
$_SESSION['last_activity'] = time();
```

---

## 4. Checklist complémentaire (hors périmètre session, probable vu l'architecture)

- **Hashage des mots de passe** — confirmer `password_hash()` / `password_verify()` (pas de md5/sha1/texte clair)
- **Injection SQL résiduelle** — le README annonce du PDO préparé partout ; vérifier qu'aucune requête ne concatène du SQL (souvent oublié dans les clauses `ORDER BY` ou `LIKE` dynamiques)
- **Validation des uploads** — `upload_max_filesize` est configuré, donc des fichiers sont acceptés quelque part ; vérifier le contrôle du type MIME réel (pas seulement l'extension) et l'impossibilité d'exécuter un fichier uploadé
- **Affichage des erreurs en production** — `display_errors` n'apparaît dans aucun des deux fichiers ini ; vérifier qu'il est bien désactivé en prod
- **Anti brute-force sur le login** — aucune librairie de limitation de débit présente ; vérifier l'existence d'un verrouillage temporaire après plusieurs échecs
- **CSRF généralisé** — au-delà de TC-SES-11, vérifier tous les formulaires POST sensibles (paiement, tâches, support)

---

*Document conçu pour servir de base de test manuel, ou à donner tel quel à un agent IA (GitHub Copilot, Claude Code...) pour automatiser la correction et la vérification.*
