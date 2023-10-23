<{* online details popup *}>
<{if isset($closeHead) ? $closeHead : true}>
    <{$headContents|default:''}>
    <script>window.resizeTo(260, 560)</script>
    </head>
    <body>
<{/if}>

<h4 class="text-center"><{$lang_whoisonline}></h4>

<{if isset($closeButton) ? $closeButton : true}>
    <div class="text-center m-3"><input class="btn btn-primary btn-block" value="<{$lang_close}>" type="button" onclick="window.close();" /></div>
<{/if}>

<{foreach item=online from=$onlineUserInfo|default:null}>
    <div class="row justify-content-center align-items-center <{cycle values='alert-primary,alert-secondary'}>">
        <div class="col-12 col-sm-3 text-center mt-2">
            <{if $online.uid == 0}>
                <h6><{$online.uname}></h6>
            <{else}>
                <{if $online.avatar != "avatars/blank.gif" }>
                    <img src="<{$upload_url}><{$online.avatar}>" alt="<{$lang_avatar}>" class="img-fluid rounded mt-2"/><br /> 
                    <a href="javascript:window.opener.location='<{$xoops_url}>/userinfo.php?uid=<{$online.uid}>';window.close();">
                        <{if $online.name==''}><{$online.uname}><{else}><{$online.name}><{/if}>
                    </a>
                <{else}>        
                    <h6><{if $online.name==''}><{$online.uname}><{else}><{$online.name}><{/if}></h6>
                <{/if}>	
            <{/if}>
        </div>
    
        <div class="col-12 col-sm-6 my-1">
            <{if $online.module_name <> "" }>
                <h5 class="text-center text-sm-left font-weight-bold"><{$online.module_name}></h5>
            <{/if}>
            <{if !empty($isadmin)}>
                <div class="ml-5 ml-sm-0">
                    <span class="fa fa-map-marker fa-fw "></span> <{$online.ip}>
                    <br>
                    <span class="fa fa-calendar fa-fw "></span> <{$online.updated}>
                </div>    
            <{/if}>
        </div>
    </div>
<{/foreach}>

<{if isset($closeButton) ? $closeButton : true}>
    <div class="text-center m-3"><input class="btn btn-primary btn-block" value="<{$lang_close}>" type="button" onclick="window.close();" /></div>
<{/if}>
