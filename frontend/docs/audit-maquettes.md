# Audit des maquettes publiques AbidjanGym

**Périmètre :** `demo/landing.html`, `demo/contact.html` et `demo/onboarding.html`, comparés au cahier des charges (`livrables/cahier-des-charges.md`, sections 1.3, 4.1.1 à 4.1.5, 5 et 8). Cet audit porte sur le HTML et les styles visibles dans les maquettes. Il ne remplace pas une vérification du site dans un navigateur ni des tests clavier et lecteur d’écran.

## Synthèse

Les maquettes couvrent une grande partie de la landing, le formulaire de contact et les quatre étapes d’inscription. Les principaux points à résoudre avant le développement sont l’absence de FAQ, les pages sans maquette, les métadonnées SEO manquantes et plusieurs informations commerciales à faire valider. Les constats sont classés par priorité : **P1** à régler ou valider avant développement, **P2** à traiter pendant la conception et l’intégration, **P3** à confirmer ou peaufiner.

| Priorité | Écart principal | Correction proposée |
|---|---|---|
| P1 | Aucune FAQ sur la landing, alors que le CDC demande au moins 8 questions. | Prévoir un bloc FAQ avec au moins 8 questions et réponses validées. |
| P1 | La landing affiche plusieurs fois 14 jours d’essai alors que le CDC §1.3 fixe 2 mois ; l’onboarding indique 2 mois. | Remplacer partout la durée de 14 jours par 2 mois et vérifier la cohérence des mentions. |
| P1 | Plusieurs chiffres, témoignages et affirmations commerciales sont présentés comme réels sans source dans le CDC. | Faire valider chaque contenu par Rufin ou le remplacer par du contenu confirmé. |
| P1 | Les pages légales et l’espace abonné n’ont pas de maquette. | Faire valider ces parcours et leurs contenus avant leur réalisation. |
| P2 | Les exigences SEO sont largement absentes des `<head>` des maquettes. | Ajouter les métadonnées et données structurées prévues par le CDC dans les pages finales. |
| P2 | Les libellés de formulaires ne sont généralement pas reliés aux champs ; plusieurs contrôles n’indiquent pas leur état requis de manière native. | Associer chaque libellé à son champ, utiliser les attributs HTML adaptés et vérifier le clavier et le lecteur d’écran. |
| P2 | Le formulaire de contact simule un succès, sans formulaire HTML ni envoi API visible. | Concevoir un vrai formulaire et le relier à l’API conformément au contrat. |
| P3 | Une page Tarifs distincte est demandée dans le périmètre frontend, mais seule une section de tarifs existe sur la landing. | Confirmer si une page dédiée est attendue et faire valider son contenu. |

## 1. Landing — CDC §4.1.1

### Exigences générales

| Exigence du CDC | État | Constat et proposition |
|---|---|---|
| Proposition de valeur compréhensible en moins de 5 secondes | Conforme | Le titre, le sous-titre et le visuel du hero présentent immédiatement la gestion de salle à Abidjan. Le temps de compréhension reste à vérifier auprès d’utilisateurs. |
| Fonctionnalités clés illustrées | Conforme | Six fonctionnalités sont présentées avec icônes, titres et descriptions. |
| Trois formules tarifaires avec CTA | Écart | Starter, Essentiel et Pro sont affichées avec un bouton chacune. Les montants (Starter gratuit, Essentiel 15 000 FCFA/mois, Pro 35 000 FCFA/mois) sont conformes au CDC §1.3. Les mentions de 14 jours sont en écart ; les avantages et limites détaillés restent à valider. |
| Témoignages de gérants | Écart | Plusieurs témoignages sont visibles. Le CDC ne fournit pas ces témoignages ni les identités et résultats associés : faire valider leur authenticité avant publication. |
| Section FAQ | Absent | Aucune section de questions-réponses n’est présente. Ajouter au moins 8 questions, comme demandé par le CDC. |
| CTA « J’inscris ma salle » en haut et en bas | Conforme | Le CTA figure dans la navigation et le hero, puis dans le CTA final. D’autres CTA apparaissent au fil de la page. |
| Responsive, mobile-first | Conforme | Des règles CSS adaptées à plusieurs largeurs sont présentes. La qualité du rendu mobile reste à vérifier visuellement. |
| Exigences SEO (§5) | Écart | Voir la section « SEO » de cet audit : plusieurs éléments obligatoires manquent dans les maquettes. |

### Les dix éléments de la structure minimale

