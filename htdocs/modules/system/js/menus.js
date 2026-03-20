jQuery(function($){
    'use strict';

    // utilities
    function getTokenData() {
        var $tokenInput = $('#menus-token').find('input').first();
        var data = {};
        if ($tokenInput.length) {
            data[$tokenInput.attr('name')] = $tokenInput.val();
            data['XOOPS_TOKEN_REQUEST'] = $tokenInput.val();
        }
        return data;
    }

    function updateTokenFromResponse(resp) {
        if (resp && resp.token) {
            $('#menus-token').html(resp.token);
        }
    }

    var labelsCfg = (window.XOOPS_MENUS && window.XOOPS_MENUS.labels) || {};
    var LABEL_YES = labelsCfg.activeYes || 'Yes';
    var LABEL_NO  = labelsCfg.activeNo  || 'No';

    function ajaxJsonPost(url, data, onSuccess) {
        return $.ajax({
            url: url,
            method: 'POST',
            data: data,
            dataType: 'json'
        }).done(function(response){
            updateTokenFromResponse(response);
            if (typeof onSuccess === 'function') onSuccess(response);
        }).fail(function(jqXHR, textStatus, errorThrown){
            console.error('Ajax error:', textStatus, errorThrown, jqXHR.responseText);
            alert('Ajax error (see console)');
        });
    }

    // EXPAND / COLLAPSE (tree view)
    $(document).on('click', '.xo-menus-tree .xo-menus-disclose:not(.xo-menus-no-toggle)', function(e) {
        e.stopPropagation();
        var $li = $(this).closest('li.xo-menus-has-children');
        $li.toggleClass('xo-menus-collapsed xo-menus-expanded');
    });

    // EXPAND / COLLAPSE (nested sortable items)
    $(document).on('click', '.xo-menus-sortable .xo-menus-disclose', function(e) {
        e.stopPropagation();
        $(this).closest('li')
            .toggleClass('mjs-nestedSortable-collapsed')
            .toggleClass('mjs-nestedSortable-expanded');
    });

    // SORTABLE (top-level category list items)
    if ($.fn.sortable) {
        $('#menus-row').sortable({
            items: '> li.xo-menus-branch',
            handle: '> .xo-menus-cat-node',
            placeholder: 'ui-sortable-placeholder',
            tolerance: 'pointer',
            axis: 'y',
            helper: function(e, ui) {
                var $clone = ui.clone();
                $clone.width(ui.outerWidth());
                return $clone;
            },
            update: function() {
                var ids = $('#menus-row').children('li[data-id]').map(function(){ return $(this).data('id'); }).get();
                var data = $.extend({ order: ids }, getTokenData());
                ajaxJsonPost('admin.php?fct=menus&op=saveorder', data, function(response){
                    if (!(response && response.success)) {
                        alert(response && response.message ? response.message : 'Save failed');
                    }
                });
            }
        }).disableSelection();

        // NESTED SORTABLE (item lists — both inside accordion on list page and on viewcat page)
        if ($.fn.nestedSortable) {
            $('ol.xo-menus-sortable').each(function() {
                var $sortable = $(this);
                $sortable.nestedSortable({
                    handle: 'div',
                    cancel: 'a, .item-active-toggle, .xo-menus-disclose',
                    items: 'li',
                    tolerance: 'pointer',
                    toleranceElement: '> div',
                    placeholder: 'ui-state-highlight',
                    helper: 'clone',
                    opacity: 0.6,
                    revert: 250,
                    tabSize: 25,
                    maxLevels: 3,
                    isTree: true,
                    expandOnHover: 700,
                    startCollapsed: false,
                    update: function() {
                        // data-category-id on viewcat, data-cid on list page accordions
                        var categoryId = $sortable.data('category-id') || $sortable.data('cid');
                        var tree = $sortable.nestedSortable('toHierarchy');
                        var data = $.extend({
                            category_id: categoryId,
                            tree: JSON.stringify(tree)
                        }, getTokenData());
                        ajaxJsonPost('admin.php?fct=menus&op=saveorderitems', data, function(response){
                            if (!(response && response.success)) {
                                alert(response && response.message ? response.message : 'Save failed');
                            }
                        });
                    }
                });
            });
        }
    }

    // helper to check ancestors for disabled state
    function hasInactiveAncestor($li) {
        var pid = parseInt($li.data('pid'), 10) || 0;
        while (pid) {
            var $parentToggle = $('.item-active-toggle[data-id="' + pid + '"]');
            if ($parentToggle.length) {
                if (parseInt($parentToggle.attr('data-active'), 10) === 0) {
                    return true;
                }
                pid = parseInt($parentToggle.closest('li[data-pid]').data('pid'), 10) || 0;
            } else {
                break;
            }
        }
        return false;
    }

    // helper to update row visuals depending on state
    function updateRowState($elem, state) {
        var $node = $elem.closest('.xo-menus-node, .xo-menus-sortable-row');
        if ($node.length) {
            if (state) {
                $node.removeClass('inactive');
            } else {
                $node.addClass('inactive');
            }
        }
    }

    function refreshChildLocks() {
        $('.item-active-toggle').each(function() {
            var $toggle = $(this);
            var $li = $toggle.closest('li');
            var active = parseInt($toggle.attr('data-active'), 10) ? 1 : 0;
            updateRowState($toggle, active);
            if (hasInactiveAncestor($li)) {
                $toggle.addClass('disabled').css('cursor', 'not-allowed').attr('title', window.XOOPS_MENUS.messages.parentInactive || 'Parent inactive');
            } else {
                $toggle.removeClass('disabled').css('cursor', '').removeAttr('title');
            }
        });
        $('.category-active-toggle').each(function() {
            var $toggle = $(this);
            var active = parseInt($toggle.attr('data-active'), 10) ? 1 : 0;
            updateRowState($toggle, active);
        });
    }

    // initial state on page load
    refreshChildLocks();

    // Auto-expand category from URL hash (e.g. #cat_4)
    if (window.location.hash) {
        var $target = $(window.location.hash);
        if ($target.length && $target.hasClass('xo-menus-has-children')) {
            $target.removeClass('xo-menus-collapsed').addClass('xo-menus-expanded');
        }
    }

    // TOGGLE ACTIVE (categories & items)
    $(document).on('click', '.category-active-toggle, .item-active-toggle', function(e){
        e.preventDefault();
        var $el = $(this);
        if ($el.hasClass('disabled')) {
            var msg = (window.XOOPS_MENUS && window.XOOPS_MENUS.messages && window.XOOPS_MENUS.messages.parentInactive) ? window.XOOPS_MENUS.messages.parentInactive : 'Parent is inactive';
            alert(msg);
            return;
        }
        var isCategory = $el.hasClass('category-active-toggle');
        var id = $el.data('id');
        if (!id) return;

        var url = isCategory ? 'admin.php?fct=menus&op=toggleactivecat' : 'admin.php?fct=menus&op=toggleactiveitem';
        var paramName = isCategory ? 'category_id' : 'item_id';
        var data = {};
        data[paramName] = id;
        $.extend(data, getTokenData());

        ajaxJsonPost(url, data, function(response){
            if (response && response.success) {
                var active = parseInt(response.active, 10) ? 1 : 0;
                function updateToggle($toggle, state) {
                    var $img = $toggle.find('img');
                    if (state) {
                        $toggle.removeClass('xo-menus-inactive').addClass('xo-menus-active').attr('data-active', 1);
                        if ($img.length) {
                            $img.attr('alt', LABEL_YES).attr('title', LABEL_YES);
                            $img.attr('src', $img.attr('src').replace(/cancel\.png/, 'success.png'));
                        }
                    } else {
                        $toggle.removeClass('xo-menus-active').addClass('xo-menus-inactive').attr('data-active', 0);
                        if ($img.length) {
                            $img.attr('alt', LABEL_NO).attr('title', LABEL_NO);
                            $img.attr('src', $img.attr('src').replace(/success\.png/, 'cancel.png'));
                        }
                    }
                }

                updateToggle($el, active);
                updateRowState($el, active);

                if (response.updated && Array.isArray(response.updated)) {
                    response.updated.forEach(function(updatedId) {
                        var $child = $('.item-active-toggle[data-id="' + updatedId + '"]');
                        if ($child.length) {
                            updateToggle($child, active);
                            updateRowState($child, active);
                        }
                    });
                }

                refreshChildLocks();
            } else {
                alert(response && response.message ? response.message : 'Toggle failed');
            }
        });
    });

});
