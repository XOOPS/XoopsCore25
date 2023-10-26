<{if isset($publisher_display_breadcrumb)}>
    <!-- Do not display breadcrumb if you are on indexpage, or you do not want to display the module name -->
    <{if $module_home || $categoryPath}>
        <ol class="breadcrumb">
            <{if isset($module_home)}>
                <li><{$module_home}></li>
            <{/if}>
            <{$categoryPath|default:''}>
        </ol>
    <{/if}>
<{/if}>

<{if !empty($title_and_welcome) && !empty($lang_mainintro)}>
    <div class="well">
        <{$lang_mainintro}>
    </div>
<{/if}>

