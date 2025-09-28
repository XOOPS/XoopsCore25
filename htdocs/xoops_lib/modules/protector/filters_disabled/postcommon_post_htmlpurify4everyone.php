<?php

/**
 * Class protector_postcommon_post_htmlpurify4everyone
 */
class Protector_postcommon_post_htmlpurify4everyone extends ProtectorFilterAbstract
{
    public $purifier;
    public $method;

    public function execute()
    {

        // use HTMLPurifier inside Protector
        require_once XOOPS_TRUST_PATH . '/vendor/ezyang/htmlpurifier/library/HTMLPurifier.auto.php';
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Cache', 'SerializerPath', XOOPS_VAR_PATH . '/configs/protector');
        $config->set('Core', 'Encoding', _CHARSET);
        //$config->set('HTML', 'Doctype', 'HTML 4.01 Transitional');
        $this->purifier = new HTMLPurifier($config);
        $this->method   = 'purify';


        $_POST = $this->purify_recursive($_POST);
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
