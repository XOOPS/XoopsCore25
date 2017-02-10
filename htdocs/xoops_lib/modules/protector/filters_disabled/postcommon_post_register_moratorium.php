<?php

define('PROTECTOR_POSTCOMMON_POST_REGISTER_MORATORIUM', 60); // minutes

/**
 * Class protector_postcommon_post_register_moratorium
 */
class Protector_postcommon_post_register_moratorium extends ProtectorFilterAbstract
{
    /**
     * @return bool
     */
    public function execute()
    {
        global $xoopsUser;

        if (!is_object($xoopsUser)) {
            return true;
        }

        $moratorium_result = (int)(($xoopsUser->getVar('user_regdate') + PROTECTOR_POSTCOMMON_POST_REGISTER_MORATORIUM * 60 - time()) / 60);
        if ($moratorium_result > 0) {
            if (preg_match('#(https?\:|\[\/url\]|www\.)#', serialize($_POST))) {
                printf(_MD_PROTECTOR_FMT_REGISTER_MORATORIUM, $moratorium_result);
                exit;
            }
        }
        return null;
    }
}
