<?php
/**
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license         GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * _LANGCODE    fr
 * _CHARSET     UTF-8
 */
// Navigation
define('_AM_SYSTEM_BLOCKS_ADMIN', 'Administration des blocs');
define('_AM_SYSTEM_BLOCKS_MANAGMENT', 'Gérer');
define('_AM_SYSTEM_BLOCKS_ADDBLOCK', 'Ajouter un nouveau bloc');
define('_AM_SYSTEM_BLOCKS_EDITBLOCK', 'Éditer un bloc');
define('_AM_SYSTEM_BLOCKS_CLONEBLOCK', 'Cloner un bloc');
// Forms
define('_AM_SYSTEM_BLOCKS_CUSTOM', 'Bloc personnalisé');
define('_AM_SYSTEM_BLOCKS_TYPES', 'Tous les types');
define('_AM_SYSTEM_BLOCKS_GENERATOR', 'Modules');
define('_AM_SYSTEM_BLOCKS_GROUP', 'Groupes');
define('_AM_SYSTEM_BLOCKS_SVISIBLEIN', 'Page');
define('_AM_SYSTEM_BLOCKS_DISPLAY', 'Afficher le bloc');
define('_AM_SYSTEM_BLOCKS_HIDE', 'Masquer le bloc');
define('_AM_SYSTEM_BLOCKS_CLONE', 'Clone');
define('_AM_SYSTEM_BLOCKS_SIDELEFT', 'Colonne - Gauche');
define('_AM_SYSTEM_BLOCKS_SIDETOPLEFT', 'Haut - Gauche');
define('_AM_SYSTEM_BLOCKS_SIDETOPCENTER', 'Haut - Centre');
define('_AM_SYSTEM_BLOCKS_SIDETOPRIGHT', 'Haut - Droite');
define('_AM_SYSTEM_BLOCKS_SIDERIGHT', 'Colonne - Droite');
define('_AM_SYSTEM_BLOCKS_SIDEBOTTOMLEFT', 'Bas - Gauche');
define('_AM_SYSTEM_BLOCKS_SIDEBOTTOMCENTER', 'Bas - Centre');
define('_AM_SYSTEM_BLOCKS_SIDEBOTTOMRIGHT', 'Bas  - Droite');

define('_AM_SYSTEM_BLOCKS_SIDEFOOTERLEFT', 'Pied de page - Gauche');
define('_AM_SYSTEM_BLOCKS_SIDEFOOTERCENTER', 'Pied de page - Centre');
define('_AM_SYSTEM_BLOCKS_SIDEFOOTERRIGHT', 'Pied de page - Droite');

define('_AM_SYSTEM_BLOCKS_ADD', 'Ajouter un bloc');
define('_AM_SYSTEM_BLOCKS_MANAGE', 'Gérer le bloc');
define('_AM_SYSTEM_BLOCKS_NAME', 'Nom');
define('_AM_SYSTEM_BLOCKS_TYPE', 'Position du bloc');
define('_AM_SYSTEM_BLOCKS_SBLEFT', 'Colonne - Gauche');
define('_AM_SYSTEM_BLOCKS_SBRIGHT', 'Colonne - Droite');
define('_AM_SYSTEM_BLOCKS_CBLEFT', 'Haut - Gauche');
define('_AM_SYSTEM_BLOCKS_CBRIGHT', 'Haut - Droite');
define('_AM_SYSTEM_BLOCKS_CBCENTER', 'Haut - Centre');
define('_AM_SYSTEM_BLOCKS_CBBOTTOMLEFT', 'Bas - Gauche');
define('_AM_SYSTEM_BLOCKS_CBBOTTOMRIGHT', 'Bas - Droite');

define('_AM_SYSTEM_BLOCKS_CBFOOTERLEFT', 'Pied de page - Gauche');
define('_AM_SYSTEM_BLOCKS_CBFOOTERCENTER', 'Pied de page - Centre');
define('_AM_SYSTEM_BLOCKS_CBFOOTERRIGHT', 'Pied de page - Droite');

