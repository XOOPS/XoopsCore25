<{if !empty($xoops_isadmin)}>
<a class="toolbar-block-edit btn btn-large btn-warning" href="<{xoAppUrl '/modules/system/admin.php?fct=blocksadmin&op=edit&bid='}><{$block.id}>" title="<{$smarty.const.THEME_TOOLBAR_EDIT_THIS_BLOCK}>"><span class="fa-solid fa-pen-to-square"></span></a>
<{/if}>
<{$block.content}>
