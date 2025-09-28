<div class="d-flex flex-row mt-1 mb-3">
   <div class=""><span class="fa-solid fa-users fa-lg text-success"></span></div>
   <div class="ml-2"><{$block.online_total|default:0}></div>
</div>
<p>
   <{if $block.online_guests > 1}>
      <span class="fa-solid fa-users text-secondary"></span> <{$block.online_guests}> <{$smarty.const.THEME_OL_GUESTS}>
   <{else}>
      <span class="fa-solid fa-user text-secondary"></span> <{$block.online_guests}>  <{$smarty.const.THEME_OL_GUEST}>
   <{/if}>
   <br />
   <{if $block.online_members > 1}>
      <span class="fa-solid fa-users text-info"></span> <{$block.online_members}> <{$smarty.const.THEME_OL_MEMBERS}>
   <{else}>
      <span class="fa-solid fa-user text-info"></span> <{$block.online_members}> <{$smarty.const.THEME_OL_MEMBER}>
   <{/if}>
</p>
<p>
   <{$block.online_names}> <a class="" href="javascript:openWithSelfMain('<{$xoops_url}>/misc.php?action=showpopups&amp;type=online','Online',420,350);"
      title="<{$block.lang_more}>"><span class="fa-solid fa-magnifying-glass-plus fa-lg"></span></a>
</p>
