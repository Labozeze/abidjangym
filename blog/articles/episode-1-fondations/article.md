# Trois IA, un seul projet : comment je fais construire mon SaaS par une équipe d'agents (épisode 1)

*Temps de lecture : environ 8 minutes*

On entend souvent qu'il suffit aujourd'hui de demander à une IA « crée-moi une application » pour la voir apparaître. J'ai voulu vérifier ce que ça donne sur un vrai projet, avec de vrais clients au bout. Ma conclusion après la première semaine : l'IA écrit vite, très vite même, mais **ce qui fait avancer le projet, c'est l'organisation autour d'elle**. Et les décisions restent les miennes.

Le projet s'appelle **AbidjanGym**. C'est un logiciel en ligne pour les gérants de salles de sport en Côte d'Ivoire : gestion des abonnés et des abonnements, paiements Mobile Money (Orange Money, MTN, Wave), contrôle d'accès par QR code à l'entrée, rappels par WhatsApp et SMS. Je n'en suis pas à la mise en ligne. Cet article raconte **la première étape : poser les fondations**.

## Pourquoi une équipe d'agents plutôt qu'un seul assistant

J'aurais pu tout confier à une seule IA. J'ai fait un autre choix : **répartir le travail entre trois IA de fournisseurs différents**, comme on le ferait avec trois prestataires.

- **Codex** (OpenAI) s'occupe de tout ce que voit le visiteur : la page d'accueil, l'inscription d'une salle, l'espace des abonnés.
- **DeepSeek** s'occupe du « moteur » : les données, les paiements, la sécurité, les connexions.
- **Claude** (Anthropic) joue le rôle de l'équipe de pilotage. Je lui ai demandé de se répartir en quatre agents, chacun avec un nom et une mission précise :
  - **Benjamin**, le chef de projet, découpe le travail en tâches et vérifie que tout respecte mon cahier des charges ;
  - **Eric** s'occupe de l'infrastructure : le dossier partagé du projet, les contrôles automatiques, la mise en ligne ;
  - **Valère** s'occupe des tests ;
  - **César** tient le journal du projet et rédige… cet article.

Au départ, les tests étaient confiés à Eric. J'ai préféré un agent séparé pour cette mission, et Valère est né. Je ne le regrette pas : c'est lui qui a attrapé plusieurs erreurs, y compris une erreur d'Eric.

Pourquoi trois fournisseurs ? Au **Labo de Zézé**, comme son nom l'indique, on fait des expériences : nous testons tout ce qui touche à l'intelligence artificielle. Faire collaborer trois fournisseurs différents sur un même projet fait partie de ces expériences. La question est simple : des IA concurrentes, qui ne se parlent pas, peuvent-elles construire ensemble un logiciel cohérent ?

## Le principe : un seul lieu de travail, et c'est moi qui décide

Premier obstacle : ces trois IA **ne peuvent pas se parler entre elles**. Sans organisation, j'aurais passé mes journées à copier-coller des messages de l'une à l'autre.

La solution proposée par Benjamin tient en une phrase : **le dossier partagé du projet est la seule salle de réunion**. Ce dossier est en ligne, sur GitHub. Tout ce qu'une IA doit savoir y est écrit, et tout ce qu'elle produit y revient.

Concrètement, une tâche suit toujours le même chemin :

1. Benjamin écrit un **ticket** : ce qu'il faut faire, pourquoi, et comment on saura que c'est réussi.
2. J'ouvre l'outil de l'IA concernée et je lui écris une seule phrase, du type « traite le ticket n° 9 ».
3. L'IA fait le travail et le dépose dans le dossier partagé. Une **demande de validation** s'ouvre automatiquement.
4. Des contrôles automatiques vérifient le travail : pas de mot de passe oublié dans le code, pas de fichier modifié hors du domaine de l'IA, tests qui passent.
5. Benjamin et Valère relisent et laissent leurs commentaires. Si quelque chose ne va pas, je relance l'IA avec « traite les commentaires ».
6. Quand ils écrivent « prêt pour le merge », **c'est moi qui valide**.

