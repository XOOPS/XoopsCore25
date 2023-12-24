<table class="outer" <{if !empty($block.bgColor)}>bgcolor="<{$block.bgColor}>"<{/if}>
       style="width:100%; text-align:center; vertical-align:middle;">
    <{if !empty($block.horloge.display)}>
        <tr>
            <td colspan="7" style="font-weight:bold;">
                <{include file="db:extcal_horloge.tpl"}>
            </td>
        </tr>
    <{/if}>

    <tr>
        <td colspan="7" style="font-weight:bold;">

            <{* En prevision
            <a href="<{$xoops_url}>/modules/extcal/<{$block.navig.page}>?<{$block.navig.uri}>">
              &nbsp;<img border="0" src="<{$smarty.const.XOOPS_URL}>/modules/extcal/assets/images/arrows/previous.png">
            </a>
             *}>
            <a href="<{$xoops_url}>/modules/extcal/<{$block.navig.page}>?<{$block.navig.uri}>">
                <{$block.navig.name}>
            </a>
            <{* En prevision
              <a href="<{$xoops_url}>/modules/extcal/<{$block.navig.page}>?<{$block.navig.uri}>">
              &nbsp;<img border="0" src="<{$smarty.const.XOOPS_URL}>/modules/extcal/assets/images/arrows/next.png">
            </a>
             *}>
        </td>


    </tr>
    <{if !empty($block.imageParam.displayImage)}>
        <tr>
            <td colspan="7" height="150px">
                <{include file="db:extcal_imgXoops.tpl"}>
            </td>
        </tr>
    <{/if}>

    <{if !empty($block.displayLink)}>
        <tr>
            <td colspan="7">
                <img src="<{$xoops_url}>/modules/extcal/assets/images/icons/addevent.gif"
                     alt="Add event"/> <a href="<{$xoops_url}>/modules/extcal/view_new-event.php"><{$block.submitText}></a>
            </td>
        </tr>
    <{/if}>
    <tr style="font-weight:bold;">
        <{foreach item=day from=$block.weekdayNames|default:null}>
            <td><{$day}></td>
        <{/foreach}>
    </tr>
    <{foreach item=weeks from=$block.tableRows|default:null}>
        <tr>
            <{foreach item=day from=$weeks.week|default:null}>
                <td <{if !empty($day.isSelected)}> style="border:1px solid #0099FF;"<{/if}> >
                    <{if !$day.isEmpty}>
                        <{if !empty($day.haveEvents)}>
                            <a href="<{$xoops_url}>/modules/extcal/view_day.php?year=<{$weeks.weekInfo.year}>&amp;month=<{$weeks.weekInfo.month}>&amp;day=<{$day.number}>"
                               style="color:#<{$day.color}>; font-weight:bold;">
                                <{$day.number}>
                            </a>
                        <{else}>
                            <{$day.number}>
                        <{/if}>
                    <{else}>
                        &nbsp;
                    <{/if}>
                </td>
            <{/foreach}>
        </tr>
    <{/foreach}>
</table>
