<div id="xo_globalnav">
    <!-- start menu -->
    <ul class="menu" id="menu">
        <{foreach item=item from=$navitems}>
            <li>
                <a href="<{$item.link}>" class="menulink"><{$item.text}></a>
                <ul>
                    <{foreach item=sub from=$item.menu}>
                        <li>
                            <{if $sub.options|default:0 != 0}>
                                <a class="sub" href="<{$sub.link}>" title="<{$sub.title|strip_tags:false}>"><{$sub.title}></a>
                                <ul>
                                    <{foreach item=option from=$sub.options}>
                                        <li><a href="<{$sub.url}><{$option.link}>"><{$option.title}></a></li>
                                    <{/foreach}>
                                </ul>
                            <{else}>
                                <a href="<{$sub.link}>" title="<{$sub.title|strip_tags:false}>"><{$sub.title}></a>
                            <{/if}>
                        </li>
                    <{/foreach}>
                </ul>
            </li>
        <{/foreach}>
    </ul>
</div>

<script type="text/javascript">
    var menu = new menu.dd("menu");
    menu.init("menu", "menuhover");
</script>
