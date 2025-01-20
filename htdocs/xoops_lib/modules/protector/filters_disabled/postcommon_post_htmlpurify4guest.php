<?php

/**
 * Class protector_postcommon_post_htmlpurify4guest
 */
class Protector_postcommon_post_htmlpurify4guest extends ProtectorFilterAbstract
{
    public $purifier;
    public $method;

    /**
     * @return bool
     */
    public function execute()
    {
        global $xoopsUser;

        if (is_object($xoopsUser)) {
            return true;
        }
        // use HTMLPurifier inside Protector
        require_once XOOPS_TRUST_PATH . '/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache', 'SerializerPath', XOOPS_VAR_PATH . '/configs/protector');
        $config->set('Core', 'Encoding', _CHARSET);
        //$config->set('HTML', 'Doctype', 'HTML 4.01 Transitional');
        $this->purifier = new HTMLPurifier($config);
        $this->method   = 'purify';

        $_POST = $this->purify_recursive($_POST);
        return null;
    }

    /**
     * @param $data
     *
     * @return array|mixed
     */
    public function purify_recursive($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'purify_recursive'], $data);
        } else {
            return strlen($data) > 32 ? call_user_func([$this->purifier, $this->method], $data) : $data;
        }
    }
}
