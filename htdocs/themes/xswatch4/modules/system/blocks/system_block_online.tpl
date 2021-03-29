<div class="d-flex flex-row mt-1 mb-3">
   <div class=""><span class="fa fa-users fa-lg fa-fw text-success"></span></div>
   <div class="ml-2"><{$block.online_total}></div>
</div>
<p>
   <{if $block.online_guests > 1}>
      <span class="fa fa-users fa-fw text-secondary"></span> <{$block.online_guests}> <{$smarty.const.THEME_OL_GUESTS}>
   <{else}>
      <span class="fa fa-user fa-fw text-secondary"></span> <{$block.online_guests}>  <{$smarty.const.THEME_OL_GUEST}>
   <{/if}>
   <br />
   <{if $block.online_members > 1}>
      <span class="fa fa-users fa-fw text-info"></span> <{$block.online_members}> <{$smarty.const.THEME_OL_MEMBERS}>
   <{else}>
      <span class="fa fa-user fa-fw text-info"></span> <{$block.online_members}> <{$smarty.const.THEME_OL_MEMBER}>
   <{/if}>
</p>
<p>
   <{$block.online_names}> <a class="" href="javascript:openWithSelfMain('<{$xoops_url}>/misc.php?action=showpopups&amp;type=online','Online',420,350);"
      title="<{$block.lang_more}>"><span class="fa fa-search-plus fa-lg fa-fw "></span></a>
</p>