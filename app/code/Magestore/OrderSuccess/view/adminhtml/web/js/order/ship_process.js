/*
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiRegistry',
    'prototype'
], function(registry){

    window.Packaging = Class.create();
    Packaging.prototype = {
        /**
         * Initialize object
         */
        initialize: function(params) {
            this.packageIncrement = 0;
            this.orderId = '';
            this.packages = [];
            this.itemsAll = [];
            this.createLabelUrl = params.createLabelUrl ? params.createLabelUrl : null;
            this.itemsGridUrl = params.itemsGridUrl ? params.itemsGridUrl : null;
            this.errorQtyOverLimit = params.errorQtyOverLimit;
            this.titleDisabledSaveBtn = params.titleDisabledSaveBtn;
            this.window = $('packaging_window');
            this.messages = this.window.select('.message-warning')[0];
            this.messagesSuccess = this.window.select('.message-success')[0];
            this.packagesContent = $('packages_content');
            this.template = $('package_template');
            this.paramsCreateLabelRequest = {};
            this.validationErrorMsg = params.validationErrorMsg;

            this.defaultItemsQty            = params.shipmentItemsQty ? params.shipmentItemsQty : null;
            this.defaultItemsProductId      = params.shipmentItemsProductId ? params.shipmentItemsProductId : null;
            this.defaultItemsOrderItemId    = params.shipmentItemsOrderItemId ? params.shipmentItemsOrderItemId : null;
            this.parentGrid                 = params.parentGrid ? params.parentGrid : null;

            this.shippingInformation= params.shippingInformation ? params.shippingInformation : null;
            this.thisPage           = params.thisPage ? params.thisPage : null;
            this.customizableContainers = params.customizable ? params.customizable : [];

            this.eps = .000001;
        },

        /**
         * Get Package Id
         */
        getPackageId: function(packageBlock) {
            return packageBlock.id.match(/\d{0,}$/)[0];
        },

//******************** Setters **********************************//
        setLabelCreatedCallback: function(callback) {
            this.labelCreatedCallback = callback;
        },
        setCancelCallback: function(callback) {
            this.cancelCallback = callback;
        },
        setConfirmPackagingCallback: function(callback) {
            this.confirmPackagingCallback = callback;
        },
        setItemQtyCallback: function(callback) {
            this.itemQtyCallback = callback;
        },
        setCreateLabelUrl: function(url) {
            this.createLabelUrl = url;
        },
        setParamsCreateLabelRequest: function(params) {
            Object.extend(this.paramsCreateLabelRequest, params);
        },
