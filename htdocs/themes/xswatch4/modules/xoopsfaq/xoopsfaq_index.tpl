<ol class="breadcrumb">
    <li class="breadcrumb-item active"><a href="index.php"><{$smarty.const._XO_LA_XOOPSFAQ}></a></li>
</ol>

<{if $categories|@count == 0}>
    <div class="alert alert-warning"><{$smarty.const._MD_XOOPSFAQ_NO_CATS}></div>
<{else}>
<div class="alert alert-primary"><{$smarty.const._MD_XOOPSFAQ_CAT_LISTING}></div>
<ul class="list-group">
    <{foreach item=category from=$categories|default:null}>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <a href="index.php?cat_id=<{$category.id}>"><{$category.name}></a>
        <span class="badge badge-primary badge-pill"><{$category.questions|@count}></span>
    </li>
    <{/foreach}>
</ul>
<{/if}>
