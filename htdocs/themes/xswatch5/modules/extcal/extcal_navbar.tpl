<ul class="nav nav-tabs">
    <{foreach item=navItem from=$tNavBar|default:null}>
    <li class="nav-item"><a href="<{$navItem.href}>" class="nav-link<{if !empty($navItem.current)}> active<{/if}>"><{$navItem.name}></a>
    </li>
    <{/foreach}>
</ul>
