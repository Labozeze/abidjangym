# Test manuel du staging AbidjanGym : parcours public

**À exécuter par :** Claude for Chrome
**Site à tester :** `https://staging.abidjangym.com` uniquement
**Référence visuelle :** maquettes `demo/landing.html`, `demo/contact.html`, `demo/onboarding.html` du dépôt `Labozeze/abidjangym`

---

## Consignes pour l'agent

1. Teste **uniquement** les pages ci-dessous, sur `staging.abidjangym.com`. Ne va sur aucun autre site.
2. Utilise **uniquement** les données fictives de ce document. N'utilise aucune donnée réelle (nom, email, téléphone).
3. **Ne clique pas** sur le lien vers l'espace d'administration de l'écran de bienvenue : il mène à une page 404 connue, car le backoffice n'existe pas encore.
4. Ne modifie rien et ne corrige rien. Tu observes et tu rapportes.
5. Si un test échoue, note ce qui s'est passé et **continue** avec le test suivant.
6. Fais tous les tests **en largeur mobile** (375 px environ), sauf indication contraire. Le téléphone est l'accès principal des visiteurs.
7. Le site bloque après trop d'essais (« Trop de tentatives »). Si ce message apparaît, attends 1 minute et reprends. Ce n'est pas une anomalie.
8. Remplis le rapport à la fin de ce document.

### Données de test

| Champ | Valeur |
|---|---|
| Nom de la salle | `Salle Test Chrome` |
| Quartier / Commune | le premier choix de la liste |
| Adresse | `Rue de test, Abidjan` |
| Téléphone et WhatsApp | `0700000061` |
| Prénom | `Test` |
| Nom | `Chrome` |
| Email | `test-chrome-<HEURE>@example.com`, en remplaçant `<HEURE>` par l'heure actuelle sans séparateur (ex. `test-chrome-1432@example.com`). Note cette adresse : elle resservira au test 2.3 |
| Numéro Mobile Money | `0700000062` |
| Mot de passe faible | `faible` |
| Mot de passe valide | `Motdepasse1` |

---

## 1. Page d'accueil : `/`

| # | Action | Résultat attendu |
|---|---|---|
| 1.1 | Ouvre `https://staging.abidjangym.com/` | Le titre « GÉREZ VOTRE SALLE AUTREMENT » s'affiche en grandes majuscules italiques, avec « AUTREMENT » en lettres contour |
| 1.2 | Fais défiler toute la page | Les éléments apparaissent progressivement. Un bandeau de texte défile. Aucun élément n'est coupé ni écrasé |
| 1.3 | Regarde les sections | Présentes : Fonctionnalités (6 cartes numérotées), « La salle, enfin sous contrôle », « Toujours en contrôle », Tarifs, « Tout sous vos yeux », FAQ, bandeau final |
| 1.4 | Vérifie les retraits décidés | **Aucun** bandeau de chiffres (233+, 93 %…), **aucune** section de témoignages, **aucune** mention « 14 jours » ou « 13 jours » |
| 1.5 | Section « Tout sous vos yeux » | Sur mobile : **l'image d'abord, puis le texte**, sur une seule colonne |
| 1.6 | Carte Starter de la section Tarifs | « Notifications WhatsApp » et « Rapports avancés » sont **grisés avec une croix** (non inclus) |
| 1.7 | FAQ | 12 questions. Chacune s'ouvre et se ferme |
| 1.8 | Thème | La page est en **thème clair**, même si le navigateur ou le téléphone est réglé en mode sombre |
| 1.9 | Installation | Le navigateur **ne propose pas** d'installer une application |

---

## 2. Inscription : `/inscription/`

