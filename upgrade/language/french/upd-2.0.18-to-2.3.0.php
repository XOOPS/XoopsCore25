<?php
// _LANGCODE: fr
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team

define('LEGEND_XOOPS_PATHS', 'XOOPS Chemins physiques');
define('LEGEND_DATABASE', 'Jeu de caractères de base de données');

define('XOOPS_LIB_PATH_LABEL', 'Répertoire de la bibliothèque XOOPS');
define('XOOPS_LIB_PATH_HELP', 'Chemin physique vers le répertoire de la bibliothèque XOOPS SANS barre oblique de fin, pour une compatibilité ascendante. Localisez le dossier hors de ' . XOOPS_ROOT_PATH . ' pour le sécuriser.');
define('XOOPS_DATA_PATH_LABEL', 'Répertoire des fichiers de données XOOPS');
define('XOOPS_DATA_PATH_HELP', 'Chemin physique vers le répertoire des fichiers de données XOOPS (accessible en écriture) SANS barre oblique de fin, pour une compatibilité ascendante. Localisez le dossier hors de ' . XOOPS_ROOT_PATH . ' pour le sécuriser.');

define('DB_COLLATION_LABEL', 'Jeu de caractères et classement de la base de données');
define('DB_COLLATION_HELP', "Depuis la version 4.12, MySQL prend en charge le jeu de caractères et le classement personnalisés. Cependant, il est plus complexe que prévu, alors N'apportez AUCUN changement à moins que vous ne soyez sûr de votre choix.");
define('DB_COLLATION_NOCHANGE', 'Ne changez pas');

define('XOOPS_PATH_FOUND', 'Chemin trouvé.');
define('ERR_COULD_NOT_ACCESS', 'Impossible d\'accéder au dossier spécifié. Veuillez vérifier qu\'il existe et qu\'il est lisible par le serveur.');
define('CHECKING_PERMISSIONS', 'Vérification des autorisations de fichier et de répertoire ...');
define('ERR_NEED_WRITE_ACCESS', 'Le serveur doit disposer d\'un accès en écriture aux fichiers et dossiers suivants <br> (c\'est-à-dire <em> chmod 777 nom_répertoire </em> sur un serveur UNIX / LINUX)');
define('IS_NOT_WRITABLE', '%s n\'est PAS accessible en écriture.');
define('IS_WRITABLE', '%s est accessible en écriture.');
define('ERR_COULD_NOT_WRITE_MAINFILE', 'Erreur lors de l\'écriture du contenu dans mainfile.php, écrivez le contenu dans mainfile.php manuellement.');
