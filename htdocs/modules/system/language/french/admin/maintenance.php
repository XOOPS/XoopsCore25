<?php
/**
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * _LANGCODE    fr
 * _CHARSET     UTF-8
 */
//Nav
define('_AM_SYSTEM_MAINTENANCE_NAV_MANAGER', 'Maintenance');
define('_AM_SYSTEM_MAINTENANCE_NAV_LIST', 'Toute la maintenance');
define('_AM_SYSTEM_MAINTENANCE_NAV_DUMP', 'Copie');
define('_AM_SYSTEM_MAINTENANCE_SESSION', 'Vider la table des sessions');
define('_AM_SYSTEM_MAINTENANCE_SESSION_OK', 'Maintenance de la session : ok');
define('_AM_SYSTEM_MAINTENANCE_SESSION_NOTOK', 'Maintenance de la session : erreur');
define('_AM_SYSTEM_MAINTENANCE_AVATAR', 'Purger les avatars personnalisés inutilisés');
define('_AM_SYSTEM_MAINTENANCE_CACHE', 'Nettoyage du dossier cache');
define('_AM_SYSTEM_MAINTENANCE_CACHE_OK', 'Maintenance du cache : ok');
define('_AM_SYSTEM_MAINTENANCE_CACHE_NOTOK', 'Maintenance du cache : erreur');
define('_AM_SYSTEM_MAINTENANCE_TABLES', 'Maintenance des tables');
define('_AM_SYSTEM_MAINTENANCE_TABLES_OK', 'Maintenance des tables : ok');
define('_AM_SYSTEM_MAINTENANCE_TABLES_NOTOK', 'Maintenance des tables : erreur');
define('_AM_SYSTEM_MAINTENANCE_QUERY_DESC', 'Optimiser, vérifier, réparer et analyser vos tables');
define('_AM_SYSTEM_MAINTENANCE_QUERY_OK', 'Maintenance de la base de données : ok');
define('_AM_SYSTEM_MAINTENANCE_QUERY_NOTOK', 'Maintenance de la base de données : erreur');
define('_AM_SYSTEM_MAINTENANCE_CHOICE1', 'Optimisation de table(s)');
define('_AM_SYSTEM_MAINTENANCE_CHOICE2', 'Vérification de table(s)');
define('_AM_SYSTEM_MAINTENANCE_CHOICE3', 'Réparation de table(s)');
define('_AM_SYSTEM_MAINTENANCE_CHOICE4', 'Analyse de table(s)');
define('_AM_SYSTEM_MAINTENANCE_TABLES_DESC', 'ANALYZE TABLE analyse et stocke la distribution de clés pour une table. Au cours de l\'analyse, la table est verrouillée en lecture.<br>
<br>CHECK TABLE vérifie une table ou des tables pour les erreurs.
OPTIMIZE TABLE pour récupérer l\'espace inutilisé et défragmenter le fichier de données.<br>
REPAIR TABLE répare une table éventuellement corrompue.');
define('_AM_SYSTEM_MAINTENANCE_RESULT', 'Résultat');
define('_AM_SYSTEM_MAINTENANCE_RESULT_NO_RESULT', 'Aucun résultat');
define('_AM_SYSTEM_MAINTENANCE_RESULT_CACHE', 'Tâche d\'effacement du cache');
define('_AM_SYSTEM_MAINTENANCE_RESULT_SESSION', 'Tâche d\'effacement du tableau des sessions');
define('_AM_SYSTEM_MAINTENANCE_RESULT_QUERY', 'Tâche de la base de données');
define('_AM_SYSTEM_MAINTENANCE_RESULT_AVATAR', 'Purger les tâches des avatars inutilisés');
define('_AM_SYSTEM_MAINTENANCE_ERROR_MAINTENANCE', 'Aucun choix d\'opération de maintenance');
define('_AM_SYSTEM_MAINTENANCE_TABLES1', 'Tables');
define('_AM_SYSTEM_MAINTENANCE_TABLES_OPTIMIZE', 'Optimiser');
define('_AM_SYSTEM_MAINTENANCE_TABLES_CHECK', 'Vérifier');
define('_AM_SYSTEM_MAINTENANCE_TABLES_REPAIR', 'Réparer');
define('_AM_SYSTEM_MAINTENANCE_TABLES_ANALYZE', 'Analyser');
//Dump
define('_AM_SYSTEM_MAINTENANCE_DUMP', 'Copie');
define('_AM_SYSTEM_MAINTENANCE_DUMP_TABLES_OR_MODULES', 'Sélectionnez les tables ou les modules');
define('_AM_SYSTEM_MAINTENANCE_DUMP_DROP', "Ajouter la commande DROP TABLE IF EXISTS 'tables' dans la copie");
define('_AM_SYSTEM_MAINTENANCE_DUMP_OR', 'OU');
define('_AM_SYSTEM_MAINTENANCE_DUMP_AND', 'ET');
define('_AM_SYSTEM_MAINTENANCE_DUMP_ERROR_TABLES_OR_MODULES', 'Vous devez sélectionner les tables ou les modules');
define('_AM_SYSTEM_MAINTENANCE_DUMP_NO_TABLES', 'Aucune table');
define('_AM_SYSTEM_MAINTENANCE_DUMP_TABLES', 'Tables');
define('_AM_SYSTEM_MAINTENANCE_DUMP_STRUCTURES', 'Structures');
define('_AM_SYSTEM_MAINTENANCE_DUMP_NB_RECORDS', 'Nombre d\'enregistrements');
define('_AM_SYSTEM_MAINTENANCE_DUMP_FILE_CREATED', 'Fichier créé');
define('_AM_SYSTEM_MAINTENANCE_DUMP_RESULT', 'Résultat');
define('_AM_SYSTEM_MAINTENANCE_DUMP_RECORDS', 'enregistrement(s)');
// Tips
define('_AM_SYSTEM_MAINTENANCE_TIPS', '<ul>
<li>Vous pouvez faire une maintenance simple de votre installation XOOPS : effacer les fichiers temporaires du cache, vider les enregistrements de la table des sessions, et effectuer la maintenance de vos tables de données</li>
</ul>');
