
<{include file="db:extcal_navbar.tpl"}>

<div class="container">
<div style="background-color:#<{$event.cat.cat_color}>; height: 4px;">&nbsp;</div>
<div class="row mb-3">
    <div class="col-12 col-md-6 mt-2">
        <h6><{$event.cat.cat_name}></h6>
        <h3><{$event.event_title}></h3>
        <div class="mb-2"><{$event.formated_event_start}></div>
        <{assign var='desctext' value=`$smarty.const._MD_EXTCAL_LOCATION_DESCRIPTION`}>
        <{assign var='desclink' value="… <a href=\"#desc\" title=\"$desctext\"><span class=\"fa fa-forward\"></span></a>"}>
        <div><{$event.event_desc|truncateHtml:60:$desclink}></div>
    </div>
    <div class="col-12 col-md-6">
        <{if $event.event_picture1 || $event.event_picture2}>
        <div id="extEventSlides" class="carousel slide mt-2 mb-2" data-ride="carousel">
            <div class="carousel-inner">
                <{assign var=active value=' active'}>
                <{if $event.event_picture1}>
                <div class="carousel-item<{$active}>">
                    <img class="img-fluid" src="<{$xoops_url}>/uploads/extcal/<{$event.event_picture1}>" alt="<{$event.event_title}>">
                </div>
                <{assign var=active value=''}>
                <{/if}>
                <{if $event.event_picture2}>
                <div class="carousel-item<{$active}>">
                    <img class="img-fluid" src="<{$xoops_url}>/uploads/extcal/<{$event.event_picture2}>" alt="<{$event.event_title}>">
                </div>
                <{assign var=active value=''}>
                <{/if}>
                <a class="carousel-control-prev" href="#extEventSlides" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only"<{$smarty.const.THEME_CONTROL_PREVIOUS}>/span>
                </a>
                <a class="carousel-control-next" href="#extEventSlides" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only"<{$smarty.const.THEME_CONTROL_NEXT}>/span>
                </a>
            </div>
        </div>
        <{elseif $smarty.const._EXTCAL_SHOW_NO_PICTURE}>
            <img src="<{$xoops_url}>/modules/extcal/assets/images/no_picture.png" height="180"/>
        <{/if}>
    </div>
</div>
<div class="mb-3">
    <h4><{$event.event_title}></h4>
    <strong><{$smarty.const._MD_EXTCAL_START}> </strong> <{$event.formated_event_start}>
    <br><br>
    <{if $event.formated_event_start != $event.formated_event_end}>
    <strong><{$smarty.const._MD_EXTCAL_END}> </strong> <{$event.formated_event_end}>
    <br><br>
    <{/if}>
</div>

<{if $event.event_desc != ''}>
<div id="desc" class="mb-3">
    <h5><{$smarty.const._MD_EXTCAL_LOCATION_DESCRIPTION}></h5>

    <{$event_desc}>
    <br>
</div>
<{/if}>

<{if $event.event_address != ''}>
<div class="mb-3">
    <h5><{$smarty.const._MD_EXTCAL_LOCATION_ADRESSE}></h5>
    <{$event_address}>
    <br>
    <br>
</div>
<{/if}>

<{if $location.id.value != 0}>
<div class="mb-3">
    <h5><{$smarty.const._MD_EXTCAL_LOCATION}></h5>
    <a class="btn btn-primary mb-3" href="./location.php?location_id=<{$event.event_location}>">
        <{$location.nom.value}>
    </a>
    <br>
    <{if $location.adresse.value}><{$location.adresse.value}><br><{/if}>
    <{if $location.adresse2.value}><{$location.adresse2.value}><br><{/if}>
    <{if $location.ville.value}><{$location.ville.value}><{/if}>
    <{if $location.cp.value}><{$location.cp.value}><br><{/if}>
    <{* if $location.adresse.value}><{$location.adresse.value}><br><{/if}>
    <{if $location.ville.value}><{$location.ville.value}><br><{/if}>
    <{if $location.telephone.value}><{$location.telephone.value}><br><{/if}>
    <{if $location.site.value}>
    <a href="<{$location.site.value}>" rel="external">
        <{$smarty.const._MD_EXTCAL_VISIT_SITE}>
    </a>
    <br>
    <{/if}>
    <{if $location.map.value}>
    <a href='<{$location.map.value}>' target='blanck'><{$smarty.const._MD_EXTCAL_LOCALISATION}></a>
    <br>
    <{/if*}>
    <{if $location.logo.value}>
    <a href="<{$xoops_url}>/uploads/extcal/location/<{$location.logo.value}>">
        <img src="<{$xoops_url}>/uploads/extcal/location/<{$location.logo.value}>" height="150px"/>
    </a>
    <br>
    <{/if}>
</div>
<{/if}>

<div class="mb-3">
    <{if $event.event_organisateur}>
    <h5><{$smarty.const._MD_EXTCAL_ORGANISATEUR}></h5>
    <{$event.event_organisateur}>
    <{/if}>
    <{if $event.event_contact}><{$event.event_contact}><br><{/if}>
    <{if $event.event_email}><a href="mailto:<{$event.event_email}>"><{$event.event_email}></a><br><{/if}>
    <{if $event.event_url}><a href="<{$event.event_url}>" target="_blank"><{$event.event_url}></a><br><{/if}>
</div>

<{if $event.event_price}>
<div class="mb-3">
<strong><{$smarty.const._MD_EXTCAL_LOCATION_TARIFS}></strong>
                <{$event.event_price}>
                <{$smarty.const._MD_EXTCAL_DEVISE2}>
