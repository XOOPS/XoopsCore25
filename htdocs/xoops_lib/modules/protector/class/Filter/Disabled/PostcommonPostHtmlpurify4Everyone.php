<?php namespace XoopsModules\Protector\Filter\Disabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

/**
 * Class PostcommonPostHtmlpurify4Everyone
 */
class PostcommonPostHtmlpurify4Everyone extends FilterAbstract
{
    /**
     * @var \HTMLPurifier
     */
    public $purifier;
    /**
     * @var string
     */
    public $method;

    public function execute()
    {
        // HTMLPurifier runs with PHP5 only
        if (version_compare(PHP_VERSION, '5.0.0') < 0) {
            die('Turn postcommon_post_htmlpurify4everyone.php off because this filter cannot run with PHP4');
        }
        /*
                if ( file_exists( XOOPS_ROOT_PATH.'/class/icms.htmlpurifier.php' ) ) {
                    // use HTMLPurifier inside ImpressCMS
                    if ( ! class_exists( 'icms_HTMLPurifier' ) ) {
                        require_once ICMS_ROOT_PATH.'/class/icms.htmlpurifier.php' ;
                    }
        //            $pure =& icms_HTMLPurifier::getPurifierInstance() ;
        //            $_POST = $pure->icms_html_purifier( $_POST , 'protector' ) ;
                    $this->purifier =& icms_HTMLPurifier::getPurifierInstance() ;
                    $this->method = 'icms_html_purifier' ;

                } else {
                    */
        // use HTMLPurifier inside Protector
        require_once dirname(__DIR__) . '/library/HTMLPurifier.auto.php';
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache', 'SerializerPath', XOOPS_VAR_PATH . '/configs/protector');
        $config->set('Core', 'Encoding', _CHARSET);
        //$config->set('HTML', 'Doctype', 'HTML 4.01 Transitional');
        $this->purifier = new HTMLPurifier($config);
        $this->method   = 'purify';
        //        }

        $_POST = $this->purify_recursive($_POST);
    }

    /**
     * @param array|string $data
     *
     * @return array|mixed
     */
    public function purify_recursive($data)
    {
        if (is_array($data)) {
            return array_map(array(
                                 $this,
                                 'purify_recursive',
                             ), $data);
        } else {
            return strlen($data) > 32 ? call_user_func(array(
                                                           $this->purifier,
                                                           $this->method,
                                                       ), $data) : $data;
        }
    }
}
