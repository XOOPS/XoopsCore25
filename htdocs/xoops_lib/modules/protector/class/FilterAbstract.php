<?php namespace XoopsModules\Protector;

// Abstract of each filter classes
/**
 * Class FilterAbstract
 */
class FilterAbstract
{
    /**
     * @var \Protector
     */
    public $protector;

    /**
     * FilterAbstract constructor.
     */
    public function __construct()
    {
        $this->protector = Guardian::getInstance();
        $lang            = empty($GLOBALS['xoopsConfig']['language']) ? @$this->protector->_conf['default_lang'] : $GLOBALS['xoopsConfig']['language'];
        @include dirname(__DIR__) . '/language/' . $lang . '/main.php';
        if (!defined('_MD_PROTECTOR_YOUAREBADIP')) {
            require_once dirname(__DIR__) . '/language/english/main.php';
        }
    }

    /**
     * @return bool
     * @deprecated unused in core, will be removed
     */
    public function isMobile()
    {
        if (class_exists('Wizin_User')) {
            // WizMobile (gusagi)
            $user =& Wizin_User::getSingleton();

            return $user->bIsMobile;
        } elseif (defined('HYP_K_TAI_RENDER') && HYP_K_TAI_RENDER) {
            // hyp_common ktai-renderer (nao-pon)
            return true;
        } else {
            return false;
        }
    }
}
