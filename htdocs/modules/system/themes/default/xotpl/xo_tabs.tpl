<!-- the tabs -->
<ul class="tabs">
    <li><a class="tooltip" href="#" title="<{$smarty.const._AM_SYSTEM_HELP}>"><img src='<{"$theme_icons/help.png"}>'/></a></li>
    <li><a class="tooltip" href="#" title="<{block id=4 display='title'}>"><img src='<{"$theme_icons/waiting.png"}>'/></a></li>
    <li><a class="tooltip" href="#" title="<{block id=9 display='title'}>"><img src='<{"$theme_icons/edituser.png"}>'/></a></li>
    <li><a class="tooltip" href="#" title="<{block id=8 display='title'}>"><img src='<{"$theme_icons/newuser.png"}>'/></a></li>
    <li><a class="tooltip" href="#" title="<{block id=10 display='title'}>"><img src='<{"$theme_icons/comments.png"}>'/></a></li>
</ul>

<!-- tab "panes" -->
<div class="panes">
    <div>
        <div class="help">
            <a href=""><{$smarty.const._OXYGEN_HELP_1}></a>

            <p><{$smarty.const._OXYGEN_HELP_DESC_1}></P>
        </div>
        <div class="help">
            <a href=""><{$smarty.const._OXYGEN_HELP_2}></a>

            <p><{$smarty.const._OXYGEN_HELP_DESC_2}></P>
        </div>
        <div class="help">
            <a href=""><{$smarty.const._OXYGEN_HELP_3}></a>

            <p><{$smarty.const._OXYGEN_HELP_DESC_3}></P>
        </div>
    </div>
    <div><{block id=4}></div>
    <div><{block id=9}></div>
    <div><{block id=8}></div>
    <div><{block id=10}></div>
</div>

<script type="text/javascript">
    // perform JavaScript after the document is scriptable.
    $(function () {
        // setup ul.tabs to work as tabs for each div directly under div.panes
        $("ul.tabs").tabs("div.panes > div");
    });
</script>
