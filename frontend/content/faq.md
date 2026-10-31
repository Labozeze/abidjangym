# Proposition de FAQ — AbidjanGym

> Proposition à faire valider par Rufin avant intégration à la landing page.

<!-- CDC §1.3 -->
## Quels sont les prix des formules ?

Starter est gratuit. Essentiel coûte 15 000 FCFA par mois et Pro coûte 35 000 FCFA par mois. Les avantages détaillés et les limites de chaque formule sont [À FOURNIR].

<!-- CDC §1.3 -->
## Puis-je essayer AbidjanGym avant de payer ?

Les formules payantes comprennent deux mois d’essai gratuit, sans carte bancaire. Starter est la formule gratuite.

<!-- CDC §4.2.6 -->
## Quels moyens de paiement la salle peut-elle accepter ?

Le CDC prévoit les espèces, Orange Money CI, MTN Mobile Money CI, Wave, CinetPay (Visa et Mastercard), le virement et le chèque. La disponibilité des comptes de paiement et la configuration des intégrations sont [À FOURNIR].

<!-- CDC §4.2.6 et §4.2.8 -->
## Un reçu est-il fourni après un paiement ?

Un reçu est généré immédiatement pour un paiement en espèces ; un reçu PDF est aussi prévu pour les paiements enregistrés. Le reçu peut être téléchargé depuis le navigateur et imprimé en PDF, sur imprimante thermique Bluetooth ou sur imprimante réseau Wi-Fi.

<!-- CDC §4.2.9 -->
## Comment le QR code contrôle-t-il les entrées ?

Un QR code unique est créé pour chaque abonné lors de la création de son profil et reste le même pour ses abonnements successifs. Après le scan, l’écran indique si l’accès est autorisé ou refusé, avec le motif du refus le cas échéant.

<!-- CDC §4.2.7 -->
## AbidjanGym peut-il rappeler aux abonnés la fin de leur abonnement ?

Le CDC prévoit des rappels à sept jours, trois jours et un jour de l’expiration, par WhatsApp et SMS. L’envoi WhatsApp nécessite un compte WhatsApp Business approuvé par Meta, et l’envoi de SMS nécessite un abonnement à une API SMS.

<!-- CDC §4.2.10 -->
## Que peut faire un caissier dans AbidjanGym ?

Un caissier peut consulter la liste des abonnés, créer un abonné, enregistrer un abonnement ou un paiement, imprimer un reçu et utiliser le scan QR code. Il ne peut pas accéder aux rapports ou aux paramètres de la salle, ni supprimer des données.

<!-- CDC §6.3 -->
## Les données de ma salle sont-elles isolées de celles des autres salles ?

Chaque salle possède un identifiant propre, et les routes de l’API vérifient l’appartenance à la salle avant toute lecture ou écriture. Le compte d’une salle ne peut pas accéder aux données d’une autre salle.

<!-- CDC §4.1.5 et §4.2.10 -->
## Que peut consulter un abonné dans son espace membre ?

L’abonné peut consulter son profil, son abonnement actif et son historique. Il peut aussi voir ses paiements, télécharger ses reçus PDF et télécharger son QR code.

<!-- CDC §4.2.9 -->
## Que faire si un abonné perd son QR code ?

L’abonné peut télécharger son QR code depuis son espace membre. En cas de perte, un administrateur peut en générer un nouveau ; l’ancien code est alors invalidé.
