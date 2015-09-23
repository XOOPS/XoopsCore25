<div class="xoopstube-blocks row">
    <{foreach item=videoload from=$block.videos}>
        <div class="col-md-4 xoopstube-thumb">
            <a href="<{$xoops_url}>/modules/<{$videoload.dirname}>/singlevideo.php?cid=<{$videoload.cid}>&lid=<{$videoload.id}>"><{$videoload.videothumb}></a>
                <span>
                    <a href="<{$xoops_url}>/modules/<{$videoload.dirname}>/singlevideo.php?cid=<{$videoload.cid}>&lid=<{$videoload.id}>"><{$videoload.title}></a>
                    <{$videoload.date}>
                </span>
        </div>
    <{/foreach}>
</div>
