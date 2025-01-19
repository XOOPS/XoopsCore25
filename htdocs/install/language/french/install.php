<?php
/**
 * Installer main english strings declaration file
 *
 * @copyright    (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license          GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package          installer
 * @since            2.3.0
 * @author           Haruki Setoyama  <haruki@planewave.org>
 * @author           Kazumi Ono <webmaster@myweb.ne.jp>
 * @author           Skalpa Keo <skalpa@xoops.org>
 * @author           Taiwen Jiang <phppp@users.sourceforge.net>
 * @author           dugris <dugris@frxoops.org>
 */
// _LANGCODE: fr
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team
define('SHOW_HIDE_HELP', 'Afficher/Masquer le texte de l\'aide');
// License
define('LICENSE_NOT_WRITEABLE', 'License file "%s" n\'est PAS accessible en écriture. ');
define('LICENSE_IS_WRITEABLE', '%s La licence est ouverte en écriture');
// Configuration check page
define('SERVER_API', 'API du Serveur');
define('PHP_EXTENSION', 'Extension \"%s\"');
define('CHAR_ENCODING', 'Encodage des caractères (Charset)');
define('XML_PARSING', 'Analyse XML');
define('REQUIREMENTS', 'Requiert');
define('_PHP_VERSION', 'Version PHP');
define('RECOMMENDED_SETTINGS', 'Paramétrage recommandé');
define('RECOMMENDED_EXTENSIONS', 'Extensions recommandées');
define('SETTING_NAME', 'Nom du paramètre');
define('RECOMMENDED', 'Recommandé');
define('CURRENT', 'Actuel');
define('RECOMMENDED_EXTENSIONS_MSG', 'Ces extensions ne sont pas obligatoires pour un usage standard, mais peuvent être nécessaires pour utiliser
quelques fonctions spécifiques (comme le multi-langage ou les flux RSS). Nous recommandons leur installation.');
define('NONE', 'Aucun');
define('SUCCESS', 'Réussit');
define('WARNING', 'Avertissement');
define('FAILED', 'Echec');
// Titles (main and pages)
define('XOOPS_INSTALL_WIZARD', 'Assistant d\'installation XOOPS');
define('LANGUAGE_SELECTION', 'Sélection de la langue');
define('LANGUAGE_SELECTION_TITLE', 'Choisissez votre langue');        // L128
define('INTRODUCTION', 'Sommaire');
define('INTRODUCTION_TITLE', 'Bienvenue dans l\'assistant d\'installation de XOOPS');        // L0
define('CONFIGURATION_CHECK', 'Vérification de la configuration');
define('CONFIGURATION_CHECK_TITLE', 'Vérification du paramétrage de votre serveur');
define('PATHS_SETTINGS', 'Vérification du paramétrage des chemins');
define('PATHS_SETTINGS_TITLE', 'Vérification du paramétrage des chemins');
define('DATABASE_CONNECTION', 'Connexion à la base de données');
define('DATABASE_CONNECTION_TITLE', 'Connexion à la base de données');
define('DATABASE_CONFIG', 'Paramétrage de la base de données');
define('DATABASE_CONFIG_TITLE', 'Paramétrage de la base de données');
define('CONFIG_SAVE', 'Enregistrement de la configuration');
define('CONFIG_SAVE_TITLE', 'Enregistrement de la configuration système');
define('TABLES_CREATION', 'Création des tables');
define('TABLES_CREATION_TITLE', 'Création des tables dans la base de données');
define('INITIAL_SETTINGS', 'Paramètres initiaux');
define('INITIAL_SETTINGS_TITLE', 'Merci de saisir vos paramètres initiaux');
define('DATA_INSERTION', 'Insertion des données');
define('DATA_INSERTION_TITLE', 'Enregistrement de vos paramètres dans la base de données');
define('WELCOME', 'Bienvenue');
define('WELCOME_TITLE', 'Bienvenue sur votre nouveau site sous XOOPS');        // L0
// Settings (labels and help text)
define('XOOPS_PATHS', 'Racine physique de votre site XOOPS');
define('XOOPS_URLS', 'Adresse du site internet');
define('XOOPS_ROOT_PATH_LABEL', 'Racine physique de votre site XOOPS');
define('XOOPS_ROOT_PATH_HELP', 'Chemin physique de votre site XOOPS sur le serveur SANS l\'antislash final');
define('XOOPS_LIB_PATH_LABEL', 'Chemin physique de la bibliothèque XOOPS');
define('XOOPS_LIB_PATH_HELP', 'Physical path to the XOOPS library directory WITHOUT trailing slash, for forward compatibility. Locate the folder out of ' . XOOPS_ROOT_PATH_LABEL . ' to make it secure.');
define('XOOPS_DATA_PATH_LABEL', 'Chemin physique de la bibliothèque XOOPS');
define('XOOPS_DATA_PATH_HELP', 'Physical path to the XOOPS data files (writable) directory WITHOUT trailing slash, for forward compatibility. Locate the folder out of ' . XOOPS_ROOT_PATH_LABEL . ' to make it secure.');
define('XOOPS_URL_LABEL', 'Adresse avec laquelle on accèdera à votre site web (URL)'); // L56
define('XOOPS_URL_HELP', 'Adresse URL principale qui est utilisée pour accéder à votre installation XOOPS'); // L58
define('LEGEND_CONNECTION', 'Connection au serveur');
define('LEGEND_DATABASE', 'Base de données'); // L51
define('DB_HOST_LABEL', 'Nom d\'hôte du serveur');    // L27
define('DB_HOST_HELP', 'Nom d\'hôte du serveur de base de données. Si vous n\'êtes pas sûr, consultez les instructions de votre hébergeur (FAQ, guide,etc...), <em>localhost</em> fonctionne dans les situations les plus courantes'); // L67
define('DB_USER_LABEL', 'Identifiant');    // L28
define('DB_USER_HELP', 'Nom du compte utilisateur qui sera utilisé pour se connecter à la base de données. Il doit posséder des droits d\'administration sur la base de données'); // L65
define('DB_PASS_LABEL', 'Mot de passe');    // L52
define('DB_PASS_HELP', 'Mot de passe associé au nom du compte utilisateur pour la base de données'); // L68
define('DB_NAME_LABEL', 'Nom de la base de données');    // L29
define('DB_NAME_HELP', 'Indiquez le nom de la base de données sur le serveur. L\'assistant essaiera de créer la base de données si elle n\'existe pas.'); // L64
define('DB_CHARSET_LABEL', 'Jeu de caractères (charset) pour la base de données');
define('DB_CHARSET_HELP', 'MySQL inclut le jeu de caractères qui vous permet de stocker des données en utilisant une variété de jeux de caractères et d\'effectuer des comparaisons selon une variété d\'interclassements.');
define('DB_COLLATION_LABEL', 'Interclassement (collation) pour la connexion à la base de données');
define('DB_COLLATION_HELP', 'L\'interclassement est un ensemble de règles pour la comparaison des caractères dans un jeu de caractères.');
define('DB_PREFIX_LABEL', 'Préfixe des tables');    // L30
define('DB_PREFIX_HELP', 'Ce préfixe sera ajouté à toutes les tables utilisées par Xoops ou ses modules, pour éviter les conflits de noms dans la base de données. Si vous n\'êtes pas sûr, conservez la proposition affichée'); // L63
define('DB_PCONNECT_LABEL', 'Utiliser des connexions persistantes');    // L54
define('DB_PCONNECT_HELP', "La valeur par défaut est 'NON'. Conserver ce choix si vous n'êtes pas sûr."); // L69
define('DB_DATABASE_LABEL', 'Base de données');
define('LEGEND_ADMIN_ACCOUNT', 'Compte Administrateur');
define('ADMIN_LOGIN_LABEL', 'Identifiant Administrateur'); // L37
define('ADMIN_EMAIL_LABEL', 'E-mail Administrateur'); // L38
define('ADMIN_PASS_LABEL', 'Mot de passe administrateur'); // L39
define('ADMIN_CONFIRMPASS_LABEL', 'Confirmation du mot de passe'); // L74
// Buttons
define('BUTTON_PREVIOUS', 'Précédent'); // L42
define('BUTTON_NEXT', 'Continuer'); // L47
// Messages
define('XOOPS_FOUND', '%s trouvé !');
define('CHECKING_PERMISSIONS', 'Contrôle des permissions sur les dossiers et fichiers ...'); // L82
define('IS_NOT_WRITABLE', '\"%s\" n\'est PAS accessible en écriture.'); // L83
define('IS_WRITABLE', '\"%s\" est accessible en écriture.'); // L84
define('XOOPS_PATH_FOUND', 'Chemin trouvé !');
//define('READY_CREATE_TABLES', 'Aucune  tables XOOPS détectée.<br>Le programme d\'installation est maintenant prêt à créer les tables système XOOPS.');
define('XOOPS_TABLES_FOUND', 'Les tables du système XOOPS existent déjà dans votre base de données.'); // L131
define('XOOPS_TABLES_CREATED', 'Les tables du système XOOPS ont été créées.');
//define('READY_INSERT_DATA', 'L\'assistant d\'installation est maintenant prêt à insérer les données initiales dans votre base de données.');
//define('READY_SAVE_MAINFILE', 'L\'assistant d\'installation est prêt à sauvegarder vos paramètres de configuration dans le fichier <em>mainfile.php</em>.');
define('SAVED_MAINFILE', 'Paramètres sauvegardés');
define('SAVED_MAINFILE_MSG', 'L\'assistant d\'installation a sauvegardé vos paramètres de configuration dans les fichiers <em>mainfile.php</em> et <em>secure.php</em>.');
define('DATA_ALREADY_INSERTED', 'XOOPS data trouvées dans la base de données.');
define('DATA_INSERTED', 'Les données initiales ont été enregistrées dans la base de données.');
// %s is database name
define('DATABASE_CREATED', 'La base de données %s a été créée !'); // L43
// %s is table name
define('TABLE_NOT_CREATED', 'Impossibilité de créer la table %s'); // L118
define('TABLE_CREATED', 'Table %s crée'); // L45
define('ROWS_INSERTED', '%d entrées insérées dans la table \"%s\".'); // L119
define('ROWS_FAILED', 'Echec d\'insertion de %d enregistrements dans la table \"%s\".'); // L120
define('TABLE_ALTERED', 'Table \"%s\" mise à jour.'); // L133
define('TABLE_NOT_ALTERED', 'Echec lors de la mise à jour de la table \"%s\".'); // L134
define('TABLE_DROPPED', 'Table \"%s\" supprimée.'); // L163
define('TABLE_NOT_DROPPED', 'La suppression de la table \"%s\" a échoué.'); // L164
// Error messages
define('ERR_COULD_NOT_ACCESS', 'Impossibilité d\'accéder au dossier. Verifier qu\'il existe et qu\'il est ouvert en écriture sur le serveur.');
define('ERR_NO_XOOPS_FOUND', 'Aucune installation de XOOPS a été trouvée dans le dossier spécifié.');
define('ERR_INVALID_EMAIL', 'Email invalide'); // L73
define('ERR_REQUIRED', 'L\'information est nécessaire.'); // L41
define('ERR_PASSWORD_MATCH', 'Les deux mots de passe ne concordent pas');
define('ERR_NEED_WRITE_ACCESS', '<br>Le serveur doit disposer de droits des permissions en écriture sur les dossiers et fichiers<br />(i.e. <em>chmod 775</em> sur un serveur UNIX/LINUX)<br />S\'ils ne sont pas disponibles ou créés correctement, créez manuellement et définissez les autorisations appropriées..');
define('ERR_NO_DATABASE', 'Impossible de procéder à la création de la base de données. Contactez l\'administrateur du serveur pour obtenir des détails supplémentaires.'); // L31
define('ERR_NO_DBCONNECTION', 'Impossible de se connecter au serveur de la base de données.'); // L106
define('ERR_WRITING_CONSTANT', 'Echec d\'écriture de la constante \"%s\"'); // L122
define('ERR_COPY_MAINFILE', 'Impossible de copier le fichier \"%s\"');
define('ERR_WRITE_MAINFILE', 'Impossible d\'écrire dans le fichier \"%s\". Veuillez vérifier les permissions en écriture pour ce fichier et recommencez.');
define('ERR_READ_MAINFILE', 'Impossible d\'ouvrir en lecture le fichier \"%s\"');
define('ERR_INVALID_DBCHARSET', "Le jeu de caractères (charset) \"%s\" n'est pas supporté.");
define('ERR_INVALID_DBCOLLATION', "L'interclassement (collation) \"%s\" n'est pas supporté.");
define('ERR_CHARSET_NOT_SET', 'Le jeu de caractères (charset) par défaut n\'est pas configuré pour la base de donnée de XOOPS.');
define('_INSTALL_CHARSET', 'UTF-8');
define('SUPPORT', 'Support');
define('LOGIN', 'Authentification');
define('LOGIN_TITLE', 'Authentification');
define('USER_LOGIN', 'Connexion Administrateur');
define('USERNAME', 'Identifiant :');
define('PASSWORD', 'Mot de passe :');
define('ICONV_CONVERSION', 'Conversion du jeu de caractère (charset)');
define('ZLIB_COMPRESSION', 'Compression Zlib');
define('IMAGE_FUNCTIONS', 'Fonctions Image');
define('IMAGE_METAS', 'Meta Data Image (exif)');
define('FILTER_FUNCTIONS', 'Fonctions de filtre');
define('ADMIN_EXIST', 'Le compte d\'administrateur existe déjà.');
define('CONFIG_SITE', 'Configuration du Site');
define('CONFIG_SITE_TITLE', 'Configuration du Site');
define('MODULES', 'Installation des Modules');
define('MODULES_TITLE', 'Installation des Modules');
define('THEME', 'Sélectionner le thème');
define('THEME_TITLE', 'Choix du thème par défaut');
define('INSTALLED_MODULES', 'Les modules suivants ont été installés.');
define('NO_MODULES_FOUND', 'Aucun module trouvé !');
define('NO_INSTALLED_MODULES', 'Aucun module installé.');
define('THEME_NO_SCREENSHOT', 'Aucune vignette trouvée');
define('IS_VALOR', ' => ');
// password message
define('PASSWORD_LABEL', 'Robustesse du mot de passe');
define('PASSWORD_DESC', 'Mot de passe absent');
define('PASSWORD_GENERATOR', 'Générateur de mot de passe');
define('PASSWORD_GENERATE', 'Générer');
define('PASSWORD_COPY', 'Copier');
define('PASSWORD_VERY_WEAK', 'Très facile');
define('PASSWORD_WEAK', 'Facile');
define('PASSWORD_BETTER', 'Simple');
define('PASSWORD_MEDIUM', 'Moyen');
define('PASSWORD_STRONG', 'Bon');
define('PASSWORD_STRONGEST', 'Très bon');
//2.5.7
define('WRITTEN_LICENSE', 'Clé de licence de XOOPS %s:  <strong>%s</strong>');
//2.5.8
define('CHMOD_CHGRP_REPEAT', 'Recommencez');
define('CHMOD_CHGRP_IGNORE', 'Utiliser dans tout les cas');
define('CHMOD_CHGRP_ERROR', 'L\'Assistant d\'Installation peut ne pas être capable d\'écrire le fichier de configuration %1$s. <P>PHP écrit les fichiers l\'utilisateur %2$s et le groupe %3$s.<P> Le répertoire %4$s/ a l\'utilisateur %5$s et le groupe %6$s');
//2.5.9
define("CURL_HTTP", "Client URL Library (cURL)");
define('XOOPS_COOKIE_DOMAIN_LABEL', 'Domaine de cookie pour le site Web');
define('XOOPS_COOKIE_DOMAIN_HELP', 'Domaine pour définir des cookies. Peut être vide, l\'hôte complet de l\'URL (www.example.com) ou le domaine enregistré sans sous-domaines (exemple.com) à partager entre les sous-domaines (www.example.com et blog.example.com).');
define('INTL_SUPPORT', 'Fonctions d\'internationalisation');
define('XOOPS_SOURCE_CODE', "XOOPS sur GitHub");
define('XOOPS_INSTALLING', 'Installation');
define('XOOPS_ERROR_ENCOUNTERED', 'Erreur');
define('XOOPS_ERROR_SEE_BELOW', 'Voir ci-dessous pour les messages.');
define('MODULES_AVAILABLE', 'Modules disponibles');
define('INSTALL_THIS_MODULE', 'Ajouter %s');
//2.5.11
define('ERR_COPY_CONFIG_FILE', 'Impossible de copier le fichier de configuration %s');