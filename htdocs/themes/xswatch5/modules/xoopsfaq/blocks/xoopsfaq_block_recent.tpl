<{if !empty($block)}>
<div>
  <{foreach item=faq from=$block.faq|default:null}>
  <div class="card">
    <div class="card-header">
      <{$faq.title}>
      <{if 1 == $block.show_date}>&nbsp;<small>(<{$faq.published}>)</small><{/if}>
    </div>
    <ul class="list-group list-group-flush">
      <li class="list-group-item"><{$faq.ans}>
      <{* requires xoopsfaq 2.0 addition of id and cid in faq variable for link support *}>
      <{if !empty($faq.id)}>
      <a class="card-link stretched-link" href="<{$xoops_url}>/modules/xoopsfaq/index.php?cat_id=<{$faq.cid}>#q<{$faq.id}>">
        <i class="fa fa-forward alignright" aria-hidden="true"></i>
      </a>
      <{/if}>
      </li>
    </ul>
  </div>
  <{/foreach}>
</div>
<{/if}>