| # | Action | Résultat attendu |
|---|---|---|
| 2.1 | Ouvre `https://staging.abidjangym.com/inscription/`. Sans rien remplir, appuie sur le bouton pour continuer | Un message d'erreur en français apparaît, et tu restes à l'étape 1 |
| 2.2 | Remplis l'étape 1 avec les données de test, puis continue. À l'étape 2, utilise le mot de passe faible et continue | L'étape 2 s'affiche, puis le mot de passe faible est **refusé** avec un message en français |
| 2.3 | Saisis le mot de passe valide dans les deux champs, puis continue | L'étape 3 (formules) s'affiche |
| 2.4 | Regarde les 3 formules | Starter : « 50 abonnés max · QR code · Mobile Money ». Essentiel : « Abonnés illimités · WhatsApp · Rapports ». Pro : « Multi-salles · API · Support prioritaire » |
| 2.5 | Choisis **Essentiel**, puis continue | Le récapitulatif reprend correctement la salle et la formule |
| 2.6 | Coche les conditions, puis valide | L'écran « Bienvenue sur AbidjanGym ! 🎉 » s'affiche. **Ne clique pas sur le lien vers l'espace** |
| 2.7 | Recharge `/inscription/` et refais tout le parcours **avec le même email** | À la validation, tu es **ramené à l'étape 2** avec le message « Cette adresse email est déjà utilisée », et tes saisies sont conservées |
| 2.8 | Remplace l'email par une nouvelle adresse (ajoute `b` avant le `@`), puis termine le parcours | L'écran de bienvenue s'affiche |

---

## 3. Tarifs : `/tarifs/`

| # | Action | Résultat attendu |
|---|---|---|
| 3.1 | Ouvre `https://staging.abidjangym.com/tarifs/` | Les 3 formules s'affichent : Starter gratuit, Essentiel 15 000 FCFA, Pro 35 000 FCFA |
| 3.2 | Carte Starter | « Notifications WhatsApp » et « Rapports avancés » sont **grisés avec une croix**, comme sur la page d'accueil |
| 3.3 | Mention de l'essai | « 2 mois », jamais « 14 jours » |

---

## 4. Contact : `/contact/`

| # | Action | Résultat attendu |
|---|---|---|
| 4.1 | Ouvre `https://staging.abidjangym.com/contact/` | Le titre « PARLONS DE VOTRE SALLE » s'affiche |
| 4.2 | Regarde les coordonnées | `contact@abidjangym.com`, « Cocody, Abidjan », mention des démos sur site. **Aucun crochet** du type « [à fournir] ». Le bouton WhatsApp est désactivé (numéro pas encore fourni) |
| 4.3 | Envoie le formulaire vide | Des messages d'erreur en français s'affichent |
| 4.4 | Remplis : prénom `Test`, nom `Chrome`, email `test-contact@example.com`, téléphone `+2250700000063`, un objet au choix, message `Message de test`. Envoie | « Message envoyé ! Merci, on vous revient dans les 24h. » s'affiche |

---

## 5. Contrôle sur grand écran

Passe en largeur ordinateur (1280 px environ).

| # | Action | Résultat attendu |
|---|---|---|
| 5.1 | Page d'accueil, section « Tout sous vos yeux » | Deux colonnes : image à gauche, texte à droite |
| 5.2 | Pages `/tarifs/`, `/contact/`, `/inscription/` | Mise en page correcte, rien de coupé ni de superposé |

---

## Rapport à remplir

Résume d'abord en une phrase, puis complète ce tableau. Mets ✅, ❌ ou ⚠️ (doute).

| Test | Résultat | Observation |
|---|---|---|
| 1.1 à 1.9 | | |
| 2.1 à 2.8 | | |
| 3.1 à 3.3 | | |
| 4.1 à 4.4 | | |
| 5.1 et 5.2 | | |

**Pour chaque ❌ ou ⚠️, indique :**
- le numéro du test ;
- ce qui était attendu ;
- ce qui s'est réellement passé ;
- la page et la largeur d'écran ;
- une capture, si possible.

**Autres remarques :** tout ce qui te semble difficile à lire, mal placé ou confus sur mobile.
