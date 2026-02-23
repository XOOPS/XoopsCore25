<div class="content-wrapper">
    <{* Module Admin Toolbar - shown when in a module admin context *}>
    <{if !empty($mod_options) && $xoops_dirname != 'system'}>
        <div id="xo-nav-options">
            <{* Module name header with inline icon toolbar *}>
            <div id="xo-modname">
                <{if $modname}>
                    <span><{$modname|escape:'html'}></span>
                <{/if}>

                <{* Horizontal Icon Toolbar - inline with module name *}>
                <{if $mod_options}>
                    <div id="xo-toolbar">
                        <{foreach item=option from=$mod_options}>
                            <a class="tooltip" href="<{$option.link}>" title="<{$option.title}>">
                                <{if $option.icon}>
                                    <img src='<{$option.icon}>' alt="<{$option.title}>"/>
                                <{else}>
                                    <span>⚙️</span>
                                <{/if}>
                            </a>
                        <{/foreach}>
                    </div>
                <{/if}>
            </div>

            <{* System-generated module admin links (Preferences|Update|Blocks|...) and tabs *}>
            <{if !empty($xo_system_menu)}>
                <{$xo_system_menu}>
            <{/if}>
        </div>
    <{/if}>

    <{if !empty($xoops_contents)}>
        <{* Display regular admin page content *}>
        <{$xoops_contents}>
    <{elseif !empty($modules)}>
        <{* Display module list on homepage if dashboard is disabled or not showing *}>
        <div class="modules-grid">
            <{foreach item=module from=$modules}>
                <div class="module-card">
                    <{if $module.icon}>
                        <img src="<{$module.icon|escape:'html'}>" alt="<{$module.title|escape:'html'}>" class="module-icon">
                    <{/if}>
                    <h3 class="module-title"><{$module.title|escape:'html'}></h3>
                    <{if $module.description}>
                        <p class="module-description"><{$module.description|escape:'html'}></p>
                    <{/if}>
                    <a href="<{$module.link}>" class="module-link"><{$smarty.const._MODERN_OPEN}></a>
                </div>
            <{/foreach}>
        </div>
    <{/if}>
</div>
