<{if !empty($error)}>
	<div class='errorMsg'><strong><{$error}></strong></div>
<{/if}>
<div class='clear spacer'></div>
<{if isset($copyright)}>
    <div class="pull-right"><{$copyright}></div>
<{/if}>
<{if isset($xoops_isadmin)}>
    <div class='clear spacer'></div>
    <div class="text-center bold"><a href="<{$admin}>"><{$smarty.const._MA_WGGALLERY_ADMIN}></a></div><br>
<{/if}>
<{if !empty($comment_mode)}>
<div class="pad2 marg2">
    <{if isset($comment_mode)}>
        <{if $comment_mode == "flat"}>
            <{include file="db:system_comments_flat.tpl"}>
        <{elseif $comment_mode == "thread"}>
            <{include file="db:system_comments_thread.tpl"}>
        <{elseif $comment_mode == "nest"}>
            <{include file="db:system_comments_nest.tpl"}>
        <{/if}>
    <{/if}>
</div>
<{/if}>
<div class='clear spacer'></div>
<{include file='db:system_notification_select.tpl'}>
