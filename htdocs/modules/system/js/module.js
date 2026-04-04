/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * Modules Javascript
 *
 * @copyright   (c) 2000-2026 XOOPS Project (https://xoops.org)
 * @license     GNU GPL 2.0 or later (https://www.gnu.org/licenses/gpl-2.0.html)
 * @author      Andricq Nicolas (AKA MusS)
 */

$(document).ready(
    function(){
        // Controls Drag + Drop
        $('#xo-module-sort tbody.xo-module').sortable({
                placeholder: 'ui-state-highlight',
                update: function(event, ui) {
                    var list = $(this).sortable( 'serialize');
                    const $form = $('form[name="moduleadmin"]');
                    const $tokenInput = $form.find("input[name='XOOPS_TOKEN_REQUEST']").first();
                    if ($tokenInput.length) {
                        list += '&' + encodeURIComponent($tokenInput.attr('name')) + '=' + encodeURIComponent($tokenInput.val());
                    }
                    $.post( 'admin.php?fct=modulesadmin&op=order', list, function (response) {
                        if (response && response.includes('<input')) {
                            // Extract the new token value from the returned HTML input element
                            const $newToken = $(response).filter('input[name="XOOPS_TOKEN_REQUEST"]');
                            if ($newToken.length && $tokenInput.length) {
                                $tokenInput.val($newToken.val());
                            }
                        }
                    });
                }
            }
        );
    }
);
