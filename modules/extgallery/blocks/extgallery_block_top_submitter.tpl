<ul class="list-unstyled">
    <{foreach item=designer from=$block.designers}>
        <li>
            <a class="btn btn-primary btn-block" href="<{$xoops_url}>/modules/extgallery/public-useralbum.php?id=<{$designer.uid}>" title="<{$designer.uname}>">
                <{$designer.uname}>
                <span class="badge"><{$designer.countphoto}></span>
            </a>
        </li>
    <{/foreach}>
</ul>
