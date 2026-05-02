# 📖 User Story - Authentification & Profil

---

## 👤 PARTIE UTILISATEUR

---

## 1. AUTHENTIFICATION

### User Story 1.1 : Inscription
> En tant que **visiteur**,  
> Je veux **m'inscrire sur le site**,  
> Afin de **créer un compte et accéder aux fonctionnalités**.

**Critères d'acceptation :**
- [ ] Formulaire d'inscription (email, mot de passe, prénom, nom)
- [ ] Validation de l'email par lien
- [ ] Mot de passe sécurisé (8+ caractères, majuscule, chiffre)
- [ ] Conditions générales acceptées
- [ ] Inscription via réseau social (Google, Facebook)
- [ ] Redirection vers page d'accueil après inscription

**Scénario :**
```
1. L'utilisateur clique sur "S'inscrire"
2. Il remplit le formulaire (email, mot de passe, prénom, nom)
3. Il accepte les CGU
4. Il reçoit un email de confirmation
5. Il clique sur le lien d'activation
6. Son compte est activé
7. Il est redirigé vers son tableau de bord
```

---

### User Story 1.2 : Connexion
> En tant que **utilisateur enregistré**,  
> Je veux **me connecter**,  
> Afin d'**accéder à mon compte et mes fonctionnalités**.

**Critères d'acceptation :**
- [ ] Formulaire avec email et mot de passe
- [ ] Option "Se souvenir de moi"
- [ ] Lien "Mot de passe oublié"
- [ ] Connexion via Google
- [ ] Connexion via Facebook
- [ ] Message d'erreur si identifiants incorrects
- [ ] Redirection vers page précédente après connexion

**Scénario :**
```
1. L'utilisateur clique sur "Se connecter"
2. Il saisit son email et mot de passe
3. Il clique sur "Connexion"
4. SiOK → Redirection vers tableau de bord
5. SiKo → Message d'erreur affiché
```

---

### User Story 1.3 : Mot de passe oublié
> En tant qu'**utilisateur ayant oublié son mot de passe**,  
> Je veux **réinitialiser mon mot de passe**,  
> Afin de **reprendre accès à mon compte**.

**Critères d'acceptation :**
- [ ] Saisie de l'email
- [ ] Envoi d'un lien de réinitialisation par email
- [ ] Lien valide pendant 1 heure
- [ ] Formulaire de nouveau mot de passe
- [ ] Confirmation du nouveau mot de passe
- [ ] Notification de succès

**Scénario :**
```
1. L'utilisateur clique sur "Mot de passe oublié"
2. Il saisit son email
3. Il reçoit un email avec lien de réinitialisation
4. Il clique sur le lien
5. Il saisit son nouveau mot de passe
6. Il est redirigé vers la page de connexion
```

---

### User Story 1.4 : Déconnexion
> En tant qu'**utilisateur connecté**,  
> Je veux **me déconnecter**,  
> Afin de **sécuriser mon compte**.

**Critères d'acceptation :**
- [ ] Bouton de déconnexion visible
- [ ] Session supprimée
- [ ] Redirection vers page d'accueil
- [ ] Cookies de session effacés

---

### User Story 1.5 : Gestion du compte
> En tant qu'**utilisateur**,  
> Je veux **gérer mon compte**,  
> Afin de **modifier mes informations**.

**Critères d'acceptation :**
- [ ] Modifier l'email
- [ ] Modifier le mot de passe
- [ ] Modifier le prénom et nom
- [ ] Changer ma photo de profil
- [ ] Supprimer mon compte
- [ ] Exporter mes données

---

## 2. MON PROFIL

### User Story 2.1 : Voir mon profil
> En tant qu'**utilisateur connecté**,  
> Je veux **voir mon profil**,  
> Afin de **consulter mes informations**.

**Critères d'acceptation :**
- [ ] Affichage avatar et nom
- [ ] Affichage email
- [ ] Date d'inscription
- [ ] Statut du compte (vérifié/non vérifié)
- [ ] Nombre de réservations effectuées
- [ ] Niveau de fidélité

---

### User Story 2.2 : Modifier mon profil
> En tant qu'**utilisateur connecté**,  
> Je veux **modifier mon profil**,  
> Afin de **mettre à jour mes informations**.

**Critères d'acceptation :**
- [ ] Modification du prénom
- [ ] Modification du nom
- [ ] Modification de l'email
- [ ] Modification du téléphone
- [ ] Modification de la date de naissance
- [ ] Modification de l'adresse
- [ ] Upload de photo de profil
- [ ] Suppression de la photo de profil
- [ ] Sauvegarde avec validation

---

