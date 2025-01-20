<?php declare(strict_types=1);

use Xmf\Module\Admin;

$pathIcon16    = Admin::iconUrl('', '16');
$moduleDirName = \basename(\dirname(__DIR__));

return [
    'name'    => \mb_strtoupper($moduleDirName) . ' IconConfigurator',
    'edit'    => "<img src='" . $pathIcon16 . "/edit.png'  alt=" . _EDIT . ' title=' . _EDIT . " align='middle'>",
    'delete'  => "<img src='" . $pathIcon16 . "/delete.png' alt=" . _DELETE . ' title=' . _DELETE . " align='middle'>",
    'clone'   => "<img src='" . $pathIcon16 . "/editcopy.png' alt=" . _CLONE . ' title=' . _CLONE . " align='middle'>",
    'preview' => "<img src='" . $pathIcon16 . "/view.png' alt=" . _PREVIEW . ' title=' . _PREVIEW . " align='middle'>",
    'print'   => "<img src='" . $pathIcon16 . "/printer.png' alt=" . _PRINT . ' title=' . _PRINT . " align='middle'>",
    'pdf'     => "<img src='" . $pathIcon16 . "/pdf.png' alt=" . _PDF . ' title=' . _PDF . " align='middle'>",
    'add'     => "<img src='" . $pathIcon16 . "/add.png' alt=" . _ADD . ' title=' . _ADD . " align='middle'>",
    '0'       => "<img src='" . $pathIcon16 . "/0.png' alt=" . 0 . ' title=' . _OFF . " align='middle'>",
    '1'       => "<img src='" . $pathIcon16 . "/1.png' alt=" . 1 . ' title=' . _ON . " align='middle'>",
];
