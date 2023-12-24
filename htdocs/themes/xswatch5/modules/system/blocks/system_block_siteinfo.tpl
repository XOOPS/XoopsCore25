<{if $block.showgroups == true}>
    <div class="d-inline-flex flex-column">
        <!-- start group loop -->
        <{foreach item=group from=$block.groups|default:null}>
            <div class="fw-bold"><{$group.name|default:''}></div>
            <!-- start group member loop -->
            <{foreach item=user from=$group.users|default:null}>
        
                <div class="align-self-center">
                    <figure class="figure text-center">
                        <img src="<{$user.avatar}>" class="figure-img rounded" alt="<{$user.name}>" style="width:48px;">
                        <figcaption class="figure-caption"><a href="<{$xoops_url}>/userinfo.php?uid=<{$user.id}>" title="<{$user.name}>" class="text-decoration-none"><{$user.name}></a></figcaption>
                    </figure>
                            
                    <a href="javascript:openWithSelfMain('<{$xoops_url}>/pmlite.php?send2=1&to_userid=<{$user.id}>','pmlite',565,500);">
                        <span class="fa fa-envelope fa-lg text-info" aria-hidden="true"></span>
                    </a>
                </div>
            <{/foreach}>
            <!-- end group member loop -->
        <{/foreach}>
        <!-- end group loop -->
    </div>
    <hr />
<{/if}>
<div class="">
    <img src="<{$block.logourl}>" alt=""/><br><{$block.recommendlink}>
</div>
