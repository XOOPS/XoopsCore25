<{* online details popup *}>
<{if isset($closeHead) ? $closeHead : true}>
    <{$headContents|default:''}>
    <script>window.resizeTo(400, 560)</script>
    </head>
    <body>
<{/if}>
<h4><{$lang_whoisonline}></h4>

<div class="pad5">
    <table style="width:100%;" cellspacing="1" class="outer">
        <{foreach item=online from=$onlineUserInfo|default:null}>
            <tr>
                <td align="center"><img src="<{$upload_url}><{$online.avatar}>" alt="<{$lang_avatar}>" /><br><br></td>
                <td align="center">
                    <{if $online.uid == 0}>
                        <{$online.uname}>
                    <{else}>
                        <a href="javascript:window.opener.location='<{$xoops_url}>/userinfo.php?uid=<{$online.uid}>';window.close();">
                        <{if $online.name==''}><{$online.uname}><{else}><{$online.name}><{/if}></a>
                    <{/if}>
                </td>
                <td align="center">
                    <{$online.module_name}>
                    <{if !empty($isadmin)}>
                        <br>(<{$online.ip}>)<br><{$online.updated}>
                    <{/if}>
                </td>
            </tr>
        <{/foreach}>
    </table>
</div>

<{if isset($closeButton) ? $closeButton : true}>
    <div style="text-align:center;"><input class="btn btn-secondary btn-default formButton" value="<{$lang_close}>" type="button" onclick="window.close();" /></div>
<{/if}>
