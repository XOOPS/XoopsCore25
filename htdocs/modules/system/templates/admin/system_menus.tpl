<{include file="db:system_header.tpl"}>
<script type="text/javascript">
/* expose labels for the external JS */
window.XOOPS_MENUS = window.XOOPS_MENUS || {};
window.XOOPS_MENUS.labels = {
    activeYes: "<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>",
    activeNo:  "<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>"
};
window.XOOPS_MENUS.messages = {
    parentInactive: "<{$smarty.const._AM_SYSTEM_MENUS_ERROR_PARENTINACTIVE}>"
};
</script>
<!-- Buttons -->
<{if $op|default:'' != 'delcat' && $op|default:'' != 'delitem'}>
<div class="floatright">
    <div class="xo-buttons">
        <{if $op|default:'' == 'list'}>
        <button class="ui-corner-all" onclick="self.location.href='admin.php?fct=menus&amp;op=addcat'">
            <img src="<{xoAdminIcons 'add.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ADDCAT}>"/>
            <{$smarty.const._AM_SYSTEM_MENUS_ADDCAT}>
        </button>
        <{/if}>
        <{if $op|default:'' == 'addcat' || $op|default:'' == 'editcat'}>
        <button class="ui-corner-all" onclick="self.location.href='admin.php?fct=menus'">
            <img src="<{xoAdminIcons 'folder.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_LISTCAT}>"/>
            <{$smarty.const._AM_SYSTEM_MENUS_LISTCAT}>
        </button>
        <{/if}>
        <{if $op|default:'' == 'viewcat'}>
        <button class="ui-corner-all" onclick="self.location.href='admin.php?fct=menus&amp;op=additem&amp;category_id=<{$category_id}>'">
            <img src="<{xoAdminIcons 'add.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ADDITEM}>"/>
            <{$smarty.const._AM_SYSTEM_MENUS_ADDITEM}>
        </button>
        <button class="ui-corner-all" onclick="self.location.href='admin.php?fct=menus'">
            <img src="<{xoAdminIcons 'folder.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_LISTCAT}>"/>
            <{$smarty.const._AM_SYSTEM_MENUS_LISTCAT}>
        </button>
        <{/if}>
        <{if $op|default:'' == 'additem' || $op|default:'' == 'edititem' || $op|default:'' == 'saveitem'}>
        <button class="ui-corner-all" onclick="self.location.href='admin.php?fct=menus&amp;op=viewcat&amp;category_id=<{$category_id}>'">
            <img src="<{xoAdminIcons 'folder.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_LISTITEM}>"/>
            <{$smarty.const._AM_SYSTEM_MENUS_LISTITEM}>
        </button>
        <{/if}>
    </div>
</div>
<div class="clear">&nbsp;</div>
<{/if}>
<{if $error_message|default:'' != ''}>
    <div class="errorMsg">
        <{$error_message}>
    </div>
