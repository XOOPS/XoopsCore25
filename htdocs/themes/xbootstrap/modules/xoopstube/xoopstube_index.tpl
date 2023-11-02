<div class="xoopstube ">
    <{if !empty($catarray.imageheader)}>
        <div class="xoopstube-header text-center">
            <{$catarray.imageheader}>
        </div>
        <!-- .xoopstube-header -->
    <{/if}>

    <{if !empty($catarray.indexheading)}>
        <div class="text-center xoopstube-header-text">
            <h1><{$catarray.indexheading}></h1>
        </div>
        <!-- .xoopstube-header-text -->
    <{/if}>

    <{if !empty($catarray.indexheader)}>
        <div class="xoopstube-description text-center">
            <{$catarray.indexheader}>
        </div>
        <!-- .xoopstube-description -->
    <{/if}>

    <div class="text-center xoopstube-navigation">
        <{$catarray.letters}>
    </div><!-- .xoopstube-navigation -->

    <{if count($categories) > 0}>
        <h1 class="xoops-default-title"><{$smarty.const._MD_XOOPSTUBE_MAINLISTING}></h1>
        <div class="row">
            <{foreach item=category from=$categories|default:null}>
                <div class="col-sm-4 col-md-4 category-titles">
                    <a href="<{$xoops_url}>/modules/<{$module_dir}>/viewcat.php?cid=<{$category.id}>" title="<{$category.title}>"
                       class="btn btn-primary btn-block">
                        <{$category.title}> <span class="badge"><{$category.totalvideos}></span>
                    </a>
                </div>
                <{if $category.subcategories}>
                    <{$category.subcategories}>
                <{/if}>
            <{/foreach}>
        </div>
        <div class="xoopstube-data">
            <div class="row">
                <div class="col-md-12 text-right"><{$lang_thereare}></div>
            </div>
        </div>
        <!-- .xoopstube-data -->
    <{/if}>

    <div class="xoopstube-footer">
        <{$catarray.indexfooter}>
    </div><!-- .xoopstube-footer -->

    <{if !empty($showlatest)}>
        <{$smarty.const._MD_XOOPSTUBE_LATESTLIST}>
        <{if isset($pagenav)}>
            <{$pagenav}>
        <{/if}>
        <{section name=i loop=$video}>
            <{include file="db:xoopstube_videoload.tpl" video=$video[i]}>
        <{/section}>
        <{if isset($pagenav)}>
            <{$pagenav}>
        <{/if}>
    <{/if}>
    <{include file="db:system_notification_select.tpl"}>
</div><!-- .xoopstube -->
