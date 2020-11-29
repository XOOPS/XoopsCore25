<{if $publisher_display_breadcrumb}>
    <!-- Do not display breadcrumb if you are on indexpage or you do not want to display the module name -->
    <{if $module_home || $categoryPath}>
        <ol class="breadcrumb">
            <{if $module_home}>
                <li><{$module_home}></li>
            <{/if}>
            <{$categoryPath|default:''}>
        </ol>
    <{/if}>
<{/if}>

<{if $title_and_welcome|default:0 && $lang_mainintro != ''}>
    <div class="well">
        <{$lang_mainintro}>
    </div>
<{/if}>

