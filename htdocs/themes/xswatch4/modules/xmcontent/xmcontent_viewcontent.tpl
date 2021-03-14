<{if $content_template|default:false}>
	<{if $content_dotitle == 1}>
	<h2><{$content_title}></h2>
	<{/if}>
	<{includeq file="$content_template"}>
<{else}>
	<{if $content_warning != ''}>
		<div class="row">
			<div class="col-12 alert alert-warning">
				<{$content_warning}>
			</div>
		</div>
	<{/if}>
	<div class="row">
		<div class="col-sm-12">
			<{if $content_dotitle == 1}>
			<h2><{$content_title}></h2>
			<{/if}>
			<p>
				<{$content_text}>
			</p>
		</div>
	</div>
	<{if $content_error != ''}>
		<div class="row">
			<div class="col-12 alert alert-danger">
				<{$content_error}>
			</div>
		</div>
	<{/if}>
<{/if}>
<{if $dorating == 1}>
	<div class="row">
		<div class="col-12">
			<{include file="db:xmsocial_rating.tpl" down_xmsocial=$xmsocial_arr}>
		</div>
	</div>
<{/if}>
<{if $social == true}>
	<{include file="db:xmsocial_social.tpl"}>
	<br>
<{/if}>
<{if $perm_edit == true}>
	<div align="center">
		<a class="btn btn-secondary" href="action.php?op=edit&content_id=<{$content_id}>"><i class="fa fa-edit" aria-hidden="true"></i> <{$smarty.const._AM_XMCONTENT_EDIT}></a>
	</div>
<{/if}>
<{if $xmdoc_viewdocs|default:false == true}>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><{$smarty.const._AM_XMCONTENT_VIEWCONTENT_XMDOC}></h3>
        </div>
        <div class="panel-body">
            <{include file="db:xmdoc_viewdoc.tpl"}>
        </div>
    </div>
<{/if}>
<{if $content_docomment == 1}>
    <div style="text-align: center; padding: 3px; margin:3px;">
        <{$commentsnav}>
        <{$lang_notice}>
    </div>
    <div style="margin:3px; padding: 3px;">
        <{if $comment_mode == "flat"}>
        <{include file="db:system_comments_flat.tpl"}>
        <{elseif $comment_mode == "thread"}>
        <{include file="db:system_comments_thread.tpl"}>
        <{elseif $comment_mode == "nest"}>
        <{include file="db:system_comments_nest.tpl"}>
        <{/if}>
    </div>
<{/if}>