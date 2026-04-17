# ✈ Fly&Go — Projet corrigé, testé et prêt à lancer

## Ce qui a été corrigé

- correction du problème Doctrine/SQL qui provoquait l’erreur `Unknown column 't0.id'`
- ajout de l’infrastructure Symfony manquante (`bin/console`, `config/bootstrap.php`, `config/bundles.php`)
- passage à une base SQLite locale prête à l’emploi pour éviter les erreurs de configuration MySQL
- ajout d’une commande de réinitialisation complète avec données de démonstration
- conservation du logo et du style Fly&Go inspirés du dossier WeTransfer
- amélioration des scénarios UX : recherche plus claire, tri visible, filtres utiles, réassurance type OTA/Booking

## Prérequis

- PHP 8.1+
- Composer

## Installation rapide

```bash
composer install
php bin/console app:setup-demo
php -S 127.0.0.1:8000 -t public public/index.php
```

Puis ouvrir :

- Site : `http://127.0.0.1:8000`
- Admin : `http://127.0.0.1:8000/admin`

## Comptes de démonstration

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | admin@flyandgo.tn | Admin123! |
| Client | client@flyandgo.tn | Client123! |

## Base de données

Le projet utilise maintenant :

```env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data/flyandgo.sqlite"
```

Pour régénérer la base et recharger les données de démonstration :

```bash
php bin/console app:setup-demo
```

## Vérifications réalisées

- routes publiques testées : accueil, hébergements, circuits, activités, forum
- routes admin testées : dashboard, CRUD, listes, export PDF
- validation Twig OK
- validation container Symfony OK
- validation mapping Doctrine OK

## Structure principale

```text
src/
├── Command/AppSetupCommand.php
├── Controller/
├── Entity/
├── Repository/
└── Kernel.php

config/
├── bootstrap.php
├── bundles.php
└── packages/

templates/
├── base/
├── home/
├── hebergement/
├── circuit/
├── activity/
├── forum/
└── admin/

public/
├── css/flyandgo.css
├── js/flyandgo.js
└── images/logo.png
```

## Notes

- le fichier SQL d’origine a été conservé à titre de référence, mais le projet livré est prêt à fonctionner sans import manuel
- si vous voulez revenir plus tard à MySQL, il faudra adapter `DATABASE_URL` et éventuellement refaire un export/import