| # | Élément demandé | État | Constat et proposition |
|---|---|---|---|
| 1 | Navigation fixe, logo, liens et CTA | Conforme | Navigation fixe avec logo, liens vers les sections et CTA d’inscription. |
| 2 | Hero avec titre, sous-titre, CTA primaire et image | Conforme | Les quatre éléments sont présents. L’image a un texte alternatif descriptif. |
| 3 | Preuves sociales : logos partenaires ou chiffres clés | Écart | Un bandeau de chiffres est présent. Les valeurs affichées ne sont pas étayées par le CDC : les confirmer ou les retirer. |
| 4 | Six fonctionnalités avec icônes et descriptions | Conforme | Six cartes sont présentes et comprennent chacune une icône, un titre et une description. |
| 5 | Section « Pour les gérants » et bénéfices business | Écart | Les sections sur le contrôle des abonnés et la gestion donnent des bénéfices, sans bloc clairement identifié « Pour les gérants ». Regrouper et nommer explicitement ces bénéfices. |
| 6 | Trois cartes tarifaires Starter / Essentiel / Pro | Conforme | Les trois cartes et CTA sont présents. Les montants affichés correspondent au CDC §1.3. Faire valider les limites et avantages détaillés, non précisés par le CDC. |
| 7 | Témoignages | Écart | Les cartes sont présentes, mais les noms, salles, citations et résultats nécessitent validation avant publication. |
| 8 | FAQ en accordéon, au moins 8 questions | Absent | Aucun contenu FAQ n’a été trouvé. Ajouter le bloc et prévoir une interaction accessible au clavier. |
| 9 | CTA final | Écart | Un bloc de fin de page propose l’inscription, mais répète la durée d’essai de 14 jours. Remplacer cette mention par 2 mois, conformément au CDC §1.3, comme dans le reste de la maquette et l’onboarding. |
| 10 | Pied de page avec liens, légal et réseaux sociaux | Écart | Le pied de page comprend les rubriques Produit, Support, Légal et des réseaux sociaux. Plusieurs liens pointent vers `#` et ne mènent donc à aucune page. Relier les destinations validées. |

## 2. Contact — CDC §4.1.2

| Exigence du CDC | État | Constat et proposition |
|---|---|---|
| Champs prénom, nom, email, téléphone, objet et message | Conforme | Les six champs demandés sont représentés. Voir les observations d’accessibilité plus bas pour l’association des libellés et la sémantique du formulaire. |
| Envoi d’email via le backend / API | Écart | Le bouton déclenche un message de succès côté navigateur. Aucun élément de la maquette ne démontre un envoi réel. Prévoir l’intégration à l’API sans inventer d’endpoint. |
| Coordonnées WhatsApp, email et adresse à Abidjan | Écart | Les trois types de coordonnées sont affichés. Le lien WhatsApp pointe sur `#` ; sa destination doit être fournie et validée. |
| Délai de réponse inférieur à 24 h | Conforme | La page annonce une réponse en moins de 24 h et répète cette promesse après l’envoi. Faire confirmer ce délai avant publication. |

## 3. Pages absentes des maquettes

### Tarifs

| Élément | État | Constat et proposition |
|---|---|---|
| Page Tarifs dédiée | Absent | La landing contient une section avec trois formules, mais aucune maquette de page `/tarifs`. Le CDC cite une page Tarifs dans ses mots-clés SEO (§5.4) et le périmètre frontend prévoit cette page. Confirmer si une page autonome est attendue et faire valider son contenu. |

### Pages légales — CDC §4.1.3

| Page | État | Constat et proposition |
|---|---|---|
| Conditions Générales d’Utilisation (`/cgu`) | Absent | Aucun écran de page légale. Prévoir le contenu validé avant mise en production. |
| Politique de Confidentialité (`/confidentialite`) | Absent | Aucun écran de page légale. Prévoir le contenu validé avant mise en production. |
| Mentions légales (`/mentions-legales`) | Absent | Aucun écran de page légale. Prévoir le contenu validé avant mise en production. |
| Politique de cookies (`/cookies`) | Absent | Aucun écran de page légale. Prévoir le contenu validé avant mise en production. |

Les liens « Confidentialité », « Conditions d’utilisation » et « Cookies » du pied de page de la landing pointent actuellement sur `#`. Ils ne remplacent pas ces pages.

## 4. Onboarding — CDC §4.1.4

