<a class='' href='index.php?op=list&amp;alb_pid=<{$category.id}>' title='<{$smarty.const._CO_WGGALLERY_COLL_ALBUMS}>'>
<{if $category.image}>
    <div class="simpleContainer center">
            <img class="img-fluid" src="<{$category.image}>" alt="<{$category.name}>" title="<{$category.name}>">
            <div class="simpleContent">
                <{if $showTitle}><p><{$category.name}></p><{/if}>
                <{if $showDesc}><p><{$category.desc}></p><{/if}>
            </div>
    </div>
<{/if}>
</a>