<{/if}>
<{if $op|default:'' == 'list' && $category_count|default:0 != 0}>
    <ol class="xo-menus-tree" id="menus-row">
    <{foreach item=itemcategory from=$category}>
        <li class="xo-menus-branch<{if $itemcategory.items_count|default:0 gt 0}> xo-menus-has-children xo-menus-collapsed<{/if}>" data-id="<{$itemcategory.id|escape}>">
            <div class="xo-menus-node xo-menus-cat-node">
                <{if $itemcategory.items_count|default:0 gt 0}>
                    <span class="xo-menus-disclose" title="<{$smarty.const._AM_SYSTEM_MENUS_LISTITEM}>"><span></span></span>
                <{else}>
                    <span class="xo-menus-disclose xo-menus-no-toggle">&bull;</span>
                <{/if}>
                <span class="xo-menus-title">
                    <{$itemcategory.prefix}> <{$itemcategory.title|escape}> <{$itemcategory.suffix}>
                </span>
                <{if $itemcategory.url|default:'' != ''}>
                    <span class="xo-menus-url">
                        <a href="<{$itemcategory.url|escape}>" target="<{$itemcategory.target}>" rel="noopener"><{$itemcategory.url|escape}></a>
                    </span>
                <{/if}>
                <span class="xo-menus-status">
                    <{if $itemcategory.active}>
                        <span class="category-active-toggle xo-menus-active" data-id="<{$itemcategory.id|escape}>" data-active="1">
                            <img src="<{xoAdminIcons 'success.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>"/>
                        </span>
                    <{else}>
                        <span class="category-active-toggle xo-menus-inactive" data-id="<{$itemcategory.id|escape}>" data-active="0">
                            <img src="<{xoAdminIcons 'cancel.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>"/>
                        </span>
                    <{/if}>
                </span>
                <span class="xo-menus-badge" title="<{$smarty.const._AM_SYSTEM_MENUS_LISTITEM}>">
                    <{$itemcategory.items_count|default:0}>
                </span>
                <span class="xo-menus-actions">
                    <a class="tooltip" href="admin.php?fct=menus&amp;op=additem&amp;category_id=<{$itemcategory.id|escape}>"
                       title="<{$smarty.const._AM_SYSTEM_MENUS_ADDITEM}>">
                        <img src="<{xoAdminIcons 'add.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ADDITEM}>"/></a>
                    <a class="tooltip" href="admin.php?fct=menus&amp;op=editcat&amp;category_id=<{$itemcategory.id|escape}>"
                       title="<{$smarty.const._AM_SYSTEM_MENUS_EDITCAT}>">
                        <img src="<{xoAdminIcons 'edit.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_EDITCAT}>"/></a>
                    <{if $itemcategory.protected|default:0 == 0}>
                    <a class="tooltip" href="admin.php?fct=menus&amp;op=delcat&amp;category_id=<{$itemcategory.id|escape}>"
                       title="<{$smarty.const._AM_SYSTEM_MENUS_DELCAT}>">
                        <img src="<{xoAdminIcons 'delete.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_DELCAT}>"/></a>
                    <{/if}>
                </span>
            </div>
            <{if $itemcategory.items_count|default:0 gt 0}>
            <ol class="xo-menus-sortable xo-menus-children" data-cid="<{$itemcategory.id|escape}>">
                <{foreach item=item from=$itemcategory.items}>
                    <{if $item.pid == 0}>
                    <li id="item_<{$item.id|escape}>" class="ui-state-default">
                        <div class="xo-menus-sortable-row">
                            <span class="xo-menus-disclose"><span></span></span>
                            <span class="xo-menus-sortable-title">
                                <{$item.prefix}> <{$item.title|escape}> <{$item.suffix}>
                            </span>
                            <{if $item.url|default:'' != ''}>
                                <span class="xo-menus-sortable-url"><{$item.url|escape}></span>
                            <{/if}>
                            <span class="xo-menus-sortable-actions">
                                <{if $item.active}>
                                    <span class="item-active-toggle xo-menus-active" data-id="<{$item.id|escape}>" data-active="1">
                                        <img src="<{xoAdminIcons 'success.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>"/></span>
                                <{else}>
                                    <span class="item-active-toggle xo-menus-inactive" data-id="<{$item.id|escape}>" data-active="0">
                                        <img src="<{xoAdminIcons 'cancel.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>"/></span>
                                <{/if}>
                                <a class="tooltip" href="admin.php?fct=menus&amp;op=edititem&amp;item_id=<{$item.id|escape}>&amp;category_id=<{$itemcategory.id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"><img src="<{xoAdminIcons 'edit.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"/></a>
                                <{if $item.protected|default:0 == 0}>
                                <a class="tooltip" href="admin.php?fct=menus&amp;op=delitem&amp;item_id=<{$item.id|escape}>&amp;category_id=<{$itemcategory.id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"><img src="<{xoAdminIcons 'delete.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"/></a>
                                <{/if}>
                            </span>
                        </div>
                        <!-- level 1 children -->
                        <{foreach item=sub from=$itemcategory.items}>
                            <{if $sub.pid != 0 && $sub.pid == $item.id}>
                            <ol>
                                <li id="item_<{$sub.id|escape}>" class="ui-state-default">
                                    <div class="xo-menus-sortable-row">
                                        <span class="xo-menus-disclose"><span></span></span>
                                        <span class="xo-menus-sortable-title">
                                            <{$sub.prefix}> <{$sub.title|escape}> <{$sub.suffix}>
                                        </span>
                                        <{if $sub.url|default:'' != ''}>
                                            <span class="xo-menus-sortable-url"><{$sub.url|escape}></span>
                                        <{/if}>
                                        <span class="xo-menus-sortable-actions">
                                            <{if $sub.active}>
                                                <span class="item-active-toggle xo-menus-active" data-id="<{$sub.id|escape}>" data-active="1">
                                                    <img src="<{xoAdminIcons 'success.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>"/></span>
                                            <{else}>
                                                <span class="item-active-toggle xo-menus-inactive" data-id="<{$sub.id|escape}>" data-active="0">
                                                    <img src="<{xoAdminIcons 'cancel.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>"/></span>
                                            <{/if}>
                                            <a class="tooltip" href="admin.php?fct=menus&amp;op=edititem&amp;item_id=<{$sub.id|escape}>&amp;category_id=<{$itemcategory.id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"><img src="<{xoAdminIcons 'edit.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"/></a>
                                            <{if $sub.protected|default:0 == 0}>
                                            <a class="tooltip" href="admin.php?fct=menus&amp;op=delitem&amp;item_id=<{$sub.id|escape}>&amp;category_id=<{$itemcategory.id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"><img src="<{xoAdminIcons 'delete.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"/></a>
                                            <{/if}>
                                        </span>
                                    </div>
                                    <!-- level 2 children -->
                                    <{foreach item=subsub from=$itemcategory.items}>
                                        <{if $subsub.pid != 0 && $subsub.pid == $sub.id}>
                                        <ol>
                                            <li id="item_<{$subsub.id|escape}>" class="ui-state-default mjs-nestedSortable-no-nesting">
                                                <div class="xo-menus-sortable-row">
                                                    <span class="xo-menus-sortable-title">
                                                        <{$subsub.prefix}> <{$subsub.title|escape}> <{$subsub.suffix}>
                                                    </span>
                                                    <{if $subsub.url|default:'' != ''}>
                                                        <span class="xo-menus-sortable-url"><{$subsub.url|escape}></span>
                                                    <{/if}>
                                                    <span class="xo-menus-sortable-actions">
                                                        <{if $subsub.active}>
                                                            <span class="item-active-toggle xo-menus-active" data-id="<{$subsub.id|escape}>" data-active="1">
                                                                <img src="<{xoAdminIcons 'success.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>"/></span>
                                                        <{else}>
                                                            <span class="item-active-toggle xo-menus-inactive" data-id="<{$subsub.id|escape}>" data-active="0">
                                                                <img src="<{xoAdminIcons 'cancel.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>"/></span>
                                                        <{/if}>
                                                        <a class="tooltip" href="admin.php?fct=menus&amp;op=edititem&amp;item_id=<{$subsub.id|escape}>&amp;category_id=<{$itemcategory.id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"><img src="<{xoAdminIcons 'edit.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"/></a>
                                                        <{if $subsub.protected|default:0 == 0}>
                                                        <a class="tooltip" href="admin.php?fct=menus&amp;op=delitem&amp;item_id=<{$subsub.id|escape}>&amp;category_id=<{$itemcategory.id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"><img src="<{xoAdminIcons 'delete.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"/></a>
                                                        <{/if}>
                                                    </span>
                                                </div>
                                            </li>
                                        </ol>
                                        <{/if}>
                                    <{/foreach}>
                                </li>
                            </ol>
                            <{/if}>
                        <{/foreach}>
                    </li>
                    <{/if}>
                <{/foreach}>
            </ol>
            <{/if}>
        </li>
    <{/foreach}>
    </ol>
    <!-- Display page navigation -->
    <div class="clear spacer"></div>
    <{if !empty($nav_menu)}>
        <div class="xo-avatar-pagenav floatright"><{$nav_menu}></div>
        <div class="clear spacer"></div>
    <{/if}>
