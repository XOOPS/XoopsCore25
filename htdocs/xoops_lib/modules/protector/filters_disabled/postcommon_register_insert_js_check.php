<?php

use Xmf\Random;

/**
 * Class protector_postcommon_register_insert_js_check
 */
class Protector_postcommon_register_insert_js_check extends ProtectorFilterAbstract
{
    /**
     * @return bool
     */
    public function execute()
    {
        ob_start(array($this, 'ob_filter'));

        if (!empty($_POST)) {
            if (!$this->checkValidate()) {
                die(_MD_PROTECTOR_TURNJAVASCRIPTON);
            }
        }

        return true;
    }

    // insert javascript into the registering form
    /**
     * @param $s
     *
     * @return mixed
     */
    public function ob_filter($s)
    {
        $antispam_htmls = $this->getHtml4Assign();

        return preg_replace('/<form[^>]*action=["\'](|#|register.php)["\'][^>]+>/i', '$0' . "\n" . $antispam_htmls['html_in_form'] . "\n" . $antispam_htmls['js_global'], $s, 1);
    }

    // import from D3forumAntispamDefault.clas.php
    /**
     * @param null|int $time
     *
     * @return string
     */
    public function getMd5($time = null)
    {
        if (empty($time)) {
            $time = time();
        }

        return md5(gmdate('YmdH', $time) . XOOPS_DB_PREFIX . XOOPS_DB_NAME);
    }

    /**
     * @return array
     */
    public function getHtml4Assign()
    {
        // Generate a secure token using the generateKey function
        $secureToken = Random::generateKey('sha512', 128);

        // JavaScript to assign the generated token to a hidden input field
        $js_in_validate_function = "
        xoopsGetElementById('antispam_md5').value = '$secureToken';
    ";

        // Return the HTML for the form and the JavaScript
        return array(
            'html_in_form' => '<input type="hidden" name="antispam_md5" id="antispam_md5" value="" />',
            'js_global'    => '<script type="text/javascript"><!--//' . "\n" . $js_in_validate_function . "\n" . '//--></script><noscript><div class="errorMsg">' . _MD_PROTECTOR_TURNJAVASCRIPTON . '</div></noscript>');
    }




    /**
     * @return bool
     */
    public function checkValidate()
    {
        $user_md5 = isset($_POST['antispam_md5']) ? trim($_POST['antispam_md5']) : '';

        // 2-3 hour margin
        if ($user_md5 != $this->getMd5() && $user_md5 != $this->getMd5(time() - 3600) && $user_md5 != $this->getMd5(time() - 7200)) {
            $this->errors[] = _MD_PROTECTOR_TURNJAVASCRIPTON;

            return false;
        }

        return true;
    }
}