### User Story 2.3 : Préférences de notification
> En tant qu'**utilisateur**,  
> Je veux **gérer mes notifications**,  
> Afin de **choisir ce que je reçois**.

**Critères d'acceptation :**
- [ ] Notifications email (on/off)
- [ ] Notifications SMS (on/off)
- [ ] Notifications push (on/off)
- [ ] Newsletter (on/off)
- [ ] Alertes prix (on/off)
- [ ] Nouveautés et offres (on/off)

---

### User Story 2.4 : Sécurité du compte
> En tant qu'**utilisateur**,  
> Je veux **sécuriser mon compte**,  
> Afin de **protéger mes données**.

**Critères d'acceptation :**
- [ ] Changer le mot de passe
- [ ] Activer l'authentification à deux facteurs (2FA)
- [ ] Voir les sessions actives
- [ ] Déconnecter les autres appareils
- [ ] Historique des connexions
- [ ] Liste des appareils autorisés

---

### User Story 2.5 : Mes favoris
> En tant qu'**utilisateur**,  
> Je veux **voir mes favoris**,  
> Afin de **retrouver mes éléments aimés**.

**Critères d'acceptation :**
- [ ] Liste des hébergements favoris
- [ ] Liste des circuits favoris
- [ ] Liste des activités favorites
- [ ] Supprimer un favori
- [ ] Partager ma liste de favoris
- [ ] Ajouter depuis la page détail

---

### User Story 2.6 : Mon historique
> En tant qu'**utilisateur**,  
> Je veux **voir mon historique**,  
> Afin de **retrouver mes recherches passées**.

**Critères d'acceptation :**
- [ ] Historique des recherches
- [ ] Historique des vues
- [ ] Dernières destinations vues
- [ ] Suggestions basées sur l'historique
- [ ] Effacer l'historique
- [ ] Désactiver l'historique

---

## 3. PROFIL VOYAGEUR

### User Story 3.1 : Créer mon profil voyageur
> En tant qu'**utilisateur**,  
> Je veux **créer un profil voyageur**,  
> Afin de **personnaliser mes recommandations**.

**Critères d'acceptation :**
- [ ] Type de voyageur (solo, couple, famille, amis)
- [ ] Budget préféré (économique, moyen, luxe)
- [ ] Style de voyage (aventure, détente, culturel)
- [ ] Types d'hébergement préférés
- [ ] Intérêts (plage, montagne, désert, ville)
- [ ] Durée de séjour habituelle
- [ ] Mode de transport préféré

---

### User Story 3.2 : Modifier mon profil voyageur
> En tant qu'**utilisateur**,  
> Je veux **modifier mon profil voyageur**,  
> Afin de **mettre à jour mes préférences**.

**Critères d'acceptation :**
- [ ] Modification des préférences
- [ ] Ajout d'intérêts
- [ ] Suppression d'intérêts
- [ ] Mise à jour du budget
- [ ] Changement du type de voyageur

---

### User Story 3.3 : Voyageur expert
> En tant qu'**utilisateur**,  
> Je veux **compléter mon profil voyageur**,  
> Afin de **devenir un voyageur expert**.

**Critères d'acceptation :**
- [ ] Badges à débloquer
- [ ] Quiz de connaissances Tunisie
- [ ] Niveau de voyageur (Débutant → Expert)
- [ ] Avantages par niveau
- [ ] Classement des voyageurs

---

### User Story 3.4 : Préférences alimentaires
> En tant qu'**utilisateur**,  
> Je veux **spécifier mes préférences alimentaires**,  
> Afin de **recevoir des recommandations adaptées**.

**Critères d'acceptation :**
- [ ] Régime alimentaire (végan, végétarien, halal, casher)
- [ ] Allergies alimentaires
- [ ] Restrictions médicales
- [ ] Préférences culinaires tunisiennes

---

### User Story 3.5 : Documents de voyage
> En tant qu'**utilisateur**,  
> Je veux **stocker mes documents**,  
> Afin de **les avoir sous la main**.

**Critères d'acceptation :**
- [ ] Upload passeport
- [ ] Upload visa
- [ ] Upload assurances
- [ ] Upload billets
- [ ] Rappels d'expiration
- [ ] Partage sécurisé

---

### User Story 3.6 : Liste de souhaits
> En tant qu'**utilisateur**,  
> Je veux **créer des listes de souhaits**,  
> Afin d'**organiser mes futurs voyages**.

**Critères d'acceptation :**
- [ ] Créer plusieurs listes (ex: "Tunisie 2025", "À faire un jour")
- [ ] Ajouter des éléments aux listes
- [ ] Partager une liste
- [ ] Collaborer sur une liste
- [ ] Déplacer des éléments entre listes

