<{if $block.template|default:false}>
	<{if $block.dotitle == 1}>
	<h2><{$block.title}></h2>
	<{/if}>
	<{foreach item=b_template from=$block.template}>
		<{include file="$b_template"}>
	<{/foreach}>
<{else}>
	<{if $block.warning != ''}>
	<div class="row">
		<div class="col-sm-12 alert alert-warning">
			<{$block.warning}>
		</div>
	</div>
	<{/if}>
	<div class="row">
		<div class="col-sm-12">
			<{if $block.dotitle == 1}>
			<h2><{$block.title}></h2>
			<{/if}>
			<p>
				<{$block.text}>
			</p>
		</div>
	</div>
	<{if $block.error != ''}>
	<div class="row">
		<div class="col-sm-12 alert alert-danger">
			<{$block.error}>
		</div>
	</div>
	<{/if}>
<{/if}>
<{if $block.dorating == 1}>
	<div class="row">
		<div class="col-sm-12">
			<{include file="db:xmsocial_rating.tpl" down_xmsocial=$block.xmsocial_arr}>
		</div>
	</div>
<{/if}>
<{if $block.perm_edit == true}>
<div align="center">
	<a class="btn btn-secondary" href="<{$xoops_url}>/modules/xmcontent/action.php?op=edit&content_id=<{$block.id}>"><i class="fa fa-edit" aria-hidden="true"></i> <{$smarty.const._AM_XMCONTENT_EDIT}></a>
</div>
<{/if}>
