<{* System Menu Administration Template *}>

<{* ─── Action Bar ─── *}>
<div class="sm-actionbar">
    <{if $op == 'list'}>
        <a href="<{$admin_url}>&op=addcat" class="btn btn-success">
            <i class="fa fa-plus"></i> <{$smarty.const._AM_SYSTEM_MENUS_ADDCAT}>
        </a>
    <{elseif $op == 'viewcat'}>
        <a href="<{$admin_url}>" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> <{$smarty.const._AM_SYSTEM_MENUS_NAV_BACK}>
        </a>
        <a href="<{$admin_url}>&op=additem&category_id=<{$category.id}>" class="btn btn-success">
            <i class="fa fa-plus"></i> <{$smarty.const._AM_SYSTEM_MENUS_ADDITEM}>
        </a>
    <{elseif $op == 'addcat' || $op == 'editcat' || $op == 'additem' || $op == 'edititem'}>
        <a href="<{$admin_url}>" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> <{$smarty.const._AM_SYSTEM_MENUS_NAV_BACK}>
        </a>
    <{/if}>
</div>

<{* ─── Error Message ─── *}>
<{if $error_message|default:''}>
    <div class="alert alert-danger"><{$error_message}></div>
<{/if}>

<{* ─── Category List ─── *}>
<{if $op == 'list'}>
    <div class="sm-tips alert alert-info">
        <{$smarty.const._AM_SYSTEM_MENUS_NAV_TIPS|default:''}>
    </div>

    <ul id="sm-cat-list" class="sm-list">
        <{foreach from=$categories item=cat}>
            <li class="sm-list__item<{if !$cat.active}> sm-list__item--inactive<{/if}>"
                data-id="<{$cat.id}>">
                <span class="sm-list__handle" title="Drag to reorder">
                    <i class="fa fa-arrows-v"></i>
                </span>
                <span class="sm-list__title">
                    <a href="<{$admin_url}>&op=viewcat&category_id=<{$cat.id}>">
                        <{$cat.title}>
                    </a>
                    <span class="sm-list__badge"><{$cat.itemCount}></span>
                </span>
                <span class="sm-list__url"><{$cat.url}></span>
                <span class="sm-list__actions">
                    <a href="#" class="sm-toggle sm-toggle--cat"
                       data-id="<{$cat.id}>"
                       data-active="<{$cat.active}>"
                       title="<{$smarty.const._AM_SYSTEM_MENUS_ACTIVE}>">
                        <i class="fa fa-<{if $cat.active}>check-circle text-success<{else}>times-circle text-danger<{/if}>"></i>
                    </a>
                    <a href="<{$admin_url}>&op=editcat&category_id=<{$cat.id}>"
                       title="<{$smarty.const._AM_SYSTEM_MENUS_EDITCAT}>">
                        <i class="fa fa-pencil"></i>
                    </a>
                    <{if !$cat.protected}>
                        <a href="<{$admin_url}>&op=delcat&category_id=<{$cat.id}>"
                           title="<{$smarty.const._AM_SYSTEM_MENUS_DELCAT}>">
                            <i class="fa fa-trash text-danger"></i>
                        </a>
                    <{/if}>
                </span>
            </li>
        <{/foreach}>
    </ul>

    <input type="hidden" id="sm-token" value="<{$token}>">
<{/if}>

<{* ─── Item Tree View ─── *}>
<{if $op == 'viewcat'}>
    <h3><{$category.title}></h3>

    <{* Item tree is built client-side from JSON data by menus.js *}>
    <ul id="sm-item-tree" class="sm-tree" data-category-id="<{$category.id}>">
    </ul>

    <input type="hidden" id="sm-token" value="<{$token}>">
    <input type="hidden" id="sm-items-json" value='<{$items|json_encode}>'>
<{/if}>

<{* ─── Form Display ─── *}>
<{if $form|default:''}>
    <{$form}>
<{/if}>
