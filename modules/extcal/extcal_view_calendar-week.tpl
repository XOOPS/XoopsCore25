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

<{include file="db:extcal_navbar.tpl"}>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <tr style="text-align:center;">
            <td colspan="2" class="even"><a
                        href="<{$xoops_url}>/modules/extcal/view_calendar-week.php?<{$navig.prev.uri}>">
                    &lt;&lt; <{$navig.prev.name}></a></td>
            <td colspan="3" class="even"><span style="font-weight:bold;"><{$navig.this.name}></span>
            </td>
            <td colspan="2" class="even"><a
                        href="<{$xoops_url}>/modules/extcal/view_calendar-week.php?<{$navig.next.uri}>"><{$navig.next.name}>
                    &gt;&gt;</a></td>
        </tr>
        <tr style="text-align:center;" class="head">
            <{foreach item=weekdayName from=$weekdayNames}>
                <td><{$weekdayName}></td>
            <{/foreach}>
        </tr>
        <tr>
            <{foreach item=day from=$week}>
                <td class="<{if $day.isEmpty}>even<{else}>odd<{/if}>"
                    style="width:14%; height:80px; vertical-align:top;<{if $day.isSelected}> background-color:#B6CDE4;<{/if}>">
                    <{if $day.isEmpty}>&nbsp;<{else}><a
                    href="<{$xoops_url}>/modules/extcal/view_day.php?year=<{$day.year}>&amp;month=<{$day.month}>&amp;day=<{$day.dayNumber}>"><{$day.dayNumber}></a><{/if}><br/>
                    <{foreach item=event from=$day.events}>
                        <{if $event}>
                            <div style="font-size:0.8em; margin-top:5px;"><img
                                        src="assets/images/icons/event-<{$event.status}>.gif"/> <a
                                        href="<{$xoops_url}>/modules/extcal/event.php?event=<{$event.event_id}>"
                                        class="extcalTips"
                                        title="<{$event.event_title}> :: <b><{$lang.start}></b> <{$event.formated_event_start}><br /><b><{$lang.end}></b> <{$event.formated_event_end}>"><{$event.event_title}></a>
                            </div>
                            <div style="background-color:#<{$event.cat.cat_color}>; height:2px; font-size:2px;">
                                &nbsp;
                            </div>
                        <{/if}>
                    <{/foreach}>
                </td>
            <{/foreach}>
        </tr>
        <tr>
            <th colspan="7">
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
                src="assets/images/icons/rss.gif" alt="RSS Feed"/></a></div>
<{include file='db:system_notification_select.tpl'}>
