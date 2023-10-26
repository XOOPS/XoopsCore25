<{$xoForm.rendered}>
<{*
<{$xoForm.javascript}>
<form id="<{$xoForm.name}>" name="<{$xoForm.name}>" action="<{$xoForm.action}>" method="<{$xoForm.method}>" <{$xoForm.extra}> >
	<div class="form-group">
		<{foreach item=element from=$xoForm.elements|default:null}>
            <{if empty($element.hidden)}>
				<label>
					<div class='xoops-form-element-caption<{if !empty($element.required)}>-required<{/if}>'>
						<span class='caption-text'><{$element.caption|default:''}></span>
						<span class='caption-marker'>*</span>
					</div>
				</label>
				<{$element.body}>
            <{/if}>
			<{if $element.description|default:"" != ""}>
				<small id="passwordHelpBlock" class="form-text text-muted">
					<{$element.description}>
				</small>
			<{/if}>
        <{/foreach}>
	</div>
	<{foreach item=element from=$xoForm.elements|default:null}>
        <{if !empty($element.hidden)}>
            <{$element.body}>
        <{/if}>
    <{/foreach}>
</form>
*}>
