<{if count($obituaries_users) > 0}>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<{$xoops_url}>/modules/obituaries/index.php">Obituaries</a></li>
            <li class="breadcrumb-item active" aria-current="page"><{$smarty.const._AM_OBITUARIES_USERS_LIST}></li>
        </ol>
    </nav>
    <p align="center">
        <a href="<{$xoops_url}>/modules/obituaries/index.php"><img src="<{$xoops_url}>/modules/obituaries/assets/images/logoModule.png" alt="<{$module.name}>" class="img-thumbnail"/></a>
    </p>
<div class="row">
        <{foreach item=obituaries_user from=$obituaries_users|default:null}>
    <div class="card col-8 col-sm-6 col-md-4 col-xl-3">
        <{if trim($obituaries_user.obituaries_full_imgurl) != ''}>
         <img class="card-img-top img-fluid" src="<{$obituaries_user.obituaries_full_imgurl}>" alt="<{$obituaries_user.obituaries_href_title}>">
        <{else}>
        <img class="card-img-top" src="<{$xoops_url}>/modules/obituaries/assets/images/nophoto.jpg" alt="<{$obituaries_user.obituaries_href_title}>">
        <{/if}>
        <div class="card-body">
            <h5 class="card-title">
                <{$obituaries_user.obituaries_lastname}>, <{$obituaries_user.obituaries_firstname}>
            </h5>
            <p class="card-text text-muted"><{$obituaries_user.obituaries_formated_date}></p>
            <p class="card-text"><{$obituaries_user.obituaries_description|truncateHtml:20:'...'}>
            <a class="stretched-link" href="<{$smarty.const.OBITUARIES_URL}>user.php?obituaries_id=<{$obituaries_user.obituaries_id}>" title="<{$obituaries_user.obituaries_href_title}>"><span class="fa fa-forward"></span></a>
            </p>
        </div>
    </div>
        <{/foreach}>
</div>
<{else}>
    <h3><{$smarty.const._AM_OBITUARIES_ERROR3}></h3>
<{/if}>

<{if isset($pagenav)}>
    <div class="generic-pagination col text-right mt-2">
        <{$pagenav|replace:'form':'div'|replace:'id="xo-pagenav"':''|replace:' //':'/'}>
    </div>
<{/if}>




