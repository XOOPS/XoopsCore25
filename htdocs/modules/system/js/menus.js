/* System Menu Administration — vanilla JS + SortableJS */
(function () {
    'use strict';

    var adminUrl = 'admin.php?fct=menus';
    var MAX_DEPTH = 3;

    /* ─── Token management ─── */

    function getToken() {
        var el = document.getElementById('sm-token');
        return el ? el.value : '';
    }

    function setToken(newToken) {
        var el = document.getElementById('sm-token');
        if (el) {
            el.value = newToken;
        }
    }

    /* ─── URL encoding helper ─── */

    function encodeParams(obj) {
        var parts = [];
        Object.keys(obj).forEach(function (key) {
            var val = obj[key];
            if (Array.isArray(val)) {
                val.forEach(function (v) {
                    parts.push(encodeURIComponent(key) + '[]=' + encodeURIComponent(v));
                });
            } else {
                parts.push(encodeURIComponent(key) + '=' + encodeURIComponent(val));
            }
        });
        return parts.join('&');
    }

    /* ─── AJAX helper ─── */

    function postJson(op, data, onSuccess) {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', adminUrl, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) {
                return;
            }
            if (xhr.status !== 200) {
                console.error('AJAX error', xhr.status, xhr.responseText);
                return;
            }
            var response;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                console.error('Invalid JSON response', xhr.responseText);
                return;
            }
            if (response.token) {
                setToken(response.token);
            }
            if (typeof onSuccess === 'function') {
                onSuccess(response);
            }
        };

        data.op = op;
        data[XOOPS_TOKEN_REQUEST] = getToken();
        xhr.send(encodeParams(data));
    }

    /* ─── HTML escape ─── */

    function escapeHtml(str) {
        var node = document.createTextNode(String(str));
        var div = document.createElement('div');
        div.appendChild(node);
        return div.innerHTML;
    }

    /* ─── Category sortable ─── */

    function initCategorySort() {
        var el = document.getElementById('sm-cat-list');
        if (!el || typeof Sortable === 'undefined') {
            return;
        }

        Sortable.create(el, {
            handle: '.sm-list__handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function () {
                var items = el.querySelectorAll('li[data-id]');
                var order = [];
                items.forEach(function (li) {
                    order.push(li.getAttribute('data-id'));
                });
                postJson('reordercat', { order: order }, function (resp) {
                    if (!resp.success) {
                        console.error('Category reorder failed', resp.message);
                    }
                });
            }
        });
    }

    /* ─── Item tree ─── */

    function nestItems(flatItems) {
        var map = {};
        var roots = [];

        flatItems.forEach(function (item) {
            map[item.id] = { data: item, children: [] };
        });

        flatItems.forEach(function (item) {
            var pid = item.pid || 0;
            if (pid && map[pid]) {
                map[pid].children.push(map[item.id]);
            } else {
                roots.push(map[item.id]);
            }
        });

        return roots;
    }

    function buildTreeHtml(nodes) {
        if (!nodes || nodes.length === 0) {
            return '';
        }
        var html = '';
        nodes.forEach(function (node) {
            var item = node.data;
            var inactive = item.active ? '' : ' sm-tree__node--inactive';
            var activeIcon = item.active
                ? 'check-circle text-success'
                : 'times-circle text-danger';
            var editUrl = adminUrl + '&op=edititem&item_id=' + encodeURIComponent(item.id);
            var delUrl  = adminUrl + '&op=delitem&item_id='  + encodeURIComponent(item.id);
            var addUrl  = adminUrl + '&op=additem&category_id=' + encodeURIComponent(item.category_id)
                        + '&pid=' + encodeURIComponent(item.id);

            html += '<li class="sm-tree__node' + inactive + '" data-id="' + escapeHtml(item.id) + '">';
            html += '<div class="sm-tree__row">';
            html += '<span class="sm-tree__handle" title="Drag to reorder"><i class="fa fa-arrows-v"></i></span>';
            html += '<span class="sm-tree__title">' + escapeHtml(item.title) + '</span>';
            html += '<span class="sm-tree__url">' + escapeHtml(item.url) + '</span>';
            html += '<span class="sm-tree__actions">';
            html += '<a href="#" class="sm-toggle sm-toggle--item" data-id="' + escapeHtml(item.id) + '" data-active="' + escapeHtml(item.active) + '">';
            html += '<i class="fa fa-' + activeIcon + '"></i></a>';
            html += '<a href="' + addUrl + '" title="Add child"><i class="fa fa-plus"></i></a>';
            html += '<a href="' + editUrl + '" title="Edit"><i class="fa fa-pencil"></i></a>';
            html += '<a href="' + delUrl + '" title="Delete"><i class="fa fa-trash text-danger"></i></a>';
            html += '</span>';
            html += '</div>';

            if (node.children && node.children.length > 0) {
                html += '<ul class="sm-tree__children">';
                html += buildTreeHtml(node.children);
                html += '</ul>';
            } else {
                html += '<ul class="sm-tree__children"></ul>';
            }

            html += '</li>';
        });
        return html;
    }

    function computeTreeDepth(nodes) {
        if (!nodes || nodes.length === 0) {
            return 0;
        }
        var max = 0;
        nodes.forEach(function (node) {
            var d = 1 + computeTreeDepth(node.children);
            if (d > max) {
                max = d;
            }
        });
        return max;
    }

    function serializeTree(el) {
        var nodes = [];
        var items = el.children;
        for (var i = 0; i < items.length; i++) {
            var li = items[i];
            if (li.tagName !== 'LI') {
                continue;
            }
            var id = li.getAttribute('data-id');
            var childUl = li.querySelector(':scope > ul.sm-tree__children');
            var node = { id: id, children: [] };
            if (childUl) {
                node.children = serializeTree(childUl);
            }
            nodes.push(node);
        }
        return nodes;
    }

    function saveItemTree(rootEl, categoryId) {
        var tree = serializeTree(rootEl);
        var depth = computeTreeDepth(tree);

        if (depth > MAX_DEPTH) {
            alert('Maximum nesting depth of ' + MAX_DEPTH + ' exceeded. Please restructure the menu.');
            return;
        }

        postJson('reorderitems', {
            category_id: categoryId,
            tree: JSON.stringify(tree)
        }, function (resp) {
            if (!resp.success) {
                console.error('Item tree save failed', resp.message);
            }
        });
    }

    function initNestedSortable(rootEl, categoryId) {
        if (typeof Sortable === 'undefined') {
            return;
        }

        var allLists = rootEl.querySelectorAll('ul.sm-tree__children');
        var lists = [rootEl];
        allLists.forEach(function (ul) {
            lists.push(ul);
        });

        lists.forEach(function (ul) {
            Sortable.create(ul, {
                group: 'nested-items',
                handle: '.sm-tree__handle',
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onEnd: function () {
                    saveItemTree(rootEl, categoryId);
                }
            });
        });
    }

    function initItemTree() {
        var treeEl = document.getElementById('sm-item-tree');
        var jsonEl = document.getElementById('sm-items-json');
        if (!treeEl || !jsonEl) {
            return;
        }

        var categoryId = treeEl.getAttribute('data-category-id');
        var flatItems;
        try {
            flatItems = JSON.parse(jsonEl.value);
        } catch (e) {
            console.error('Failed to parse items JSON', e);
            return;
        }

        if (!Array.isArray(flatItems) || flatItems.length === 0) {
            return;
        }

        var nested = nestItems(flatItems);
        treeEl.innerHTML = buildTreeHtml(nested);
        initNestedSortable(treeEl, categoryId);
    }

    /* ─── Toggle handler ─── */

    function initToggles() {
        document.addEventListener('click', function (e) {
            var target = e.target;
            // Walk up to find .sm-toggle anchor
            while (target && target !== document) {
                if (target.classList && target.classList.contains('sm-toggle')) {
                    break;
                }
                target = target.parentNode;
            }
            if (!target || !target.classList || !target.classList.contains('sm-toggle')) {
                return;
            }

            e.preventDefault();

            var id     = target.getAttribute('data-id');
            var active = target.getAttribute('data-active');
            var isCat  = target.classList.contains('sm-toggle--cat');
            var op     = isCat ? 'toggleactivecat' : 'toggleactiveitem';

            postJson(op, { id: id, active: active }, function (resp) {
                if (!resp.success) {
                    console.error('Toggle failed', resp.message);
                    return;
                }

                var newActive = resp.active ? 1 : 0;
                target.setAttribute('data-active', newActive);

                var icon = target.querySelector('i');
                if (icon) {
                    if (newActive) {
                        icon.className = 'fa fa-check-circle text-success';
                    } else {
                        icon.className = 'fa fa-times-circle text-danger';
                    }
                }

                var li = target.closest('li');
                if (li) {
                    if (newActive) {
                        li.classList.remove('sm-list__item--inactive', 'sm-tree__node--inactive');
                    } else {
                        li.classList.add(isCat ? 'sm-list__item--inactive' : 'sm-tree__node--inactive');
                    }
                }

                // Reload if server signals a cascade (e.g. category toggled items)
                if (resp.reload) {
                    window.location.reload();
                }
            });
        });
    }

    /* ─── Bootstrap ─── */

    document.addEventListener('DOMContentLoaded', function () {
        initCategorySort();
        initItemTree();
        initToggles();
    });

})();
