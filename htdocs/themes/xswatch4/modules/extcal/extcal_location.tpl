<div class="container">
    <div class="row mb-3">
        <{if $location.logo|default:false}>
        <div class="col-12 col-md-6 mt-2">
            <img class="img-fluid" src="<{$smarty.const.XOOPS_URL}>/uploads/extcal/location/<{$location.logo}>">
        </div>
        <{/if}>
        <div class="col-12 col-md-6 mt-2">
            <h4><{$location.nom|default:false}></h4>
            <{if $location.categorie|default:false}><{$location.categorie}><br><{/if}>
            <{if $location.adresse|default:false}><{$location.adresse}><br><{/if}>
            <{if $location.adresse2|default:false}><{$location.adresse2}><br><{/if}>
            <{if $location.ville|default:false}><{$location.ville}><{/if}>
            <{if $location.cp|default:false}><{$location.cp}><br><{/if}>
            <{if $location.map|default:false}>
            <a href="<{$location.map}>" rel="external"><{$smarty.const._MD_EXTCAL_LOCATION_MAP2}></a><br>
            <{/if}>

            <{if $location.tel_fixe|default:false}><a href="tel:<{$location.tel_fixe}>"><{$location.tel_fixe}></a><br><{/if}>
            <{if $location.tel_portable|default:false}><a href="tel:<{$location.tel_portable}>"><{$location.tel_portable}></a><br><{/if}>
            <{if $location.mail|default:false}><a href="mailto:<{$location.mail}>"><{$location.mail}></a><br><{/if}>
            <{if $location.site|default:false}><a href="<{$location.site}>" rel="external"><{$smarty.const._MD_EXTCAL_VISIT_SITE}></a><br>
            <{/if}>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
        <h6><{$smarty.const._MD_EXTCAL_LOCATION_INFO_COMPL}></h6>
        <{if $location.description|default:false}><p><{$location.description}></p><{/if}>
        <{if $location.horaires|default:false}><{$location.horaires}><br><{/if}>
        <{if $location.tarifs|default:false}><{$location.tarifs}>&nbsp; <{$smarty.const._MD_EXTCAL_DEVISE2}><br><{/if}>
        <{if $location.divers|default:false}><{$location.divers}><br><{/if}>
        </div>
    </div>
    <{if $xoops_isadmin}>
    <div class="float-right">
        <a title="<{$smarty.const._MD_EXTCAL_LOCATION_EDIT}>" href="<{$xoops_url}>/modules/extcal/admin/location.php?op=edit_location&location_id=<{$location.id}>">
            <span class="fa fa-fw fa-2x fa-pencil-square-o"></span>
        </a>
        <a title="<{$smarty.const._MD_EXTCAL_LOCATION_DELETE}>" href="<{$xoops_url}>/modules/extcal/admin/location.php?op=delete_location&location_id=<{$location.id}>">
            <span class="fa fa-fw fa-2x fa-trash-o"></span>
        </a>
    </div>
    <{/if}>
</div>