Un détail m'a frappé : Claude n'a même pas la possibilité de valider à ma place. Le garde-fou de son outil l'en empêche, et c'est très bien ainsi. **Les agents proposent, je décide.**

## Les points où j'ai décidé

Le cahier des charges prévoit des moments où rien n'avance sans moi. Cette semaine, j'en ai franchi deux.

**Les maquettes.** Codex a comparé les maquettes du site à mon cahier des charges et a relevé les écarts. J'ai pris plusieurs décisions, dont une qui me tient à cœur : **aucun chiffre ni témoignage inventé sur le site**. Les maquettes affichaient des statistiques et des avis de gérants qui n'existent pas encore. On les retire. Ils reviendront quand j'aurai de vrais clients.

**Le contrat de l'application.** C'est le document qui décrit précisément tout ce que l'application saura faire : 68 opérations, de la connexion d'un caissier au scan d'un QR code. Il permet à Codex et à DeepSeek de travailler en même temps sans s'attendre. DeepSeek a rédigé le contrat, et il m'a remonté sept questions plutôt que de trancher seul. Benjamin m'a présenté chacune avec une recommandation, en langage clair. J'ai validé.

D'autres décisions sont arrivées en chemin, parfois de façon inattendue. En relisant la foire aux questions destinée à mes futurs clients, j'ai réalisé qu'un gérant doit pouvoir **choisir les moyens de paiement qu'il accepte**. Chaque moyen s'active ou se désactive en un clic, et par défaut seules les espèces sont activées. En relisant un simple texte, j'ai fait naître une nouvelle fonctionnalité. Elle a été ajoutée au cahier des charges et au contrat le jour même.

J'ai aussi proposé une idée pour la mise en ligne : relier directement mon hébergement au dossier partagé. Eric l'a adaptée pour qu'aucun code non testé ne puisse arriver sur le serveur.

## Ce qui a été refusé, et ce que ça m'a appris

C'est la partie qui m'a le plus appris. **Sur dix livraisons de Codex et DeepSeek, quatre ont été renvoyées pour correction** avant d'être acceptées.

- **L'analyse des maquettes** me demandait de choisir la durée de l'essai gratuit. Or mon cahier des charges la fixait déjà : deux mois. Codex n'avait lu que les sections citées dans sa tâche. Benjamin l'a repéré, Codex a corrigé, et j'ai eu deux décisions inutiles en moins.
- **La foire aux questions** était juste sur le fond, mais écrite pour l'équipe technique, avec des phrases comme « Le CDC prévoit… » que mes clients ne comprendraient jamais. Elle a été réécrite du point de vue du gérant.
- **Le contrat** oubliait que le gérant doit pouvoir créer et désactiver ses caissiers, et laissait à tort un caissier modifier un paiement.
- **Le socle du « moteur »** permettait, dans un cas précis, de déplacer une donnée d'une salle vers une autre. Pour un logiciel où chaque salle doit rester totalement isolée des autres, c'était inacceptable.

Le cas le plus instructif concerne **l'équipe de pilotage elle-même**. En vérifiant le travail de DeepSeek, Valère a vu un test échouer. La cause ne venait pas de DeepSeek mais d'Eric : un fichier de configuration qu'il avait écrit au tout début empêchait certains dossiers indispensables d'être enregistrés. Sur le poste de DeepSeek, tout fonctionnait. Sur le serveur, le jour de la mise en ligne, tout aurait échoué. Eric a corrigé son erreur dans la foulée.

J'ai moi aussi commis une erreur : j'ai validé une livraison avant que Benjamin et Valère l'aient relue. Le contenu était bon, mais j'ai décidé ensuite d'attendre systématiquement leur feu vert. Une autre fois, j'ai validé avant que le dernier commentaire soit traité. Le point a été repris dans une petite tâche séparée. Rien de grave, mais la règle est maintenant claire pour moi aussi.

## Une alerte de sécurité venue d'une IA

