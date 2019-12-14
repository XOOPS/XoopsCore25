<{if $block.template}>
	<{if $block.dotitle == 1}>
	<h2><{$block.title}></h2>
	<{/if}>
	<{foreach item=b_template from=$block.template}>
		<{includeq file="$b_template"}>
	<{/foreach}>
<{else}>
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
<{/if}>