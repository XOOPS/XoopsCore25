<div class="xoopstube">
    <{if $catarray.imageheader != ""}>
        <div class="xoopstube-header text-center">
            <{$catarray.imageheader}>
        </div>
        <!-- .xoopstube-header -->
    <{/if}>

    <{$description}>

    <div class="text-center xoopstube-navigation">
        <{$catarray.letters}>
    </div><!-- .xoopstube-navigation -->

    <{$category_path}>

    <{if $subcategories}>
        <{$smarty.const._MD_XOOPSTUBE_SUBCATLISTING}>
        <{foreach item=subcat from=$subcategories}>
            <a href="viewcat.php?cid=<{$subcat.id}>" title="<{$subcat.alttext}>"><img src="<{$subcat.image}>" alt="<{$subcat.alttext}>"></a>
            <a href="viewcat.php?cid=<{$subcat.id}>"><{$subcat.title}></a>
            (<{$subcat.totalvideos}>)
            <{if $subcat.infercategories}>
                <{$subcat.infercategories}>
            <{/if}>
        <{/foreach}>
    <{/if}>


    <div class="order-by">
        <{if $show_videos == true}>
            <h3 class="xoops-default-title"><{$smarty.const._MD_XOOPSTUBE_SORTBY}></h3>
            <div class="row">
                <div class="col-sm-3 col-md-3">
                    <{$smarty.const._MD_XOOPSTUBE_TITLE}>
                    <a href="viewcat.php?cid=<{$category_id}>&orderby=titleA">
                        <span class="glyphicon glyphicon glyphicon-collapse-up"></span>
                    </a>
                    <a href="viewcat.php?cid=<{$category_id}>&orderby=titleD">
                        <span class="glyphicon glyphicon glyphicon-collapse-down"></span>
                    </a>
                </div>

                <div class="col-sm-3 col-md-3">
                    <{$smarty.const._MD_XOOPSTUBE_DATE}>
                    <a href="viewcat.php?cid=<{$category_id}>&orderby=dateA">
                        <span class="glyphicon glyphicon glyphicon-collapse-up"></span>
                    </a>
                    <a href="viewcat.php?cid=<{$category_id}>&orderby=dateD">
                        <span class="glyphicon glyphicon glyphicon-collapse-down"></span>
                    </a>
                </div>

                <div class="col-sm-3 col-md-3">
                    <{$smarty.const._MD_XOOPSTUBE_RATING}>
                    <a href="viewcat.php?cid=<{$category_id}>&orderby=ratingA">
                        <span class="glyphicon glyphicon glyphicon-collapse-up"></span>
                    </a>
                    <a href="viewcat.php?cid=<{$category_id}>&orderby=ratingD">
                        <span class="glyphicon glyphicon glyphicon-collapse-down"></span>
                    </a>
                </div>

                <div class="col-sm-3 col-md-3">
                    <{$smarty.const._MD_XOOPSTUBE_POPULARITY}>
                    <a href="viewcat.php?cid=<{$category_id}>&orderby=hitsA">
                        <span class="glyphicon glyphicon glyphicon-collapse-up"></span>
                    </a>
                    <a href="viewcat.php?cid=<{$category_id}>&orderby=hitsD">
                        <span class="glyphicon glyphicon glyphicon-collapse-down"></span>
                    </a>
                </div>

                <div class="col-md-12"><p class="text-center text-muted"><{$lang_cursortedby}></p></div>
            </div>
            <!--.row -->
        <{/if}>
    </div><!-- .order-by -->

    <{if $page_nav == true}>
        <{$pagenav}>
    <{/if}>

    <{section name=i loop=$video}>
        <{include file="db:xoopstube_videoload.tpl" video=$video[i]}>
    <{/section}>

    <{if $page_nav == true}>
        <{$pagenav}>
    <{/if}>

    <{if $moderate == true}>
        <{$smarty.const._MD_XOOPSTUBE_MODERATOR_OPTIONS}>

        <{section name=a loop=$mod_arr}>
            <{include file="db:xoopstube_videoload.tpl" video=$mod_arr[a]}>
        <{/section}>
    <{/if}>

    <{include file="db:system_notification_select.tpl"}>
</div><!-- .xoopstube -->