| Étape et exigence du CDC | État | Constat et proposition |
|---|---|---|
| Étape 1 — Nom de la salle | Conforme | Champ présent et marqué comme requis dans la maquette. |
| Étape 1 — Quartier / commune | Conforme | Liste de quartiers et communes présente ; champ marqué comme requis. |
| Étape 1 — Adresse | Conforme | Champ d’adresse complète présent et marqué comme requis. |
| Étape 1 — Téléphone | Conforme | Champ téléphone présent et marqué comme requis. |
| Étape 1 — WhatsApp | Écart | Champ présent, sans indication de caractère obligatoire. Faire confirmer s’il est requis et afficher cet état de façon cohérente. |
| Étape 1 — Champ supplémentaire site web / Facebook | Écart | Champ facultatif ajouté, absent du CDC. Confirmer son utilité ; le retirer s’il n’est pas nécessaire. |
| Étape 2 — Prénom et nom de l’administrateur | Conforme | Les deux champs sont présents et indiqués comme requis. |
| Étape 2 — Email | Conforme | Champ présent et indiqué comme requis. |
| Étape 2 — Numéro Mobile Money | Conforme | Champ présent. Le CDC ne précise pas s’il est obligatoire ; clarifier le caractère requis avant l’intégration. |
| Étape 2 — Mot de passe | Conforme | Mot de passe et confirmation sont présents et indiqués comme requis. |
| Étape 3 — Choix Starter / Essentiel / Pro | Conforme | Les trois formules sont proposées et l’essai de 2 mois est indiqué, conformément au CDC §1.3. Les montants affichés correspondent au CDC ; faire valider les limites et avantages détaillés. |
| Étape 3 — Résumé de la formule | Conforme | Le récapitulatif de formule apparaît à l’étape suivante. |
| Étape 4 — Récapitulatif et acceptation des CGU | Conforme | Un récapitulatif de salle et de formule ainsi qu’une case d’acceptation sont présents. Les liens légaux associés doivent pointer vers les pages réelles. |
| À la validation — création du compte via l’API | Écart | Le script de la maquette affiche un succès local ; il ne démontre pas une création de compte via API. Connecter au contrat d’API validé. |
| À la validation — email de confirmation et redirection vers le backoffice | Écart | Un écran affirme qu’un email a été envoyé et propose un lien vers un tableau de bord de démonstration. Le scénario réel n’est pas démontré. Confirmer le parcours avec l’API et l’Entité B. |

## 5. Espace abonné — CDC §4.1.5

| Exigence du CDC | État | Constat et proposition |
|---|---|---|
| Connexion par email ou lien magique envoyé par WhatsApp / SMS | Absent | Aucun écran de connexion abonné dans les trois maquettes. Concevoir et faire valider le parcours. |
| Profil : nom, photo et numéro de membre | Absent | Aucun écran de profil abonné. |
| Abonnement actif : type, expiration et jours restants | Absent | Aucun écran de suivi d’abonnement. |
| QR code en grand et téléchargement PNG | Absent | Aucun écran de QR code abonné. |
| Historique des paiements avec mode et reçu PDF | Absent | Aucun écran d’historique ou de téléchargement de reçu. |
| Historique des entrées | Absent | Aucun écran d’historique des visites. |

## 6. SEO repérable dans les maquettes — CDC §5