</div>
<{/if}>

<{if $event_attachement|@count > 0}>
<div>
    <h5><{$smarty.const.THEME_EVENT_DOWNLOADS}></h5>
    <table class="table table-sm table-hover">
        <thead>
        <tr>
            <th scope="col"><{$smarty.const.THEME_FILE_NAME}></th>
            <th scope="col"><{$smarty.const.THEME_FILE_SIZE}></th>
        </tr>
        </thead>
        <tbody>
        <{foreach item=eventFile from=$event_attachement}>
        <tr>
            <td><a href="download_attachement.php?file=<{$eventFile.file_id}>"><{$eventFile.file_nicename}></a></td>
            <td><{$eventFile.formated_file_size}></td>
        </tr>
        <{/foreach}>
        </tbody>
    </table>
</div>
<{/if}>

<{if $whosGoing}>
<div class="mt-3 mb-3">
    <h5><{$smarty.const._MD_EXTCAL_WHOS_GOING}> <span class="badge badge-secondary"><{$eventmember.member.nbUser}></span></h5>
    <{foreach name=eventMemberList from=$eventmember.member.userList item=member}><{if $smarty.foreach.eventMemberList.first != 1}>, <{/if}>
    <a href="<{$xoops_url}>/userinfo.php?uid=<{$member.uid}>"><{$member.uname}></a>
    <{/foreach}>
    <{if $eventmember.member.show_button}>
    <form method="post" action="event_member.php">
        <input type="hidden" name="mode" value="<{$eventmember.member.joinevent_mode}>"/>
        <input type="hidden" name="event" value="<{$event.event_id}>"/>
        <{$token}>
        <input class="btn btn-sm btn-primary" type="submit" value="<{$eventmember.member.button_text}>"<{$eventmember.member.button_disabled}> />
    </form>
    <{/if}>
</div>
<{/if}>

<{if $whosNotGoing}>
<div class="mt-3 mb-3">
    <h5><{$smarty.const._MD_EXTCAL_WHOSNOT_GOING}> <span class="badge badge-secondary"><{$eventmember.notmember.nbUser}></span></h5>
    <{foreach name=eventMemberList from=$eventmember.notmember.userList item=member}><{if $smarty.foreach.eventMemberList.first != 1}>, <{/if}>
    <a href="<{$xoops_url}>/userinfo.php?uid=<{$member.uid}>"><{$member.uname}></a>
    <{/foreach}>
    <{if $eventmember.notmember.show_button}>
    <form method="post" action="event_notmember.php">
        <input type="hidden" name="mode" value="<{$eventmember.notmember.joinevent_mode}>"/>
        <input type="hidden" name="event" value="<{$event.event_id}>"/>
        <{$token}>
        <input class="btn btn-sm btn-primary" type="submit" value="<{$eventmember.notmember.button_text}>"<{$eventmember.notmember.button_disabled}> />
    </form>
    <{/if}>
</div>
<{/if}>

    <div class="row mb-3">
        <hr>
        <div class="float-right">
        <a href="<{$xoops_url}>/modules/extcal/print.php?event=<{$event.event_id}>"
           title="<{$smarty.const._MD_EXTCAL_ICONE_PRINT}>">
            <span class="fa fa-fw fa-2x fa-print"></span>
        </a>
        <{if $isAdmin || $canEdit}>
        <a href="<{$smarty.const._EXTCAL_FILE_NEW_EVENT}>?event=<{$event.event_id}>&action=edit"
           title="<{$smarty.const._MD_EXTCAL_ICONE_EDIT}>">
            <span class="fa fa-fw fa-2x fa-pencil-square-o"></span>
        </a>
        <a href="<{$smarty.const._EXTCAL_FILE_NEW_EVENT}>?event=<{$event.event_id}>&action=clone"
           title="<{$smarty.const._MD_EXTCAL_ICONE_CLONE}>">
            <span class="fa fa-fw fa-2x fa-clone"></span>
        </a>
        <{/if}>
        <{if $isAdmin}>
        <a href="admin/event.php?op=delete&event_id=<{$event.event_id}>"
           title="<{$smarty.const._MD_EXTCAL_ICONE_DELETE}>">
            <span class="fa fa-fw fa-2x fa-trash-o"></span>
        </a>
        <{/if}>
    </div>
    </div>
</div>

<{*
<table class="outer">
    <div id="map" align="center" style="visibility: hidden;"><br>
        <{$map}>
    </div>
</table>
<p style="text-align:right;">
    <{foreach item=eventFile from=$event_attachement}>
        <a href="download_attachement.php?file=<{$eventFile.file_id}>"><{$eventFile.file_nicename}>
            (<i><{$eventFile.file_mimetype}></i>) <{$eventFile.formated_file_size}></a>
        <br>
    <{/foreach}>
</p>
*}>

<{* include file="db:extcal_buttons_event.tpl" *}>

<div class="align-content-center mt-3">
    <{$commentsnav}>
</div>
<div class="row d-flex justify-content-center"><{$lang_notice}></div>

<div style="margin-top: 10px;">
    <!-- start comments loop -->
    <{if $comment_mode == "flat"}>
        <{include file="db:system_comments_flat.tpl"}>
    <{elseif $comment_mode == "thread"}>
        <{include file="db:system_comments_thread.tpl"}>
    <{elseif $comment_mode == "nest"}>
        <{include file="db:system_comments_nest.tpl"}>
    <{/if}>
    <!-- end comments loop -->
</div>
<{include file='db:system_notification_select.tpl'}>