//******************** End Setters *******************************//

        showWindow: function(orderId, callBackUrl) {
            this.orderId = orderId;
            if(callBackUrl) {
                this.labelCreatedCallback = callBackUrl;
            }
            if (this.packagesContent.childElements().length == 0 || callBackUrl) {
                this.cleanPackages();
                this.newPackage();
            }
            jQuery(this.window).modal('openModal')
        },

        closeWindow: function() {
            jQuery(this.window).modal('closeModal');
        },

        cancelPackaging: function() {
            if (Object.isFunction(this.cancelCallback)) {
                this.cancelCallback();
            }
        },

        confirmPackaging: function(params) {
            if (Object.isFunction(this.confirmPackagingCallback)) {
                this.confirmPackagingCallback();
            }
        },

        checkAllItems: function(headCheckbox) {
            $(headCheckbox).up('table').select('tbody input[type="checkbox"]').each(function(checkbox){
                if(checkbox.hasClassName('child-check-box')) {
                    return;
                }
                checkbox.checked = headCheckbox.checked;
                this._observeQty.call(checkbox);
            }.bind(this));
        },

        cleanPackages: function() {
            this.cleanAllItems();
            this.messages.hide().update();
            this.messagesSuccess.hide().update();
            if(jQuery('.fulfil_message') != undefined){
                jQuery('.fulfil_message').remove();
            }
        },

        cleanAllItems: function() {
            this.packagesContent.update();
            this.packages = [];
            this.itemsAll = [];
            this.packageIncrement = 0;
            this._setAllItemsPackedState();
        },

        sendCreateLabelRequest: function() {
            var package = this;
            if (!this.validate()) {
                this.messages.show().update(this.validationErrorMsg);
                return;
            } else {
                this.messages.hide().update();
            }
            if (this.createLabelUrl) {
                var weight, length, width, height = null;
                var packagesParams = [];
                this.packagesContent.childElements().each(function(pack) {
                    var packageId = this.getPackageId(pack);
                    packagesParams[packageId] = {
                        container: pack.select('select[name="package_container"]')[0].value
                    };
                }.bind(this));
                this.paramsCreateLabelRequest['order_id'] = this.orderId;
                for (var packageId in this.packages) {
                    if (!isNaN(packageId)) {
                        this.paramsCreateLabelRequest['packages['+packageId+']'+'[params]'+'[container]'] = packagesParams[packageId]['container'];
                        for (var packedItemId in this.packages[packageId]['items']) {
                            if (!isNaN(packedItemId)) {
                                this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][qty]']           = this.packages[packageId]['items'][packedItemId]['qty'];
                                if(this.packages[packageId]['items'][packedItemId]['resource'] != undefined) {
                                    this.paramsCreateLabelRequest['packages[' + packageId + ']' + '[items]' + '[' + packedItemId + '][resource]'] = this.packages[packageId]['items'][packedItemId]['resource'];
                                }
                                // this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][product_id]']    = package.defaultItemsProductId[packedItemId];
                                this.paramsCreateLabelRequest['packages['+packageId+']'+'[items]'+'['+packedItemId+'][order_item_id]'] = this.packages[packageId]['items'][packedItemId]['order_item_id'];
                            }
                        }
                    }
                }

                new Ajax.Request(this.createLabelUrl, {
                    parameters: this.paramsCreateLabelRequest,
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        if (response.isJSON()) {
                            response = response.evalJSON();
                            if (response.error) {
                                this.messages.show().innerHTML = response.message;
                            } else if (response.ok) {
                                if(Object.isFunction(this.labelCreatedCallback)){
                                    this.labelCreatedCallback(response);
                                }else{
                                    // this.messagesSuccess.show().innerHTML = response.message;
                                    this.reloadParent(response.message);
                                    this.cleanAllItems();
                                    this.newPackage();
                                    this.closeWindow();
                                } 

                            }
                        }
                    }.bind(this)
                });
                if (this.paramsCreateLabelRequest['code']
                    && this.paramsCreateLabelRequest['carrier_title']
                    && this.paramsCreateLabelRequest['method_title']
                    && this.paramsCreateLabelRequest['price']
                ) {
                    var a = this.paramsCreateLabelRequest['code'];
                    var b = this.paramsCreateLabelRequest['carrier_title'];
                    var c = this.paramsCreateLabelRequest['method_title'];
                    var d = this.paramsCreateLabelRequest['price'];

                    this.paramsCreateLabelRequest = {};
                    this.paramsCreateLabelRequest['code']           = a;
                    this.paramsCreateLabelRequest['carrier_title']  = b;
                    this.paramsCreateLabelRequest['method_title']   = c;
                    this.paramsCreateLabelRequest['price']          = d;
                } else {
                    this.paramsCreateLabelRequest = {};
                }
            }
        },

        validate: function() {
            var dimensionElements = $("packaging_window").select(
                'input[name=container_length],input[name=container_width],input[name=container_height],input[name=container_girth]:not("._disabled")'
            );
            var callback = null;
            if ( dimensionElements.any(function(element) { return !!element.value; })) {
                callback = function(element) { $(element).addClassName('required-entry'); };
            } else {
                callback = function(element) { $(element).removeClassName('required-entry'); };
            }
            dimensionElements.each(callback);

            return result = $$('[id^="package_block_"] input').collect(function (element) {
                return this.validateElement(element)
            }, this).all();
        },

        validateElement: function(elm) {
            var cn = $w(elm.className);
            return result = cn.all(function(value) {
                var v = Validation.get(value);
                if (Validation.isVisible(elm) && !v.test($F(elm), elm)) {
                    $(elm).addClassName('validation-failed');
                    return false;
                } else {
                    $(elm).removeClassName('validation-failed');
                    return true;
                }
            });
        },

        validateCustomsValue: function() {
            var items = [];
            var isValid = true;
            var itemsPrepare = [];
            var itemsPacked = [];

            this.packagesContent.childElements().each(function(pack) {
                itemsPrepare = pack.select('[data-role="package-items"]')[0];
                if (itemsPrepare) {
                    items = items.concat(itemsPrepare.select('.grid tbody tr'));
                }
                itemsPacked = pack.select('.package_items')[0];
                if (itemsPacked) {
                    items = items.concat(itemsPacked.select('.grid tbody tr'));
                }
            }.bind(this));

            if (isValid) {
                this.messages.hide().update();
            } else {
                this.messages.show().update(this.validationErrorMsg);
            }
            return isValid;
        },

        newPackage: function() {
            var pack = this.template.cloneNode(true);
            pack.id = 'package_block_' + (++this.packageIncrement);
            pack.addClassName('package-block');
            pack.select('[data-role=package-number]')[0].update(this.packageIncrement);
            this.packagesContent.insert({top: pack});
            pack.select('[data-action=package-save-items]')[0].hide();
            pack.show();
        },

        deletePackage: function(obj) {
            var pack = $(obj).up('[id^="package_block"]');

            var packItems = pack.select('.package_items')[0];
            var packageId = this.getPackageId(pack);

            delete this.packages[packageId];
            pack.remove();
            this.messages.hide().update();
            this._setAllItemsPackedState();
        },

        deleteItem: function(obj) {
            var item = $(obj).up('tr');
            var itemId = item.select('[type="checkbox"]')[0].value;
            var pack = $(obj).up('[id^="package_block"]');
            var packItems = pack.select('.package_items')[0];
            var packageId = this.getPackageId(pack);
            var packageBlock = $(obj).up('[id^="package_block"]');
            var containerBlock = packageBlock.select('select[name="package_container"]')[0];

            delete this.packages[packageId]['items'][itemId];
            if (item.offsetParent.rows.length <= 2) { /* head + this last row */
                $(packItems).hide();
            }
            item.remove();
            this.messages.hide().update();
            this.messagesSuccess.hide().update();

            if(!this.checkPackedItemsInPackage(packageId)) {
                Form.Element.enable(containerBlock);
            }
            this._setAllItemsPackedState();
        },

        getItemsForPack: function(obj) {
            if (this.itemsGridUrl) {
                var packageBlock = $(obj).up('[id^="package_block"]');
                var packedItems = Object.keys(this.getPackedItemsQty()).toArray();
                var containerValue = packageBlock.select('select[name="package_container"]')[0].value;
                var parameters = $H({
                        'shipment_id': this.shipmentId,
                        'order_id': this.orderId,
                        'packed_items': packedItems.join(),
                        'container': containerValue
                });

                var packagePrapare = packageBlock.select('[data-role=package-items]')[0];
                var packagePrapareGrid = packagePrapare.select('.grid_prepare')[0];
                new Ajax.Request(this.itemsGridUrl, {
                    parameters: parameters,
                    onSuccess: function(transport) {
                        var response = transport.responseText;
                        if (response) {
                            packagePrapareGrid.update(response);
                            this._processPackagePrapare(packagePrapareGrid);
                            if (packagePrapareGrid.select('.grid tbody tr').length) {
                                packageBlock.select('[data-action=package-add-items]')[0].hide();
                                packageBlock.select('[data-action=package-save-items]')[0].show();
                                packagePrapare.show();
                            } else {
                                packagePrapareGrid.update();
                            }
                            Form.Element.disable(packageBlock.select('select[name="package_container"]')[0]);
                        }
                    }.bind(this)
                });
            }
        },

        getPackedItemsQty: function() {
            var items = [];
            for (var packageId in this.packages) {
                if (!isNaN(packageId)) {
                    for (var packedItemId in this.packages[packageId]['items']) {
                        if (!isNaN(packedItemId)) {
                            if (items[packedItemId]) {
                                items[packedItemId] += this.packages[packageId]['items'][packedItemId]['qty'];
                            } else {
                                items[packedItemId] = this.packages[packageId]['items'][packedItemId]['qty']
                            }
                        }
                    }
                }
            }
            return items;
        },

        checkPackedItemsInPackage: function(packageId) {
            var items = [];
            if (!isNaN(packageId) && this.packages[packageId] != undefined) {
                for (var packedItemId in this.packages[packageId]['items']) {
                    if (!isNaN(packedItemId)) {
                        return true;
                    }
                }
            }
            return false;
        },

        _parseQty: function(obj) {
            var qty = $(obj).hasClassName('qty-decimal') ? parseFloat(obj.value) : parseInt(obj.value);
            if (isNaN(qty) || qty <= 0) {
                qty = 1;
            }
            return qty;
        },

        packItems: function(obj) {
            var anySelected = false;
            var packageBlock = $(obj).up('[id^="package_block"]');
            var packageId = this.getPackageId(packageBlock);
            var packagePrepare = packageBlock.select('[data-role=package-items]')[0];
            var packagePrepareGrid = packagePrepare.select('.grid_prepare')[0];
            var containerBlock = packageBlock.select('select[name="package_container"]')[0];

            // check for exceeds the total shipped quantity
            var checkExceedsQty = false;
            this.messages.hide().update();
            packagePrepareGrid.select('.grid tbody tr').each(function(item) {
                var checkbox = item.select('[type="checkbox"]')[0];
                var itemId = checkbox.value;
                var qtyValue  = this._parseQty(item.select('[name="qty"]')[0]);
                item.select('[name="qty"]')[0].value = qtyValue;
                if (checkbox.checked && this._checkExceedsQty(itemId, qtyValue)) {
                    this.messages.show().update(this.errorQtyOverLimit);
                    checkExceedsQty = true;
                }
            }.bind(this));
            if (checkExceedsQty) {
                return;
            }

            if (!this.validateCustomsValue()) {
                return;
            }
            
            /* process custom validation */
            if(!this.processCustomValidation()) {
                return;
            }

            // prepare items for packing
            packagePrepareGrid.select('.grid tbody tr').each(function(item) {
                var checkbox = item.select('[type="checkbox"]')[0];
                if (checkbox.checked) {
                    var qty  = item.select('[name="qty"]')[0];
                    var resourceElement  = item.select('[name="resource"]')[0];
                    if(resourceElement != undefined) {
                        var resource = resourceElement.value;
                        item.select('[name="resource"]')[0].value = resource;
                    }
                    var qtyValue  = this._parseQty(qty);
                    item.select('[name="qty"]')[0].value = qtyValue;
                    anySelected = true;
                    qty.disabled = 'disabled';
                    if(resourceElement != undefined) {
                        resourceElement.disabled = 'disabled';
                    }
                    checkbox.disabled = 'disabled';
                    packagePrepareGrid.select('.grid th [type="checkbox"]')[0].up('th label').hide();
                    item.select('[data-action=package-delete-item]')[0].show();
                } else {
                    item.remove();
                }
            }.bind(this));

            // packing items
            if (anySelected) {
                var packItems = packageBlock.select('.package_items')[0];
                if (!packItems) {
                    packagePrepare.insert(new Element('div').addClassName('grid_prepare'));
                    packagePrepare.insert({after: packagePrepareGrid});
                    packItems = packagePrepareGrid.removeClassName('grid_prepare').addClassName('package_items');
                    packItems.select('.grid tbody tr').each(function(item) {
                        var itemId = item.select('[type="checkbox"]')[0].value;
                        var qtyValue  = parseFloat(item.select('[name="qty"]')[0].value);
                        var resourceElement  = item.select('[name="resource"]')[0];
                        if(resourceElement != undefined) {
                            var resource = resourceElement.value;
                        }
                        qtyValue = (qtyValue <= 0) ? 1 : qtyValue;

                        if ('undefined' == typeof this.packages[packageId]) {
                            this.packages[packageId] = {'items': [], 'params': {}};
                        }
                        if ('undefined' == typeof this.packages[packageId]['items'][itemId]) {
                            this.packages[packageId]['items'][itemId] = {};
                            this.packages[packageId]['items'][itemId]['qty'] = qtyValue;
                            this.packages[packageId]['items'][itemId]['order_item_id'] = itemId;
                            if(resourceElement != undefined) {
                                this.packages[packageId]['items'][itemId]['resource'] = resource;
                            }
                        } else {
                            this.packages[packageId]['items'][itemId]['qty'] += qtyValue;
                            if(resourceElement != undefined) {
                                this.packages[packageId]['items'][itemId]['resource'] = resource;
                            }
                        }
                    }.bind(this));
                } else {
                    packagePrepareGrid.select('.grid tbody tr').each(function(item) {
                        var itemId = item.select('[type="checkbox"]')[0].value;
                        var qtyValue  = parseFloat(item.select('[name="qty"]')[0].value);
                        var resourceElement  = item.select('[name="resource"]')[0];
                        if(resourceElement != undefined) {
                            var resource = item.select('[name="resource"]')[0].value;
                        }
                        qtyValue = (qtyValue <= 0) ? 1 : qtyValue;

                        if ('undefined' == typeof this.packages[packageId]['items'][itemId]) {
                            this.packages[packageId]['items'][itemId] = {};
                            this.packages[packageId]['items'][itemId]['qty'] = qtyValue;
                            this.packages[packageId]['items'][itemId]['order_item_id'] = itemId;
                            if(resourceElement != undefined) {
                                this.packages[packageId]['items'][itemId]['resource'] = resource;
                            }
                            packItems.select('.grid tbody')[0].insert(item);
                        } else {
                            this.packages[packageId]['items'][itemId]['qty'] += qtyValue;
                            this.packages[packageId]['items'][itemId]['order_item_id'] = itemId;
                            // this.packages[packageId]['items'][itemId]['resource'] = resource;
                            var packItem = packItems.select('[type="checkbox"][value="'+itemId+'"]')[0].up('tr').select('[name="qty"]')[0];
                            packItem.value = this.packages[packageId]['items'][itemId]['qty'];
                        }
                    }.bind(this));
                    packagePrepareGrid.update();
                }
                $(packItems).show();
            } else {
                if(!this.checkPackedItemsInPackage(packageId)) {
                    Form.Element.enable(containerBlock);
                }
                packagePrepareGrid.update();
            }

            // show/hide disable/enable
            packagePrepare.hide();
            packageBlock.select('[data-action=package-save-items]')[0].hide();
            packageBlock.select('[data-action=package-add-items]')[0].show();
            this._setAllItemsPackedState()
        },

        validateItemQty: function (itemId, qty) {
            return (this.defaultItemsQty[itemId] < qty) ? this.defaultItemsQty[itemId] : qty;
        },

        changeMeasures: function(obj) {
            var incr = 0;
            var incrSelected = 0;
            obj.childElements().each(function(option) {
                if (option.selected) {
                    incrSelected = incr;
                }
                incr++;
            }.bind(this));

            var packageBlock = $(obj).up('[id^="package_block"]');
            packageBlock.select('.measures').each(function(item){
                if (item.name != obj.name) {
                    var incr = 0;
                    item.select('option').each(function(option){
                        if (incr == incrSelected) {
                            item.value = option.value;
                            //option.selected = true
                        }
                        incr++;
                    }.bind(this));
                }
            }.bind(this));

        },

        changeContainerType: function(obj) {
            if (this.customizableContainers.length <= 0) {
                return;
            }

            var disable = true;
            for (var i in this.customizableContainers) {
                if (this.customizableContainers[i] == obj.value) {
                    disable = false;
                    break;
                }
            }

            var currentNode = obj;
            while (currentNode.nodeName != 'TBODY') {
                currentNode = currentNode.parentNode;
            }
            if (!currentNode) {
                return;
            }

            $(currentNode).select(
                'input[name=container_length],input[name=container_width],input[name=container_height],select[name=container_dimension_units]'
            ).each(function(inputElement) {
                if (disable) {
                    Form.Element.disable(inputElement);
                    inputElement.addClassName('_disabled');
                    if (inputElement.nodeName == 'INPUT') {
                        $(inputElement).value = ''
                    }
                } else {
                    Form.Element.enable(inputElement);
                    inputElement.removeClassName('_disabled');
                }
            })
        },

        changeContentTypes: function(obj) {
            var packageBlock = $(obj).up('[id^="package_block"]');
            var contentType = packageBlock.select('[name=content_type]')[0];
            var contentTypeOther = packageBlock.select('[name=content_type_other]')[0];
            if (contentType.value == 'OTHER') {
                Form.Element.enable(contentTypeOther);
                contentTypeOther.removeClassName('_disabled');
            } else {
                Form.Element.disable(contentTypeOther);
                contentTypeOther.addClassName('_disabled');
            }

        },
        
        addCustomValidation: function(validation){
            this.customValidation = [validation];
            /*
            if(typeof this.customValidation == 'undefined' || !this.customValidation) {
                this.customValidation = [validation];
            } else {
                this.cutomValidation.push(validation)
            }
            */
        },
        
        removeCustomValidation: function() {
            this.customValidation = [];
        },
        
        processCustomValidation: function() {
            if(!this.customValidation) {
                return true;
            }
            for(var i in this.customValidation) {
                if(typeof this.customValidation[i].validate != 'function') {
                    continue;
                }
                
                if(!this.customValidation[i].validate()) {
                    this.messages.show().update(this.customValidation[i].errorMessage);
                    return false;
                }
            }
            return true;
        },
        reloadParent: function (message) {
            if(this.parentGrid) {
                var params = [];
                var target = registry.get(this.parentGrid);
                if (target && typeof target === 'object') {
                    target.set('params.t ', Date.now());
                }
                if(message != undefined){
                    jQuery('.page-main-actions').after(
                        "<div id='messages' class='fulfil_message'><div class='messages'><div class='message message-success success'>" +
                        "<div data-ui-id='messages-message-success'>"+message+"</div></div></div></div></div>");
                }
            }
        },

