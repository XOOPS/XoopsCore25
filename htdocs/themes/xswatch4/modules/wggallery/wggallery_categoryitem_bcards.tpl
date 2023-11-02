<{assign var='cols' value='col-12'}>
<{if isset($number_cols_album)}>
    <{if $number_cols_album == 6}><{assign var='cols' value='col-12 col-md-2'}>
    <{elseif $number_cols_album == 4}><{assign var='cols' value='col-12 col-md-3'}>
    <{elseif $number_cols_album == 3}><{assign var='cols' value='col-12 col-md-4'}>
    <{elseif $number_cols_album == 2}><{assign var='cols' value='col-12 col-md-6'}>
    <{/if}>
<{/if}>
<div class="card <{$cols}>">
    <{if $category.image}>
        <a class='' href='index.php?op=list&amp;alb_pid=<{$category.id}>' title='<{$smarty.const._CO_WGGALLERY_COLL_ALBUMS}>'>
            <img class="card-img-top img-responsive" src="<{$category.image}>" alt="<{$category.name}>" title="<{$category.name}>"></a>
    <{/if}>
    <div class="card-body">
        <{if isset($showTitle)}><h5 class="center"><{$category.name}></h5><{/if}>
        <{if isset($showDesc)}><p class="center"><{$category.desc}></p><{/if}>
        <p class="center"><a class='btn btn-primary wg-color1' href='index.php?op=list&amp;alb_pid=<{$category.id}>' title='<{$smarty.const._CO_WGGALLERY_COLL_ALBUMS}>'><{$smarty.const._CO_WGGALLERY_COLL_ALBUMS}></a></p>
    </div>
</div>
<{if $category.linebreak}>
	<div class='clear'>&nbsp;</div>
<{/if}>
