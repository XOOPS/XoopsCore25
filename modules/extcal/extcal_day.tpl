<script>
    jQuery(document).ready(function ($) {
        $('.extcalform > form select').addClass('form-control');
        $('.extcalform > form input[type="submit"]').addClass('btn btn-primary');
    });
</script>
<div class="extcalform mb10 text-center">
    <form action="<{$navigSelectBox.action}>" method="<{$navigSelectBox.method}>">
        <ul class="list-inline">
            <{foreachq item=element from=$navigSelectBox.elements}>
            <li><{$element.body}></li>
            <{/foreach}>
        </ul>
    </form>
</div>

<{include file="db:extcal_navbarwysibb.tpl"}>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <tr style="text-align:center;">
            <td class="even" style="width:33%;"><a
                        href="<{$xoops_url}>/modules/extcal/day.php?<{$navig.prev.uri}>">
                    &lt;&lt;&nbsp;&nbsp;<{$navig.prev.name}></a></td>
            <td class="even" style="width:33%;"><span style="font-weight:bold;"><{$navig.this.name}></span>
            </td>
            <td class="even" style="width:33%;"><a
                        href="<{$xoops_url}>/modules/extcal/day.php?<{$navig.next.uri}>"><{$navig.next.name}>&nbsp;&nbsp;&gt;&gt;</a>
            </td>
        </tr>
        <{foreach item=event from=$events}>
            <tr>
                <td colspan="3" class="odd" style="vertical-align:middle;">
                    <div style="height:20px; width:5px; background-color:#<{$event.cat.cat_color}>; border:1px solid black; float:left; margin-right:5px;"></div>
                    <{$event.formated_event_start}>&nbsp;&nbsp;<a
                            href="<{$xoops_url}>/modules/extcal/event.php?event=<{$event.event_id}>"
                            class="extcalTips"
                            title="<{$event.event_title}> :: <b><{$lang.start}></b> <{$event.formated_event_start}><br /><b><{$lang.end}></b> <{$event.formated_event_end}>"><{$event.event_title}></a>
                </td>
            </tr>
        <{/foreach}>
        <tr>
            <th colspan="3">
                <{foreach item=cat from=$cats}>
                    <div style="float:left; margin-left:5px;">
                        <div style="float:left; background-color:#<{$cat.cat_color}>; border:1px solid white; margin-right:5px;">
                            &nbsp;
                        </div>
                        <{$cat.cat_name}>
                    </div>
                <{/foreach}>
            </th>
        </tr>
    </table>
</div>

<div style="text-align:right;"><a
            href="<{$xoops_url}>/modules/extcal/rss.php?cat=<{$selectedCat}>"><img
                src="images/icons/rss.gif" alt="RSS Feed"/></a></div>
<{include file='db:system_notification_select.tpl'}>
