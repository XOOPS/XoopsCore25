<figure class="effect-<{$hovereffect}> figure<{$number_cols_cat}>">
    <img class='' src='<{$category.image}>' alt='<{$category.name}>'>
    <figcaption>
        <div class="text_figure<{$number_cols_cat}>">
            <h3><{$category.name}></h3>
            <{if $category.desc}><p><{$category.desc}></p><{/if}>
        </div>
        <a class='' href='index.php?op=list&amp;alb_pid=<{$category.id}><{if isset($subm_id)}>&amp;subm_id=<{$subm_id}><{/if}>' title='<{$smarty.const._CO_WGGALLERY_COLL_ALBUMS}>'></a>
    </figcaption>
</figure>
