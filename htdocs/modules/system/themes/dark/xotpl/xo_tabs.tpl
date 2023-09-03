<!-- the tabs -->
<ul class="tabs">
    <li><a class="tooltip" href="#" title="<{$smarty.const._AM_SYSTEM_HELP}>"><img src='<{"$theme_icons/help.png"}>'/></a></li>
    <li><a class="tooltip" href="#" title="<{xoBlock id=4 display='title'}>"><img src='<{"$theme_icons/waiting.png"}>'/></a></li>
    <li><a class="tooltip" href="#" title="<{xoBlock id=9 display='title'}>"><img src='<{"$theme_icons/edituser.png"}>'/></a></li>
    <li><a class="tooltip" href="#" title="<{xoBlock id=8 display='title'}>"><img src='<{"$theme_icons/newuser.png"}>'/></a></li>
    <li><a class="tooltip" href="#" title="<{xoBlock id=10 display='title'}>"><img src='<{"$theme_icons/comments.png"}>'/></a></li>
</ul>

<!-- tab "panes" -->
<div class="panes">
    <div>
        <div class="help">
            <h4><{$smarty.const._OXYGEN_HELP_1}></h4>

            <p><{$smarty.const._OXYGEN_HELP_DESC_1}></p>
        </div>
        <div class="help">
            <h4><{$smarty.const._OXYGEN_HELP_2}></h4>

            <p><{$smarty.const._OXYGEN_HELP_DESC_2}></p>
        </div>
        <div class="help">
            <h4><{$smarty.const._OXYGEN_HELP_3}></h4>

            <p><{$smarty.const._OXYGEN_HELP_DESC_3}></p>
        </div>
    </div>
    <div><{xoBlock id=4}></div>
    <div><{xoBlock id=9}></div>
    <div><{xoBlock id=8}></div>
    <div><{xoBlock id=10}></div>
</div>

<script type="text/javascript">
    // perform JavaScript after the document is scriptable.
    $(function () {
        // setup ul.tabs to work as tabs for each div directly under div.panes
        $("ul.tabs").tabs("div.panes > div");
    });
</script>
