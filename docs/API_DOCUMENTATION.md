# 📋 Documentation Complète des APIs - Fly&Go

## Table des Matières
1. [Traduction & Localisation](#1-traduction--localisation)
2. [Génération IA](#2-génération-ia)
3. [Recommandations](#3-recommandations)
4. [Carte & Localisation](#4-carte--localisation)
5. [Météo](#5-météo)
6. [Autocomplete & Recherche](#6-autocomplete--recherche)
7. [Statistiques & Analytics](#7-statistiques--analytics)
8. [Authentification & Utilisateurs](#8-authentification--utilisateurs)
9. [Gestion Contenu](#9-gestion-contenu)
10. [Forum](#10-forum)
11. [Messenger](#11-messenger)
12. [Réservations](#12-réservations)

---

## 1. TRADUCTION & LOCALISATION

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 1.1 | GET | `/api/translate` | Traduire un texte dans une langue | Public |
| 1.2 | GET | `/api/translate/detect` | Détecter la langue du texte | Public |
| 1.3 | GET | `/api/translate/languages` | Liste des langues disponibles | Public |
| 1.4 | POST | `/api/translate/batch` | Traduction batch de textes | Admin |
| 1.5 | GET | `/api/locale/current` | Langue actuelle de l'utilisateur | User |
| 1.6 | POST | `/api/locale/change` | Changer la langue | User |

---

## 2. GÉNÉRATION IA

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 2.1 | POST | `/api/ai/generate/circuit` | Générer un circuit personnalisé | User |
| 2.2 | POST | `/api/ai/generate/reponse-faq` | Générer une réponse FAQ | Admin |
| 2.3 | POST | `/api/ai/generate/description` | Générer description hébergement/activité | Admin |
| 2.4 | POST | `/api/ai/generate/itineraire` | Créer un itinéraire détaillé | User |
| 2.5 | POST | `/api/ai/summarize` | Résumer un texte long | User |
| 2.6 | POST | `/api/ai/improve-text` | Améliorer un texte | User |
| 2.7 | GET | `/api/ai/chatbot` | Envoyer message au chatbot | Public |
| 2.8 | POST | `/api/ai/embeddings` | Générer embeddings pour FAQ | Admin |
| 2.9 | POST | `/api/ai/image-description` | Décrire une image | User |
| 2.10 | GET | `/api/ai/status` | Vérifier status API IA | Admin |

---

## 3. RECOMMANDATIONS

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 3.1 | GET | `/api/recommendations/hebergements` | Recommandations hébergements | User |
| 3.2 | GET | `/api/recommendations/circuits` | Recommandations circuits | User |
| 3.3 | GET | `/api/recommendations/activites` | Recommandations activités | User |
| 3.4 | GET | `/api/recommendations/transports` | Recommandations transports | User |
| 3.5 | GET | `/api/recommendations/similaire/{type}/{id}` | Items similaires | Public |
| 3.6 | GET | `/api/recommendations/perso` | Recommandations personnalisées | User |
| 3.7 | POST | `/api/recommendations/feedback` | Feedback sur recommandation | User |
| 3.8 | GET | `/api/recommendations/destinations` | Destinations populaires | Public |

---

## 4. CARTE & LOCALISATION

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 4.1 | GET | `/api/map/markers/{type}` | Marqueurs sur la carte | Public |
| 4.2 | GET | `/api/map/zone/{id}` | Détails d'une zone | Public |
| 4.3 | POST | `/api/map/geocode` | Convertir adresse en coordonnées | Public |
| 4.4 | POST | `/api/map/reverse-geocode` | Convertir coordonnées en adresse | Public |
| 4.5 | GET | `/api/map/distance` | Calculer distance entre 2 points | Public |
| 4.6 | GET | `/api/map/itineraire` | Calculer itinéraire | Public |
| 4.7 | GET | `/api/regions` | Liste des régions | Public |
| 4.8 | GET | `/api/regions/{id}` | Détails d'une région | Public |
| 4.9 | GET | `/api/regions/{id}/hebergements` | Hébergements par région | Public |
| 4.10 | GET | `/api/regions/{id}/circuits` | Circuits par région | Public |

---

## 5. MÉTÉO

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 5.1 | GET | `/api/meteo/actuelle` | Météo actuelle d'une ville | Public |
| 5.2 | GET | `/api/meteo/forecast` | Prévisions 7 jours | Public |
| 5.3 | GET | `/api/meteo/{ville}` | Météo par ville | Public |
| 5.4 | GET | `/api/meteo/best-period` | Meilleure période pour voyage | Public |
| 5.5 | GET | `/api/meteo/alertes` | Alertes météo | Public |

---

## 6. AUTOCOMPLETE & RECHERCHE

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 6.1 | GET | `/api/search/autocomplete` | Autocomplete global | Public |
| 6.2 | GET | `/api/search/hebergements` | Recherche hébergements | Public |
| 6.3 | GET | `/api/search/circuits` | Recherche circuits | Public |
| 6.4 | GET | `/api/search/activites` | Recherche activités | Public |
| 6.5 | GET | `/api/search/transports` | Recherche transports | Public |
| 6.6 | GET | `/api/search/forum` | Recherche dans le forum | Public |
| 6.7 | GET | `/api/search/all` | Recherche globale | Public |
| 6.8 | GET | `/api/search/trending` | Recherches tendances | Public |
| 6.9 | GET | `/api/autocomplete/ville` | Autocomplete villes Tunisie | Public |
| 6.10 | GET | `/api/autocomplete/keyword` | Autocomplete mots-clés | Public |

---

## 7. STATISTIQUES & ANALYTICS

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 7.1 | GET | `/api/admin/stats/dashboard` | Dashboard principal | Admin |
| 7.2 | GET | `/api/admin/stats/ventes` | Statistiques ventes | Admin |
| 7.3 | GET | `/api/admin/stats/reservations` | Statistiques réservations | Admin |
| 7.4 | GET | `/api/admin/stats/users` | Statistiques utilisateurs | Admin |
| 7.5 | GET | `/api/admin/stats/geo` | Répartition géographique | Admin |
| 7.6 | GET | `/api/admin/stats/revenus` | Revenus par période | Admin |
| 7.7 | GET | `/api/admin/stats/chatbot` | Utilisation chatbot | Admin |
| 7.8 | GET | `/api/admin/stats/puzzle` | Statistiques AI Puzzle | Admin |
| 7.9 | GET | `/api/admin/stats/forum` | Statistiques forum | Admin |
| 7.10 | GET | `/api/admin/stats/messenger` | Statistiques messenger | Admin |
| 7.11 | GET | `/api/admin/stats/performance` | Performance API | Admin |
| 7.12 | GET | `/api/stats/analytics` | Analytics frontend | Public |

---

## 8. AUTHENTIFICATION & UTILISATEURS

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 8.1 | POST | `/api/auth/register` | Inscription | Public |
| 8.2 | POST | `/api/auth/login` | Connexion | Public |
| 8.3 | POST | `/api/auth/logout` | Déconnexion | User |
| 8.4 | POST | `/api/auth/refresh` | Rafraîchir token | Public |
| 8.5 | POST | `/api/auth/forgot-password` | Mot de passe oublié | Public |
| 8.6 | POST | `/api/auth/reset-password` | Réinitialiser mot de passe | Public |
| 8.7 | GET | `/api/user/profile` | Profil utilisateur | User |
| 8.8 | PUT | `/api/user/profile` | Modifier profil | User |
| 8.9 | POST | `/api/user/avatar` | Changer avatar | User |
| 8.10 | GET | `/api/user/preferences` | Préférences utilisateur | User |
| 8.11 | PUT | `/api/user/preferences` | Modifier préférences | User |
| 8.12 | DELETE | `/api/user/account` | Supprimer compte | User |

---

## 9. GESTION CONTENU

### 9.1 Hébergements
| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 9.1.1 | GET | `/api/hebergements` | Liste hébergements | Public |
| 9.1.2 | GET | `/api/hebergements/{id}` | Détail hébergement | Public |
| 9.1.3 | POST | `/api/admin/hebergements` | Créer hébergement | Admin |
| 9.1.4 | PUT | `/api/admin/hebergements/{id}` | Modifier hébergement | Admin |
| 9.1.5 | DELETE | `/api/admin/hebergements/{id}` | Supprimer hébergement | Admin |
| 9.1.6 | POST | `/api/admin/hebergements/{id}/images` | Upload images | Admin |

### 9.2 Circuits
| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 9.2.1 | GET | `/api/circuits` | Liste circuits | Public |
| 9.2.2 | GET | `/api/circuits/{id}` | Détail circuit | Public |
| 9.2.3 | POST | `/api/admin/circuits` | Créer circuit | Admin |
| 9.2.4 | PUT | `/api/admin/circuits/{id}` | Modifier circuit | Admin |
| 9.2.5 | DELETE | `/api/admin/circuits/{id}` | Supprimer circuit | Admin |
| 9.2.6 | POST | `/api/admin/circuits/{id}/etapes` | Ajouter étape | Admin |

### 9.3 Activités
| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 9.3.1 | GET | `/api/activites` | Liste activités | Public |
| 9.3.2 | GET | `/api/activites/{id}` | Détail activité | Public |
| 9.3.3 | POST | `/api/admin/activites` | Créer activité | Admin |
| 9.3.4 | PUT | `/api/admin/activites/{id}` | Modifier activité | Admin |
| 9.3.5 | DELETE | `/api/admin/activites/{id}` | Supprimer activité | Admin |

### 9.4 Transports
| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 9.4.1 | GET | `/api/transports` | Liste transports | Public |
| 9.4.2 | GET | `/api/transports/{id}` | Détail transport | Public |
| 9.4.3 | POST | `/api/admin/transports` | Créer transport | Admin |
| 9.4.4 | PUT | `/api/admin/transports/{id}` | Modifier transport | Admin |
| 9.4.5 | DELETE | `/api/admin/transports/{id}` | Supprimer transport | Admin |

### 9.5 FAQ Chatbot
| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 9.5.1 | GET | `/api/chatbot/faqs` | Liste FAQs | Public |
| 9.5.2 | GET | `/api/chatbot/faqs/{id}` | Détail FAQ | Public |
| 9.5.3 | POST | `/api/admin/chatbot/faqs` | Créer FAQ | Admin |
| 9.5.4 | PUT | `/api/admin/chatbot/faqs/{id}` | Modifier FAQ | Admin |
| 9.5.5 | DELETE | `/api/admin/chatbot/faqs/{id}` | Supprimer FAQ | Admin |
| 9.5.6 | POST | `/api/admin/chatbot/faqs/sync` | Synchroniser embeddings | Admin |

---

## 10. FORUM

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 10.1 | GET | `/api/forum/categories` | Liste catégories | Public |
| 10.2 | POST | `/api/admin/forum/categories` | Créer catégorie | Admin |
| 10.3 | GET | `/api/forum/sujets` | Liste sujets | Public |
| 10.4 | GET | `/api/forum/sujets/{id}` | Détail sujet | Public |
| 10.5 | POST | `/api/forum/sujets` | Créer sujet | User |
| 10.6 | PUT | `/api/forum/sujets/{id}` | Modifier sujet | User |
| 10.7 | DELETE | `/api/forum/sujets/{id}` | Supprimer sujet | User |
| 10.8 | GET | `/api/forum/sujets/{id}/reponses` | Liste réponses | Public |
| 10.9 | POST | `/api/forum/sujets/{id}/reponses` | Ajouter réponse | User |
| 10.10 | PUT | `/api/forum/reponses/{id}` | Modifier réponse | User |
| 10.11 | DELETE | `/api/forum/reponses/{id}` | Supprimer réponse | User |
| 10.12 | POST | `/api/forum/sujets/{id}/solution` | Marquer solution | User |
| 10.13 | POST | `/api/forum/sujets/{id}/report` | Signaler sujet | User |
| 10.14 | POST | `/api/admin/forum/reports` | Liste signalements | Admin |
| 10.15 | POST | `/api/admin/forum/users/ban` | Bannir utilisateur | Admin |

---

## 11. MESSENGER

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 11.1 | GET | `/api/messages/conversations` | Liste conversations | User |
| 11.2 | POST | `/api/messages/conversations` | Créer conversation | User |
| 11.3 | GET | `/api/messages/conversations/{id}` | Détail conversation | User |
| 11.4 | DELETE | `/api/messages/conversations/{id}` | Supprimer conversation | User |
| 11.5 | GET | `/api/messages/{conversationId}` | Liste messages | User |
| 11.6 | POST | `/api/messages` | Envoyer message | User |
| 11.7 | PUT | `/api/messages/{id}` | Modifier message | User |
| 11.8 | DELETE | `/api/messages/{id}` | Supprimer message | User |
| 11.9 | POST | `/api/messages/{id}/react` | Ajouter réaction | User |
| 11.10 | DELETE | `/api/messages/{id}/react` | Retirer réaction | User |
| 11.11 | POST | `/api/messages/{id}/reply` | Répondre à un message | User |
| 11.12 | GET | `/api/friends` | Liste amis | User |
| 11.13 | POST | `/api/friends/add` | Ajouter ami | User |
| 11.14 | DELETE | `/api/friends/{id}` | Supprimer ami | User |
| 11.15 | POST | `/api/friends/{id}/block` | Bloquer utilisateur | User |
| 11.16 | DELETE | `/api/friends/{id}/block` | Débloquer utilisateur | User |
| 11.17 | POST | `/api/messages/call/audio` | Appel audio | User |
| 11.18 | POST | `/api/messages/call/video` | Appel vidéo | User |
| 11.19 | GET | `/api/messages/call/history` | Historique appels | User |
| 11.20 | POST | `/api/messages/theme` | Changer thème conversation | User |

---

## 12. RÉSERVATIONS

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| 12.1 | POST | `/api/reservations/hebergement` | Réserver hébergement | User |
| 12.2 | POST | `/api/reservations/circuit` | Réserver circuit | User |
| 12.3 | POST | `/api/reservations/activite` | Réserver activité | User |
| 12.4 | POST | `/api/reservations/transport` | Réserver transport | User |
| 12.5 | GET | `/api/reservations` | Mes réservations | User |
| 12.6 | GET | `/api/reservations/{id}` | Détail réservation | User |
| 12.7 | PUT | `/api/reservations/{id}` | Modifier réservation | User |
| 12.8 | DELETE | `/api/reservations/{id}` | Annuler réservation | User |
| 12.9 | POST | `/api/reservations/{id}/paiement` | Effectuer paiement | User |
| 12.10 | GET | `/api/reservations/{id}/facture` | Télécharger facture | User |
| 12.11 | GET | `/api/admin/reservations` | Toutes les réservations | Admin |
| 12.12 | PUT | `/api/admin/reservations/{id}` | Modifier réservation | Admin |
| 12.13 | POST | `/api/admin/reservations/{id}/confirm` | Confirmer réservation | Admin |
| 12.14 | POST | `/api/admin/reservations/{id}/cancel` | Annuler réservation | Admin |

---

## AI PUZZLE

| # | Méthode | Endpoint | Description | Auth |
|---|---------|----------|-------------|------|
| AP1 | GET | `/api/ai-puzzle/quiz` | Liste des quiz | Public |
| AP2 | GET | `/api/ai-puzzle/quiz/{id}` | Questions quiz | Public |
| AP3 | POST | `/api/ai-puzzle/quiz/{id}/submit` | Soumettre quiz | User |
| AP4 | GET | `/api/ai-puzzle/scores` | Mon score | User |
| AP5 | GET | `/api/ai-puzzle/leaderboard` | Classement | Public |
| AP6 | GET | `/api/ai-puzzle/badges` | Liste badges | Public |
| AP7 | GET | `/api/ai-puzzle/badges/mon` | Mes badges | User |
| AP8 | POST | `/api/ai-puzzle/defis/{id}/accept` | Accepter défi | User |
| AP9 | POST | `/api/ai-puzzle/defis/{id}/complete` | Terminer défi | User |
| AP10 | GET | `/api/ai-puzzle/rewards` | Récompenses | User |
| AP11 | POST | `/api/ai-puzzle/rewards/{id}/claim` | Claim récompense | User |

---

## 📊 RÉSUMÉ STATISTIQUE

| Catégorie | Nombre d'APIs |
|-----------|---------------|
| Traduction & Localisation | 6 |
| Génération IA | 10 |
| Recommandations | 8 |
| Carte & Localisation | 10 |
| Météo | 5 |
| Autocomplete & Recherche | 10 |
| Statistiques & Analytics | 12 |
| Authentification & Utilisateurs | 12 |
| Gestion Contenu | 24 |
| Forum | 15 |
| Messenger | 20 |
| Réservations | 14 |
| AI Puzzle | 11 |
| **TOTAL** | **147 APIs** |

---

## 🔗 Liens Rapides

| Module | Documentation |
|--------|---------------|
| Hébergements | `/api/doc/hebergements` |
| Circuits | `/api/doc/circuits` |
| Forum | `/api/doc/forum` |
| Messenger | `/api/doc/messenger` |
| Chatbot | `/api/doc/chatbot` |
| AI Puzzle | `/api/doc/puzzle` |

---

*Document généré pour Fly&Go - Dernière mise à jour: 2026*
