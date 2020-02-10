<{if !empty($block)}>
<div class="list-group">
<{foreach from=$block.cat item=cat}>
  <a href="<{$cat.link}>" class="list-group-item list-group-item-action"><{$cat.title}></a>
<{/foreach}>
</div>
<{/if}>
