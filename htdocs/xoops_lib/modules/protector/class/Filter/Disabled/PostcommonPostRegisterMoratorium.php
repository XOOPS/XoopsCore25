<?php namespace XoopsModules\Protector\Filter\Disabled;

use XoopsModules\Protector;
use XoopsModules\Protector\FilterAbstract;

define('PROTECTOR_POSTCOMMON_POST_REGISTER_MORATORIUM', 60); // minutes

/**
 * Class PostcommonPostRegisterMoratorium
 */
class PostcommonPostRegisterMoratorium extends FilterAbstract
{
    /**
     * @return bool|null
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
