<?php

return array(
    /**
     * Extended HTML editor for XoopsFormDhtmlTextArea
     *
     * If an extended HTML editor is set, the renderer will be replaced by the
     * specified editor, usually a visual or WYSIWYG editor.
     *
     * Developer and user guide:
     *   For run-time settings per call
     *   - To use an editor pre-configured by XoopsEditor, e.g. 'fckeditor':
     *       $options['editor'] = 'fckeditor';
     *   - To use a custom editor, e.g. 'MyEditor' class located in "/modules/myeditor/myeditor.php":
     *       $options['editor'] = array('MyEditor', XOOPS_ROOT_PATH . "/modules/myeditor/myeditor.php");
     *
     *   For pre-configured settings, which will force to use an editor if no specific editor is set for call
     *     - Set up custom configs: in XOOPS_VAR_PATH . '/configs/xoopsconfig.php'
     *     - set an editor as default, e.g. a pre-configured editor 'fckeditor':
     *         'editor' => 'fckeditor',
     *     - set a custom editor 'MyEditor' class located in "/modules/myeditor/myeditor.php":
     *         'editor' => array('MyEditor', XOOPS_ROOT_PATH . "/modules/myeditor/myeditor.php"),
     *
     *   To disable the default editor, in XOOPS_VAR_PATH . '/configs/xoopsconfig.php':
     *         <code>return array();</code>
     *
     *   To disable the default editor for a specific call:
     *         $options['editor'] = 'dhtmltextarea';
     */
    //"editor"    => "fckeditor",
    //"editor"    => "dhtmlext",

    /**
     * Debug level for XOOPS
     *
     * Displaying debug information to different level(s) of users:
     *   0 - To all users
     *   1 - To members
     *   2 - To admins only
     */
    'debugLevel' => 2,

    /** XOOPS admin security warnings
     *
     * Display admin security warnings:
     *   0 - Disabled
     *   1 - Enabled
     */
    'admin_warnings_enable' => 1,

    /**
     * Value for X-Frame-Options header for clickjacking defense. Default is 'sameorigin'.
     * Other possible values are DENY and ALLOW-FROM uri
     * To disable the header completely set to empty string
     */
    //'xFrameOptions' => '',

    /**
     * proxy_env controls which HTTP header is used to determine an alternate
     * client IP address. Some common options are HTTP_X_FORWARDED_FOR,
     * HTTP_CLIENT_IP and HTTP_FORWARDED. This is determined by the configuration
     * of any proxy server or load balancer in your server environment.
     * The default if proxy_env is not specified is no proxy ip address is considered.
     */
    //'proxy_env' => 'HTTP_FORWARDED',
    );
