<?php
//
// _LANGCODE: fr
// _CHARSET : UTF-8
// Translator: XOOPS Translation Team

$content .= '
<p>
    <abbr title="Script de portail OO (Orienté Objet)">XOOPS</abbr> est un script de portail OO (Orienté Objet) open-source 
    écrit en PHP. C\'est un outil idéal pour développer de petits comme de grands sites communautaires,
    intranet, portails d\'entreprise, réseaux sociaux, boutiques e-commerce, et plus encore.
</p>
<p>
    XOOPS est publié sous les termes de 
    <a href="https://www.gnu.org/licenses/gpl-2.0.html" rel="external" title="Voir la licence GPL">GNU General Public License (GPL)</a>
    version 2 ou supérieure, et est libre d\'utilisation et de modification.
    Il est possible de le redistribuer à condition de respecter les termes de distribution de la licence.</p>
</p>
<h3>Pré-requis</h3>
<ul>
    <li>WWW Server (<a href="https://www.apache.org/" rel="external">Apache</a>, <a href="https://www.nginx.com/" rel="external">NGINX</a>, IIS, etc)</li>
    <li><a href="https://www.php.net/" rel="external" title="Visitez le site Officiel PHP">PHP</a> 5.3.9 ou supérieure, 7.2+ recommendée</li>
    <li><a href="https://www.mysql.com/" rel="external">MySQL</a> 5.5 ou supérieure, 5.7+ recommandée </li>
</ul>
<h3>Avant de procéder à l\'installation</h3>
<ol>
    <li>Installez correctement le serveur WWW, PHP et le serveur de base de données.</li>
    <li>Le cas échéant, préparez une base de données pour votre site XOOPS. A noter que la procédure d\'installation de Xoops est à même de la créer (Recommandé)</li>
    <li>Préparez un compte utilisateur et accordez lui des droits d\'administrateur (lecture, écriture et exécution).</li>
    <li>Rendre ces répertoires et fichiers accessibles en écriture: %s</li>
    <li>Pour des raisons de sécurité, nous vous invitons à déplacer en dehors <a href="http://phpsec.org/projects/guide/3.html" rel="external">de la racine de votre site</a>, et/ou de changer le nom des répertoires suivants : %s</li>
    <li>Créez (si ils n\'existent pas) et ouvrez en écriture les répertoires suivant: %s</li>
    <li>Veillez à autoriser l\'écriture des cookies et l\'exécution du Javascript dans votre navigateur internet.</li>
</ol>
<h3> Remarques spéciales </ h3>
<p>Certaines combinaisons de logiciels système spécifiques peuvent nécessiter certaines configurations supplémentaires pour fonctionner.
  avec XOOPS. Si l’un de ces sujets s’applique à votre environnement, veuillez consulter la documentation complète. 
 <a href="https://xoops.gitbook.io/xoops-install-upgrade/" rel="external">XOOPS 
  manuel d\'installation</a> pour plus d\'informations. 
</p>
<p>MySQL 8.0 n’est pas supporté dans toutes les versions de PHP. Même dans les versions prises en charge, des problèmes avec
  la bibliothèque PHP <em>mysqlnd</em> peut nécessiter le serveur MySQL <em>default-authentication-plugin</em> 
 être défini sur <em>mysql_native_password</em> pour fonctionner correctement..
</p>
<p>Les systèmes activés pour SELinux (tels que CentOS et RHEL) peuvent nécessiter des modifications du contexte de sécurité.
  pour les répertoires XOOPS en plus des autorisations de fichiers normales pour rendre les répertoires accessibles en écriture.
  Consultez la documentation de votre système et / ou votre administrateur système.
</p>
';