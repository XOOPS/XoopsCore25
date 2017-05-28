<div class="xo-title" id="xo-title-accordion"><{$smarty.const._MD_CPANEL_OVERVIEW}></div>
<div id="accordion">

    <h5><{$smarty.const._MD_CPANEL_OVERVIEW}></h5>

    <div class="pane">
        <table>
            <tr>
                <td><{$smarty.const._OXYGEN_VERSION_XOOPS}></td>
                <td><{$xoops_version}></td>
            </tr>
            <tr>
                <td><{$smarty.const._OXYGEN_VERSION_PHP}></td>
                <td><{$lang_php_vesion}></td>
            </tr>
            <tr>
                <td><{$smarty.const._OXYGEN_VERSION_MYSQL}></td>
                <td><{$lang_mysql_version}></td>
            </tr>
            <tr>
                <td><{$smarty.const._OXYGEN_Server_API}></td>
                <td><{$lang_server_api}></td>
            </tr>
            <tr>
                <td><{$smarty.const._OXYGEN_OS}></td>
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
            <{*<tr>*}>
                <{*<td>allow_url_fopen</td>*}>
                <{*<td><{$allow_url_fopen}></td>*}>
            <{*</tr>*}>
            <tr>
                <td>fsockopen</td>
                <td><{$fsockopen}></td>
            </tr>
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
    </div>

    <h5><{$smarty.const._OXYGEN_XOOPS_LICENSE}></h5>

    <div class="pane">
        <p id="xolicenses"><a class="tooltip" rel="external" href="http://www.gnu.org/licenses/gpl-2.0.html" title="<{$smarty.const.XOOPS_LICENSE_TEXT}>"><{$smarty.const.XOOPS_LICENSE_TEXT}></a></p>
    </div>

    <h5><{$smarty.const._OXYGEN_ABOUT}></h5>

    <div class="pane"><{$smarty.const._OXYGEN_ABOUT_TEXT}></div>

    <h5><{$smarty.const._OXYGEN_XOOPS_LINKS}></h5>

    <div class="pane">
        <table>
            <tr>
                <td><a rel="external" href="http://xoops.org"><{$smarty.const._OXYGEN_XOOPSPROJECT}></a></td>
                <td><a rel="external" href="https://github.com/XOOPS/XoopsCore25/releases"><{$smarty.const._OXYGEN_XOOPSCORE}></a></td>
            </tr>
            <tr>
                <td><a rel="external" href="http://www.xoops.org/modules/xoopspartners"><{$smarty.const._OXYGEN_LOCALSUPPORT}></a></td>
                <td><a rel="external" href="https://github.com/XOOPS/XoopsCore25"><{$smarty.const._OXYGEN_CODESVN}></a></td>
            </tr>
            <tr>
                <td><a rel="external" href="https://github.com/XOOPS/XoopsCore25/issues"><{$smarty.const._OXYGEN_REPORTBUG}></a></td>
            </tr>
        </table>
    </div>

</div>

<script type="text/javascript">
    $("#accordion > h5").bind("click", function () {
        $(".pane").slideUp("fast");
        if ($(this).hasClass("current")) {
            $(this).removeClass("current");
        } else {
            $("#accordion > h5").removeClass("current");
            $(this).addClass("current");
            $(this).next().slideDown("fast");
        }
    });
</script>
