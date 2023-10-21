<form method="get" action="<{$pageName}>">
    <table width="95%" class="outer" cellspacing="1">
        <tr>
            <td class="even" align="center">
                <{$commentModeSelect->render()|replace:'id="com_mode"':''}>
                <{$commentOrderSelect->render()|replace:'id="com_order"':''}>
                <{$commentRefreshButton->render()}>
                <{if !empty($commentPostButton) }>
                <{$commentPostButton->render()}>
                <{/if}>
                <{$commentPostHidden}>
            </td>
        </tr>
    </table>
</form>
