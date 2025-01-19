<?php
/*
 Vous ne pouvez pas modifier ou altérer toute partie de ce commentaire ou crédits
 d'accompagnement des promoteurs de ce code source ou tout support
 du code source qui est considéré comme droits d'auteur (c) matériau du

 Ce programme est distribué dans l'espoir qu'il sera utile,
 mais SANS AUCUNE GARANTIE ; sans même la garantie implicite de
 COMMERCIALISATION ou D'ADAPTATION À UN USAGE PARTICULIER. Voir la
*/
/**
 *  Xoops Language
 *
 * @copyright       (c) 2000-2016 XOOPS Project (www.xoops.org)
 * @license             GNU GPL 2 (http://www.gnu.org/licenses/gpl-2.0.html)
 * @package             kernel
 * @subpackage          Xoops Mailer Local Language
 * @since               2.3.0
 * @author              Taiwen Jiang <phppp@users.sourceforge.net>
 */
defined('XOOPS_ROOT_PATH') || exit('Restricted access');
/**
 * Localize the mail functions
 *
 * The English localization is solely for demonstration
 */
// Do not change the class name
class XoopsMailerLocal extends XoopsMailer
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        // It is supposed no need to change the charset
        $this->charSet = strtolower(_CHARSET);
        // You MUST specify the language code value so that the file exists: XOOPS_ROOT_PAT/class/mail/phpmailer/language/lang-["your-language-code"].php
        $this->multimailer->setLanguage('fr');
    }

    /**
     * Multibyte languages are encouraged to make their proper method for encoding FromName
     *
     * @param $text
     *
     * @return mixed
     */
    public function encodeFromName($text)
    {
        // Activez la ligne suivante si nécessaire
        // $text = "=?{$this->charSet}?B?".base64_encode($text)."?=";
        return $text;
    }


    /**
     * Multibyte languages are encouraged to make their proper method for encoding Subject
     *
     * @param $text
     *
     * @return mixed
     */
    public function encodeSubject($text)
    {
        // Activez la ligne suivante si nécessaire
        // $text = "=?{$this->charSet}?B?".base64_encode($text)."?=";
        return $text;
    }
}