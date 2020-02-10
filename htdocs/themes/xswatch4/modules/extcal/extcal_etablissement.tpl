<div class="container">
    <div class="row mb-3">
        <{if $etablissement.logo|default:false}>
        <div class="col-12 col-md-6 mt-2">
            <img class="img-fluid" src="<{$smarty.const.XOOPS_URL}>/uploads/extcal/etablissement/<{$etablissement.logo}>">
        </div>
        <{/if}>
        <div class="col-12 col-md-6 mt-2">
            <h4><{$etablissement.nom|default:false}></h4>
            <{if $etablissement.categorie|default:false}><{$etablissement.categorie}><br><{/if}>
            <{if $etablissement.adresse|default:false}><{$etablissement.adresse}><br><{/if}>
            <{if $etablissement.adresse2|default:false}><{$etablissement.adresse2}><br><{/if}>
            <{if $etablissement.ville|default:false}><{$etablissement.ville}><{/if}>
            <{if $etablissement.cp|default:false}><{$etablissement.cp}><br><{/if}>
            <{if $etablissement.map|default:false}>
            <a href="<{$etablissement.map}>" rel="external"><{$smarty.const._MD_EXTCAL_ETABLISSEMENT_MAP2}></a><br>
            <{/if}>

            <{if $etablissement.tel_fixe|default:false}><a href="tel:<{$etablissement.tel_fixe}>"><{$etablissement.tel_fixe}></a><br><{/if}>
            <{if $etablissement.tel_portable|default:false}><a href="tel:<{$etablissement.tel_portable}>"><{$etablissement.tel_portable}></a><br><{/if}>
            <{if $etablissement.mail|default:false}><a href="mailto:<{$etablissement.mail}>"><{$etablissement.mail}></a><br><{/if}>
            <{if $etablissement.site|default:false}><a href="<{$etablissement.site}>" rel="external"><{$smarty.const._MD_EXTCAL_VISIT_SITE}></a><br>
            <{/if}>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
        <h6><{$smarty.const._MD_EXTCAL_ETABLISSEMENT_INFO_COMPL}></h6>
        <{if $etablissement.description|default:false}><p><{$etablissement.description}></p><{/if}>
        <{if $etablissement.horaires|default:false}><{$etablissement.horaires}><br><{/if}>
        <{if $etablissement.tarifs|default:false}><{$etablissement.tarifs}>&nbsp; <{$smarty.const._MD_EXTCAL_DEVISE2}><br><{/if}>
        <{if $etablissement.divers|default:false}><{$etablissement.divers}><br><{/if}>
        </div>
    </div>
    <{if $xoops_isadmin}>
    <div class="float-right">
        <a title="<{$smarty.const._MD_EXTCAL_ETABLISSEMENT_EDIT}>" href="<{$xoops_url}>/modules/extcal/admin/etablissement.php?op=edit_etablissement&etablissement_id=<{$etablissement.id}>">
            <span class="fa fa-fw fa-2x fa-pencil-square-o"></span>
        </a>
        <a title="<{$smarty.const._MD_EXTCAL_ETABLISSEMENT_DELETE}>" href="<{$xoops_url}>/modules/extcal/admin/etablissement.php?op=delete_etablissement&etablissement_id=<{$etablissement.id}>">
            <span class="fa fa-fw fa-2x fa-trash-o"></span>
        </a>
    </div>
    <{/if}>
</div>
