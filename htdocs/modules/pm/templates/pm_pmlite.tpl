<{$pmform.javascript}>
<form name="<{$pmform.name}>" id="<{$pmform.name}>" action="<{$pmform.action}>" method="<{$pmform.method}>" <{$pmform.extra}> >
    <table class='outer txtcenter width100'>
        <tr>
            <td class='head width30 txtright'><{$smarty.const._PM_TO}></td>
            <td class='even txtleft'><{if $pmform.elements.to_userid.hidden != 1}><{$pmform.elements.to_userid.body}><{/if}><{$to_username}></td>
        </tr>
        <tr>
            <td class='head width30 txtright'><{$smarty.const._PM_SUBJECTC}></td>
            <td class='even txtleft'><{$pmform.elements.subject.body}></td>
        </tr>
        <tr>
            <td class='head width30 txtright'><{$smarty.const._PM_SUBJECT_ICONS}></td>
            <td class='even txtleft'>

                <{foreachq item=icon from=$radio_icons}>
                <input type='radio' name='icon' id='<{$icon}>' value='<{$icon}>'/><label name='xolb_icon' for='<{$icon}>'><img
                            src='<{xoAppUrl}>images/subject/<{$icon}>' alt=""/></label>
                <{/foreach}>  </td>
        </tr>
        <tr class='aligntop'>
            <td class='head width30 txtright'><{$smarty.const._PM_MESSAGEC}></td>
            <td class='even txtleft'><{$pmform.elements.message.body}></td>
        </tr>
        <tr class='aligntop'>
            <td class='head width30'><{$smarty.const._PM_SAVEINOUTBOX}></td>
            <td class='even'><{$pmform.elements.savecopy.body}></td>
        </tr>
        <tr>
            <td class='head'>&nbsp;</td>
            <td class='even'>
                <{foreach item=element from=$pmform.elements}>
                    <{if $element.hidden == 1}>
                        <{$element.body}>
                    <{/if}>
                <{/foreach}>
                <{$pmform.elements.tray.body}>&nbsp;
            </td>
        </tr>
    </table>
</form>