define('_AM_SYSTEM_BLOCKS_CBBOTTOM', 'Bas - Centre');
define('_AM_SYSTEM_BLOCKS_WEIGHT', 'Poids');
define('_AM_SYSTEM_BLOCKS_VISIBLE', 'Visible');
define('_AM_SYSTEM_BLOCKS_VISIBLEIN', 'Visible dans');
define('_AM_SYSTEM_BLOCKS_TOPPAGE', 'Page d\'accueil');
define('_AM_SYSTEM_BLOCKS_ALLPAGES', 'Toutes les Pages');
define('_AM_SYSTEM_BLOCKS_UNASSIGNED', 'Non assigné');
define('_AM_SYSTEM_BLOCKS_TITLE', 'Titre');
define('_AM_SYSTEM_BLOCKS_CONTENT', 'Contenu');
define('_AM_SYSTEM_BLOCKS_USEFULTAGS', 'Balises utiles :');
define('_AM_SYSTEM_BLOCKS_BLOCKTAG', '%s affichera %s');
define('_AM_SYSTEM_BLOCKS_CTYPE', 'Type de contenu');
define('_AM_SYSTEM_BLOCKS_HTML', 'HTML');
define('_AM_SYSTEM_BLOCKS_PHP', 'Script PHP');
define('_AM_SYSTEM_BLOCKS_AFWSMILE', 'Format automatique (émoticônes activées)');
define('_AM_SYSTEM_BLOCKS_AFNOSMILE', 'Format automatique (émoticônes désactivées)');
define('_AM_SYSTEM_BLOCKS_BCACHETIME', 'Durée du cache');
define('_AM_SYSTEM_BLOCKS_CUSTOMHTML', 'Bloc personnalisé (HTML)');
define('_AM_SYSTEM_BLOCKS_CUSTOMPHP', 'Bloc personnalisé (PHP)');
define('_AM_SYSTEM_BLOCKS_CUSTOMSMILE', 'Bloc personnalisé (format Auto + émoticônes)');
define('_AM_SYSTEM_BLOCKS_CUSTOMNOSMILE', 'Bloc personnalisé (format Auto)');
define('_AM_SYSTEM_BLOCKS_EDITTPL', 'Modifier un modèle');
define('_AM_SYSTEM_BLOCKS_OPTIONS', 'Options');
define('_AM_SYSTEM_BLOCKS_DRAG', 'Faites glisser ou triez le bloc');
// Messages
define('_AM_SYSTEM_BLOCKS_DBUPDATED', _AM_SYSTEM_DBUPDATED);
define('_AM_SYSTEM_BLOCKS_RUSUREDEL', 'Êtes-vous sûr de vouloir supprimer le bloc <strong>%s</strong>?');
define('_AM_SYSTEM_BLOCKS_SYSTEMCANT', 'Les blocs système ne peuvent pas être supprimés !');
define('_AM_SYSTEM_BLOCKS_MODULECANT', 'Ce bloc ne peut pas être supprimé directement ! Si vous souhaitez désactiver ce bloc, désactivez le module.');
// Tips
define('_AM_SYSTEM_BLOCKS_TIPS','Vous pouvez<ul>
<li>Déplacer un bloc via glisser-déposer simplement en cliquant sur le titre d\'un bloc ou l\'icône <img class="tooltip" src="%s" alt="'._AM_SYSTEM_BLOCKS_DRAG.'" title="'._AM_SYSTEM_BLOCKS_DRAG.'" /> et le relacher sur la position souhaitée,</li>
<li>Ajouter un nouveau bloc personnalisé,</li>
<li>Activer ou désactiver un bloc en cliquant sur <img class="tooltip" width="16" src="%s" alt="'._AM_SYSTEM_BLOCKS_DISPLAY.'" title="'._AM_SYSTEM_BLOCKS_DISPLAY.'"/> ou <img class="tooltip" width="16" src="%s" alt="'._AM_SYSTEM_BLOCKS_HIDE.'" title="'._AM_SYSTEM_BLOCKS_HIDE.'" />.</li>
</ul>');

define('_AM_SYSTEM_BLOCKS_FOOTER_LEFT', 'Pied de page - Gauche');
define('_AM_SYSTEM_BLOCKS_FOOTER_CENTER', 'Pied de page - Centre');
define('_AM_SYSTEM_BLOCKS_FOOTER_RIGHT', 'Pied de page - Droite');
