<{$xoForm.javascript}>
<form id="<{$xoForm.name}>" name="<{$xoForm.name}>" action="<{$xoForm.action}>" method="<{$xoForm.method}>" <{$xoForm.extra}> >
	<div class="form-group">
		<{foreach item=element from=$xoForm.elements}>
            <{if !$element.hidden}>
				<label>
					<div class='xoops-form-element-caption<{if $element.required}>-required<{/if}>'>
						<span class='caption-text'><{$element.caption}></span>
						<span class='caption-marker'>*</span>
					</div>
				</label>				
				<{$element.body}>
            <{/if}>
			<{if $element.description != ""}>
				<small id="passwordHelpBlock" class="form-text text-muted">
					<{$element.description}>
				</small>
			<{/if}>
        <{/foreach}>
	</div>
	<{foreach item=element from=$xoForm.elements}>
        <{if $element.hidden}>
            <{$element.body}>
        <{/if}>
    <{/foreach}>
</form>
