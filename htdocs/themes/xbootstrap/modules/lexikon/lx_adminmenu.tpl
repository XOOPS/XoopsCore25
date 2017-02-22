<style type='text/css'>
    #buttontop {
        float: left;
        width: 100%;
        background: #e7e7e7;
        font-size: 93%;
        line-height: normal;
        border-top: 1px solid black;
        border-left: 1px solid black;
        border-right: 1px solid black;
        margin: 0;
    }

    #buttonbar {
        float: left;
        width: 100%;
        background: #e7e7e7 url('../assets/images/bg.gif') repeat-x left bottom;
        font-size: 93%;
        line-height: normal;
        border-left: 1px solid black;
        border-right: 1px solid black;
        margin-bottom: 12px;
    }

    #buttonbar ul {
        margin: 0;
        margin-top: 15px;
        padding: 10px 10px 0;
        list-style: none;
    }

    #buttonbar li {
        display: inline;
        margin: 0;
        padding: 0;
    }

    #buttonbar a {
        float: left;
        background: url('../assets/images/left_both.gif') no-repeat left top;
        margin: 0;
        padding: 0 0 0 9px;
        border-bottom: 1px solid #000;
        text-decoration: none;
    }

    #buttonbar a span {
        float: left;
        display: block;
        background: url('../assets/images/right_both.gif') no-repeat right top;
        padding: 5px 15px 4px 6px;
        font-weight: bold;
        color: #765;
    }

    /* Commented Backslash Hack hides rule from IE5-Mac \*/
    #buttonbar a span {
        float: none;
    }

    /* End IE5-Mac hack */
    #buttonbar a:hover span {
        color: #333;
    }

    #buttonbar #current a {
        background-position: 0 -150px;
        border-width: 0;
    }

    #buttonbar #current a span {
        background-position: 100% -150px;
        padding-bottom: 5px;
        color: #333;
    }

    #buttonbar a:hover {
        background-position: 0 -150px;
    }

    #buttonbar a:hover span {
        background-position: 100% -150px;
    }
</style>

<div id="buttontop">
    <table style="width: 100%; padding: 0;" cellspacing="0">
        <tr>
            <td style="font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;">
                <{foreach from=$headermenu key=itemnum item=menuitem}>
                    <a class="nobutton" href="<{$menuitem.link}>"><{$menuitem.title}></a>
                    <{if ($itemnum + 1) <> $headermenucount}>
                        |
                    <{/if}>
                <{/foreach}>
            </td>
            <td style="font-size: 10px; text-align: right; color: #2F5376; padding: 0 6px; line-height: 18px;">
                <{$breadcrumb}>
            </td>
        </tr>
    </table>
</div>
<div id="buttonbar">
    <ul>
        <{foreach from=$adminmenu key=itemnum item=menuitem}>
            <{if $itemnum==$current}>
                <li id="current">
                    <{else}>
                <li>
            <{/if}>
            <a href="../<{$menuitem.link}>"><span><{$menuitem.title}></span></a>
            </li>
        <{/foreach}>
    </ul>
</div>
