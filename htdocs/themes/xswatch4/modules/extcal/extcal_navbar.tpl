<ul class="nav nav-tabs">
    <{foreach item=navItem from=$tNavBar}>
    <li class="nav-item"><a href="<{$navItem.href}>" class="nav-link<{if $navItem.current|default:false}> active<{/if}>"><{$navItem.name}></a>
    </li>
    <{/foreach}>
</ul>
