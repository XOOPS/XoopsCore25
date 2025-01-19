<?php

if (defined('FOR_XOOPS_LANG_CHECKER')) {
    $mydirname = 'protector';
}
$constpref = '_MI_' . strtoupper($mydirname);

if (defined('FOR_XOOPS_LANG_CHECKER') || !defined($constpref . '_LOADED')) {
    define($constpref . '_LOADED', 1);

    // Le nom de ce module
    define($constpref . '_NAME', 'Protector');

    // Courte description de ce module
    define($constpref . '_DESC', 'Ce module protège votre site XOOPS des diiférentes attaques comme DoS, Injection SQL et la contamination par variables.');

    // Menu
    define($constpref . '_ADMININDEX', 'Centre de Protection');
    define($constpref . '_ADVISORY', 'Avertissement de Sécurité');
    define($constpref . '_PREFIXMANAGER', 'Gestionnaire de Prefixe');
    define($constpref . '_ADMENU_MYBLOCKSADMIN', 'Permissions');

    // Configuration
    define($constpref . '_GLOBAL_DISBL', 'Désactiver temporairement');
    define($constpref . '_GLOBAL_DISBLDSC', 'Toutes les protections sont temporairement désactivées. <br>Ne pas oublier de les remettre après avoir réglé le problème');

    define($constpref . '_DEFAULT_LANG', 'Langue par défaut');
    define($constpref . '_DEFAULT_LANGDSC', 'Spécifiez la langue utilisée pour l\'affichage des messages avant traitement du fichier common.php');

    define($constpref . '_RELIABLE_IPS', 'Adresses IP de confiance');
    define($constpref . '_RELIABLE_IPSDSC', 'Renseignez les adresses IPs de confiance séparées par le caractère |. En cas de saisie partielle, le caractère ^ indique le début de l\'adresse IP et le caractère $ indique la fin de l\'adresse IP.');

    define($constpref . '_LOG_LEVEL', 'Niveau de journalisation');
    define($constpref . '_LOG_LEVELDSC', '');

    define($constpref . '_BANIP_TIME0', 'Durée pendant laquelle est bannie une adresse IP (sec)');

    define($constpref . '_LOGLEVEL0', 'aucun');
    define($constpref . '_LOGLEVEL15', 'Bas');
    define($constpref . '_LOGLEVEL63', 'bas');
    define($constpref . '_LOGLEVEL255', 'complet');

    define($constpref . '_HIJACK_TOPBIT', 'Bits IP protégés pour la session');
    define($constpref . '_HIJACK_TOPBITDSC', 'Protection contre les détournements de  session : <br>Par défaut 24/56 (masque de réseau pour les IPV4/IPV6). (Tous les bits sont protégés)<br>Si votre adresse IP n\'est pas fixe, indiquez la plage d\'adresses IP possibles par le nombre de bits.<br>(exemple) Si votre adresse IP peut se trouver dans la plage 192.168.0.0-192.168.0.255, alors saisissez 24(bit) ici');
    define($constpref . '_HIJACK_DENYGP', 'Les groupes interdisent le changement d\'IP dans une session');
    define($constpref . '_HIJACK_DENYGPDSC', 'Anti-session de Hi-Jacking :<br>sélectionnez les groupes qui ne sont pas autorisés à déplacer leur IP dans une session.<br>(je recommande de laisser Administrateur sur « Activé ».)');
    define($constpref . '_SAN_NULLBYTE', 'Désinfection des octets nuls');
    define($constpref . '_SAN_NULLBYTEDSC', 'Le caractère de fin "\\0" est souvent utilisé dans les attaques malveillantes.<br>Un octet nul sera remplacé par un espace.<br>(fortement recommandé sur « Activé »)');
    define($constpref . '_DIE_NULLBYTE', 'Quitter si des octets nuls sont trouvés');
    define($constpref . '_DIE_NULLBYTEDSC', 'Le caractère de fin "\\0" est souvent utilisé dans les attaques malveillantes<br>(fortement recommandé sur « Activé »)');
    define($constpref . '_DIE_BADEXT', 'Quitter si des fichiers défectueux sont envoyés');
    define($constpref . '_DIE_BADEXTDSC', 'Si quelqu\'un essaie d\'envoyer des fichiers qui ont des extensions non souhaitées comme .php, ce module quitte votre XOOPS.<br>Si vous attachez souvent des fichiers php dans B-Wiki ou PukiWikiMod, désactivez-le. ');
    define($constpref . '_CONTAMI_ACTION', 'Action si une contamination est trouvée');
    define($constpref . '_CONTAMI_ACTIONDS', 'Sélectionnez l\'action lorsque quelqu\'un tente de contaminer les variables globales du système dans votre XOOPS.<br>(l\'option recommandée est un écran blanc) ');
    define($constpref . '_ISOCOM_ACTION', 'Action si un commentaire isolé est trouvé');
    define($constpref . '_ISOCOM_ACTIONDSC', 'Anti Injection SQL :<br>Sélectionnez l\'action quand un "/ *" isolé est trouvé.<br>« Désinfection » signifie ajouter un autre "* /" dans la queue.<br>(l\'option recommandée est Désinfection)');
    define($constpref . '_UNION_ACTION', 'Action si un UNION est trouvé');
    define($constpref . '_UNION_ACTIONDSC', 'Anti Injection SQL :<br>Sélectionnez l\'action lorsque la syntaxe est UNION de SQL.<br>« Désinfection »  signifie changer "union" en "uni-on".<br>(l\'option recommandée est Désinfection)');
    define($constpref . '_ID_INTVAL', 'Forcer intval à des variables comme id');
    define($constpref . '_ID_INTVALDSC', 'Toutes les requêtes nommées "* id" seront traitées comme des entiers.<br>Cette option vous protège contre tous type d\'injections XSS et SQL.<br>Bien que je recommande d\'activer cette option, cela peut causer des problèmes avec certains modules.');
    define($constpref . '_FILE_DOTDOT', 'Protection contre la faille Directory Traversal');
    define($constpref . '_FILE_DOTDOTDSC', 'Il élimine ".." de toutes les demandes suspicieuses de Directory Traversal');

    define($constpref . '_BF_COUNT', 'Anti brute-force');
    define($constpref . '_BF_COUNTDSC', 'Autorise les comptes non confirmés à se connecter dans les 10 minutes. Celui qui n\'y parvient pas voit son IP  bannie.');

    define($constpref . '_BWLIMIT_COUNT', 'Restriction de la bande passante');
    define($constpref . '_BWLIMIT_COUNTDSC', 'Spécifie le nombre maximum d\'accès à mainfile.php pendant une période. Cette valeur doit être 0 pour les environnements normaux qui ont suffisamment de bande passante CPU. Un nombre inférieur à 10 sera ignoré.');

    define($constpref . '_DOS_SKIPMODS', 'Modules n\'utilisant pas de vérificateur DoS/Crawler');
    define($constpref . '_DOS_SKIPMODSDSC', 'définir les noms des répertoires des modules séparés par |. Cette option sera utile avec le module de discussion, etc..');

    define($constpref . '_DOS_EXPIRE', 'Surveiller le temps pour les gros chargements (sec)');
    define($constpref . '_DOS_EXPIREDSC', 'Cette valeur spécifie la durée tolérée pour les gros rechargements fréquents (attaque F5) et les robots à chargement élevé.');

    define($constpref . '_DOS_F5COUNT', 'Mauvais comptes concernant les attaques F5');
    define($constpref . '_DOS_F5COUNTDSC', 'Prévention des attaques DoS.<br> Cette valeur spécifie le nombre de rechargements à considérer comme attaque malveillante.');
    define($constpref . '_DOS_F5ACTION', 'Action contre les attaques F5');

    define($constpref . '_DOS_CRCOUNT', 'Mauvais comptes concernant les robots logiciels');
    define($constpref . '_DOS_CRCOUNTDSC', 'Prévenir des accès robots  élevés.<br>Cette valeur spécifie le nombre d\'accès pour considérer un robot comme indésirable.');
    define($constpref . '_DOS_CRACTION', 'Action contre les chargements élevés des robots logiciels');

    define($constpref . '_DOS_CRSAFE', 'Bienvenue Agent utilisateur');
    define($constpref . '_DOS_CRSAFEDSC', 'Un modèle perl regex pour Agent utilisateur.<br>Si cela correspond, le robot n\'est jamais considéré comme un robot à chargement élevé.<br>exemple /(bingbot|Googlebot|Yahoo! Slurp)/i');

    define($constpref . '_OPT_NONE', 'Aucun (uniquement la journalisation)');
    define($constpref . '_OPT_SAN', 'Désinfection');
    define($constpref . '_OPT_EXIT', 'Écran blanc');
    define($constpref . '_OPT_BIP', 'Interdire l\'IP (aucune limite)');
    define($constpref . '_OPT_BIPTIME0', 'Interdire l\'IP (moratoire)');

    define($constpref . '_DOSOPT_NONE', 'Aucun (uniquement la journalisation)');
    define($constpref . '_DOSOPT_SLEEP', 'Sommeil');
    define($constpref . '_DOSOPT_EXIT', 'Écran blanc');
    define($constpref . '_DOSOPT_BIP', 'Interdire l\'IP (aucune limite)');
    define($constpref . '_DOSOPT_BIPTIME0', 'Interdire l\'IP (moratoire)');
    define($constpref . '_DOSOPT_HTA', 'DENY par .htaccess (Expérimental)');

    define($constpref . '_BIP_EXCEPT', 'Groupes jamais enregistrés en tant que mauvaise adresse IP');
    define($constpref . '_BIP_EXCEPTDSC', 'Un utilisateur qui appartient au groupe spécifié ici ne sera jamais banni (je recommande d\'activer Administrateur.) ');

    define($constpref . '_DISABLES', 'Désactiver les fonctionnalités dangereuses dans XOOPS');

    define($constpref . '_DBLAYERTRAP', 'Activer le piégeage anti-SQL-Injection au niveau de la base de données');
    define($constpref . '_DBLAYERTRAPDSC', 'Les attaques de type Almost SQL seront annulées par cette fonctionnalité. Cette fonction est nécessaire au support du moteur de base de données. Vous pouvez le vérifier sur la page d\'avis de sécurité. Ce paramètre doit être activé. Ne jamais l\'éteindre avec désinvolture.');
    define($constpref . '_DBTRAPWOSRV', 'Ne jamais vérifier _SERVER pour l\'anti-SQL-Injection');
    define($constpref . '_DBTRAPWOSRVDSC', 'Certains serveurs activent toujours le piégeage au niveau de la base de données. Cela provoque des détections erronées en tant qu\'attaque d\'injection SQL. Si vous avez de telles erreurs, activez cette option. Vous devez savoir que cette option affaiblit la sécurité de piégeage anti injection SQL au niveau de la base de données.');

    define($constpref . '_BIGUMBRELLA', 'activer le anti-XSS (BigUmbrella)');
    define($constpref . '_BIGUMBRELLADSC', 'Cela vous protège en partie des attaques via les vulnérabilités XSS. Mais pas à 100%');

    define($constpref . '_SPAMURI4U', 'anti-SPAM : URLs pour les utilisateurs normaux');
    define($constpref . '_SPAMURI4UDSC', 'Si ce nombre d\'URLs est trouvé dans les envois d\'utilisateurs autres qu\'Administrateur, l\'envoi est considéré comme SPAM. 0 signifie désactiver cette fonctionnalité.');
    define($constpref . '_SPAMURI4G', 'anti-SPAM : URLs pour les invités');
    define($constpref . '_SPAMURI4GDSC', 'Si ce nombre d\'URLs est trouvé dans l\'envoi par des invités, celui-ci est considéré comme SPAM. 0 signifie désactiver cette fonctionnalité.');

    //3.40b
    define($constpref . '_ADMINHOME', 'Accueil');
    define($constpref . '_ADMINABOUT', 'À propos');
    //3.50
    define($constpref . '_STOPFORUMSPAM_ACTION', 'Bloquer les spams sur le forum');
    define($constpref . '_STOPFORUMSPAM_ACTIONDSC', 'Vérifie si les données proviennent de spammeurs enregistrés sur la base de données www.stopforumspam.com. Nécessite la librairie php cURL.');
	// 3.60
    define($constpref . '_ADMINSTATS', 'Vue d\'ensemble');
    define($constpref . '_BANIP_TIME0DSC', 'Temps de suspension en secondes pour les interdictions IP automatiques');
}