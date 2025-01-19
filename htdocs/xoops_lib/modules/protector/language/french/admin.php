<?php
// mymenu
define('_MD_A_MYMENU_MYTPLSADMIN','');
define('_MD_A_MYMENU_MYBLOCKSADMIN','Autorisations');
define('_MD_A_MYMENU_MYPREFERENCES','Préférences');
// index.php
define('_AM_TH_DATETIME', 'Heure');
define('_AM_TH_USER', 'Utilisateur');
define('_AM_TH_IP', 'IP');
define('_AM_TH_AGENT', 'AGENT');
define('_AM_TH_TYPE', 'Type');
define('_AM_TH_DESCRIPTION', 'Description');
define('_AM_TH_BADIPS','Mauvaises IP<br /><br /><span style="font-weight:normal;">Écrivez une ligne pour chaque IP<br />laisser vide autorise toutes les adresses IP</span>');
define('_AM_TH_GROUP1IPS','Autoriser les IP pour le Groupe=1<br /><br /><span style="font-weight:normal;">Écrivez une ligne pour chaque IP.<br />192.168. équivaut 192.168.*<br />laisser vide autorise toutes les adresses IP</span>');
define('_AM_LABEL_COMPACTLOG', 'Compacter l\'historique des évènements');
define('_AM_BUTTON_COMPACTLOG', 'Compactez-le !');
define('_AM_JS_COMPACTLOGCONFIRM', 'Les enregistrements en double (IP, Type) seront supprimés');
define('_AM_LABEL_REMOVEALL', 'Supprimer tous les enregistrements');
define('_AM_BUTTON_REMOVEALL', 'Tout supprimer !');
define('_AM_JS_REMOVEALLCONFIRM', 'Tous les journaux seront complètement supprimés. Êtes-vous vraiment d\'accord ?');
define('_AM_LABEL_REMOVE', 'Supprimer les enregistrements vérifiés :');
define('_AM_BUTTON_REMOVE', 'Supprimer !');
define('_AM_JS_REMOVECONFIRM', 'Vous confirmez ?');
define('_AM_MSG_IPFILESUPDATED', 'Les fichiers des IP ont été mis à jour');
define('_AM_MSG_BADIPSCANTOPEN', 'Le fichier des mauvaises IP ne peut pas être ouvert');
define('_AM_MSG_GROUP1IPSCANTOPEN', 'Le fichier des autorisations du groupe = 1, ne peut pas être ouvert');
define('_AM_MSG_REMOVED', 'Les enregistrements sont supprimés');
define('_AM_FMT_CONFIGSNOTWRITABLE', 'Rendez inscriptible le répertoire configs : %s');
// prefix_manager.php
define('_AM_H3_PREFIXMAN', 'Gestionnaire de préfixe');
define('_AM_MSG_DBUPDATED', 'Base de données mise à jour avec succès !');
define('_AM_CONFIRM_DELETE', 'Toutes les données seront supprimées. Ok ?');
define('_AM_TXT_HOWTOCHANGEDB',"Si vous souhaitez modifier le préfixe,<br /> modifiez %s/data/secure.php manuellement.<br /><br />define('XOOPS_DB_PREFIX', '<b>%s</b>');");
// advisory.php
define('_AM_ADV_NOTSECURE', 'Pas sécurisé');
define('_AM_ADV_TRUSTPATHPUBLIC', 'Si vous pouvez voir une image -NG- ou que le lien renvoi à une page normale, votre XOOPS_TRUST_PATH n\'est pas placé correctement. Le meilleur endroit pour le XOOPS_TRUST_PATH est en dehors de la racine. Si vous ne pouvez pas faire cela, vous devez mettre .htaccess (DENY FROM ALL) juste en dessous du XOOPS_TRUST_PATH en second choix.');
define('_AM_ADV_TRUSTPATHPUBLICLINK', 'Vérifiez que les fichiers PHP à l\'intérieur du TRUST_PATH sont mis en lecture seule (il doivent produire une erreur 404, 403 ou 500)');
define('_AM_ADV_REGISTERGLOBALS',"Si sur \"ON\", ce paramètre ouvre la porte à une variété d'attaques par injection. Si vous pouvez, réglez 'register_globals off' dans php.ini, ou si ce n'est pas possible, créez ou modifiez le fichier .htaccess dans votre répertoire XOOPS :");
define('_AM_ADV_ALLOWURLFOPEN',"Si  sur \"ON\", ce paramètre permet aux pirates d'exécuter des scripts arbitraires sur des serveurs distants.<br />Seul(e) l'administrateur(trice) peut modifier cette option.<br />Si vous êtes administrateur(trice), modifiez php.ini ou httpd.conf.<br /><b>Exemple de httpd.conf:<br /> &nbsp; php_admin_flag &nbsp; allow_url_fopen &nbsp; off</b><br />Sinon, demandez à vos administrateurs.");
define('_AM_ADV_USETRANSSID',"Si sur 'ON', votre ID de session sera affichée dans les balises d'ancrage, etc.<br />Pour éviter le détournement de session, ajoutez une ligne dans .htaccess du XOOPS_ROOT_PATH.<br /><b>php_flag session.use_trans_sid off</b>");
define('_AM_ADV_DBPREFIX',"Ce paramètre invite aux « Injections SQL ».<br />Ne pas oublier de mettre 'forcer désinfection *' sur ON dans les préférences de ce module.");
define('_AM_ADV_LINK_TO_PREFIXMAN', 'Accéder au gestionnaire de préfixe');
define('_AM_ADV_MAINUNPATCHED', 'Vous devez modifier votre mainfile.php comme décrit dans le README.');
define('_AM_ADV_DBFACTORYPATCHED', 'Votre ensemble de base de données est prêt pour DBLayer Piégeage anti-SQL-Injection');
define('_AM_ADV_DBFACTORYUNPATCHED', 'Votre DatabaseFactory n\'est pas prêt pour dblayer Piégeage anti-SQL-Injection. Certains correctifs sont nécessaires.');
define('_AM_ADV_SUBTITLECHECK', 'Vérifiez si Protector fonctionne bien');
define('_AM_ADV_CHECKCONTAMI', 'Contamination');
define('_AM_ADV_CHECKISOCOM', 'Commentaires isolés');
//XOOPS 2.5.4
define('_AM_ADV_REGISTERGLOBALS2', 'et insérez la ligne ci-dessous :');
//XOOPS 2.5.8
define('_AM_PROTECTOR_PREFIX', 'Préfixe');
define('_AM_PROTECTOR_TABLES', 'Tables');
define('_AM_PROTECTOR_UPDATED', 'Mis à jour');
define('_AM_PROTECTOR_COPY', 'Copier');
define('_AM_PROTECTOR_ACTIONS', 'Actions');
// XOOPS 2.5.10 v Protector 3.60
define('_AM_LABEL_BAN_BY_IP', 'Interdire les IP sur les enregistrements vérifiés:');
define('_AM_BUTTON_BAN_BY_IP', 'Interdiction IP!');
define('_AM_JS_BANCONFIRM', 'Interdiction IP OK?');
define('_AM_MSG_BANNEDIP', 'Les IPs sont bannie');
define('_AM_ADMINSTATS_TITLE', 'Résumé du journal de protection');
// XOOPS 2.5.11
define('_AM_ADMINSTATS_LAST_MONTH', 'Le mois dernier');
define('_AM_ADMINSTATS_LAST_WEEK', 'La semaine dernière');
define('_AM_ADMINSTATS_LAST_DAY', 'Dernier jour');
define('_AM_ADMINSTATS_LAST_HOUR', 'Dernière heure');
