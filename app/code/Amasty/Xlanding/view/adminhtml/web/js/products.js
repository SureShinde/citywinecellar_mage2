
/*global $H */
/*global $$ */
/*global jQuery */
/*@api*/
define([
    'jquery',
    'mage/template',
    'mage/translate',
    'jquery/ui', // can't be removed, but can be replaced with: 'jquery-ui-modules/sortable'
    'prototype',
], function ($, mageTemplate, $translate) {
    'use strict';

    $.widget('mage.amlandingProducts', {
        options: {
            'viewSelector': '[data-role=page_products]',
            'savePositionsUrl': null,
            'currentPageId': null,
            'formSelector': null
        },
        sourcePosition: null,
        sourceIndex: null,
        view: null,

        _create: function () {
            this.view = $(this.options.viewSelector);
            this.setupView();
            this.initViewEventHandlers();
            this.updateProductsByConditions();

            this.view.on(
                'contentUpdated', function () {
                    this.setupView();
                }.bind(this)
            );

            $('#am-products-sort').on('click', function () {
                this.savePositions(function () {
                    this.reloadView();
                }.bind(this));
            }.bind(this));

            $('select.store').on('change', function () {
                this.updateProducts(function () {
                    this.reloadView();
                }.bind(this));
            }.bind(this));

            $('.rule-tree-wrapper').on('DOMSubtreeModified', function () {
                $('#page_tabs_conditions_section').addClass('_changed');
            });
        },

        initViewEventHandlers: function () {
            this.view.on('change', '[data-amlanding-js="am-display-mode"]', this.changeDisplayMode.bindAsEventListener(this));
            this.view.on('click', '[data-amlanding-js="am-move-top"]', this.moveToTop.bindAsEventListener(this));
            this.view.on('mousedown mouseup', '[data-amlanding-js="am-switch-button"], [data-amlanding-js="am-move-top"]', this.clickEffect.bindAsEventListener(this));
        },

        reloadView: function () {
            $(this.options.viewSelector).amlandingProductsPager('reload');
        },

        setupView: function () {
            var sortableParent = this.view.find('#am-product-list'),
                data;

            sortableParent.sortable({
                distance: 8,
                tolerance: 'pointer',
                cancel: 'input, button',
                forcePlaceholderSize: true,
                update: this.sortableDidUpdate.bind(this),
                start: this.sortableStartUpdate.bind(this)
            });
            data = {};
            sortableParent.data('sortable').items.each(function (instance) {
                var key = $(instance.item).find('input[name=entity_id]').val();

                data[key] = $(instance.item);
            });

            sortableParent.data('item_id_mapper', data);
        },

        getSortedPositionsFromData: function (sortData) {
            // entity_id => pos
            var sortedArr = [];

            sortData.each(Array.prototype.push.bindAsEventListener(sortedArr));
            sortedArr.sort(this.sortArrayAsc.bind(this));

            return sortedArr;
        },

        getPage: function (view) {
            var parentView = $(view).parents('.am-tab');

            return parseInt(parentView.find('input[name=page]').val(), 10);
        },

        getPageSize: function (view) {
            var parentView = $(view).parents('.am-tab');

            return parseInt(parentView.find('select[name=limit]').val(), 10);
        },

        getStartIdx: function (view) {
            var perPage = this.getPageSize(view);

            return this.getPage(view) * perPage - perPage;
        },

        sortableDidUpdate: function (event, ui) {
            var checkbox = ui.item.find("input[type=checkbox]");

            this.populateFromIdx(ui.item.parents('.ui-sortable').children());
            checkbox.prop( "checked", true );
            this.changeDisplayModeLabel(checkbox);
            this.sortDataObject();
            this.changeDisplayModeLabel(ui.item.find("input[type=checkbox]"));
        },

        moveItemInView: function (view, from, to) {
            var items = view.find('>*');

            if (to > from) {
                $(items.get(from)).insertAfter($(items.get(to)));
            } else {
                $(items.get(from)).insertBefore($(items.get(to)));
            }
            items.removeClass('selected');
            this.populateFromIdx(items);
        },

        sortableStartUpdate: function (event, ui) {
            ui.item.data('originIndex', ui.item.index());
        },

        changeDisplayMode: function(event) {
            var checkbox = $(event.target);

            this.changeDisplayModeLabel(checkbox);
            if (checkbox.is(':checked')) {
                this.sortDataObject();
            } else {
                var product = checkbox.parents('li'),
                    productDataObject = {
                        entity_id: product.find('input[name=entity_id]').val(),
                        source_position: product.find('input[name=position]').val()
                    };
                this.saveProductAutomaticMode(productDataObject);
            }
        },

        changeDisplayModeLabel: function(checkbox) {
            var labelText = checkbox.parent().find('[data-amlanding-js="am-label-text"]'),
                label = checkbox.parent().find('[data-amlanding-js="am-label"]'),
                switchButton = checkbox.parent();

            if (checkbox.is(':checked')) {
                labelText.text($translate('Manual'));
                label.prop('title', $translate('Enable Auto Sorting'));
                switchButton.addClass('-manual');
            } else {
                labelText.text($translate('Auto'));
                label.prop('title', $translate('Enable Manual Sorting'));
                switchButton.removeClass('-manual');
            }
        },

        moveToTop: function (event) {
            var product = $(event.currentTarget).parents('li'),
                input = product.find('input[name=position]'),
                checkbox = product.find('[data-amlanding-js="am-display-mode"]'),
                idx = parseInt(product.index(), 10),
                pos = idx + this.getStartIdx($(input));

            event.preventDefault();
            product.find("input[type=checkbox]").prop( "checked", true );

            if (!this.isValidPosition(pos)) {
                this.sourcePosition = null;
                this.sourceIndex = null;
            } else {
                this.sourcePosition = pos;
                this.sourceIndex = idx;
            }

            input.val(0);
            this.changePosition(input);
            this.changeDisplayModeLabel(checkbox);
        },

        clickEffect: function(event) {
            var button;

            if (event.which == 1) {
                if ($(event.target).hasClass('am-button')) {
                    button = $(event.target);
                    button.toggleClass('-pressed');
                } else {
                    button = $(event.target).parents('.am-button');
                    button.toggleClass('-pressed');
                }
            }
        },

        changePosition: function (input) {
            var destinationPosition = parseInt(input.val(), 10),
                destinationIndex = destinationPosition - this.getStartIdx(input),
                product = $(input).parents('li'),
                totalIndex = parseInt($('#catalog_category_products-total-count').text(), 10) - 1;

            if (destinationPosition > totalIndex) {
                input.val(totalIndex);
                this.changePosition(input);

                return;
            }

            // Moving within current page
            if (this.isValidPosition(this.sourcePosition)
                && this.isValidPosition(destinationPosition)
                && this.sourcePosition !== destinationPosition
            ) {
                // Move on all views
                this.element.find('.ui-sortable').each(function (idx, item) {
                    this.moveItemInView($(item), this.sourceIndex, destinationIndex);
                }.bind(this));

                this.sortDataObject();

                return;
            }

            // Moving off the current page
            if (
                this.isValidPosition(this.sourcePosition) &&
                destinationPosition >= 0 &&
                this.sourcePosition !== destinationPosition
            ) {
                var productDataObject = {
                    entity_id: product.find('input[name=entity_id]').val(),
                    source_position: this.sourcePosition
                };
                this.saveTopPosition(productDataObject);
            }

        },

        populateFromIdx: function (items) {
            var startIdx = this.getStartIdx(items);

            items.find('input[name=position]').each(function (idx, item) {
                $(item).val(startIdx + idx);
            });
        },

        isValidPosition: function (pos) {
            var view = this.view.find('>*:eq(0)'),
                maxPos = this.getPage(view) * this.getPageSize(view),
                minPos = this.getStartIdx(view);

            return pos !== null && pos >= minPos && pos < maxPos;
        },

        sortArrayAsc: function (a, b) {
            var sortData = this.getSortData(),
                keyA = sortData.get(a.key),
                keyB = sortData.get(b.key),
                diff = parseFloat(a.value) - parseFloat(b.value);

            if (diff !== 0) {
                return diff;
            }

            if (keyA === undefined && keyB !== undefined) {
                return -1;
            }

            if (keyA !== undefined && keyB === undefined) {
                return 1;
            }

            return 0;
        },

        getSortData: function () {
            return $H(JSON.parse($('#vm_landing_products').val()));
        },

        sortDataObject: function (event) {
            this.initSortDataObject();
            this.savePositions();
        },

        initSortDataObject: function() {
            var data = this.getSortData(),
                sorted = $H(),
                sortedNew = $H(),
                uiSortable = this.view.find('.ui-sortable'),
                startIdx = this.getStartIdx(uiSortable),
                sortedArr = this.getSortedPositionsFromData(data);

            // Pre-sort all items
            sortedArr.each(function (item, idx) {
                sorted.set(item.key, String(idx));
            });

            $(uiSortable).find('> *').each(function (idx, item) {
                var entityId = $(item).find('[name=entity_id]').val();
                sorted.set(entityId, String(startIdx));
                if ($(item).find('input:checked').length){
                    sortedNew.set(entityId, String(startIdx));
                }
                startIdx++;
            });

            $('#vm_landing_products_manual').val(Object.toJSON(sortedNew));
            $('#vm_landing_products').val(Object.toJSON(sorted));
            return sorted;
        },

        savePositions: function (callback) {
            var data = {
                'page_id': this.getPage(this.view),
                'limit': $('[name="limit"]').val(),
                'positions': JSON.parse($('#vm_landing_products_manual').val()),
                'store_id': $('select.store').val(),
                'sort_order': $('select.sort_order').val()
            },
            showLoader = typeof callback !== 'undefined';

            $.ajax({
                type: 'POST',
                url: this.options.savePositionsUrl,
                data: data,
                context: $('body'),
                showLoader: showLoader
            }).success(function () {
                if (callback) {
                    callback();
                }
            });
        },

        updateProducts: function(callback) {
            var data = {
                    'page_id': this.getPage(this.view),
                    'store_id': $('select.store').val(),
                    'limit': $('[name="limit"]').val(),
                    'sort_order': $('select.sort_order').val()
                },
                showLoader = typeof callback !== 'undefined';

            $.ajax({
                type: 'POST',
                url: this.options.savePositionsUrl,
                data: data,
                context: $('body'),
                showLoader: showLoader
            }).success(function () {
                if (callback) {
                    callback();
                }
            });
        },

        saveProductAutomaticMode: function (productData) {
            var data = {
                'page_id': this.getPage(this.view),
                'automatic_product_data': productData,
                'store_id': $('select.store').val(),
                'limit': $('[name="limit"]').val(),
                'sort_order': $('select.sort_order').val()
            };

            $.ajax({
                type: 'POST',
                url: this.options.savePositionsUrl,
                data: data,
                context: $('body'),
                showLoader: true
            }).success(function () {
               this.reloadView();
            }.bind(this));
        },

        saveTopPosition: function (productData) {
            var data = {
                'page_id': this.getPage(this.view),
                'top_product_data': productData,
                'store_id': $('select.store').val(),
                'limit': $('[name="limit"]').val(),
                'sort_order': $('select.sort_order').val()
            };

            $.ajax({
                type: 'POST',
                url: this.options.savePositionsUrl,
                data: data,
                context: $('body'),
                showLoader: true
            }).success(function () {
                this.reloadView();
            }.bind(this));
        },

        updateProductsByConditions: function () {
            $('#page_tabs_products_section').on('click', function () {
                if ($('#page_tabs_conditions_section').hasClass('_changed')) {
                    $('#page_tabs_conditions_section').removeClass('_changed');
                    $.ajax({
                        type: 'POST',
                        url: this.options.savePositionsUrl,
                        data: $(this.options.formSelector).find('[name^=rule]').serialize(),
                        context: $('body'),
                        showLoader: true
                    }).success(function () {
                        this.reloadView();
                    }.bind(this));
                }
            }.bind(this));
        }
    });

    return $.mage.amlandingProducts;
});