---

## 👨‍💻 PARTIE ADMINISTRATEUR

---

## 4. GESTION DES UTILISATEURS (ADMIN)

### Admin Story 4.1 : Liste des utilisateurs
> En tant qu'**administrateur**,  
> Je veux **voir la liste des utilisateurs**,  
> Afin de **gérer les comptes**.

**Critères d'acceptation :**
- [ ] Liste paginée
- [ ] Recherche par email, nom
- [ ] Filtrer par statut (actif, inactif, banni)
- [ ] Filtrer par date d'inscription
- [ ] Export CSV/Excel

---

### Admin Story 4.2 : Détail utilisateur
> En tant qu'**administrateur**,  
> Je veux **voir le détail d'un utilisateur**,  
> Afin de **diagnostiquer les problèmes**.

**Critères d'acceptation :**
- [ ] Informations du profil
- [ ] Historique des réservations
- [ ] Activité sur le forum
- [ ] Messages échangés
- [ ] Historique des connexions
- [ ] Modifications récentes

---

### Admin Story 4.3 : Modifier un utilisateur
> En tant qu'**administrateur**,  
> Je veux **modifier un utilisateur**,  
> Afin de **corriger les problèmes**.

**Critères d'acceptation :**
- [ ] Modifier les informations
- [ ] Changer le rôle (utilisateur, modérateur, admin)
- [ ] Valider manuellement l'email
- [ ] Réinitialiser le mot de passe

---

### Admin Story 4.4 : Bannir un utilisateur
> En tant qu'**administrateur**,  
> Je veux **bannir un utilisateur**,  
> Afin de **maintenir la sécurité**.

**Critères d'acceptation :**
- [ ] Suspension temporaire
- [ ] Bannissement permanent
- [ ] Raison du bannissement
- [ ] Notification par email
- [ ] Possibilité de débannissement

---

### Admin Story 4.5 : Rôles et permissions
> En tant qu'**administrateur**,  
> Je veux **gérer les rôles**,  
> Afin de **déléguer les responsabilités**.

**Critères d'acceptation :**
- [ ] Liste des rôles
- [ ] Créer un rôle
- [ ] Modifier les permissions
- [ ] Assigner un rôle à un utilisateur
- [ ] Rôles par défaut (user, moderator, admin)

---

## 5. STATISTIQUES UTILISATEURS (ADMIN)

### Admin Story 5.1 : Inscriptions
> En tant qu'**administrateur**,  
> Je veux **voir les statistiques d'inscription**,  
> Afin de **suivre la croissance**.

**Critères d'acceptation :**
- [ ] Nombre d'inscriptions aujourd'hui
- [ ] Nombre d'inscriptions ce mois
- [ ] Graphique d'évolution
- [ ] Inscriptions par source (direct, Google, Facebook)

---

### Admin Story 5.2 : Utilisateurs actifs
> En tant qu'**administrateur**,  
> Je veux **voir les utilisateurs actifs**,  
> Afin de **mesurer l'engagement**.

**Critères d'acceptation :**
- [ ] Utilisateurs actifs ce mois
- [ ] Utilisateurs actifs quotidiens (DAU)
- [ ] Temps moyen de session
- [ ] Pages les plus visitées

---

### Admin Story 5.3 : Profil voyageur stats
> En tant qu'**administrateur**,  
> Je veux **voir les statistiques des profils voyageurs**,  
> Afin de **comprendre les utilisateurs**.

**Critères d'acceptation :**
- [ ] Répartition par type de voyageur
- [ ] Budget moyen préférences
- [ ] Destinations les plus demandées
- [ ] Intérêts les plus populaires

---

## 📊 MATRICE DES FONCTIONNALITÉS

| Fonctionnalité | User | Admin | Priorité |
|----------------|------|-------|----------|
| **Authentification** | | | |
| Inscription | ✅ | - | P0 |
| Connexion | ✅ | - | P0 |
| Mot de passe oublié | ✅ | - | P0 |
| Déconnexion | ✅ | - | P0 |
| 2FA | ✅ | - | P1 |
| **Mon Profil** | | | |
| Voir profil | ✅ | ✅ | P0 |
| Modifier profil | ✅ | - | P0 |
| Photo de profil | ✅ | - | P0 |
| Préférences notifications | ✅ | - | P1 |
| Sécurité compte | ✅ | - | P1 |
| Historique | ✅ | - | P1 |
| Favoris | ✅ | - | P1 |
| **Profil Voyageur** | | | |
| Créer profil voyageur | ✅ | - | P0 |
| Modifier profil voyageur | ✅ | - | P0 |
| Préférences alimentaires | ✅ | - | P1 |
| Documents de voyage | ✅ | - | P1 |
| Liste de souhaits | ✅ | - | P1 |
| Voyageur expert | ✅ | - | P2 |
| **Admin** | | | |
| Liste utilisateurs | - | ✅ | P0 |
| Détail utilisateur | - | ✅ | P0 |
| Modifier utilisateur | - | ✅ | P0 |
| Bannir utilisateur | - | ✅ | P0 |
| Gestion rôles | - | ✅ | P1 |
| Statistiques | - | ✅ | P0 |

