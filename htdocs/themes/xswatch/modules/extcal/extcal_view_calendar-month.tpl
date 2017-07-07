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
            <th rowspan="2">&nbsp;</th>
            <td colspan="2" class="even"><a
                        href="<{$xoops_url}>/modules/extcal/view_calendar-month.php?<{$navig.prev.uri}>">
                    &lt;&lt; <{$navig.prev.name}></a></td>
            <td colspan="3" class="even"><span style="font-weight:bold;"><{$navig.this.name}></span>
            </td>
            <td colspan="2" class="even"><a
                        href="<{$xoops_url}>/modules/extcal/view_calendar-month.php?<{$navig.next.uri}>"><{$navig.next.name}>
                    &gt;&gt;</a></td>
        </tr>
        <tr style="text-align:center;" class="head">
            <{foreachq item=weekdayName from=$weekdayNames}>
            <td><{$weekdayName}></td>
            <{/foreach}>
        </tr>
        <{foreachq item=row from=$tableRows}>
        <tr>
            <th style="text-align:center; vertical-align:middle;"><a
                        href="view_calendar-week.php?year=<{$row.weekInfo.year}>&amp;month=<{$row.weekInfo.month}>&amp;day=<{$row.weekInfo.day}>"><{$row.weekInfo.week}></a>
            </th>
            <{foreachq item=cell from=$row.week}>
            <td class="<{if $cell.isEmpty}>even<{else}>odd<{/if}>"
                style="width:14%; height:80px; vertical-align:top;<{if $cell.isSelected}> background-color:#B6CDE4;<{/if}>">
                <{if $cell.isEmpty}>&nbsp;<{else}><a
                href="<{$xoops_url}>/modules/extcal/view_day.php?year=<{$year}>&amp;month=<{$month}>&amp;day=<{$cell.number}>"><{$cell.number}></a><{/if}><br/>
                <{foreach item=event from=$cell.events}>
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
        <{/foreach}>
        <tr>
            <th colspan="8">
                <{foreachq item=cat from=$cats}>
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