<{/if}>
<{if $op|default:'' == 'viewcat'}>
    <h4><{$cat_title}> <small>(#<{$category_id}>)</small></h4>
    <{if $items_count|default:0 != 0}>
        <ol class="xo-menus-sortable" id="menus-items-sortable">
            <{foreach item=item from=$items}>
                <{if $item.pid == 0}>
                <li id="item_<{$item.id|escape}>" class="ui-state-default">
                    <div class="xo-menus-sortable-row">
                        <span class="xo-menus-drag-handle" title="Drag to reorder">&#x2195;</span>
                        <span class="xo-menus-disclose"><span></span></span>
                        <span class="xo-menus-sortable-title">
                            <{$item.prefix}> <{$item.title|escape}> <{$item.suffix}>
                        </span>
                        <{if $item.url|default:'' != ''}>
                            <span class="xo-menus-sortable-url"><{$item.url|escape}></span>
                        <{/if}>
                        <span class="xo-menus-sortable-actions">
                            <{if $item.active}>
                                <span class="item-active-toggle xo-menus-active" data-id="<{$item.id|escape}>" data-active="1">
                                    <img src="<{xoAdminIcons 'success.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>"/></span>
                            <{else}>
                                <span class="item-active-toggle xo-menus-inactive" data-id="<{$item.id|escape}>" data-active="0">
                                    <img src="<{xoAdminIcons 'cancel.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>"/></span>
                            <{/if}>
                            <a class="tooltip" href="admin.php?fct=menus&amp;op=edititem&amp;item_id=<{$item.id|escape}>&amp;category_id=<{$category_id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"><img src="<{xoAdminIcons 'edit.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"/></a>
                            <{if $item.protected|default:0 == 0}>
                            <a class="tooltip" href="admin.php?fct=menus&amp;op=delitem&amp;item_id=<{$item.id|escape}>&amp;category_id=<{$category_id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"><img src="<{xoAdminIcons 'delete.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"/></a>
                            <{/if}>
                        </span>
                    </div>
                    <!-- level 1 children -->
                    <{foreach item=sub from=$items}>
                        <{if $sub.pid != 0 && $sub.pid == $item.id}>
                        <ol>
                            <li id="item_<{$sub.id|escape}>" class="ui-state-default">
                                <div class="xo-menus-sortable-row">
                                    <span class="xo-menus-drag-handle" title="Drag to reorder">&#x2195;</span>
                                    <span class="xo-menus-disclose"><span></span></span>
                                    <span class="xo-menus-sortable-title">
                                        <{$sub.prefix}> <{$sub.title|escape}> <{$sub.suffix}>
                                    </span>
                                    <{if $sub.url|default:'' != ''}>
                                        <span class="xo-menus-sortable-url"><{$sub.url|escape}></span>
                                    <{/if}>
                                    <span class="xo-menus-sortable-actions">
                                        <{if $sub.active}>
                                            <span class="item-active-toggle xo-menus-active" data-id="<{$sub.id|escape}>" data-active="1">
                                                <img src="<{xoAdminIcons 'success.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>"/></span>
                                        <{else}>
                                            <span class="item-active-toggle xo-menus-inactive" data-id="<{$sub.id|escape}>" data-active="0">
                                                <img src="<{xoAdminIcons 'cancel.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>"/></span>
                                        <{/if}>
                                        <a class="tooltip" href="admin.php?fct=menus&amp;op=edititem&amp;item_id=<{$sub.id|escape}>&amp;category_id=<{$category_id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"><img src="<{xoAdminIcons 'edit.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"/></a>
                                        <{if $sub.protected|default:0 == 0}>
                                        <a class="tooltip" href="admin.php?fct=menus&amp;op=delitem&amp;item_id=<{$sub.id|escape}>&amp;category_id=<{$category_id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"><img src="<{xoAdminIcons 'delete.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"/></a>
                                        <{/if}>
                                    </span>
                                </div>
                                <!-- level 2 children -->
                                <{foreach item=subsub from=$items}>
                                    <{if $subsub.pid != 0 && $subsub.pid == $sub.id}>
                                    <ol>
                                        <li id="item_<{$subsub.id|escape}>" class="ui-state-default mjs-nestedSortable-no-nesting">
                                            <div class="xo-menus-sortable-row">
                                                <span class="xo-menus-drag-handle" title="Drag to reorder">&#x2195;</span>
                                                <span class="xo-menus-sortable-title">
                                                    <{$subsub.prefix}> <{$subsub.title|escape}> <{$subsub.suffix}>
                                                </span>
                                                <{if $subsub.url|default:'' != ''}>
                                                    <span class="xo-menus-sortable-url"><{$subsub.url|escape}></span>
                                                <{/if}>
                                                <span class="xo-menus-sortable-actions">
                                                    <{if $subsub.active}>
                                                        <span class="item-active-toggle xo-menus-active" data-id="<{$subsub.id|escape}>" data-active="1">
                                                            <img src="<{xoAdminIcons 'success.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_YES}>"/></span>
                                                    <{else}>
                                                        <span class="item-active-toggle xo-menus-inactive" data-id="<{$subsub.id|escape}>" data-active="0">
                                                            <img src="<{xoAdminIcons 'cancel.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>" title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE_NO}>"/></span>
                                                    <{/if}>
                                                    <a class="tooltip" href="admin.php?fct=menus&amp;op=edititem&amp;item_id=<{$subsub.id|escape}>&amp;category_id=<{$category_id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"><img src="<{xoAdminIcons 'edit.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_EDITITEM}>"/></a>
                                                    <{if $subsub.protected|default:0 == 0}>
                                                    <a class="tooltip" href="admin.php?fct=menus&amp;op=delitem&amp;item_id=<{$subsub.id|escape}>&amp;category_id=<{$category_id|escape}>" title="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"><img src="<{xoAdminIcons 'delete.png'}>" alt="<{$smarty.const._AM_SYSTEM_MENUS_DELITEM}>"/></a>
                                                    <{/if}>
                                                </span>
                                            </div>
                                        </li>
                                    </ol>
                                    <{/if}>
                                <{/foreach}>
                            </li>
                        </ol>
                        <{/if}>
                    <{/foreach}>
                </li>
                <{/if}>
            <{/foreach}>
        </ol>
    <{else}>
        <div class="confirmMsg"><{$smarty.const._AM_SYSTEM_MENUS_ERROR_NOITEMS}></div>
    <{/if}>
<{/if}>
<!-- token container for JS -->
<div id="menus-token" style="display:none;"><{$xoops_token nofilter}></div>

<{if $form|default:'' != ''}>
<div class="spacer"><{$form|default:''}></div>
<{/if}>
