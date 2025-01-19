<?php
// _LANGCODE: en
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team

define('_XOOPS_SMARTY3_MIGRATION', 'XOOPS Smarty3 Migration');

define('_XOOPS_SMARTY3_SCANNER_RESULTS', 'Résultats de l\'analyse');
define('_XOOPS_SMARTY3_SCANNER_RUN', 'Exécuter l\'analyse');
define('_XOOPS_SMARTY3_SCANNER_END', 'Quitter l\'analyse');
define('_XOOPS_SMARTY3_SCANNER_RULE', 'Règle');
define('_XOOPS_SMARTY3_SCANNER_MATCH', 'Correspondance');
define('_XOOPS_SMARTY3_SCANNER_FILE', 'Fichier');
define('_XOOPS_SMARTY3_SCANNER_FIXED', 'Compte des corrections');
define('_XOOPS_SMARTY3_SCANNER_MANUAL_REVIEW', 'Examen manuel requis');
define('_XOOPS_SMARTY3_SCANNER_NOT_WRITABLE', 'Pas de possibilité d\'écrire');

define('_XOOPS_SMARTY3_RESCAN_OPTIONS', 'Options de réanalyse');

define('_XOOPS_SMARTY3_FIX_BUTTON', 'Exécuter les correctifs ?');
define('_XOOPS_SMARTY3_SCANNER_MARK_COMPLETE', 'Mark Complete');

define('_XOOPS_SMARTY3_TEMPLATE_DIR', 'Répertoire des modèles (facultatif)');
define('_XOOPS_SMARTY3_TEMPLATE_EXT', 'Extension des modèles (facultatif)');


define('_XOOPS_SMARTY3_SCANNER_OFFER', <<<'EOT'
<h3>XOOPS 2.5.11 introduit un changement important : Smarty 3</h3>

<p>Malheureusement, ce changement peut potentiellement perturber certains thèmes plus anciens. Par conséquent, avant de procéder à la mise à jour, assurez-vous de suivre les étapes suivantes :

<li>Exécutez preflight.php pour vérifier si des thèmes ou des modèles de modules ne sont pas obsolètes.</li>
<li>Si des problèmes sont identifiés, consultez ce document pour comprendre les modifications nécessaires avant de procéder à la mise à niveau.</li>
<li>Après avoir effectué les modifications nécessaires, exécutez à nouveau preflight.php.</li>
<li>S'il n'y a plus de problème, vous pouvez commencer le processus de mise à niveau.</li>
</p>
EOT
);
