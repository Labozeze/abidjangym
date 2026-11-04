Bonjour {{ $admin->first_name }},

Votre salle « {{ $gym->name }} » est bien enregistrée sur AbidjanGym.

Vous pouvez dès maintenant accéder à votre backoffice :
{{ $administrationUrl }}

Connectez-vous avec l'adresse {{ $admin->email }} et le mot de passe choisi lors de
l'inscription.

Prochaines étapes, depuis les paramètres :
- compléter le profil de la salle (logo, horaires, couleurs) ;
- créer vos formules d'abonnement ;
- activer les moyens de paiement que vous acceptez (les espèces sont déjà actives).

À bientôt,
L'équipe AbidjanGym
