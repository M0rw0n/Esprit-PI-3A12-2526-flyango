# 🚀 FLY & GO - Travel Management Platform

Application web Symfony 7 - Équivalent exact du projet JavaFX **Fly & Go**

## 📋 Table des matières

- [Fonctionnalités](#fonctionnalités)
- [Prérequis](#prérequis)
- [Installation](#installation)
- [Configuration](#configuration)
- [Base de données](#base-de-données)
- [Démarrage](#démarrage)
- [Architecture](#architecture)
- [Authentification](#authentification)
- [Styles & Design](#styles--design)

## ✨ Fonctionnalités

### Authentification
- ✅ Connexion locale (email/mot de passe)
- ✅ Inscription avec validation
- ✅ Mot de passe oublié avec réinitialisation par email
- 🔄 OAuth2 Google, Apple, Facebook (infrastructure préparée)

### Tableau de bord Admin
- 📊 Statistiques utilisateurs en temps réel
- 👥 Gestion complète des utilisateurs (CRUD)
- ✈️ Gestion des profils voyageurs
- 🔍 Recherche et filtrage avancés
- 📥 Export PDF des utilisateurs

### Profil Utilisateur
- 👤 Édition des informations personnelles
- ✈️ Gestion du profil voyageur (destination, type, budget)
- 🔐 Changement sécurisé du mot de passe
- 📷 Upload de photo de profil et de couverture

### Sécurité
- 🔒 Authentification basée sur formulaire
- 🛡️ Protection CSRF
- 🔐 Hachage sécurisé des mots de passe (Argon2)
- 📧 Email de bienvenue automatique
- ⏱️ Tokens de réinitialisation de mot de passe expirables

## 🛠️ Prérequis

- **PHP 8.2+** (Symfony 7 requires PHP 8.2)
- **MySQL 8.0+** ou **MariaDB 10.5+**
- **Composer** (dernière version)
- **Node.js 18+** (optionnel, pour npm)
- **Git**

## 📥 Installation

### 1. Cloner le projet

```bash
git clone <repository-url>
cd skeleton
```

### 2. Installer les dépendances Composer

```bash
composer install
```

### 3. Créer le fichier .env.local

```bash
cp .env.example .env.local
```

### 4. Configurer les variables d'environnement

Éditez `.env.local` avec vos paramètres :

```env
APP_ENV=dev
APP_SECRET=YourSecretKeyHereChangeMe!
DATABASE_URL="mysql://root:password@127.0.0.1:3306/pidev3a229?serverVersion=8.0&charset=utf8mb4"
MAILER_DSN=smtp://your_email@gmail.com:your_app_password@smtp.gmail.com:465
```

## 🗄️ Base de données

### Créer la base de données

```bash
php bin/console doctrine:database:create
```

### Exécuter les migrations

```bash
php bin/console doctrine:migrations:migrate
```

### (Optionnel) Charger des données de test

```bash
php bin/console doctrine:fixtures:load  # si DataFixtures sont configurées
```

## 🚀 Démarrage

### Mode développement avec Symfony

```bash
symfony server:start
```

L'application sera accessible à : `http://localhost:8000`

### Ou avec PHP natif

```bash
php -S localhost:8000 -t public
```

### Créer un utilisateur admin (command optionnelle)

```bash
php bin/console app:create-admin --email=admin@flyandgo.com --password=Admin123
```

## ⚙️ Configuration

### Mailer (Gmail SMTP)

1. Activer l'authentification 2FA sur Gmail
2. Générer un mot de passe d'app : https://myaccount.google.com/apppasswords
3. Ajouter dans `.env.local` :

```env
MAILER_DSN=smtp://your_email@gmail.com:your_app_password@smtp.gmail.com:465
MAILER_FROM_ADDRESS=your_email@gmail.com
MAILER_FROM_NAME="Fly & Go"
```

### Uploads de fichiers

Les fichiers uploadés sont stockés dans :
- `public/uploads/profile-pictures/` - Photos de profil
- `public/uploads/cover-photos/` - Photos de couverture

Taille maximale : **5MB** (configurable dans `ImageService.php`)

## 🏗️ Architecture

### Dossiers princip aux

```
skeleton/
├── src/
│   ├── Entity/           # Entités Doctrine (User, ProfilVoyageur, PasswordResetToken)
│   ├── Repository/       # Repositories pour les requêtes
│   ├── Form/            # Formulaires Symfony
│   ├── Service/         # Services métier (User, Email, Image)
│   ├── Controller/      # Contrôleurs (Security, Admin, Profil)
│   └── Security/        # Authenticateur personnalisé
├── templates/            # Templates Twig
│   ├── auth/            # Pages d'authentification
│   ├── admin/           # Dashboard admin
│   ├── profil/          # Pages de profil
│   └── emails/          # Templates d'emails
├── public/
│   ├── css/             # Feuilles de style
│   ├── js/              # Scripts JavaScript
│   └── uploads/         # Fichiers uploadés
├── config/              # Configuration Symfony
└── migrations/          # Migrations Doctrine
```

## 🔐 Authentification

### Routes publiques
- `GET /login` - Page de connexion
- `POST /login` - Traitement du formulaire de connexion
- `GET /register` - Page d'inscription
- `POST /register` - Création d'un compte
- `GET /forgot-password` - Demande de réinitialisation
- `POST /forgot-password` - Envoi du lien
- `GET /reset-password/{token}` - Réinitialisation
- `POST /reset-password/{token}` - Sauvegarde du nouveau mot de passe
- `GET /logout` - Déconnexion

### Routes authentifiées utilisateur
- `GET /profil` - Page du profil
- `GET /profil/edit-info` - Édition des infos
- `POST /profil/edit-info` - Sauvegarde des infos
- `GET /profil/edit-profil-voyageur` - Édition du profil voyageur
- `POST /profil/edit-profil-voyageur` - Sauvegarde du profil voyageur
- `GET /profil/change-password` - Changement de mot de passe
- `POST /profil/change-password` - Sauvegarde du mot de passe
- `POST /profil/upload-profile-picture` - Upload photo de profil
- `POST /profil/upload-cover-photo` - Upload photo de couverture
- `POST /profil/remove-profile-picture` - Suppression photo de profil
- `POST /profil/remove-cover-photo` - Suppression photo de couverture

### Routes admin (ROLE_ADMIN required)
- `GET /admin/dashboard` - Tableau de bord
- `GET /admin/users` - Liste des utilisateurs
- `GET /admin/users/create` - Création d'utilisateur
- `POST /admin/users/create` - Sauvegarde d'un nouvel utilisateur
- `GET /admin/users/{id}/edit` - Édition d'utilisateur
- `POST /admin/users/{id}/edit` - Sauvegarde de l'édition
- `POST /admin/users/{id}/delete` - Suppression d'utilisateur
- `POST /admin/users/{id}/toggle-active` - Activation/Désactivation
- `GET /admin/profiles` - Liste des profils voyageurs
- `GET /admin/profiles/{userId}/edit` - Édition du profil voyageur
- `POST /admin/profiles/{userId}/edit` - Sauvegarde du profil voyageur
- `POST /admin/profiles/{userId}/delete` - Suppression du profil voyageur

## 🎨 Styles & Design

### Palette de couleurs

```css
/* Primaire */
--primary: #16a085 (turquoise)
--primary-dark: #117a65
--primary-darker: #0d6b55
--primary-light: #1abc9c

/* Accent */
--orange: #F78D00

/* États */
--success: #27ae60
--danger: #e74c3c
--warning: #e67e22

/* Sidebar */
--sidebar-dark: #0F2E3A
--sidebar-light: #152E3C
```

### Typographie

- Font: Inter (Google Fonts)
- Headings: Bold (700)
- Body: Regular (400)

### Composants

- Boutons dégradés d'effet hover
- Cartes avec ombres progressives
- Badges avec couleurs thématiques
- Forms avec validation CSS
- Filtres dynamiques
- Layout responsive

## 📧 Emails

Templates HTML personnalisés :
- `templates/emails/welcome.html.twig` - Email de bienvenue
- `templates/emails/reset_password.html.twig` - Réinitialisation

## 🧪 Validation

- **Côté serveur** : Contraintes Symfony Assert
- **Côté client** : HTML5 validation + JavaScript custom
- **Regex** : Téléphone, mot de passe, etc.

## 📦 Dépendances principales

```
symfony/framework-bundle ^7.0
symfony/security-bundle ^7.0
symfony/orm-pack ^2.0
symfony/mailer ^7.0
doctrine/orm ^3.0
doctrine/doctrine-bundle ^2.0
symfony/form ^7.0
symfony/validator ^7.0
symfony/twig-bundle ^7.0
```

## 🚢 Déploiement

### Préparation pour production

```bash
# Mettre APP_ENV=prod dans .env
# Générer une clé secrète sécurisée
php bin/console secrets:set APP_SECRET

# Vérifier la configuration
php bin/console about

# Optimiser l'autoloader
composer dump-autoload --optimize

# Chauffe du cache
php bin/console cache:warmup
```

### Avec un serveur Web (Nginx/Apache)

Configurer le DocumentRoot vers `public/` et s'assurer que les droits d'écriture existent pour `var/`.

## 📝 Commandes utiles

```bash
# Vider le cache
php bin/console cache:clear

# Créer une migration
php bin/console make:migration

# Voir les erreurs de constitution
php bin/console debug:config

# Routes disponibles
php bin/console debug:router

# Entités/Mappings
php bin/console doctrine:mapping:info
```

## 🐛 Troubleshooting

### Problème : "No database selected"
Vérifier que `DATABASE_URL` est correct et la base existe.

### Problème : "Email not sent"
Vérifier `MAILER_DSN` et les paramètres Gmail/SMTP.

### Problème : "Uploads ne fonctionnent pas"
S'assurer que les dossiers `public/uploads/*` existent et sont accessibles en écriture.

### Problème : Authentification refusée
Vérifier que `APP_SECRET` est défini et que la table `user` est peuplée.

## 📄 Licence

Tous droits réservés - FLY & GO Platform

## ✉️ Support

Pour une assistance, contacter l'équipe de développement.

---

**Dernière mise à jour** : Avril 2024  
**Version** : 1.0.0  
**Symfony** : 7.x  
**PHP** : 8.2+
