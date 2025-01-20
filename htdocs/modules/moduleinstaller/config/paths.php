<?php declare(strict_types=1);

/** @return object */
$moduleDirName      = \basename(\dirname(__DIR__));
$moduleDirNameUpper = \mb_strtoupper($moduleDirName);

return [
    'name'          => \mb_strtoupper($moduleDirName) . ' PathConfigurator',
    'dirname'       => $moduleDirName,
    'admin'         => XOOPS_ROOT_PATH . '/modules/' . $moduleDirName . '/admin',
    'modPath'       => XOOPS_ROOT_PATH . '/modules/' . $moduleDirName,
    'modUrl'        => XOOPS_URL . '/modules/' . $moduleDirName,
    'uploadPath'    => XOOPS_UPLOAD_PATH . '/' . $moduleDirName,
    'uploadUrl'     => XOOPS_UPLOAD_URL . '/' . $moduleDirName,
    'uploadFolders' => [
        XOOPS_UPLOAD_PATH . '/' . $moduleDirName,
        XOOPS_UPLOAD_PATH . '/' . $moduleDirName . '/category',
        XOOPS_UPLOAD_PATH . '/' . $moduleDirName . '/screenshots',
        //XOOPS_UPLOAD_PATH . '/flags'
    ],
];
