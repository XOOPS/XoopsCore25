<{if !empty($block)}>
<div class="list-group">
<{foreach item=cat from=$block.cat|default:null}>
  <a href="<{$cat.link}>" class="list-group-item list-group-item-action"><{$cat.title}></a>
<{/foreach}>
</div>
<{/if}>
