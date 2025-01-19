<div id="help-template" class="outer">
    <h1 class="head">Aide : <a class="ui-corner-all tooltip" href="<{$xoops_url}>/modules/protector/admin/index.php" title="Revenir à l'interface d'administration du module"> Protector <img src="<{xoAdminIcons 'home.png'}>" alt="Accueil Protector"/></a></h1>
    <!-- -----Help Content ---------- -->
    <h4 class="odd">Description</h4>

    <p class="even">Protector est un module qui permet d'accroître la sécurité de XOOPS contre divers types d'attaques malicieuses.</p>
    <h4 class="odd">Installation / Désinstallation</h4>

    <p>Tout d'abord, définir XOOPS_TRUST_PATH dans mainfile.php si vous ne l'avez pas encore fait.</p>
    <br>

    <p>Copier le dossier html/modules/protector contenu dans l'archive vers XOOPS_ROOT_PATH/modules/</p>

    <p>Copier le dossier xoops_trust_path/modules/protector contenu dans l'archive vers XOOPS_ROOT_PATH/modules/</p>
    <br>

    <p>Donner la permission d'écriture sur le fichier XOOPS_TRUST_PATH/modules/protector/configs</p>
    <h4 class="odd">= Sauvetage =</h4>

    <p class="even">Si vous êtes banni par protector, il vous faut juste supprimer les fichiers dans XOOPS_TRUST_PATH/modules/protector/configs/</p>
    <h4 class="odd">Introduction sur les filtres-plugins dans cette archive.</h4>

    <p class="even">- postcommon_post_deny_by_rbl.php
        <br>
        un plugin anti-spam.
        <br>
        Tous les messages dont les IP sont enregistrées dans RBL seront rejetées.
        <br>
        Ce plugin peut ralentir les performances des messages, en particulier les modules de discussion.
    </p>

    <p>- postcommon_post_deny_by_httpbl.php
        <br>
        un plugin anti-spam.
        <br>
        Tous les messages dont les IP sont enregistrées dans http: BL seront rejetés.
        <br>
        Avant de l'utiliser, obtenir la clef HTTPBL_KEY sur http://www.projecthoneypot.org/ et mettez-la dans le fichier de filtre.
        <br>
        define( 'PROTECTOR_HTTPBL_KEY' , '............' ) ;
    </p>

    <p class="even">- postcommon_post_need_multibyte.php
        <br>
        un plugin anti-spam.
        <br>
        Les messages sans caractères multi-octets seront rejetés.
        <br>
        Ce plugin est uniquement pour les sites en japonais, chinois traditionnel, chinois et coréen.
    </p>

    <p>- postcommon_post_htmlpurify4guest.php
        <br>
        Toutes les données des messages envoyées par les clients seront purifiées par HTMLPurifier.
        <br>
        Si vous autorisez les visiteurs à afficher en HTML, je vous recommande fortement de l'activer.
    </p>

    <p class="even">-postcommon_register_insert_js_check.php
        <br>
        Ce plugin protège votre site des robots d'enregistrement utilisateur.
        <br>
        Requiert JavaScript activé sur l'explorateur internet de vos visiteurs.
    </p>

    <p>- bruteforce_overrun_message.php
        <br>
        Définissez un message pour les visiteurs qui ont essayé un mauvais mot de passe plus de fois que spécifié.
        <br>
        Tous les plugins nommés *_message.php spécifie le message pour les accès refusés.
    </p>

    <p class="even">- precommon_bwlimit_errorlog.php
        <br>
        Quand il y a malheureusement une limitation de la bande passante, ce plugin le consigne dans le journal des erreurs d'Apache.
    </p>

    <p>Tous les plugins nommés *_errorlog.php génèreront quelques informations dans le journal Apache error_log.</p>
    <h4 class="odd">Tutoriel</h4>

    <p class="even">En cours d'élaboration.</p>
    <!-- -----Help Content ---------- -->
</div>