<{includeq file="db:system_header.tpl"}>
<{if $install_mods}>
    <script type="text/javascript">
        IMG_ON = '<{xoAdminIcons success.png}>';
        IMG_OFF = '<{xoAdminIcons cancel.png}>';
    </script>
    <div class="floatleft">
        <img class="cursorpointer tooltip" onclick="system_moduleLargeView();" src="<{xoAdminIcons view_large.png}>"
             alt="<{$smarty.const._AM_SYSTEM_MODULES_VIEWLARGE}>" title="<{$smarty.const._AM_SYSTEM_MODULES_VIEWLARGE}>"/>
        <img class="cursorpointer tooltip" onclick="system_moduleListView();" src="<{xoAdminIcons view_small.png}>"
             alt="<{$smarty.const._AM_SYSTEM_MODULES_VIEWLINE}>" title="<{$smarty.const._AM_SYSTEM_MODULES_VIEWLINE}>"/>
    </div>
    <div class="floatright">
        <div class="xo-buttons">
            <a class="ui-corner-all tooltip" href="admin.php?fct=modulesadmin&amp;op=installlist" title="<{$smarty.const._AM_SYSTEM_MODULES_TOINSTALL}>">
                <img src="<{xoAdminIcons install.png}>" alt="<{$smarty.const._AM_SYSTEM_MODULES_TOINSTALL}>"/>
                <{$smarty.const._AM_SYSTEM_MODULES_TOINSTALL}>&nbsp;<span class="red">(<{$toinstall_nb}>)</span>
            </a>
        </div>
    </div>
    <div class="clear spacer"></div>
    <form action="admin.php" method="post" name="moduleadmin">
        <table id="xo-module-sort" class="outer" cellspacing="1">
            <thead>
            <tr class="txtcenter">
                <th><{$smarty.const._AM_SYSTEM_MODULES_MODULE}></th>
                <th><{$smarty.const._AM_SYSTEM_MODULES_VERSION}></th>
                <th><{$smarty.const._AM_SYSTEM_MODULES_LASTUP}></th>
                <th><{$smarty.const._AM_SYSTEM_MODULES_ACTIVE}></th>
                <th><{$smarty.const._AM_SYSTEM_MODULES_MENU}></th>
                <th><{$smarty.const._AM_SYSTEM_MODULES_ACTION}></th>
            </tr>
            </thead>
            <tbody>
            <{foreach item=row from=$install_mods}>
                <{if $row.dirname == 'system'}>
                    <tr class="txtcenter foot">
                        <td>
                            <a class="xo-logonormal tooltip" href="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.adminindex}>" title="<{$row.name}>">
                                <img src="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.image}>" alt="<{$row.name}>"/>
                            </a>

                            <div class="spacer xo-modsimages">
                                <a class="tooltip" href="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.adminindex}>" title="<{$row.name}>">
                                    <img class="xo-mods hide" src="<{xoAdminIcons applications.png}>" alt="<{$row.name}>" title="<{$row.name}>"/>
                                </a>
                                <input type="text" name="newname[<{$row.mid}>]" value="<{$row.name}>" maxlength="150" size="20"/>
                                <input type="hidden" name="oldname[<{$row.mid}>]" value="<{$row.name}>"/>
                            </div>
                        </td>
                        <td>
                            <{if $row.warning_update == 1}>
                                <strong class="red"><{$row.version}></strong>
                            <{else}>
                                <{$row.version}> <{$row.module_status}>
                            <{/if}>
                        </td>
                        <td><{$row.last_update}></td>
                        <td class="xo-modsimages"></td>
                        <td class="xo-modsimages"></td>
                        <td class="xo-modsimages">
                            <{if $row.isactive == 1}>
                                <a class="tooltip" href="<{$xoops_url}>/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=<{$row.dirname}>"
                                   title="<{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>">
                                    <{if $row.warning_update == 1}>
                                        <img src="<{xoAdminIcons messagebox_warning.png}>" alt="<{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>"/>
                                    <{else}>
                                        <img src="<{xoAdminIcons reload.png}>" alt="<{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>"/>
                                    <{/if}>
                                </a>
                            <{/if}>
                            <img class="cursorpointer tooltip" onclick="display_dialog(<{$row.mid}>, true, true, 'slide', 'slide', 240, 450);"
                                 src="<{xoAdminIcons info.png}>" alt="<{$smarty.const._INFO}>" title="<{$smarty.const._INFO}>"/>
                            <input type="hidden" name="module[]" value="<{$row.mid}>"/>
                        </td>
                    </tr>
                <{/if}>
            <{/foreach}>
            <tr class="head">
                <td colspan="6"></td>
            </tr>
            </tbody>
            <tbody class="xo-module">
            <{foreach item=row from=$install_mods}>
                <{if $row.dirname != 'system' && $row.hasmain}>
                    <tr id="mod_<{$row.mid}>" class="<{if $row.dirname == 'system'}>xo-system <{/if}>txtcenter <{cycle values='odd, even'}>">
                        <td>
                            <{if $row.hasadmin == 1 && $row.isactive == 1}>
                                <a class="xo-logonormal tooltip" href="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.adminindex}>" title="<{$row.name}>">
                                    <img src="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.image}>" alt="<{$row.name}>"/>
                                </a>
                            <{else}>
                                <img class="xo-logonormal tooltip" src="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.image}>" alt="<{$row.name}>"
                                     title="<{$row.name}>"/>
                            <{/if}>
                            <div class="spacer xo-modsimages">
                                <{if $row.hasadmin == 1 && $row.isactive == 1}>
                                    <a class="tooltip" href="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.adminindex}>" title="<{$row.name}>">
                                        <img class="xo-mods hide" src="<{xoAdminIcons applications.png}>" alt="<{$row.name}>" title="<{$row.name}>"/>
                                    </a>
                                <{else}>
                                    <img class="xo-mods hide tooltip" src="<{xoAdminIcons applications.png}>" alt="<{$row.name}>" title="<{$row.name}>"/>
                                <{/if}>
                                <input type="text" name="newname[<{$row.mid}>]" value="<{$row.name}>" maxlength="150" size="20"/>
                                <input type="hidden" name="oldname[<{$row.mid}>]" value="<{$row.name}>"/>
                            </div>
                        </td>
                        <td>
                            <{if $row.warning_update == 1}>
                                <a class="tooltip maxi" style="color:red;"
                                   href="<{$xoops_url}>/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=<{$row.dirname}>"
                                   title="<{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>"><{$row.version}></a>
                                <br>
                                <{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>
                            <{else}>
                                <{$row.version}> <{$row.module_status}>
                            <{/if}>
                        </td>
                        <td><{$row.last_update}></td>
                        <td class="xo-modsimages">
                            <img id="loading_active<{$row.mid}>" src="images/spinner.gif" style="display:none;" alt="<{$smarty.const._AM_SYSTEM_LOADING}>"/>
                            <img class="cursorpointer tooltip" id="active<{$row.mid}>"
                                 onclick="system_setStatus( { fct: 'modulesadmin', op: 'display', mid: <{$row.mid}> }, 'active<{$row.mid}>', 'admin.php' )"
                                 src="<{if $row.isactive}><{xoAdminIcons success.png}><{else}><{xoAdminIcons cancel.png}><{/if}>"
                                 alt="<{if $row.isactive}><{$smarty.const._AM_SYSTEM_MODULES_DEACTIVATE}><{else}><{$smarty.const._AM_SYSTEM_MODULES_ACTIVATE}><{/if}>"
                                 title="<{if $row.isactive}><{$smarty.const._AM_SYSTEM_MODULES_DEACTIVATE}><{else}><{$smarty.const._AM_SYSTEM_MODULES_ACTIVATE}><{/if}>"/>
                        </td>
                        <td class="xo-modsimages">
                            <img id="loading_menu<{$row.mid}>" src="images/spinner.gif" style="display:none;" title="<{$smarty.const._AM_SYSTEM_LOADING}>"
                                 alt="<{$smarty.const._AM_SYSTEM_LOADING}>"/>
                            <img class="cursorpointer tooltip" id="menu<{$row.mid}>"
                                 onclick="system_setStatus( { fct: 'modulesadmin', op: 'display_in_menu', mid: <{$row.mid}> }, 'menu<{$row.mid}>', 'admin.php' )"
                                 src="<{if $row.weight != 0}><{xoAdminIcons success.png}><{else}><{xoAdminIcons cancel.png}><{/if}>"
                                 alt="<{if $row.weight != 0}><{$smarty.const._AM_SYSTEM_MODULES_HIDE}><{else}><{$smarty.const._AM_SYSTEM_MODULES_SHOW}><{/if}>"
                                 title="<{if $row.weight != 0}><{$smarty.const._AM_SYSTEM_MODULES_HIDE}><{else}><{$smarty.const._AM_SYSTEM_MODULES_SHOW}><{/if}>"/>
                        </td>
                        <td class="xo-modsimages">
                            <{if $row.isactive == 1}>
                                <a class="tooltip" href="<{$xoops_url}>/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=<{$row.dirname}>"
                                   title="<{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>">
                                    <{if $row.warning_update == 1}>
                                        <img src="<{xoAdminIcons messagebox_warning.png}>" alt="<{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>"/>
                                    <{else}>
                                        <img src="<{xoAdminIcons reload.png}>" alt="<{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>"/>
                                    <{/if}>
                                </a>
                            <{/if}>
                            <a class="tooltip" href="<{$xoops_url}>/modules/system/admin.php?fct=modulesadmin&amp;op=uninstall&amp;module=<{$row.dirname}>"
                               title="<{$smarty.const._AM_SYSTEM_MODULES_UNINSTALL}>">
                                <img src="<{xoAdminIcons uninstall.png}>" alt="<{$smarty.const._AM_SYSTEM_MODULES_UNINSTALL}>"/>
                            </a>
                            <img class="cursorpointer tooltip" onclick="display_dialog(<{$row.mid}>, true, true, 'slide', 'slide', 240, 450);"
                                 src="<{xoAdminIcons info.png}>" alt="<{$smarty.const._INFO}>" title="<{$smarty.const._INFO}>"/>
                            <input type="hidden" name="module[]" value="<{$row.mid}>"/>
                        </td>
                    </tr>
                <{/if}>
            <{/foreach}>
            </tbody>
            <tbody>
            <tr class="head">
                <td colspan="6"></td>
            </tr>
            <{foreach item=row from=$install_mods}>
                <{if $row.dirname != 'system' && !$row.hasmain}>
                    <tr class="txtcenter foot">
                        <td>
                            <a class="xo-logonormal tooltip" href="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.adminindex}>" title="<{$row.name}>">
                                <img src="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.image}>" alt="<{$row.name}>"/>
                            </a>

                            <div class="spacer xo-modsimages">
                                <a class="tooltip" href="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.adminindex}>" title="<{$row.name}>">
                                    <img class="xo-mods hide" src="<{xoAdminIcons applications.png}>" alt="<{$row.name}>" title="<{$row.name}>"/>
                                </a>
                                <input type="text" name="newname[<{$row.mid}>]" value="<{$row.name}>" maxlength="150" size="20"/>
                                <input type="hidden" name="oldname[<{$row.mid}>]" value="<{$row.name}>"/>
                            </div>
                        </td>
                        <td>
                            <{if $row.warning_update == 1}>
                                <a class="tooltip maxi" style="color:red;"
                                   href="<{$xoops_url}>/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=<{$row.dirname}>"
                                   title="<{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>"><{$row.version}></a>
                                <br>
                                <{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>
                            <{else}>
                                <{$row.version}> <{$row.module_status}>
                            <{/if}>
                        </td>
                        <td><{$row.last_update}></td>
                        <td class="xo-modsimages"><img id="loading_mid<{$row.mid}>" src="images/spinner.gif" style="display:none;"
                                                       title="<{$smarty.const._AM_SYSTEM_LOADING}>" alt="<{$smarty.const._AM_SYSTEM_LOADING}>"/><img
                                    class="cursorpointer tooltip" id="mid<{$row.mid}>"
                                    onclick="system_setStatus( { fct: 'modulesadmin', op: 'display', mid: <{$row.mid}> }, 'mid<{$row.mid}>', 'admin.php' )"
                                    src="<{if $row.isactive}><{xoAdminIcons success.png}><{else}><{xoAdminIcons cancel.png}><{/if}>"
                                    alt="<{if $row.isactive}><{$smarty.const._AM_SYSTEM_MODULES_DEACTIVATE}><{else}><{$smarty.const._AM_SYSTEM_MODULES_ACTIVATE}><{/if}>"
                                    title="<{if $row.isactive}><{$smarty.const._AM_SYSTEM_MODULES_DEACTIVATE}><{else}><{$smarty.const._AM_SYSTEM_MODULES_ACTIVATE}><{/if}>"/>
                        </td>
                        <td class="xo-modsimages"></td>
                        <td class="xo-modsimages">
                            <{if $row.isactive == 1}>
                                <a class="tooltip" href="<{$xoops_url}>/modules/system/admin.php?fct=modulesadmin&amp;op=update&amp;module=<{$row.dirname}>"
                                   title="<{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>">
                                    <img src="<{xoAdminIcons reload.png}>" alt="<{$smarty.const._AM_SYSTEM_MODULES_UPDATE}>"/>
                                </a>
                            <{/if}>
                            <a class="tooltip" href="<{$xoops_url}>/modules/system/admin.php?fct=modulesadmin&amp;op=uninstall&amp;module=<{$row.dirname}>"
                               title="<{$smarty.const._AM_SYSTEM_MODULES_UNINSTALL}>">
                                <img src="<{xoAdminIcons uninstall.png}>" alt="<{$smarty.const._AM_SYSTEM_MODULES_UNINSTALL}>"/>
                            </a>
                            <img class="cursorpointer tooltip" onclick="display_dialog(<{$row.mid}>, true, true, 'slide', 'slide', 240, 450);"
                                 src="<{xoAdminIcons info.png}>" alt="<{$smarty.const._INFO}>" title="<{$smarty.const._INFO}>"/>
                            <input type="hidden" name="module[]" value="<{$row.mid}>"/>
                        </td>
                    </tr>
                <{/if}>
            <{/foreach}>
            </tbody>
            <tfoot>
            <tr class="txtcenter foot">
                <td colspan="6">
                    <input type="hidden" name="fct" value="modulesadmin"/>
                    <input type="hidden" name="op" value="confirm"/>
                    <input class="xo-formbuttons" type="submit" name="submit" value="<{$smarty.const._AM_SYSTEM_MODULES_SUBMIT}>"/>
                </td>
            </tr>
            </tfoot>
        </table>
        <{php}>echo $GLOBALS['xoopsSecurity']->getTokenHTML();<{/php}>
    </form>
<{/if}>

<{if $toinstall_mods}>
    <div class="floatleft">
        <img class="cursorpointer tooltip" onclick="system_moduleLargeView();" src="<{xoAdminIcons view_large.png}>"
             alt="<{$smarty.const._AM_SYSTEM_MODULES_VIEWLARGE}>" title="<{$smarty.const._AM_SYSTEM_MODULES_VIEWLARGE}>"/>
        <img class="cursorpointer tooltip" onclick="system_moduleListView();" src="<{xoAdminIcons view_small.png}>"
             alt="<{$smarty.const._AM_SYSTEM_MODULES_VIEWLINE}>" title="<{$smarty.const._AM_SYSTEM_MODULES_VIEWLINE}>"/>
    </div>
    <div class="clear spacer"></div>
    <table class="outer" cellspacing="1">
        <thead>
        <tr class="txtcenter">
            <th><{$smarty.const._AM_SYSTEM_MODULES_MODULE}></th>
            <th><{$smarty.const._AM_SYSTEM_MODULES_VERSION}></th>
            <th><{$smarty.const._AM_SYSTEM_MODULES_ACTION}></th>
        </tr>
        </thead>
        <tbody>
        <{foreach item=row from=$toinstall_mods}>
            <tr class="txtcenter <{cycle values='odd, even'}>">
                <td>
                    <img class="xo-logonormal" src="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.image}>" alt="<{$row.name}>" title="<{$row.name}>"/>

                    <div class="spacer xo-modsimages">
                        <{if $row.hasadmin == 1 && $row.isactive == 1}>
                            <a class="tooltip" href="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.adminindex}>" title="<{$row.name}>">
                                <img class="xo-mods hide" src="<{xoAdminIcons applications.png}>" alt="<{$row.name}>" title="<{$row.name}>"/>
                            </a>
                        <{else}>
                            <img class="xo-mods hide tooltip" src="<{xoAdminIcons applications.png}>" alt="<{$row.name}>" title="<{$row.name}>"/>
                        <{/if}>
                        <span class="spacer bold"><{$row.name}></span>
                    </div>
                </td>
                <td><{$row.version}> <{$row.module_status}></td>
                <td class="xo-modsimages">
                    <a class="tooltip" href="<{$xoops_url}>/modules/system/admin.php?fct=modulesadmin&amp;op=install&amp;module=<{$row.dirname}>"
                       title="<{$smarty.const._AM_SYSTEM_MODULES_INSTALL}>">
                        <img src="<{xoAdminIcons install.png}>" alt="<{$smarty.const._AM_SYSTEM_MODULES_INSTALL}>"/>
                    </a>
                    <img class="cursorpointer tooltip" onclick="display_dialog(<{$row.mid}>, true, true, 'slide', 'slide', 240, 450);"
                         src="<{xoAdminIcons info.png}>" alt="<{$smarty.const._INFO}>" title="<{$smarty.const._INFO}>"/>
                </td>
            </tr>
        <{/foreach}>
        </tbody>
    </table>
<{/if}>
<!--Pop-pup-->
<{foreach item=row from=$mods_popup}>
    <div id="dialog<{$row.mid}>" title="<{$row.name}>" style='display:none;'>
        <table>
            <tr>
                <td class="width10 aligntop">
                    <img src="<{$xoops_url}>/modules/<{$row.dirname}>/<{$row.image}>" alt="<{$row.name}>" title="<{$row.name}>"/>
                </td>
                <td>
                    <ul class="xo-moduleinfos">
                        <li><span class="bold"><{$smarty.const._VERSION}></span>&nbsp;:&nbsp;<{$row.version}> <{$row.module_status}></li>
                        <li><span class="bold"><{$smarty.const._AUTHOR}></span>&nbsp;:&nbsp;<{$row.author}></li>
                        <li><span class="bold"><{$smarty.const._CREDITS}></span>&nbsp;:&nbsp;<{$row.credits}></li>
                        <li><span class="bold"><{$smarty.const._LICENCE}></span>&nbsp;:&nbsp;<{$row.license}></li>
                    </ul>
                </td>
            </tr>
        </table>
        <p><{$row.description}></p>
    </div>
<{/foreach}>
<!--Pop-pup-->
