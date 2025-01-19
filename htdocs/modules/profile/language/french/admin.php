<?php
// 
// _LANGCODE: fr
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team
define('_PROFILE_AM_FIELD', 'Champ');
define('_PROFILE_AM_FIELDS', 'Champs');
define('_PROFILE_AM_CATEGORY', 'Catégorie');
define('_PROFILE_AM_STEP', 'Étape');
define('_PROFILE_AM_SAVEDSUCCESS', '%s enregistré avec succès');
define('_PROFILE_AM_DELETEDSUCCESS', '%s supprimé avec succès');
define('_PROFILE_AM_RUSUREDEL', 'Êtes-vous certain de vouloir supprimer %s ?');
define('_PROFILE_AM_FIELDNOTCONFIGURABLE', 'Champ non configurable.');
define('_PROFILE_AM_ADD', 'Ajouter %s');
define('_PROFILE_AM_EDIT', 'Éditer %s');
define('_PROFILE_AM_TYPE', 'Type de champ');
define('_PROFILE_AM_VALUETYPE', 'Type de valeur');
define('_PROFILE_AM_NAME', 'Nom');
define('_PROFILE_AM_TITLE', 'Titre');
define('_PROFILE_AM_DESCRIPTION', 'Description');
define('_PROFILE_AM_REQUIRED', 'Obligatoire ?');
define('_PROFILE_AM_MAXLENGTH', 'Longueur maximale');
define('_PROFILE_AM_WEIGHT', 'Poids');
define('_PROFILE_AM_DEFAULT', 'Défaut');
define('_PROFILE_AM_NOTNULL', 'Pas Nul ?');
define('_PROFILE_AM_ARRAY', 'Tableau');
define('_PROFILE_AM_EMAIL', 'Courriel');
define('_PROFILE_AM_INT', 'Entier');
define('_PROFILE_AM_TXTAREA', 'Zone de texte');
define('_PROFILE_AM_TXTBOX', 'Champ de texte');
define('_PROFILE_AM_URL', 'Adresse internet');
define('_PROFILE_AM_OTHER', 'Autre');
define('_PROFILE_AM_FLOAT', 'Point flottant');
define('_PROFILE_AM_DECIMAL', 'Nombre décimal');
define('_PROFILE_AM_UNICODE_ARRAY', 'Tableau Unicode');
define('_PROFILE_AM_UNICODE_EMAIL', 'Courriel Unicode');
define('_PROFILE_AM_UNICODE_TXTAREA', 'Zone de texte Unicode');
define('_PROFILE_AM_UNICODE_TXTBOX', 'Champ de texte Unicode');
define('_PROFILE_AM_UNICODE_URL', 'URL Unicode');
define('_PROFILE_AM_PROF_VISIBLE_ON', "Champ visible sur le profil de ces groupes");
define('_PROFILE_AM_PROF_VISIBLE_FOR', 'Champ du profil visible pour ces groupes');
define('_PROFILE_AM_PROF_VISIBLE', 'Visibilité');
define('_PROFILE_AM_PROF_EDITABLE', 'Champ éditable depuis le profil');
define('_PROFILE_AM_PROF_REGISTER', 'Affiché sur le formulaire d\'enregistrement');
define('_PROFILE_AM_PROF_SEARCH', 'Consultable par ces groupes');
define('_PROFILE_AM_PROF_ACCESS', 'Profil accessible par ces groupes');
define('_PROFILE_AM_PROF_ACCESS_DESC', '<ul>' . "<li>Admin groups: If a user belongs to admin groups, the current user has access if and only if one of the current user's groups is allowed to access admin group; else</li>" . "<li>Non basic groups: If a user belongs to one or more non basic groups (NOT admin, user, anonymous), the current user has access if and only if one of the current user's groups is allowed to allowed to any of the non basic groups; else</li>" . '<li>User group: If a user belongs to User group only, the current user has access if and only if one of his groups is allowed to access User group</li>' . '</ul>');
define('_PROFILE_AM_FIELDVISIBLE', 'Le champ');
define('_PROFILE_AM_FIELDVISIBLEFOR', ' est visible pour ');
define('_PROFILE_AM_FIELDVISIBLEON', ' la consultation du profil de ');
define('_PROFILE_AM_FIELDVISIBLETOALL', '- Tout le monde');
define('_PROFILE_AM_FIELDNOTVISIBLE', 'n\'est pas accessible');
define('_PROFILE_AM_CHECKBOX', 'Case à cocher');
define('_PROFILE_AM_GROUP', 'Sélection de groupe');
define('_PROFILE_AM_GROUPMULTI', 'Sélection multi-groupes');
define('_PROFILE_AM_LANGUAGE', 'Sélection de la langue');
define('_PROFILE_AM_RADIO', 'Boutons radio');
define('_PROFILE_AM_SELECT', 'Sélectionner');
define('_PROFILE_AM_SELECTMULTI', 'Sélection multiple');
define('_PROFILE_AM_TEXTAREA', 'Zone de texte');
define('_PROFILE_AM_DHTMLTEXTAREA', 'Zone de texte DHTML');
define('_PROFILE_AM_TEXTBOX', 'Champ de texte');
define('_PROFILE_AM_TIMEZONE', 'Fuseau horaire');
define('_PROFILE_AM_YESNO', 'Bouton radio Oui / Non');
define('_PROFILE_AM_DATE', 'Date');
define('_PROFILE_AM_AUTOTEXT', 'Texte automatique');
define('_PROFILE_AM_DATETIME', 'Date et heure');
define('_PROFILE_AM_LONGDATE', 'Date longue');
define('_PROFILE_AM_ADDOPTION', 'Ajouter une option');
define('_PROFILE_AM_REMOVEOPTIONS', 'Supprimer les options');
define('_PROFILE_AM_KEY', 'Valeur à stocker');
define('_PROFILE_AM_VALUE', 'Texte à afficher');
// User management
define('_PROFILE_AM_EDITUSER', 'Éditer l\'utilisateur');
define('_PROFILE_AM_SELECTUSER', 'Sélectionner l\'utilisateur');
define('_PROFILE_AM_ADDUSER', 'Ajouter un utilisateur');
define('_PROFILE_AM_THEME', 'Thème');
define('_PROFILE_AM_RANK', 'Classement');
define('_PROFILE_AM_USERDONEXIT', "L'utilisateur n'existe pas !");
define('_PROFILE_MA_USERLEVEL', 'Niveau d\'accès de l\'utilisateur');
define('_PROFILE_MA_ACTIVE', 'Actif');
define('_PROFILE_MA_INACTIVE', 'Inactif');
define('_PROFILE_AM_USERCREATED', 'Utilisateur créé');
define('_PROFILE_AM_CANNOTDELETESELF', 'La suppression de votre propre compte n\'est pas autorisée - utilisez la page de votre profil pour effectuer cette suppression');
define('_PROFILE_AM_CANNOTDELETEADMIN', 'La suppression d\'un compte administrateur est interdite');
define('_PROFILE_AM_NOSELECTION', 'Pas d\'utilisateur sélectionné');
define('_PROFILE_AM_USER_ACTIVATED', 'Utilisateur activé');
define('_PROFILE_AM_USER_DEACTIVATED', 'Utilisateur désactivé');
define('_PROFILE_AM_USER_NOT_ACTIVATED', 'Erreur : l\'utilisateur n\'est pas activé');
define('_PROFILE_AM_USER_NOT_DEACTIVATED', 'Erreur : l\'utilisateur n\'est pas désactivé');
define('_PROFILE_AM_STEPNAME', 'Nom de l\'étape');
define('_PROFILE_AM_STEPORDER', 'Ordre de l\'étape');
define('_PROFILE_AM_STEPSAVE', 'Sauvegarder après l\'étape');
define('_PROFILE_AM_STEPINTRO', 'Description de l\'étape');
//1.62
define('_PROFILE_AM_ACTION', 'Action');
//1.63
define('_PROFILE_AM_REQUIRED_TOGGLE', 'Modifier un champ obligatoire');
define('_PROFILE_AM_REQUIRED_TOGGLE_SUCCESS', 'Champ obligatoire modifié avec succès');
define('_PROFILE_AM_REQUIRED_TOGGLE_FAILED', 'La modification du champ obligatoire a échoué');
define('_PROFILE_AM_SAVESTEP_TOGGLE', 'Modification enregistrée');
define('_PROFILE_AM_SAVESTEP_TOGGLE_SUCCESS', 'Changement réussi après l\'étape');
define('_PROFILE_AM_SAVESTEP_TOGGLE_FAILED', "La modification de l'option « Enregistrer après l'étape » a échoué");
//XOOPS 2.5.9
define('_PROFILE_AM_CANNOTDEACTIVATEWEBMASTERS', 'Vous ne pouvez pas désactiver un compte Webmestre');

