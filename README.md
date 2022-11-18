# Projet PRWB 2122 - Gestion de skills et d'expériences

## Notes de version itération 1 

  * Nous avons implémenté l'ensemble des fonctionnalités.

### Liste des utilisateurs et mots de passes

  * boverhaegen@epfc.eu, password "Password1,", administrateur
  * bepenelle@epfc.eu, password "Password1,", utilisateur
  * xapigeolet@epfc.eu, password "Password1,", utilisateur

### Liste des bugs connus

  * (pas de bug connu)

### Liste des fonctionnalités supplémentaires
  * ajout d'un input level mastering dans manage users quand on filtre par skill
  * ajout d'une règle métier qui empêche d'encoder une expérience avec une date de départ inférieure à l'âge légal pour travailler. (ici fixée à 16 ans)

### Divers
  
## Notes de version itération 2

 * Nous avons implémenté l'ensemble des fonctionnalités.
 * Certaines pages html ne passent pas la validation w3c à cause des attributs customs.
 * En désactivant Javascript les modals d'erreur ne fonctionnent plus ainsi le bouton Edit dans 'Manage Users'.

### Liste des utilisateurs et mots de passes

  * boverhaegen@epfc.eu, password "Password1,", administrateur
  * bepenelle@epfc.eu, password "Password1,", utilisateur
  * xapigeolet@epfc.eu, password "Password1,", utilisateur

### Liste des bugs connus

  ### Bugs mineurs

  * Problème d'affichage : le message d'erreur s'affiche lorsque la startDate est > stopDate et que je corrige le stopDate, mais la  soumission du formulaire fonctionne bien.

  * Soumission du formulaire ok malgré une erreur dans la startDate (attente réponse ajax, renvoie toujours true dans methode checkStartDate), validation coté serveur prend le relais pour empêcher la soumission.


## Notes de version itération 3 

 * Nous avons implémenté l'ensemble des fonctionnalités.
 * Nous avons checké au drag/drop de l'expérience dans la timeline que la start date ne doit pas être après la date du jour.
 
### Liste des utilisateurs et mots de passes

  * boverhaegen@epfc.eu, password "Password1,", administrateur
  * bepenelle@epfc.eu, password "Password1,", utilisateur
  * xapigeolet@epfc.eu, password "Password1,", utilisateur

### Liste des bugs connus

  ### Bugs mineurs

  * Le bouton Edit dans manage user et les modales ne fonctionnent pas qd on désactive javascript.

  * JustValidatePluginDate ne fonctionne pas du tout pour les dates. La validation sur le champs "requis" de la start date ne fonctionne pas en même temps que la validation custom sur la ddn.

  * Bug qui ne permet pas de valider correctement la start date avec la date d'anniversaire quand nous la tapons trop vite.

  ### Divers

  * Fenêtre alert pour notifier de l'erreur sur la ddn pour la timeline
  * Nous avons rendu certaines methodes static en méthodes d'instance mais par faute de temps nous n'avons pas pu le faire partout.
  * A propos des $_POST dans la vue edit_profile, c'était pour garder les erreurs dans les inputs, nous n'avons pas trouvé d'autres alternatives. Par contre le $_GET dans la vue manage_user a été remplacé.
  * Nous avons retiré le check sur l'age légal pour travailler lors de la création ou l'édition d'une expérience.
  * Nous avons essayé de checker avec un service l'age légal avec justValidate malgré une réponse du serveur correct, le front ne réagissait pas correctement avec le plugin.
