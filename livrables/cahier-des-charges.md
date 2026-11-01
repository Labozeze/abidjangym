# Cahier des Charges — AbidjanGym

**Projet :** AbidjanGym — Plateforme SaaS de gestion de salle de gym  
**Client :** Rufin Ismael Baroan — Le Labo de Zézé  
**Domaine :** https://abidjangym.com/  
**Date :** Octobre 2026  
**Version :** 2.3  
**Statut :** Document de référence — toutes les entités doivent s'y conformer

---

## Sommaire

1. [Présentation du Projet](#1-présentation-du-projet)
2. [Organisation du Projet Multi-Entités](#2-organisation-du-projet-multi-entités)
3. [Hébergement et Contraintes Techniques](#3-hébergement-et-contraintes-techniques)
4. [Périmètre Fonctionnel](#4-périmètre-fonctionnel)
5. [Exigences SEO](#5-exigences-seo)
6. [Exigences Sécurité](#6-exigences-sécurité)
7. [Architecture Technique par Entité](#7-architecture-technique-par-entité)
8. [Exigences Non Fonctionnelles](#8-exigences-non-fonctionnelles)
9. [Agent Rédaction Article Blog](#9-agent-rédaction-article-blog)
10. [Flux Principaux](#10-flux-principaux)
11. [Glossaire](#11-glossaire)

---

## 1. Présentation du Projet

### 1.1 Vision
AbidjanGym est une plateforme SaaS multi-tenant destinée aux gérants de salles de sport en Côte d'Ivoire et en Afrique francophone. Elle permet à chaque salle de gérer ses abonnés, ses abonnements, ses encaissements et ses communications depuis une interface web simple, sans formation technique.

### 1.2 Proposition de valeur
- Zéro paperasse : gestion 100 % numérique des membres et abonnements
- Contrôle d'accès par QR code (et domotique optionnelle)
- Notifications automatiques (WhatsApp, SMS, Email) pour fidéliser les abonnés
- Impression de reçus depuis l'application ou une imprimante thermique externe
- Paiements Mobile Money natifs (Orange Money, MTN MoMo, Wave)
- Personnalisation complète aux couleurs de chaque salle

### 1.3 Modèle économique
- Abonnement SaaS mensuel par salle (Starter gratuit, Essentiel 15 000 FCFA/mois, Pro 35 000 FCFA/mois)
- 2 mois d'essai gratuit sur toutes les formules payantes, sans carte bancaire
- Évolution future : commission sur les paiements en ligne traités via la plateforme

---

## 2. Organisation du Projet Multi-Entités

> Ce projet est développé par **trois entités distinctes**, chacune pouvant être un fournisseur d'IA différent (Claude, DeepSeek, ChatGPT, etc.) ou une équipe humaine. Chaque entité dispose d'une autonomie sur sa stack technique, sous réserve de compatibilité avec l'hébergement défini à la section 3.

### 2.1 Vue d'ensemble des entités

| Entité | Rôle | Périmètre | Chef de projet |
|--------|------|-----------|----------------|
| **Entité A — Frontend** | Développement du Front Office | Landing page, onboarding, espace abonné (front public) | Non |
| **Entité B — Backend** | Développement de l'API et du Backoffice | API REST, backoffice admin, base de données | Non |
| **Entité C — DevOps & Tests** | Infrastructure, CI/CD, qualité, coordination | Déploiement, tests, intégration, sécurité | **Oui — Chef de projet** |

### 2.2 Entité A — Frontend (Front Office)

**Mission :** Développer toutes les interfaces publiques visibles par les visiteurs et les abonnés. Le Front Office communique avec le backend exclusivement via l'API REST fournie par l'Entité B.

**Périmètre :**
- Landing page marketing (SEO-first, statique ou SSG)
- Page de contact
- Page de tarifs
- Tunnel d'onboarding (inscription d'une nouvelle salle)
- Espace abonné (consultation profil, QR code, historique)
- Pages légales (CGU, confidentialité)

**Interfaces avec l'Entité B :**
- Consommation de l'API REST pour le tunnel d'onboarding
- Consommation de l'API REST pour l'espace abonné (authentification, profil, QR code)

**Contraintes :**
- Respect du design system défini dans les maquettes (demo/)
- Toutes les pages publiques doivent être indexables par les moteurs de recherche (voir section SEO)
- Génération statique ou SSR pour les pages marketing

**Agents suggérés (Entité A peut créer ses propres sous-agents) :**
- Agent Design/Intégration : traduit les maquettes HTML en composants
- Agent SEO Frontend : vérifie les balises, métadonnées, sitemaps
- Agent Accessibilité : vérifie les contrastes, aria-labels, navigation clavier

### 2.3 Entité B — Backend

**Mission :** Développer l'API REST qui alimente le backoffice et le Front Office. Gérer la base de données, la logique métier, les paiements, les notifications, les QR codes et la sécurité des données.

**Périmètre :**
- API REST JSON (versionnée : `/api/v1/`)
- Backoffice admin (interface web interne aux gérants et caissiers)
- Gestion multi-tenant (isolation des données par salle)
- Moteur d'abonnements et de paiements
- Système de notifications (WhatsApp, SMS, Email)
- Génération de QR codes et de reçus PDF
- Authentification JWT + gestion des rôles

**Interfaces avec l'Entité A :**
- Fourniture d'une documentation API complète (format OpenAPI / Swagger)
- Environnements : `dev`, `staging`, `production`
- Gestion des CORS pour les domaines du Front Office

**Interfaces avec l'Entité C :**
- Fourniture des scripts de migration base de données
- Fourniture des fichiers de configuration d'environnement (.env.example)
- Variables d'environnement nécessaires documentées

**Agents suggérés (Entité B peut créer ses propres sous-agents) :**
- Agent API : génère et valide les endpoints REST
- Agent Base de données : gère les migrations et les relations
- Agent Paiements : intègre les APIs Mobile Money
- Agent Notifications : configure Twilio / SendGrid / API SMS

### 2.4 Entité C — DevOps, Tests & Chef de Projet

**Mission (Chef de projet) :** Coordonner les deux autres entités, valider les livrables, gérer les environnements de déploiement et garantir la qualité du projet.

**Périmètre DevOps :**
- Configuration du dépôt Git (GitHub) et des branches (`main`, `develop`, `feature/*`, `hotfix/*`)
- Pipeline CI/CD via GitHub Actions → déploiement automatique sur Hostinger (FTP/SSH)
- Gestion des environnements : local, staging (sous-domaine Hostinger), production
- Configuration SSL/TLS sur Hostinger (Let's Encrypt)
- Monitoring uptime (UptimeRobot ou équivalent gratuit)
- Sauvegarde automatique de la base de données (cron Hostinger)

**Périmètre Tests :**
- Tests unitaires du backend (PHPUnit ou équivalent)
- Tests d'intégration API (Postman / Newman en CI)
- Tests E2E du front office (Playwright ou Cypress)
- Tests de sécurité (OWASP ZAP scan automatique)
- Tests de performance (PageSpeed, GTmetrix)
- Rapport de test livré après chaque sprint

**Périmètre Chef de projet :**
- Définition et suivi du backlog (GitHub Issues ou Notion)
- Validation des livrables de l'Entité A et de l'Entité B
- Organisation des points de synchronisation entre entités
- Rédaction du rapport final de collaboration (voir section 9)

**Agents suggérés (Entité C peut créer ses propres sous-agents) :**
- Agent CI/CD : configure et maintient les pipelines GitHub Actions
- Agent Tests : génère et exécute les suites de tests
- Agent Sécurité : réalise les audits OWASP, vérifie les headers HTTP
- Agent Blog : rédige l'article de présentation de la collaboration (voir section 9)
- Agent Chef de projet : coordonne les échanges entre entités, suit l'avancement

### 2.5 Règles de collaboration inter-entités

1. **Pas de communication directe entre agents d'entités différentes.** Toute coordination passe par le Chef de projet (Entité C).
2. **Contrat d'API figé.** Une fois validée par l'Entité C, la documentation API ne peut être modifiée sans validation. Les évolutions passent par un changelog versionné.
3. **Dossier de livrables partagé.** Chaque entité dépose ses livrables dans un dossier commun (repo Git) avec un nom de fichier clair : `entity-a/landing-v1.html`, `entity-b/api-spec-v1.yaml`, etc.
4. **Points de validation humains.** Le client (Rufin Ismael Baroan) valide obligatoirement : (a) les maquettes avant développement, (b) la documentation API avant implémentation, (c) chaque déploiement en staging avant mise en production.
5. **Aucun fait inventé.** Si une information manque (credential API, URL de webhook, numéro de téléphone), l'agent marque `[À FOURNIR]` et attend la validation humaine avant de continuer.
6. **Langue de travail :** Français pour toute la documentation et les interfaces. Anglais toléré pour le code et les commentaires techniques.

---

## 3. Hébergement et Contraintes Techniques

### 3.1 Hébergement — Hostinger Espace Web

Le projet est hébergé sur un **espace web Hostinger** (hébergement mutualisé). Toutes les décisions techniques doivent tenir compte de ces contraintes.

| Caractéristique | Valeur |
|---|---|
| Type | Hébergement mutualisé (shared hosting) |
| Panneau de contrôle | hPanel (Hostinger) |
| Langages supportés | PHP 8.x (8.1, 8.2, 8.3). **Version retenue : PHP 8.3** (exigée par Laravel 13, v2.3) |
| Base de données | MySQL 8.x / MariaDB |
| Serveur web | Apache (avec support .htaccess) |
| SSL | Let's Encrypt (fourni par Hostinger, renouvellement automatique) |
| Accès SSH | Oui (selon le plan Hostinger souscrit) |
| Cron jobs | Oui (via hPanel) |
| Email | Oui (comptes email professionnels inclus) |
| Stockage fichiers | Stockage serveur (disk quota selon plan) |
| FTP/SFTP | Oui |

### 3.2 Contraintes liées à l'hébergement mutualisé

- **Pas de processus Node.js persistant** (no `node server.js`, no PM2). Les outils Node.js ne peuvent être utilisés qu'en phase de build (pre-compile), le résultat statique étant déployé sur Hostinger.
- **Pas de Redis ni Memcached** sans plan premium. Le cache et les sessions utilisent le système de fichiers ou la base de données.
- **Pas de Docker** sur hébergement mutualisé.
- **Queues/Workers :** Les tâches asynchrones (envoi de notifications, génération de PDF) sont gérées via **cron jobs** Hostinger plutôt que des workers Laravel Queue (ou via un service externe).
- **Stockage de fichiers :** Logos, images, QR codes stockés sur le serveur Hostinger (dans un dossier `storage/` hors du webroot) ou sur **Cloudflare R2** (compatible S3, plan gratuit généreux).
- **Performances :** Activation obligatoire du cache OPcache PHP (disponible sur Hostinger). CDN Cloudflare recommandé en front.

### 3.3 Domaines et sous-domaines

| Sous-domaine | Usage | Entité responsable |
|---|---|---|
| `abidjangym.com` | Landing page + Front Office | Entité A |
| `www.abidjangym.com` | Redirect vers `abidjangym.com` | Entité C |
| `app.abidjangym.com` | Backoffice (admin/caissier) | Entité B |
| `api.abidjangym.com` | API REST | Entité B |
| `staging.abidjangym.com` | Environnement de staging | Entité C |
| `member.abidjangym.com` | Espace abonné | Entité A |

---

## 4. Périmètre Fonctionnel

### 4.1 Front Office — Pages publiques (Entité A)

#### 4.1.1 Landing page (`abidjangym.com`)
La page d'accueil est la vitrine commerciale de la plateforme. Elle doit :
- Présenter la proposition de valeur en moins de 5 secondes (hero section impactant)
- Afficher les fonctionnalités clés avec visuels (photos, icônes)
- Présenter les 3 formules tarifaires avec CTA clair
- Afficher des témoignages de gérants de salles
- Inclure une section FAQ
- Proposer un CTA "J'inscris ma salle" présent à minima en haut et en bas de page
- Être entièrement responsive (mobile-first)
- Respecter toutes les exigences SEO (section 5)

Structure minimale de la landing page :
1. Nav fixe avec logo + liens + CTA
2. Hero : titre accrocheur + sous-titre + CTA primaire + image
3. Bande de preuves sociales (logos de salles partenaires ou chiffres clés)
4. Section fonctionnalités (6 features avec icônes et descriptions)
5. Section "Pour les gérants" (bénéfices côté business)
6. Section tarifs (3 cards : Starter / Essentiel / Pro)
7. Section témoignages
8. Section FAQ (accordion, min. 8 questions)
9. CTA final
10. Footer (liens, légal, réseaux sociaux)

#### 4.1.2 Page Contact (`abidjangym.com/contact`)
- Formulaire de contact (prénom, nom, email, téléphone, objet, message)
- Intégration backend pour envoi d'email (via API Entité B)
- Coordonnées de l'équipe (WhatsApp, email, adresse Abidjan)
- Temps de réponse affiché (< 24h)

#### 4.1.3 Pages légales
- Conditions Générales d'Utilisation (`/cgu`)
- Politique de Confidentialité (`/confidentialite`)
- Mentions légales (`/mentions-legales`)
- Politique de cookies (`/cookies`)

#### 4.1.4 Tunnel d'onboarding (`abidjangym.com/inscription`)
Formulaire multi-étapes (4 étapes) pour inscrire une nouvelle salle :
- **Étape 1 — Votre salle :** Nom, quartier/commune, adresse, téléphone, WhatsApp
- **Étape 2 — Compte admin :** Prénom, nom, email, numéro Mobile Money, mot de passe
- **Étape 3 — Formule :** Choix du plan (Starter / Essentiel / Pro) avec résumé
- **Étape 4 — Confirmation :** Récapitulatif, CGU, bouton de finalisation

À la validation : création du compte via API (Entité B) + email de confirmation + redirection vers le backoffice.

#### 4.1.5 Espace abonné (`member.abidjangym.com`)
Interface simplifiée pour les membres des salles :
- Connexion par email ou lien magique (envoyé par WhatsApp/SMS)
- Profil : nom, photo, numéro de membre
- Abonnement actif : type, date d'expiration, jours restants
- QR code : affichage en grand + bouton télécharger (PNG)
- Historique des paiements (date, montant, mode, reçu PDF)
- Historique des entrées (date, heure)

---

### 4.2 Backoffice — Interface Admin (Entité B)

#### 4.2.1 Authentification
| Écran | Description |
|---|---|
| Connexion | Email + mot de passe. Token JWT stocké en httpOnly cookie. |
| Mot de passe oublié | Envoi d'un lien de réinitialisation par email (validité 1h). |
| Réinitialisation | Formulaire de nouveau mot de passe (min. 8 caractères, 1 majuscule, 1 chiffre). |
| Déconnexion | Révocation du token côté serveur. |
| Session | Durée de session : 8h. Rafraîchissement automatique du token si activité. |

#### 4.2.2 Dashboard Principal (`/dashboard`)
Vue d'ensemble avec des widgets actualisés en temps réel (polling toutes les 60s) :

| Widget | Données affichées |
|---|---|
| Abonnés actifs | Nombre total d'abonnés avec abonnement valide aujourd'hui |
| Revenus du jour | Somme des paiements enregistrés depuis 00h00 |
| Entrées du jour | Nombre de scans validés depuis 00h00 |
| Abonnements expirant bientôt | Abonnés dont l'abonnement expire dans ≤ 7 jours |
| Graphique revenus | Courbe des 30 derniers jours (revenus journaliers) |
| Graphique fréquentation | Histogramme des entrées par heure (semaine courante) |
| Derniers paiements | 5 derniers paiements enregistrés (nom, montant, mode, heure) |
| Alertes | Abonnements expirés non renouvelés depuis > 7 jours |

#### 4.2.3 Gestion du profil salle

Chaque salle dispose d'une fiche de profil entièrement personnalisable :

| Champ | Type | Détails |
|---|---|---|
| Nom de la salle | Texte (max 80 car.) | Affiché sur les reçus, QR codes, notifications |
| Logo | Image (PNG/JPG, max 2 MB) | Fond transparent recommandé, redimensionné à 300×300 px |
| Slogan / Tagline | Texte court (max 120 car.) | Optionnel, affiché sur les reçus |
| Adresse | Texte | Rue, quartier, ville |
| Téléphone principal | Téléphone | Format +225 XXXXXXXXXX, affiché sur les reçus |
| WhatsApp business | Téléphone | Numéro dédié pour les réponses des abonnés |
| Email de contact | Email | Pour les notifications système et les abonnés |
| Site web | URL | Optionnel |
| Horaires d'ouverture | Plages horaires | Par jour (lundi–dimanche) + option "Fermé" + jours fériés |
| Couleur primaire | Couleur HEX | Utilisée dans l'interface et sur les reçus |
| Couleur secondaire | Couleur HEX | Accents, boutons secondaires |
| Images bannières | Images (max 3) | Affichées dans l'espace abonné (16:9 recommandé) |
| Réseaux sociaux | URLs | Instagram, Facebook, TikTok, WhatsApp Business |
| Fuseau horaire | Sélection | Abidjan (Africa/Abidjan, GMT+0) par défaut |
| Devise | Sélection | FCFA (XOF) par défaut, non modifiable en v1 |
| Message de bienvenue | Texte riche (max 500 car.) | Affiché dans l'espace abonné |

#### 4.2.4 Gestion des types d'abonnements (Plans)

##### Types disponibles

| Type | Description | Durée de validité |
|---|---|---|
| **Mensuel** | Accès illimité pendant 1 mois | 30 jours calendaires depuis la date d'activation |
| **Hebdomadaire** | Accès illimité pendant 1 semaine | 7 jours calendaires depuis la date d'activation |
| **Journalier** | Accès pour une journée | Le jour d'activation uniquement (jusqu'à 23h59) |
| **Pack de jours** | X jours d'accès dans une fenêtre Y | Ex : 10 séances utilisables dans 60 jours |

##### Configuration d'un plan par le gérant

| Champ | Type | Détails |
|---|---|---|
| Nom de l'offre | Texte | Ex : "Pass Découverte", "Abonnement Gold", "Pack 10 séances" |
| Type | Sélection | Mensuel / Hebdo / Journalier / Pack de jours |
| Nombre de jours inclus | Nombre entier | Uniquement pour Pack de jours (ex : 10) |
| Fenêtre de validité | Nombre de jours | Uniquement pour Pack (ex : 60 jours pour utiliser les 10 séances) |
| Prix | Montant (FCFA) | Entier positif, pas de décimales |
| Description courte | Texte (max 200 car.) | Affichée dans la liste des plans |
| Statut | Actif / Inactif | Inactif = masqué lors de la création d'un abonnement |
| Ordre d'affichage | Nombre | Réordonnancement par drag-and-drop dans la liste |

#### 4.2.5 Gestion des abonnés (Membres)

##### Profil abonné

| Champ | Type | Requis | Détails |
|---|---|---|---|
| Prénom | Texte | Oui | |
| Nom | Texte | Oui | |
| Téléphone | Téléphone | Oui | Format international, utilisé pour WhatsApp/SMS |
| Email | Email | Non | Utilisé pour les notifications par email |
| Date de naissance | Date | Non | |
| Genre | Sélection | Non | Homme / Femme / Non renseigné |
| Photo de profil | Image | Non | Max 1 MB, recadrée en cercle dans l'interface |
| Notes internes | Texte libre | Non | Visible uniquement par l'admin, pas par l'abonné |
| Numéro de membre | Auto-généré | — | Format : `AAAA-XXXXX` (ex : 2026-00042) |
| QR code | Auto-généré | — | UUID v4 encodé en QR code PNG + SVG |

##### États d'un abonné

| État | Condition | Couleur indicative |
|---|---|---|
| **Actif** | Abonnement en cours et valide aujourd'hui | Vert |
| **Expire bientôt** | Abonnement expirant dans ≤ 7 jours | Orange |
| **Expiré** | Abonnement terminé, aucun renouvellement | Rouge |
| **Suspendu** | Temporairement bloqué manuellement par l'admin | Gris |
| **Nouveau** | Profil créé, pas encore d'abonnement assigné | Bleu |

##### Fonctionnalités de la liste abonnés

- Recherche plein texte (prénom, nom, numéro de membre, téléphone)
- Filtres : état, type de plan, commune, date d'expiration
- Tri : alphabétique, date de création, date d'expiration, nombre d'entrées
- Export CSV de la liste filtrée
- Actions groupées : envoyer une notification, suspendre, archiver

##### Fiche détaillée abonné

- Toutes les informations du profil (avec édition inline)
- Abonnement actif en cours : plan, date de début, date de fin, jours restants
- Historique de tous les abonnements passés
- Historique de tous les paiements (avec reçu PDF téléchargeable)
- Historique de toutes les entrées (date, heure, validé par qui)
- QR code affiché + bouton "Renvoyer par WhatsApp/Email"
- Journal des notifications envoyées à cet abonné
- Boutons d'action : Renouveler, Suspendre, Envoyer QR, Imprimer carte membre, Archiver

#### 4.2.6 Gestion des paiements

##### Enregistrement d'un paiement

Chaque paiement est lié à un abonné et à un abonnement. Les informations enregistrées :

| Champ | Type | Détails |
|---|---|---|
| Abonné | Relation | Lié au profil membre |
| Plan d'abonnement | Relation | Plan sélectionné |
| Montant payé | Montant (FCFA) | Peut différer du prix du plan (remise manuelle possible) |
| Mode de paiement | Sélection | Voir modes ci-dessous |
| Date de paiement | Date/Heure | Par défaut : maintenant (modifiable) |
| Référence externe | Texte | Numéro de transaction Mobile Money ou chèque |
| Statut | Sélection | Confirmé / En attente / Annulé |
| Notes | Texte libre | Optionnel (visible en interne uniquement) |

##### Modes de paiement supportés

**Cash (espèces)**
- Enregistrement manuel par l'admin ou caissier
- Saisie du montant, de la date, d'une référence optionnelle
- Génération immédiate du reçu

**Orange Money CI**
- Intégration via API Orange Money Côte d'Ivoire
- Flux : initiation depuis l'app → l'abonné reçoit une demande USSD → confirme sur son téléphone → callback de confirmation → enregistrement automatique + reçu
- Numéro Orange Money de la salle configuré dans les paramètres

**MTN Mobile Money CI**
- Intégration via API MTN MoMo (collection API)
- Même flux que Orange Money
- Numéro MTN de la salle configuré dans les paramètres

**Wave**
- Intégration via lien de paiement Wave ou API Wave CI
- Flux : génération d'un lien de paiement → partage par WhatsApp → abonné paie → webhook de confirmation

**CinetPay (carte bancaire)**
- Via API CinetPay (Visa / Mastercard)
- Lien de paiement ou intégration widget
- Webhook de confirmation

**Virement / Chèque**
- Enregistrement manuel avec upload de la preuve (photo du chèque ou bordereau, max 5 MB)
- Statut initial : "En attente de confirmation"
- L'admin confirme manuellement → passage au statut "Confirmé" → génération du reçu

##### Activation des moyens de paiement *(ajout v2.2)*

- Chaque moyen de paiement (espèces, Orange Money, MTN MoMo, Wave, CinetPay, virement, chèque) s'**active ou se désactive (on/off)** dans les paramètres de la salle, par le gérant uniquement.
- **Seuls les moyens activés sont proposés** lors de l'encaissement. Un paiement avec un moyen désactivé est refusé.
- Les identifiants des prestataires saisis pour un moyen ne sont jamais réaffichés (écriture seule, chiffrés, voir §6.6).

##### Historique des paiements

- Liste complète avec filtres : date, mode, statut, abonné, montant
- Export CSV pour comptabilité
- Total par mode de paiement sur la période sélectionnée
- Détail de chaque paiement : toutes les informations + reçu PDF

#### 4.2.7 Système de Notifications

##### Canaux de communication

| Canal | Fournisseur recommandé | Condition d'utilisation |
|---|---|---|
| **WhatsApp** | API WhatsApp Business (Twilio ou 360dialog) | Compte WhatsApp Business approuvé par Meta |
| **SMS** | Orange CI API, Infobip, ou Twilio SMS | Abonnement API SMS |
| **Email** | Mailgun, SendGrid, ou SMTP Hostinger | Toujours actif (canal par défaut) |

Chaque salle configure dans ses paramètres les canaux à activer et les credentials correspondants.

##### Notifications côté abonné

| Événement | Contenu du message | Canal(aux) | Déclencheur |
|---|---|---|---|
| Bienvenue | Message de bienvenue personnalisé + QR code en pièce jointe | WhatsApp + Email | Création du profil |
| Abonnement créé | Confirmation + détails du plan + date de fin | WhatsApp + Email | Création d'un abonnement |
| Paiement confirmé | Montant, mode, date, numéro de reçu + lien PDF | WhatsApp + Email | Enregistrement d'un paiement confirmé |
| Rappel J-7 | "Votre abonnement expire dans 7 jours. Pensez à renouveler !" | WhatsApp + SMS | Cron job quotidien |
| Rappel J-3 | "Plus que 3 jours. Renouvelez maintenant !" | WhatsApp + SMS | Cron job quotidien |
| Rappel J-1 | "Votre abonnement expire demain." | WhatsApp + SMS | Cron job quotidien |
| Expiration | "Votre abonnement est expiré. Contactez votre salle." | WhatsApp + SMS | Cron job quotidien (J+0) |
| Pack — dernière séance | "Il vous reste 1 séance dans votre pack." | WhatsApp | Après chaque scan |
| QR code — renvoi | QR code en image (WhatsApp) ou en pièce jointe (email) | WhatsApp / Email | Action manuelle admin |
| Suspension | "Votre accès a été temporairement suspendu. Contactez la salle." | WhatsApp + Email | Suspension manuelle |

##### Notifications côté gérant/admin

| Événement | Canal(aux) | Fréquence |
|---|---|---|
| Nouvel abonné inscrit | Email | Immédiat |
| Paiement enregistré | Email / WhatsApp | Immédiat |
| Rapport journalier : abonnements expirant aujourd'hui | Email | 8h00 chaque matin |
| Rapport hebdo : abonnés expirés sans renouvellement > 7j | Email | Lundi 8h00 |
| Erreur d'envoi de notification | Email | Immédiat |

##### Configuration des notifications par le gérant

- Activer / désactiver chaque type de notification individuellement
- Personnaliser le texte des messages (variables disponibles : `{prenom}`, `{nom}`, `{date_expiration}`, `{jours_restants}`, `{nom_salle}`, `{montant}`, `{mode_paiement}`)
- Définir les horaires d'envoi (ex : rappels envoyés uniquement entre 8h et 20h)
- Journal des notifications : liste de toutes les notifications envoyées avec statut (envoyé ✅ / échoué ❌ / en attente ⏳) et horodatage

#### 4.2.8 Impression de Reçus

##### Contenu obligatoire du reçu

| Élément | Description |
|---|---|
| En-tête | Logo + Nom de la salle + couleur primaire de la salle |
| Coordonnées | Adresse, téléphone, email de la salle |
| Numéro de reçu | Auto-incrémenté, format `REC-AAAA-XXXXX` |
| Date et heure | Date et heure d'émission du reçu |
| Informations abonné | Prénom, Nom, Numéro de membre |
| Abonnement | Type de plan, date de début, date de fin |
| Paiement | Montant en FCFA, mode de paiement, référence de transaction |
| Bas de page | Message personnalisable par le gérant (ex : "Merci de votre confiance !") |
| QR code abonné | Optionnel (configurable par le gérant) |
| Signature/Cachet | Image uploadable par le gérant (optionnel) |

##### Formats d'impression

- **PDF in-app :** Génération via API backend (Dompdf ou similaire). Téléchargeable depuis le navigateur.
- **Imprimante thermique Bluetooth :** Compatible imprimantes 58mm et 80mm (ESC/POS). Connexion Bluetooth depuis l'appareil de l'admin (via navigateur Web Bluetooth API ou application mobile future).
- **Imprimante réseau Wi-Fi :** Impression via le protocole d'impression du système d'exploitation (dialog natif du navigateur).
- **Export pour impression ultérieure :** Lien de téléchargement du PDF valable 72h.

#### 4.2.9 QR Code Abonné

##### Génération et format

- Un QR code unique est généré automatiquement à la création du profil abonné
- Le QR code encode un identifiant UUID v4 (ex : `550e8400-e29b-41d4-a716-446655440000`)
- L'UUID est lié à l'abonné dans la base de données, pas à son abonnement — le même QR code est valable pour tous ses abonnements successifs
- Format généré : PNG haute résolution (800×800 px) + SVG vectoriel
- Taille minimale d'impression recommandée : 3×3 cm (scannable à 50 cm)
- Régénération possible par l'admin en cas de perte (l'ancien UUID est invalidé)

##### Scan et contrôle d'accès (interface `/scan`)

- Interface plein écran optimisée pour tablette ou smartphone
- Activation de la caméra du dispositif
- Scan en temps réel : détection automatique du QR code dans le champ de la caméra
- Résultat affiché immédiatement après scan :
  - ✅ **Accès autorisé :** Nom de l'abonné, photo de profil (si disponible), type d'abonnement, jours restants ou séances restantes. Fond vert.
  - ❌ **Accès refusé :** Motif (abonnement expiré / abonné suspendu / QR invalide). Fond rouge.
- Enregistrement automatique du passage (historique des entrées) avec date, heure et identifiant du caissier ayant scanné

##### Domotique optionnelle (module additionnel)

- Intégration avec une borne de contrôle d'accès physique (tourniquet, porte électrique)
- Déclenchement via webhook HTTP POST après chaque scan validé
- Configuration dans les paramètres : URL du webhook + token d'authentification Bearer
- Payload du webhook : `{ member_id, member_name, scan_time, access_granted, subscription_type }`

#### 4.2.10 Rôles Utilisateurs et Permissions

##### Super Admin (équipe AbidjanGym)

| Permission | Détail |
|---|---|
| Plateforme multi-tenant | Vue de toutes les salles inscrites |
| Gestion des salles | Créer, suspendre, réactiver, supprimer une salle |
| Métriques globales | Nombre de salles actives, MRR, abonnés totaux, taux de churn |
| Plans SaaS | Créer, modifier, archiver les plans d'abonnement SaaS |
| Accès à une salle | Connexion en tant qu'admin d'une salle pour support |

##### Admin Salle (Gérant)

| Permission | Détail |
|---|---|
| Accès | Limité à sa salle uniquement — aucun accès aux autres tenants |
| CRUD abonnés | Créer, lire, modifier, archiver des abonnés |
| CRUD abonnements | Créer, modifier, renouveler, suspendre des abonnements |
| CRUD paiements | Enregistrer, modifier, annuler des paiements |
| Rapports | Accès à tous les rapports de sa salle |
| Paramètres | Configuration complète : profil salle, notifications, paiements, utilisateurs |
| Gestion des caissiers | Créer et désactiver des comptes caissier |

##### Caissier / Réceptionniste

| Permission | Détail |
|---|---|
| Abonnés | Consulter la liste, créer un abonné, enregistrer un abonnement |
| Paiements | Enregistrer un paiement, imprimer un reçu |
| Scan | Accès à l'interface de scan QR code |
| Accès refusé | Rapports, paramètres de la salle, suppression de données |

##### Abonné (Espace membre)

| Permission | Détail |
|---|---|
| Profil | Consulter son profil (lecture seule) |
| Abonnement | Voir son abonnement actif et son historique |
| QR code | Télécharger son QR code |
| Paiements | Voir son historique de paiements et télécharger ses reçus PDF |

#### 4.2.11 Rapports et Analyses

| Rapport | Données | Filtres disponibles | Export |
|---|---|---|---|
| Revenus | Total par jour/semaine/mois, répartition par mode de paiement | Période, mode de paiement | CSV, PDF |
| Abonnés | Nouveaux inscrits, actifs, expirés, churns (non renouvelés après 30j) | Période, plan, commune | CSV |
| Fréquentation | Entrées par jour, par heure, par abonné | Période | CSV |
| Abonnements | Répartition par type de plan, taux de renouvellement | Période, plan | CSV |

---

## 5. Exigences SEO

Toutes les pages publiques (Entité A) doivent respecter les bonnes pratiques SEO suivantes.

### 5.1 Métadonnées et balises

- `<title>` unique sur chaque page, 50–60 caractères, incluant le mot-clé principal
- `<meta name="description">` unique par page, 120–160 caractères, incitative au clic
- Balises Open Graph (og:title, og:description, og:image, og:url) pour le partage social
- Twitter Card (twitter:card, twitter:title, twitter:description, twitter:image)
- Canonical URL sur chaque page (`<link rel="canonical" href="...">`)
- Hreflang si extension multilingue future
- `<html lang="fr">` sur toutes les pages

### 5.2 Structure HTML

- Une seule balise `<h1>` par page, contenant le mot-clé principal
- Hiérarchie des titres respectée : H1 > H2 > H3 (pas de sauts)
- Images : attribut `alt` descriptif sur chaque image (pas de "image1.jpg")
- Liens internes : texte d'ancre descriptif (pas de "cliquez ici")
- URLs propres et lisibles : `abidjangym.com/tarifs` plutôt que `abidjangym.com/?p=42`

### 5.3 Performance (Core Web Vitals)

| Métrique | Objectif |
|---|---|
| LCP (Largest Contentful Paint) | < 2,5 secondes |
| FID / INP (Interaction to Next Paint) | < 200ms |
| CLS (Cumulative Layout Shift) | < 0,1 |
| Score PageSpeed Insights mobile | ≥ 80 |
| Score PageSpeed Insights desktop | ≥ 90 |

Actions requises :
- Images au format WebP (avec fallback JPG/PNG)
- Images redimensionnées côté serveur (pas de 3000px pour un thumbnail 300px)
- Lazy loading des images hors viewport (`loading="lazy"`)
- CSS et JS minifiés et concaténés
- Fonts Google chargées avec `display=swap`
- CDN Cloudflare activé devant Hostinger
- Cache navigateur configuré via `.htaccess` (images : 1 an, CSS/JS : 1 mois)

### 5.4 Contenu et mots-clés cibles

| Page | Mot-clé principal | Mots-clés secondaires |
|---|---|---|
| Landing page | "logiciel gestion salle de sport Côte d'Ivoire" | "SaaS gym Abidjan", "gestion abonnés gym CI" |
| Tarifs | "prix logiciel salle de sport Abidjan" | "abonnement SaaS gym Côte d'Ivoire" |
| Contact | "contact AbidjanGym" | "support salle de sport Abidjan" |
| Fonctionnalités | "fonctionnalités logiciel gym Afrique" | "QR code salle de sport", "Mobile Money gym" |

### 5.5 Fichiers techniques SEO

- `sitemap.xml` : généré automatiquement, soumis à Google Search Console
- `robots.txt` : autoriser Google, interdire les dossiers sensibles (`/api/`, `/admin/`, `/storage/`)
- `favicon.ico` + icônes Apple Touch + manifeste PWA (`manifest.json`)
- Données structurées JSON-LD : Organisation, WebSite, FAQPage (sur la landing)
- Fil d'Ariane (BreadcrumbList) sur les pages intérieures

---

## 6. Exigences Sécurité

### 6.1 HTTPS et transport

- HTTPS obligatoire sur tous les domaines et sous-domaines (SSL via Let's Encrypt, Hostinger)
- Redirection automatique HTTP → HTTPS via `.htaccess`
- HSTS activé (Strict-Transport-Security : max-age=31536000; includeSubDomains)
- Protocoles TLS : TLS 1.2 minimum, TLS 1.3 recommandé

### 6.2 Authentification et sessions

- Authentification par JWT (access token 1h + refresh token 30 jours)
- Tokens stockés en `httpOnly` cookie (non accessible via JavaScript)
- Révocation des tokens à la déconnexion (liste noire en base de données)
- Limitation des tentatives de connexion : blocage après 5 échecs consécutifs (délai exponentiel)
- Mot de passe : minimum 8 caractères, 1 majuscule, 1 chiffre, haché avec bcrypt (cost factor ≥ 12)
- Réinitialisation de mot de passe : token à usage unique, validité 1h, lien envoyé uniquement à l'email enregistré

### 6.3 Isolation des données (multi-tenant)

- Chaque salle est identifiée par un `tenant_id` unique
- Toutes les requêtes SQL incluent obligatoirement la clause `WHERE tenant_id = :current_tenant`
- Les routes API vérifient l'appartenance de chaque ressource au tenant courant avant toute lecture ou écriture
- Aucune donnée d'un tenant n'est accessible depuis le compte d'un autre tenant

### 6.4 Protection contre les attaques courantes

| Menace | Protection |
|---|---|
| SQL Injection | ORM avec requêtes préparées (Eloquent / PDO). Aucune requête SQL construite par concaténation de chaînes. |
| XSS (Cross-Site Scripting) | Échappement systématique des données en sortie. Content Security Policy (CSP) dans les headers HTTP. |
| CSRF | Token CSRF sur tous les formulaires POST/PUT/DELETE. Validation côté serveur. |
| Clickjacking | Header `X-Frame-Options: DENY` sur toutes les réponses. |
| Injection de headers | Validation et sanitisation de toutes les entrées utilisateur (nom, email, téléphone, etc.). |
| Brute force | Rate limiting sur les routes d'authentification (5 req/min par IP). |
| Exposition de fichiers | Le dossier `storage/` est hors du webroot Hostinger. Le `.env` n'est jamais accessible publiquement. |
| Données sensibles en logs | Les logs n'enregistrent jamais les mots de passe, tokens, ou numéros de carte. |

### 6.5 Headers de sécurité HTTP

À configurer dans `.htaccess` (Hostinger/Apache) :

```
Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' fonts.googleapis.com; font-src 'self' fonts.gstatic.com; img-src 'self' data: *.cloudflare.com; frame-ancestors 'none';
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
Permissions-Policy: camera=(), microphone=(), geolocation=()
```

Exception sur `/scan` : autoriser l'accès à la caméra dans Permissions-Policy.

### 6.6 Données personnelles et conformité

- Collecte minimale : ne collecter que les données nécessaires au service
- Consentement explicite demandé à l'inscription (CGU + politique de confidentialité)
- Droit à l'effacement : suppression ou anonymisation des données d'un abonné sur demande
- Les credentials API (clés Orange Money, MTN, etc.) sont chiffrés en base de données (AES-256)
- Logs d'audit sur les actions sensibles : création/suppression de compte, modification de rôle, paiement, export de données

### 6.7 Sauvegardes

- Backup automatique de la base de données : quotidien à 02h00 (cron Hostinger)
- Rétention : 30 jours de backups
- Stockage des backups : sur Hostinger + copie hebdomadaire sur Cloudflare R2
- Procédure de restauration testée et documentée par l'Entité C

---

## 7. Architecture Technique par Entité

> Chaque entité est libre de choisir sa stack technique, à condition de respecter les contraintes d'hébergement Hostinger (section 3) et les interfaces définies ci-dessous.

### 7.1 Entité A — Frontend (stack recommandée)

**Option A — Recommandée pour le SEO :**
- **Framework :** Nuxt.js 3 (Vue.js 3) en mode SSG (Static Site Generation)
- **CSS :** Tailwind CSS ou CSS modules
- **Build :** `nuxt generate` → déploiement du dossier `dist/` sur Hostinger via FTP/SFTP
- **Avantage :** HTML pré-rendu côté build, optimal pour le SEO et la performance

**Option B — Alternative :**
- **Framework :** Next.js 14 (React) en mode `output: 'export'` (static export)
- **CSS :** Tailwind CSS
- **Build :** `next build` → déploiement du dossier `out/`

**Pour le backoffice (espace abonné) :**
- SPA Vue.js 3 ou React déployée en sous-domaine

**Dépendances communes :**
- Axios ou Fetch API pour les appels à l'API backend
- Vuelidate ou React Hook Form pour la validation des formulaires
- HTML5 QR Code library pour la lecture du QR code côté client (si besoin)

### 7.2 Entité B — Backend (stack recommandée)

**Stack recommandée (compatible Hostinger PHP) :**
- **Framework :** Laravel 13, version ≥ 13.30 (PHP 8.3+). *Laravel 11, prévu en v2.0, est en fin de maintenance : voir changelog v2.3*
- **Base de données :** MySQL 8.x (Hostinger)
- **ORM :** Eloquent (inclus dans Laravel)
- **Authentification :** Laravel Sanctum (tokens JWT httpOnly)
- **PDF :** Dompdf ou Browsershot (reçus)
- **QR Code :** BaconQrCode ou Simple QrCode (génération)
- **Cache :** File-based cache (compatible hébergement mutualisé)
- **Queue / Tâches async :** Laravel Scheduler via cron job Hostinger (pas de worker persistant)
- **Emails :** Mailgun ou SMTP Hostinger + Laravel Mail
- **Stockage fichiers :** Laravel Storage avec driver `local` (Hostinger) + optionnel driver S3 (Cloudflare R2)

**API REST — Conventions :**
- Base URL : `https://api.abidjangym.com/api/v1/`
- Format : JSON (Content-Type: application/json)
- Authentification : Bearer token dans le header Authorization
- Pagination : `?page=1&per_page=20` (cursor-based pour les grandes listes)
- Versionning : préfixe `/v1/` — les évolutions breaking créent `/v2/`
- Gestion des erreurs : codes HTTP standards (200, 201, 400, 401, 403, 404, 422, 500) + corps JSON `{ "message": "...", "errors": {} }`

**Documentation API obligatoire :**
- Format OpenAPI 3.0 (fichier `api-spec.yaml`)
- Hébergée sur `api.abidjangym.com/docs` (Swagger UI)
- Mise à jour à chaque modification d'endpoint

### 7.3 Entité C — DevOps (stack recommandée)

**Gestion de code source :**
- GitHub (organisation `Labozeze`, dépôt `Labozeze/abidjangym`)
- Branches : `main` (production), `develop` (intégration), `feature/*`, `hotfix/*`
- Protection de la branche `main` : aucun push direct, PR obligatoire, 1 reviewer minimum

**CI/CD (GitHub Actions) :**

```yaml
# Déploiement automatique sur Hostinger via FTP
# Déclencheur : push sur la branche main ou develop
# Étapes : lint → tests → build → déploiement FTP
```

- Lint et tests exécutés à chaque Pull Request (bloquants si échec)
- Build du frontend déclenché sur chaque PR vers `main`
- Déploiement automatique sur `staging.abidjangym.com` depuis `develop`
- Déploiement en production depuis `main` uniquement après validation humaine

**Tests :**
- Unitaires backend : PHPUnit (Laravel) — couverture cible ≥ 70%
- Intégration API : Postman Collection + Newman (exécuté en CI)
- E2E Front Office : Playwright — scénarios principaux (onboarding, affichage landing, formulaire contact)
- Sécurité : OWASP ZAP (scan passif) en CI — rapport d'alertes à chaque déploiement staging

**Monitoring :**
- Uptime : UptimeRobot (plan gratuit, vérification toutes les 5 minutes)
- Alertes : email + WhatsApp en cas d'indisponibilité > 2 minutes
- Logs d'erreur : Laravel Telescope (désactivé en production) ou Bugsnag (plan free)

---

## 8. Exigences Non Fonctionnelles

| Exigence | Objectif | Responsable |
|---|---|---|
| Temps de réponse API | < 500ms pour 95% des requêtes | Entité B |
| Temps de chargement page | < 2,5s (LCP) sur mobile 4G | Entité A + C |
| Disponibilité | 99,5 % (hors maintenance planifiée) | Entité C |
| Sauvegarde | Backup quotidien de la BDD, rétention 30 jours | Entité C |
| Conformité données | Lois ivoiriennes sur la protection des données personnelles | Entité B + C |
| Capacité | Jusqu'à 10 000 abonnés par salle sans dégradation | Entité B |
| Multi-tenant | Isolation stricte des données entre salles | Entité B |
| Responsive | Mobile-first, compatible tablette (borne de scan) | Entité A + B |
| Mode hors-ligne partiel | Cache local des abonnements actifs pour le scan (PWA) | Entité A |
| Accessibilité | WCAG 2.1 niveau AA — contraste, navigation clavier, screen readers | Entité A |
| SEO | Score PageSpeed ≥ 80 mobile, métadonnées complètes | Entité A + C |

---

## 9. Agent Rédaction Article Blog

> Cet agent est créé et géré par l'**Entité C (DevOps & Tests)**. Sa mission est de documenter la collaboration multi-entités et de la partager sur le blog du client.

### 9.1 Mission

Rédiger un article de blog destiné à être publié sur **lelabodezeze.com/blog** à la fin du projet. L'article présente comment trois entités IA distinctes (Claude, DeepSeek, ChatGPT ou tout autre fournisseur utilisé) ont collaboré pour construire la plateforme AbidjanGym SaaS — de la conception au déploiement.

### 9.2 Style et format

L'article suit le style éditorial de Rufin Ismael Baroan, tel qu'illustré dans l'article de référence :
- **Ton :** première personne (le client raconte), direct, concret, honnête sur les limites
- **Longueur :** 1 500 à 2 500 mots (environ 8 minutes de lecture)
- **Format HTML ou Markdown** compatible avec WordPress ou le CMS du blog
- **Structure :**
  1. Accroche (contraste entre l'idée reçue et la réalité du projet)
  2. Pourquoi une équipe d'agents plutôt qu'un seul assistant
  3. Présentation de l'équipe : chaque entité (nom du fournisseur, outils utilisés, mission en une ligne)
  4. Le principe de fonctionnement (qui livre quoi, comment les agents se passent les fichiers)
  5. Les points de validation humaine (4 étapes où le client a décidé)
  6. Un moment où le client a refusé un livrable et ce que ça a appris
  7. Les étapes du projet (chronologique, avec ce que chaque entité a produit)
  8. Les leçons apprises (liste de phrases en gras, une par leçon)
  9. Conclusion et invitation pour les entreprises intéressées

### 9.3 Règles pour l'agent

- **Ne pas inventer de faits.** Si une information n'est pas disponible (ex : quel fournisseur a réellement été utilisé pour l'Entité A), marquer `[À COMPLÉTER]` et laisser le client remplir.
- **Citer des exemples concrets** tirés du projet (noms de fonctionnalités, décisions prises, outils réels).
- **Ne pas surestimer.** Mentionner honnêtement ce qui a bien fonctionné et ce qui a nécessité l'intervention humaine.
- **Pas de jargon technique inutile.** L'article cible un public de dirigeants de PME africaines, pas des développeurs.
- Les trois citations réutilisables de l'article de référence peuvent inspirer le ton : *"Les agents proposent, je décide"*, *"L'organisation compte plus que l'outil"*, *"L'IA ne remplace pas la décision."*

### 9.4 Livrables attendus de l'agent Blog

| Livrable | Format | Délai |
|---|---|---|
| Plan de l'article (sommaire détaillé) | Markdown | Avant rédaction → validation humaine requise |
| Brouillon complet de l'article | Markdown | Après validation du plan |
| Version HTML formatée | HTML (compatible WordPress) | Après validation du brouillon |
| 3 visuels suggérés | Descriptions pour Midjourney / Dall-E | Avec le brouillon |
| Métadonnées SEO de l'article | title, description, slug, tags | Avec la version HTML |

---

## 10. Flux Principaux

### 10.1 Inscription d'une nouvelle salle (onboarding)
```
Visiteur → landing.html → Clic "J'inscris ma salle"
  → Étape 1 : Informations de la salle (nom, adresse, quartier, téléphone, WhatsApp)
  → Étape 2 : Compte admin (prénom, nom, email, Mobile Money, mot de passe)
  → Étape 3 : Choix du plan SaaS (Starter / Essentiel / Pro)
  → Étape 4 : Récapitulatif + acceptation CGU + validation
  → API POST /api/v1/gyms (Entité B)
  → Email de confirmation + lien d'accès au backoffice
  → Redirection vers app.abidjangym.com/dashboard (compte activé)
  → Configuration guidée : profil salle, plans d'abonnement, modes de paiement
```

### 10.2 Inscription d'un nouvel abonné
```
Admin → /dashboard → "Nouvel abonné"
  → Formulaire : prénom, nom, téléphone, email (optionnel), photo (optionnel)
  → Choix du plan d'abonnement
  → Saisie du paiement : montant, mode (cash / Mobile Money / carte), référence
  → Génération : numéro de membre (auto), QR code (auto)
  → Notifications envoyées : WhatsApp bienvenue + QR code, email confirmation
  → Génération du reçu PDF
  → Redirection vers la fiche abonné
```

### 10.3 Renouvellement d'abonnement
```
Admin → /members/:id → "Renouveler"
  → Choix du plan (peut changer de formule)
  → Saisie du paiement
  → Mise à jour de l'abonnement (nouvelle date de début = lendemain de l'expiration OU aujourd'hui si déjà expiré)
  → Notification de confirmation envoyée à l'abonné
  → Génération du reçu PDF
```

### 10.4 Contrôle d'accès par QR code
```
Caissier → /scan (interface plein écran, caméra active)
  → Scan du QR code de l'abonné
  → Requête API GET /api/v1/access/:uuid
  → ✅ Accès autorisé :
      - Affichage : nom, photo, type d'abonnement, jours restants — fond vert
      - Enregistrement du passage en base de données
      - [Optionnel] Appel webhook domotique (ouverture de porte)
  → ❌ Accès refusé :
      - Affichage : motif (expiré / suspendu / QR invalide) — fond rouge
      - Aucun enregistrement de passage
```

### 10.5 Envoi d'un paiement Mobile Money
```
Admin → Fiche abonné → "Enregistrer un paiement" → Mode : Orange Money
  → Saisie du montant
  → Appel API Orange Money (initiation de la collecte)
  → L'abonné reçoit une notification USSD sur son téléphone
  → L'abonné confirme le paiement
  → Callback Orange Money → API POST /api/v1/payments/callback/orange
  → Enregistrement du paiement (statut : Confirmé)
  → Génération et envoi du reçu (WhatsApp + Email)
```

---

## 11. Glossaire

| Terme | Définition |
|---|---|
| **Tenant** | Une salle de gym cliente utilisant la plateforme (chaque salle est un tenant isolé) |
| **Abonné / Membre** | Client d'une salle inscrit sur la plateforme |
| **Plan** | Type d'abonnement proposé par une salle (mensuel, hebdo, journalier, pack) |
| **Pack de jours** | Abonnement avec un nombre fixe de séances utilisables dans une fenêtre de temps |
| **QR Code abonné** | Code unique permettant d'identifier et valider l'accès d'un abonné |
| **Domotique** | Système automatisé d'ouverture de porte/tourniquet intégré via webhook HTTP |
| **Reçu** | Document de preuve de paiement généré automatiquement par la plateforme |
| **Caissier** | Employé de la salle ayant un accès limité (scan, paiements, reçus) |
| **Multi-tenant** | Architecture où un seul système sert plusieurs clients (salles) en isolation complète |
| **JWT** | JSON Web Token — format standard de token d'authentification |
| **Mobile Money** | Paiement via téléphone mobile (Orange Money, MTN MoMo, Wave) |
| **SSG** | Static Site Generation — génération des pages HTML au moment du build (SEO-friendly) |
| **SSR** | Server-Side Rendering — génération des pages à la demande côté serveur |
| **CDN** | Content Delivery Network — réseau de distribution de contenu (ex : Cloudflare) |
| **OWASP** | Open Web Application Security Project — référence pour la sécurité des applications web |
| **Entité A/B/C** | Les trois équipes (IA ou humaines) responsables du Frontend, Backend et DevOps |

---

*Document rédigé par Le Labo de Zézé pour AbidjanGym — Version 2.0 — Octobre 2026*  
*Ce document est la référence unique de toutes les entités. Toute évolution doit être validée par Rufin Ismael Baroan (client) et consignée dans le changelog ci-dessous.*

### Changelog

| Version | Date | Auteur | Changements |
|---|---|---|---|
| 1.0 | Oct. 2026 | Le Labo de Zézé | Version initiale |
| 2.0 | Oct. 2026 | Le Labo de Zézé | Ajout : organisation multi-entités, hébergement Hostinger, SEO, Sécurité, Agent Blog, stack technique par entité |
| 2.1 | 30 oct. 2026 | Benjamin (Entité C), validé par Rufin I. Baroan | §7.3 : organisation GitHub corrigée (`Labozeze` au lieu de `lelabodeze`). L'organisation des entités et le mode de déploiement sont détaillés dans `docs/gouvernance/plan-de-collaboration.md` v1.0 |
| 2.2 | 1er nov. 2026 | Benjamin (Entité C), décision de Rufin I. Baroan | §4.2.6 : activation on/off de chaque moyen de paiement par le gérant, seuls les moyens activés sont proposés (décision prise lors de la relecture de la FAQ, PR #22, ticket #23) |
| 2.3 | 1er nov. 2026 | Benjamin (Entité C), décision de Rufin I. Baroan | §3.1 et §7.2 : **Laravel 13 (≥ 13.30) et PHP 8.3** au lieu de Laravel 11 / PHP 8.2. Laravel 11 n'est plus maintenu et `composer audit` y relève 5 failles, dont une critique (CVE-2026-114509), sans correctif dans cette branche (signalé par DeepSeek, PR #28, confirmé par Eric, ticket #30) |
