<{* avatar selector popup *}>
<{if isset($closeHead) ? $closeHead : true}>
<{$headContents|default:''}>
<script>window.resizeTo(600, 400)</script>
</head>
<body>
<{/if}>
<h4><{$lang_avavatars}></h4>
<table>
    <{counter name=avatarid start=-1 print=false}>
    <{counter name=loopid start=0 print=false}>
    <{assign var=tdcnt value=1}>
    <tr>
    <{foreach item=name from=$avatars|default:null key=file }>
        <td align="center" valign="center">
            <img src="<{$upload_url}><{$file}>" alt="<{$name}>" title="<{$name}>" /><br>
            <{$name}><br>
            <{counter name=avatarid assign=avatarid}>
            <{counter name=loopid assign=loopid}>
            <button name="myimage" type="button" onclick="myimage_onclick(<{$avatarid}>)"><{$lang_select}></button>
        </td>
        <{if $loopid is div by 4}>
            </tr>
            <tr>
        <{/if}>
    <{/foreach}>
    </tr>
</table>
<{if isset($closeButton) ? $closeButton : true}>
    <div style="text-align:center;"><input class="formButton" value="<{$lang_close}>" type="button" onclick="window.close();" /></div>
<{/if}>

