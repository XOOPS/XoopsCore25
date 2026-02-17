<header class="modern-header">
    <div class="header-brand">
        <button id="sidebarToggle" class="btn-icon logo-hamburger" title="<{$smarty.const._MODERN_TOGGLE_MENU}>">
            <span>☰</span>
        </button>
        <h1><{$xoops_sitename}></h1>
        <span class="system-status">
            <span class="status-dot"></span>
            <span><{$smarty.const._MODERN_ONLINE}></span>
        </span>
    </div>

    <{* Quick-access icon strip - always shows System Services *}>
    <{if $system_services}>
    <div class="header-toolbar-icons">
        <{foreach item=op from=$system_services}>
            <a class="header-toolbar-icon" href="<{$op.link}>" title="<{$op.title}>">
                <{if $op.icon}>
                    <img src="<{$op.icon}>" alt="<{$op.title}>">
                <{else}>
                    <span>⚙️</span>
                <{/if}>
            </a>
        <{/foreach}>
    </div>
    <{/if}>

    <div class="header-actions">
        <button id="darkModeToggle" class="btn-icon" title="<{$smarty.const._MODERN_TOGGLE_DARK_MODE}>">
            <span class="icon-moon">🌙</span>
            <span class="icon-sun">☀️</span>
        </button>
    </div>
</header>