| Exigence | État | Constat et proposition |
|---|---|---|
| `<html lang="fr">` | Conforme | Présent sur les trois fichiers. |
| Titre unique de 50 à 60 caractères | Écart | Un `<title>` est présent sur chaque fichier, mais les trois titres visibles sont plus courts que la longueur demandée. Réécrire un titre distinct de 50 à 60 caractères pour chaque page finale. |
| Description unique de 120 à 160 caractères | Absent | Aucune méta-description n’est présente. En rédiger une par page. |
| Open Graph | Absent | Les métadonnées `og:*` ne sont pas présentes. Les ajouter aux pages publiables. |
| Twitter Card | Absent | Les métadonnées Twitter ne sont pas présentes. Les ajouter aux pages publiables. |
| URL canonique | Absent | Aucun lien canonical n’est présent. Définir une URL canonique par page. |
| Hreflang si le site devient multilingue | Absent | Aucun hreflang n’est présent. Le CDC le conditionne à une extension multilingue future ; à ajouter si cette extension est décidée. |
| Un seul `<h1>` par page | Écart | Landing et contact ont chacune un `<h1>`. L’onboarding n’en a pas : son titre principal est un `<h2>`. Ajouter un H1 pertinent sur cette page. |
| Hiérarchie des titres sans saut | Écart | L’onboarding utilise un H2 dans le panneau latéral et les titres des étapes sont des blocs `div`. Structurer les étapes comme des titres. Sur la landing, les titres de pied de page sont des H4 après des H2 : revoir leur niveau. |
| Texte alternatif descriptif pour chaque image | Conforme | Les images repérées dans la landing et la page contact ont un attribut `alt` descriptif. Aucune image `<img>` n’est présente dans l’onboarding. |
| Liens internes avec une ancre descriptive | Écart | Plusieurs liens du pied de page et le lien WhatsApp pointent sur `#`. Leur destination ou leur libellé doit être finalisé avant publication. |
| URL propres | Écart | Les liens des prototypes pointent vers des fichiers HTML (`landing.html`, `contact.html`, `onboarding.html`), pas vers les URL finales propres du CDC. À transformer dans le site final. |
| Données structurées Organisation et WebSite | Absent | Aucun JSON-LD n’est présent dans les maquettes. Prévoir ces données sur les pages finales. |
| Données structurées FAQPage sur la landing | Absent | Pas de FAQ ni de données FAQPage. Ajouter les deux après validation du contenu FAQ. |
| Fil d’Ariane BreadcrumbList sur les pages intérieures | Absent | Aucun fil d’Ariane ni BreadcrumbList n’est présent. Prévoir cet élément sur les pages intérieures concernées. |
| Sitemap, robots.txt, favicon, icônes Apple et manifeste PWA | Absent | Ces fichiers ne figurent pas dans les maquettes. Ils devront être prévus dans le site généré ; le HTML seul ne permet pas de valider leur livraison. |
| Core Web Vitals et score PageSpeed | Écart | Ces résultats ne se déduisent pas du prototype source. Mesurer la page générée sur mobile et bureau avant livraison. |
| Lazy loading des images hors écran | Écart | Les balises `<img>` repérées n’ont pas `loading="lazy"`. Ajouter le chargement différé aux images hors premier écran, en conservant le chargement immédiat du visuel principal. |

## 7. Accessibilité repérable — CDC §8

| Point examiné | État | Constat et proposition |
|---|---|---|
| Libellés de formulaire associés aux champs | Écart | Sur Contact et dans les étapes 1 et 2 de l’onboarding, les `<label>` ne comportent pas de `for` correspondant et les champs n’ont pas tous d’identifiant. Lier chaque libellé à son champ. La case CGU, elle, possède un libellé associé. |
| Formulaire Contact sémantique | Écart | Les champs et le bouton sont contenus dans des `div`, sans élément `<form>` visible. Utiliser un formulaire HTML avec un bouton de soumission et des états de validation compréhensibles. |
| Navigation au clavier et ordre de focus | Écart | Le code montre une navigation d’étapes déclenchée par boutons, mais le stepper est composé de `div` sans rôle ni état accessible visibles. Exposer l’étape courante et vérifier tout le parcours au clavier. |
| Indicateur de focus | Écart | Les champs ont un style `:focus` qui change leur bordure. Un indicateur clavier visible et suffisamment contrasté doit être vérifié pour les liens, boutons et autres contrôles. |
| Contraste WCAG 2.1 AA | Écart | Le respect des contrastes ne peut pas être confirmé à la seule lecture du HTML. Mesurer les couleurs du design final et corriger celles qui échouent. |
| Boutons avec icône seule | Conforme | Aucun bouton sans texte discernable n’a été relevé dans les actions principales. Vérifier les icônes des composants finaux lors de leur intégration. |
| Alternatives aux images | Conforme | Les images photo repérées ont un texte alternatif ; vérifier qu’il reste pertinent dans le contexte de la page finale. |
| Réduction des animations | Écart | La landing contient des animations et défilements animés ; aucune règle `prefers-reduced-motion` n’a été repérée. Respecter la préférence de réduction des animations. |

## Décisions à faire valider

1. Aucune décision à prendre sur la durée : le CDC §1.3 fixe 2 mois. Remplacer les occurrences « 14 jours » sur la landing.
2. Limites et avantages détaillés des formules Starter, Essentiel et Pro ; les montants sont fixés par le CDC §1.3.
3. Authenticité des chiffres, témoignages, identités et affirmations de performance affichés.
4. Contenu des huit questions FAQ au minimum et décision sur une page Tarifs distincte.
5. Contenus légaux et coordonnées de contact, dont la destination WhatsApp.
6. Parcours et contenu de l’espace abonné, ainsi que confirmation des champs facultatifs de l’onboarding.
