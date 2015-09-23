<div id="navbarCP">
    <ul id="nav" class="shadow">
        <{foreach item=item from=$navitems}>
            <li><a href="<{$item.link}>" title="<{$item.text}>"><{$item.text}></a>
                <ul>
                    <{foreach item=sub from=$item.menu}>
                        <li>
                            <{if $sub.options != 0}>
                                <a href="<{$sub.link}>" title="<{$sub.title}>" style='background-image: url(<{$sub.icon|default:"$theme_icons/item.png"}>);'>
                                    <{$sub.title}>
                                </a>
                                <ul>
                                    <{foreach item=option from=$sub.options}>
                                        <li><a href="<{$sub.url}><{$option.link}>" title="<{$option.title}>" style='background-image: url(<{$sub.icon|default:"$theme_icons/forward.png"}>);'><{$option.title}></a></li>
                                    <{/foreach}>
                                </ul>
                            <{else}>
                                <a href="<{$sub.link}>" title="<{$sub.title}>" style='background-image: url(<{$sub.icon|default:"$theme_icons/item.png"}>);'><{$sub.title}></a>
                            <{/if}>
                        </li>
                    <{/foreach}>
                </ul>
            </li>
        <{/foreach}>
    </ul>
</div>