L'épisode qui m'a le plus rassuré sur la méthode s'est produit en fin de semaine. En vérifiant une correction, **DeepSeek a lancé de lui-même un contrôle de sécurité** et a trouvé cinq failles connues dans la version de l'outil prévue par mon cahier des charges, dont une **critique**. Aucun correctif n'existera jamais pour cette version.

DeepSeek aurait pu changer de version tout seul. Il ne l'a pas fait : ce n'était pas dans sa tâche, et ça modifiait un choix de mon cahier des charges. **Il me l'a signalé.** Eric a vérifié l'alerte de son côté, Benjamin m'a présenté deux options avec une recommandation, et j'ai choisi la version la plus récente. La migration a été faite, revérifiée, et le contrôle ne signale plus aucune faille.

En creusant, Eric a même découvert que **mon propre ordinateur** téléchargeait certains composants sans chiffrement, probablement un ancien contournement d'un problème de configuration. Avec mon accord, il a trouvé la vraie cause, l'a corrigée et a mis à jour l'outil concerné, qui avait lui-même des failles graves.

## Les étapes de la première semaine

| Moment | Ce qui s'est passé |
|---|---|
| **Préparation** | Plan de collaboration, rôles des agents, choix des outils, mode de mise en ligne. Une erreur dans le nom de mon organisation sur GitHub est repérée et corrigée |
| **Jour 1** | Le dossier partagé, ses protections et ses contrôles automatiques sont en place. Codex et DeepSeek réussissent leur première livraison test |
| **Jour 2** | Maquettes validées. Contrat de l'application rédigé, corrigé et validé. Premières fondations du site et du « moteur » |
| **Fin de semaine** | Alerte de sécurité, passage à la version récente, contrôles automatiques renforcés sur chaque livraison |

Le bilan : la première étape était prévue sur une semaine, elle a été **bouclée en deux jours, avec quatre jours d'avance**. Au total, 16 tâches terminées, 15 livraisons validées, et les deux points de décision franchis.

Côté calendrier, **une journée** a suffi pour définir l'organisation et les rôles des agents, puis **deux jours** pour cette première étape. Pendant ces deux jours, mon rôle a été de lancer chaque IA d'une phrase, de lire les résumés de relecture, de prendre une dizaine de décisions et de faire quelques réglages sur mon ordinateur et sur GitHub. Je n'ai pas écrit une seule ligne de code.

## Ce que je retiens

**L'organisation compte plus que l'outil.** Trois IA différentes travaillent ensemble sans se parler, parce que les règles sont écrites et partagées.

**Les relectures croisées valent de l'or.** Quatre livraisons sur dix ont été corrigées avant d'entrer dans le projet, y compris une erreur de l'équipe de pilotage.

**Une bonne IA signale plutôt que d'improviser.** Le jour où DeepSeek a trouvé une faille critique, il m'a prévenu au lieu de changer seul un choix important.

**Rien n'est inventé.** Quand une information manque, les agents écrivent « à fournir » et attendent. C'est moins spectaculaire, mais c'est ce qui rend le résultat fiable.

**L'IA ne remplace pas la décision.** Chaque point important est passé par moi, résumé en langage clair, avec une recommandation et une explication.

## Et maintenant ?

Le sprint 0 est bouclé. Les fondations tiennent. Mais des fondations, ça ne se voit pas.

Le prochain épisode change de registre : on construit ce que les futurs clients verront en premier — la page d'accueil, le formulaire d'inscription d'une salle, la connexion des gérants. Et pour la première fois, le résultat sera en ligne sur un site de démonstration accessible à tous.

La vraie question que je me pose : est-ce que trois IA qui ne se sont jamais parlé vont produire quelque chose de cohérent quand on les assemble pour la première fois ?

Réponse dans l'épisode 2.

---

*Vous pilotez un projet similaire, ou vous avez des questions sur la méthode ? Écrivez-moi sur [WhatsApp](https://wa.me/2250757816452) ou par [e-mail](mailto:contact@lelabodezeze.com) — je lis tout.*
