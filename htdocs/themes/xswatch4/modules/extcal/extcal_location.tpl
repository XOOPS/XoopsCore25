<div class="container">
    <div class="row mb-3">
        <{if !empty($location.logo)}>
        <div class="col-12 col-md-6 mt-2">
            <img class="img-fluid" src="<{$smarty.const.XOOPS_URL}>/uploads/extcal/location/<{$location.logo}>">
        </div>
        <{/if}>
        <div class="col-12 col-md-6 mt-2">
            <h4><{$location.nom}></h4>
            <{if !empty($location.categorie)}><{$location.categorie}><br><{/if}>
            <{if !empty($location.adresse)}><{$location.adresse}><br><{/if}>
            <{if !empty($location.adresse2)}><{$location.adresse2}><br><{/if}>
            <{if !empty($location.ville)}><{$location.ville}><{/if}>
            <{if !empty($location.cp)}><{$location.cp}><br><{/if}>
            <{if !empty($location.map)}> <a href="<{$location.map}>" rel="external"><{$smarty.const._MD_EXTCAL_LOCATION_MAP2}></a><br>
            <{/if}>

            <{if !empty($location.tel_fixe)}><a href="tel:<{$location.tel_fixe}>"><{$location.tel_fixe}></a><br><{/if}>
            <{if !empty($location.tel_portable)}><a href="tel:<{$location.tel_portable}>"><{$location.tel_portable}></a><br><{/if}>
            <{if !empty($location.mail)}><a href="mailto:<{$location.mail}>"><{$location.mail}></a><br><{/if}>
            <{if !empty($location.site)}><a href="<{$location.site}>" rel="external"><{$smarty.const._MD_EXTCAL_VISIT_SITE}></a><br>
            <{/if}>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
        <h6><{$smarty.const._MD_EXTCAL_LOCATION_INFO_COMPL}></h6>
        <{if !empty($location.description)}><p><{$location.description}></p><{/if}>
        <{if !empty($location.horaires)}><{$location.horaires}><br><{/if}>
        <{if !empty($location.tarifs)}><{$location.tarifs}>&nbsp; <{$smarty.const._MD_EXTCAL_DEVISE2}><br><{/if}>
        <{if !empty($location.divers)}><{$location.divers}><br><{/if}>
        </div>
    </div>
    <{if !empty($xoops_isadmin)}>
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
