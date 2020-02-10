<{if $publisher_display_breadcrumb}>
    <!-- Do not display breadcrumb if you are on indexpage or you do not want to display the module name -->
    <{if $module_home or $categoryPath}>
        <ol class="breadcrumb">
            <{if $module_home}>
                <li class="breadcrumb-item<{if !$categoryPath|default:false}> active<{/if}>"><{$module_home}></li>
            <{/if}>
            <{if $categoryPath|default:false}>
                <{if !$categoryPath|strstr:'<li>'}>
                    <{assign var=categoryPath value="<li>$categoryPath</li>"}>
                <{/if}>
                <{$categoryPath|replace:'<li>':'<li class="breadcrumb-item">'}>
            <{/if}>
        </ol>
    <{/if}>
<{/if}>

<{if $title_and_welcome|default:false && $lang_mainintro != ""}>
    <div class="well">
        <{$lang_mainintro}>
    </div>
<{/if}>