//******************** Private functions **********************************//
        _getItemsCount: function(items) {
            var count = 0;
            items.each(function(itemCount) {
                if (!isNaN(itemCount)) {
                    count += parseFloat(itemCount);
                }
            }.bind(this));
            return count;
        },
 
        /**
         * Show/hide disable/enable buttons in case of all items packed state
         */
        _setAllItemsPackedState: function() {
            var addPackageBtn = $$('[data-action=add-packages]')[0];
            var savePackagesBtn = $$('[data-action=save-packages]')[0];
            if (this._getItemsCount(this.itemsAll) > 0
                && (this._checkExceedsQtyFinal(this._getItemsCount(this.getPackedItemsQty()),this._getItemsCount(this.itemsAll)))
            ) {
                this.packagesContent.select('[data-action=package-add-items]').each(function(button){
                    button.disabled = 'disabled';
                    button.addClassName('_disabled');
                });
                addPackageBtn.addClassName('_disabled');
                Form.Element.disable(addPackageBtn);
                savePackagesBtn.removeClassName('_disabled');
                Form.Element.enable(savePackagesBtn);
                savePackagesBtn.title = '';

                // package number recalculation
                var packagesRecalc = [];
                this.packagesContent.childElements().each(function(pack) {
                    if (!pack.select('.package_items .grid tbody tr').length) {
                        pack.remove();
                    }
                }.bind(this));
                var packagesCount = this.packagesContent.childElements().length;
                this.packageIncrement = packagesCount;
                this.packagesContent.childElements().each(function(pack) {
                    var packageId = this.getPackageId(pack);
                    pack.id = 'package_block_' + packagesCount;
                    pack.select('[data-role=package-number]')[0].update(packagesCount);
                    packagesRecalc[packagesCount] = this.packages[packageId];
                    --packagesCount;
                }.bind(this));
                this.packages = packagesRecalc;

            } else if(this.getPackedItemsQty().length > 0){
                this.packagesContent.select('[data-action=package-add-items]').each(function(button){
                    button.removeClassName('_disabled');
                    Form.Element.enable(button);
                });
                addPackageBtn.removeClassName('_disabled');
                Form.Element.enable(addPackageBtn);
                savePackagesBtn.removeClassName('_disabled');
                Form.Element.enable(savePackagesBtn);
                savePackagesBtn.title = '';
            }else if(this.getPackedItemsQty().length == 0){
                this.packagesContent.select('[data-action=package-add-items]').each(function(button){
                    button.removeClassName('_disabled');
                    Form.Element.enable(button);
                });
                addPackageBtn.removeClassName('_disabled');
                Form.Element.enable(addPackageBtn);
                savePackagesBtn.addClassName('_disabled');
                Form.Element.disable(savePackagesBtn);
                savePackagesBtn.title = this.titleDisabledSaveBtn;
            }
        },

        _processPackagePrapare: function(packagePrapare) {
            var itemsAll = [];
            packagePrapare.select('.grid tbody tr').each(function(item) {
                var qty  = item.select('[name="qty"]')[0];
                var itemId = item.select('[type="checkbox"]')[0].value;
                var value = item.select('[name="qty"]')[0].value;
                qtyValue = ((typeof value == 'string') && (value.length == 0)) ? 0 : parseFloat(value);
                if (isNaN(qtyValue) || qtyValue < 0) {
                    qtyValue = 1;
                }
                if (qtyValue == 0) {
                    item.remove();
                    return;
                }
                var packedItems = this.getPackedItemsQty();
                itemsAll[itemId] = qtyValue;
                for (var packedItemId in packedItems) {
                    if (!isNaN(packedItemId)) {
                        var packedQty = packedItems[packedItemId];
                        if (itemId == packedItemId) {
                            if (qtyValue == packedQty || qtyValue <= packedQty) {
                                item.remove();
                            } else if (qtyValue > packedQty) {
                                /* fix float number precision */
                                qty.value = Number(Number(Math.round((qtyValue - packedQty) + "e+4") + "e-4").toFixed(4));
                            }
                        }
                    }
                }
            }.bind(this));
            if (!this.itemsAll.length) {
                this.itemsAll = itemsAll;
            }

            packagePrapare.select('tbody input[type="checkbox"]').each(function(item){
                $(item).observe('change', this._observeQty);
                this._observeQty.call(item);
            }.bind(this))
        },

        _observeQty: function() {
            /** this = input[type="checkbox"] */
            var tr  = jQuery(this).closest('tr')[0];
            var resource = $(tr.cells[tr.cells.length - 1]).select('select[name="resource"]')[0];
            var qty = $(tr.cells[tr.cells.length - 2]).select('input[name="qty"]')[0];
            if(resource == undefined){
                qty = $(tr.cells[tr.cells.length - 1]).select('input[name="qty"]')[0];
            }
            if (qty.disabled = !this.checked) {
                $(qty).addClassName('_disabled');
            } else {
                $(qty).removeClassName('_disabled');
            }
            if(resource != undefined){
                if (resource.disabled = !this.checked) {
                    $(resource).addClassName('_disabled');
                } else {
                    $(resource).removeClassName('_disabled');
                }
            }
        },

        _checkExceedsQty: function(itemId, qty) {
            var packedItemQty = this.getPackedItemsQty()[itemId] ? this.getPackedItemsQty()[itemId] : 0;
            var allItemQty = this.itemsAll[itemId];
            return (qty * (1 - this.eps) > (allItemQty *  (1 + this.eps)  - packedItemQty * (1 - this.eps)));
        },

        _checkExceedsQtyFinal: function(checkOne, defQty) {
            return checkOne * (1 + this.eps) >= defQty * (1 - this.eps);
        },

        _getElementText: function(el) {
            if ('string' == typeof el.textContent) {
                return el.textContent;
            }
            if ('string' == typeof el.innerText) {
                return el.innerText;
            }
            return el.innerHTML.replace(/<[^>]*>/g,'');
        }
//******************** End Private functions ******************************//
    };

});