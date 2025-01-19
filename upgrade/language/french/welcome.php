<?php
// _LANGCODE: fr
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team

define('_XOOPS_UPGRADE_WELCOME', <<<'EOT'
<h2> Mise à jour de XOOPS </h2>

<p>
<em> Upgrade </em> examinera cette installation de XOOPS et appliquera les correctifs nécessaires pour la rendre compatible
avec le nouveau code XOOPS. Les correctifs peuvent inclure des modifications de base de données, l'ajout de paramètres par défaut pour les nouveaux
éléments de configuration, mises à jour de fichiers et de données, etc.
<p>
Après chaque correctif, le système de mise à jour signalera l'état et attendra votre validation pour poursuive. A la
fin de la mise à niveau, le contrôle passera à la fonction de mise à jour du module système.

<div class = "alert alert-warning">
Une fois la mise à niveau terminée, n'oubliez pas de:
<ul class = "fa-ul">
  <li> <span class = "fa-li fa fa-folder-open-o"> </span> supprimez le dossier de mise à niveau (dossier upgrade)</li>
  <li> <span class = "fa-li fa fa-refresh"> </span> mettez à jour tous les modules qui ont changé </li>
</div>

EOT
);