---

## 🔄 FLUX UTILISATEUR - AUTHENTIFICATION

```
┌─────────────────────────────────────────────────────────────┐
│                    PARCOURS UTILISATEUR                     │
└─────────────────────────────────────────────────────────────┘

  [VISITEUR]                                                   │
      │                                                        │
      ▼                                                        │
┌─────────────┐                                                │
│ Page d'accueil │ ──► [S'inscrire]                           │
└─────────────┘                                                │
      │                                                        │
      ▼                                                        │
┌─────────────────┐                                            │
│  Formulaire     │                                            │
│  Inscription    │                                            │
└─────────────────┘                                            │
      │                                                        │
      ▼                                                        │
┌─────────────────┐                                            │
│  Email          │ ──► [Lien activation]                       │
│  Confirmation   │                                            │
└─────────────────┘                                            │
      │                                                        │
      ▼                                                        │
┌─────────────────┐                                            │
│  Compte activé  │ ──► [Tableau de bord]                     │
└─────────────────┘                                            │
      │                                                        │
      ▼                                                        │
┌────────────────────────────────────────────────────────────┐
│                    UTILISATEUR CONNECTÉ                      │
└────────────────────────────────────────────────────────────┘
      │
      ├──► [Mon Profil] ──► Modifier informations
      │
      ├──► [Profil Voyageur] ──► Définir préférences
      │
      ├──► [Mes Réservations] ──► Voir historique
      │
      ├──► [Mes Favoris] ──► Gérer favoris
      │
      └──► [Sécurité] ──► Changer mot de passe / 2FA

```

---

## 📝 FORMULAIRES

### Formulaire d'inscription
| Champ | Type | Obligatoire | Validation |
|-------|------|-------------|-------------|
| Prénom | Text | Oui | 2-50 caractères |
| Nom | Text | Oui | 2-50 caractères |
| Email | Email | Oui | Format email valide |
| Mot de passe | Password | Oui | 8+ caractères, 1 majuscule, 1 chiffre |
| Confirmer mot de passe | Password | Oui | Identique au mot de passe |
| CGU | Checkbox | Oui | Doit être coché |

### Formulaire de connexion
| Champ | Type | Obligatoire |
|-------|------|-------------|
| Email | Email | Oui |
| Mot de passe | Password | Oui |
| Se souvenir de moi | Checkbox | Non |

### Profil Voyageur
| Champ | Type | Options |
|-------|------|---------|
| Type de voyageur | Select | Solo, Couple, Famille, Amis, Groupe |
| Budget | Select | Économique (< 500€), Moyen (500-1000€), Luxe (> 1000€) |
| Style de voyage | Multi-select | Aventure, Détente, Culturel, Gastronomie, Sport |
| Durée séjour | Select | Weekend, 1 semaine, 2 semaines, 1 mois+ |
| Transport | Multi-select | Avion, Bus, Location voiture, Taxi |

---

## 📋 USER STORIES DÉTAILLÉES

### US 1 : Inscription avec Google
> En tant que **visiteur**,  
> Je veux **m'inscrire avec Google**,  
> Afin de **créer un compte rapidement**.

**Critères :**
- [ ] Bouton "S'inscrire avec Google"
- [ ] Demande de permission Google
- [ ] Import automatique nom et email
- [ ] Optionnel: compléter le profil après

### US 2 : Vérification en deux étapes
> En tant qu'**utilisateur**,  
> Je veux **activer la 2FA**,  
> Afin de **sécuriser mon compte**.

**Critères :**
- [ ] Activation dans les paramètres de sécurité
- [ ] Scan QR code avec app (Google Authenticator)
- [ ] Code de backup à sauvegarder
- [ ] Connexion avec code à 6 chiffres

### US 3 : Programme de fidélité
> En tant qu'**utilisateur**,  
> Je veux **accumuler des points**,  
> Afin de **bénéficier de réductions**.

**Critères :**
- [ ] Points par réservation
- [ ] Niveau de fidélité (Bronze, Argent, Or, Platine)
- [ ] Avantages par niveau
- [ ] Échange de points contre coupons

---

*Document généré pour Fly&Go - 2026*
