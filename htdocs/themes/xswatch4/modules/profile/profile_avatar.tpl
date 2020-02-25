<{include file="db:profile_breadcrumbs.tpl"}>

<{if $old_avatar}>
    <div class="pad10 center">
        <h4 class="bold red"><{$smarty.const._US_OLDDELETED}></h4>
        <img src="<{$old_avatar}>" alt="" />
    </div>
<{/if}>

<{if $uploadavatar}>
<{$uploadavatar.javascript}>
<legend class="bold"><{$uploadavatar.title}></legend>
<form name="<{$uploadavatar.name}>" action="<{$uploadavatar.action}>" method="<{$uploadavatar.method}>" <{$uploadavatar.extra}>>
	<div class="form-group row">
		<!-- start of form elements loop -->
		<{foreach item=element from=$uploadavatar.elements}>
			<{if !$element.hidden}>
				<label class="col-2 col-form-label">
					<span class='caption-text'><{$element.caption}></span>
				</label>
				<div class="col-10">
					<{$element.body}>
				</div>
		    <{else}>
			<{$element.body}>
			<{/if}>
			<{if $element.description != ""}>
				<small id="passwordHelpBlock" class="form-text text-muted">
					<{$element.description}>
				</small>
			<{/if}>
		<{/foreach}>
		<!-- end of form elements loop -->	
	</div>
</form>
<br>
<{/if}>

<br>
<{$chooseavatar.javascript}>
<legend class="bold"><{$chooseavatar.title}></legend>
<form name="<{$chooseavatar.name}>" action="<{$chooseavatar.action}>" method="<{$chooseavatar.method}>" <{$chooseavatar.extra}>>
	<div class="form-group">
		<!-- start of form elements loop -->
		<{foreach item=element from=$chooseavatar.elements}>
			<{if !$element.hidden}>
				<label class="col-sm-2 col-form-label">
					<span class='caption-text'><{$element.caption}></span>
				</label>
				<div class="col-sm-10">
					<{$element.body}>
				</div>
			<{else}>
			<{$element.body}>
			<{/if}>
			<{if $element.description != ""}>
				<small id="passwordHelpBlock" class="form-text text-muted">
					<{$element.description}>
				</small>
			<{/if}>
		<{/foreach}>
		<!-- end of form elements loop -->	
	</div>
</form>
