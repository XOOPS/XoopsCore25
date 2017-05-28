<div id="xo-accordion">
    <dl>
        <!--[if IE]>
        <a href="#Section1" class="ie">
            <div>
        <![endif]-->
        <dt><!--[if !IE]>--><a href="#Section1"><!--<![endif]--><{$lang_overview}><!--[if !IE]>--></a><!--<![endif]--></dt>
        <dd id="Section1">
            <table>
                <tr>
                    <td><{$lang_version_xoops}></td>
                    <td><{$lang_xoops_version}></td>
                </tr>
                <tr>
                    <td><{$lang_version_php}></td>
                    <td><{$lang_php_vesion}></td>
                </tr>
                <tr>
                    <td><{$lang_version_mysql}></td>
                    <td><{$lang_mysql_version}></td>
                </tr>
                <tr>
                    <td><{$lang_server_api_name}></td>
                    <td><{$lang_server_api}></td>
                </tr>
                <tr>
                    <td><{$lang_os}></td>
                    <td><{$lang_os_name}></td>
                </tr>
                <{*<tr>*}>
                    <{*<td>safe_mode</td>*}>
                    <{*<td><{$safe_mode}></td>*}>
                <{*</tr>*}>
                <{*<tr>*}>
                    <{*<td>register_globals</td>*}>
                    <{*<td><{$register_globals}></td>*}>
                <{*</tr>*}>
                <{*<tr>*}>
                    <{*<td>magic_quotes_gpc</td>*}>
                    <{*<td><{$magic_quotes_gpc}></td>*}>
                <{*</tr>*}>
                <tr>
                    <td>allow_url_fopen</td>
                    <td><{$allow_url_fopen}></td>
                </tr>
                <tr>
                    <td>fsockopen</td>
                    <td><{$fsockopen}></td>
                </tr>
                <{*<tr>*}>
                    <{*<td>allow_call_time_pass_reference</td>*}>
                    <{*<td><{$allow_call_time_pass_reference}></td>*}>
                <{*</tr>*}>
                <tr>
                    <td>post_max_size</td>
                    <td><{$post_max_size}></td>
                </tr>
                <tr>
                    <td>max_input_time</td>
                    <td><{$max_input_time}></td>
                </tr>
                <tr>
                    <td>output_buffering</td>
                    <td><{$output_buffering}></td>
                </tr>
                <tr>
                    <td>max_execution_time</td>
                    <td><{$max_execution_time}></td>
                </tr>
                <tr>
                    <td>memory_limit</td>
                    <td><{$memory_limit}></td>
                </tr>
                <tr>
                    <td>file_uploads</td>
                    <td><{$file_uploads}></td>
                </tr>
                <tr>
                    <td>upload_max_filesize</td>
                    <td><{$upload_max_filesize}></td>
                </tr>
            </table>
        </dd>
        <!--[if IE]>
        </div></a>
        <![endif]-->

        <!--[if IE]>
        <a href="#Section2" class="ie">
            <div>
        <![endif]-->
        <dt><!--[if !IE]>--><a href="#Section2"><!--<![endif]-->XOOPS License<!--[if !IE]>--></a><!--<![endif]--></dt>
        <dd id="Section2">
            <table>
                <tr>
                    <td>XOOPS Key</td>
                    <td><{$smarty.const.XOOPS_LICENSE_KEY}></td>
                </tr>
                <tr>
                    <td>License</td>
                    <td>
                        <a rel="external" href="http://www.gnu.org/licenses/gpl.tpl" title="<{$smarty.const.XOOPS_LICENSE_CODE}>">
                            <{$smarty.const.XOOPS_LICENSE_CODE}>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <a rel="external" href="http://www.gnu.org/licenses/gpl-2.0.tpl" title="<{$smarty.const.XOOPS_LICENSE_TEXT}>">
                            <{$smarty.const.XOOPS_LICENSE_TEXT}>
                        </a>
                    </td>
                </tr>
            </table>
        </dd>
        <!--[if IE]>
        </div></a>
        <![endif]-->

        <!--[if IE]>
        <a href="#Section3" class="ie">
            <div>
        <![endif]-->
        <dt><!--[if !IE]>--><a href="#Section3"><!--<![endif]--><{$lang_about_xoops}><!--[if !IE]>--></a><!--<![endif]--></dt>
        <dd id="Section3">
            <div class="menu_body"><{$lang_about_xoops_text}></div>
        </dd>
        <!--[if IE]>
        </div></a>
        <![endif]-->

        <!--[if IE]>
        <a href="#Section4" class="ie">
            <div>
        <![endif]-->
        <dt><!--[if !IE]>--><a href="#Section4"><!--<![endif]--><{$lang_xoops_links}><!--[if !IE]>--></a><!--<![endif]--></dt>
        <dd id="Section4">
            <table>
                <tr>
                    <td>
                        <a class="tooltip" rel="external" href="http://xoops.org" title="<{$lang_xoops_xoopsproject}>"><{$lang_xoops_xoopsproject}></a>
                    </td>
                    <td>
                        <a class="tooltip" rel="external" href="https://github.com/XOOPS/XoopsCore25/releases" title="<{$lang_xoops_xoopscore}>"><{$lang_xoops_xoopscore}></a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a class="tooltip" rel="external" href="http://www.xoops.org/modules/xoopspartners" title="<{$lang_xoops_localsupport}>"><{$lang_xoops_localsupport}></a>
                    </td>
                    <td>
                        <a class="tooltip" rel="external" href="https://github.com/XOOPS/XoopsCore25" title="<{$lang_xoops_codesvn}>"><{$lang_xoops_codesvn}></a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <a class="tooltip" rel="external" href="https://github.com/XOOPS/XoopsCore25/issues" title="<{$lang_xoops_reportbug}>"><{$lang_xoops_reportbug}></a>
                    </td>
                </tr>
            </table>
        </dd>
        <!--[if IE]>
        </div></a>
        <![endif]-->
    </dl>
</div>
