define([
    'jquery',
    'priceUtils'
], function ($,priceUtils) {
    'use strict';
    var localCache = {
        /**
         * timeout for cache in millis
         * @type {number}
         */
        timeout: 30000,
        /** 
         * @type {{_: number, data: {}}}
         **/
        data: {},
        remove: function (url) {
            delete localCache.data[url];
        },
        exist: function (url) {
            return !!localCache.data[url] && ((new Date().getTime() - localCache.data[url]._) < localCache.timeout);
        },
        get: function (url) {
            return localCache.data[url].data;
        },
        set: function (url, cachedData, callback) {
            localCache.remove(url);
            localCache.data[url] = {
                _: new Date().getTime(),
                data: cachedData
            };
            if ($.isFunction(callback)) {
                callback(cachedData);
            }
            ;
        }
    };

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {



            _getSelectedChildId: function () {
                var inScopeProductIds = this._CalcProducts();
                if ((typeof inScopeProductIds != 'undefined') && (inScopeProductIds.length == 1)) {
                    return inScopeProductIds[0];
                }
                return false;
            },
            /**
             * Get Scope In Products Ids
             *
             * @private
             */
            getInScopeProductIds: function () {
                return this._CalcProducts();
            },

            /**
             * Get Product Id Of Cheapest Product In Scope
             *
             * @private
             */
            getProductIdOfCheapestProductInScope: function (priceType, optionalAllowedProducts) {
                var childProducts = this.options.jsonConfig.childProducts;
                var productIds = this.getInScopeProductIds();

                var minPrice = Infinity;
                var lowestPricedProdId = false;

                for (var x = 0, len = productIds.length; x < len; ++x) {
                    var thisPrice = Number(childProducts[productIds[x]][priceType]);
                    if (thisPrice < minPrice) {
                        minPrice = thisPrice;
                        lowestPricedProdId = productIds[x];
                    }
                }

                return lowestPricedProdId;
            },
            /**
             * Get Product Id Of Most Expensive Product In Scope
             *
             * @private
             */
            getProductIdOfMostExpensiveProductInScope: function (priceType, optionalAllowedProducts) {

                var childProducts = this.options.jsonConfig.childProducts;
                var productIds = this.getInScopeProductIds();

                var maxPrice = 0;
                var highestPricedProdId = false;

                for (var x = 0, len = productIds.length; x < len; ++x) {
                    var thisPrice = Number(childProducts[productIds[x]][priceType]);
                    if (thisPrice >= maxPrice) {
                        maxPrice = thisPrice;
                        highestPricedProdId = productIds[x];
                    }
                }
                return highestPricedProdId;
            },
            /**
             * Rebuild container
             *
             * @private
             */

            _Rebuild: function () {
                var childProductId = this._getSelectedChildId();
                if (childProductId) {
                    if (this.options.jsonConfig.updateurl) {
                        this._changeUrl(childProductId);
                    }
                    this._showPriceBlock(childProductId, false);
                    if (this.options.jsonConfig.sku) {
                        this._updateProductSku(childProductId);
                    }
                    if (this.options.jsonConfig.name) {
                        this._updateProductName(childProductId);
                    }
                    if (this.options.jsonConfig.updateShortDescription) {
                        this._updateProductShortDescription(childProductId);
                    }
                    if (this.options.jsonConfig.updateDescription) {
                        this._updateProductDescription(childProductId);
                    }
                    if (this.options.jsonConfig.additional) {
                        this._updateAdditionalInformation(childProductId);
                    }
                    this._showTierPricingBlock(childProductId);
                    if (this.options.jsonConfig.image) {
                        this._changeProductImage(childProductId);
                    }
                } else {
                    var cheapestPid = this.getProductIdOfCheapestProductInScope("finalPrice");
                    this._showPriceBlock(cheapestPid, true);
                    if (this.options.jsonConfig.sku) {
                        this._updateProductSku(false);
                    }
                    if (this.options.jsonConfig.name) {
                        this._updateProductName(false);
                    }
                    if (this.options.jsonConfig.updateShortDescription) {
                        this._updateProductShortDescription(false);
                    }
                    if (this.options.jsonConfig.updateDescription) {
                        this._updateProductDescription(false);
                    }
                    this._showTierPricingBlock(this.options.jsonConfig.productId);
                    if (this.options.jsonConfig.image) {
                        // this._changeProductImage(this.options.jsonConfig.productId);
                    }
                }
                return this._super();
            },

            _changeUrl: function (productId) {
                var childProducts = this.options.jsonConfig.childProducts;
                var productUrl = childProducts[productId].productUrl;
                history.pushState({}, '', productUrl);

            },

            _getSelectedAttributes: function () {
                if (this.options.jsonConfig.page != 'catalog_category_view') {
                    var coUrl = this.options.jsonConfig.ajaxBaseUrl + "options/";
                    var url = window.location.pathname;
                    var data = this._defaultValues(url, coUrl);
                    var obj = $.parseJSON(JSON.stringify(data));
                    this.options.jsonConfig.defaultValues = obj;
                }
                if (typeof this.options.jsonConfig.defaultValues !== 'undefined') {
                    return this.options.jsonConfig.defaultValues;
                }
            },
            _init: function () {
                if (_.isEmpty(this.options.jsonConfig.images)) {
                    this.options.useAjax = true;
                    // creates debounced variant of _LoadProductMedia()
                    // to use it in events handlers instead of _LoadProductMedia()
                    this._debouncedLoadProductMedia = _.debounce(this._LoadProductMedia.bind(this), 500);
                }
    
                if (this.options.jsonConfig !== '' && this.options.jsonSwatchConfig !== '') {
                    // store unsorted attributes
                    this.options.jsonConfig.mappedAttributes = _.clone(this.options.jsonConfig.attributes);
                    this._sortAttributes();
                    this._RenderControls();
    
                    //this is additional code for select first attribute value
                    if (this.options.jsonConfig.attributes.length > 0) {
                        var selectswatch = this.element.find('.' + this.options.classes.attributeClass + ' .' + this.options.classes.attributeOptionsWrapper);
                        $.each(selectswatch, function (index, item) {
                            var swatchOption = $(item).find('div.swatch-option').first();
                            var swatchOptions = $(item).find('div.swatch-option');
                            if (swatchOption.length && !$(item).find('div.swatch-option').hasClass('selected') && swatchOptions.length == 1) {
                                swatchOption.trigger('click');
                            } else {
                                if($(item).find('select').length > 0) {
                                    $(item).find('select').val($("option:eq(1)").val()).trigger('change');
                                }
                            }
                        });
                    }
    
                    this._setPreSelectedGallery();
                    $(this.element).trigger('swatch.initialized');
                } else {
                    console.log('SwatchRenderer: No input data received');
                }
                this.options.tierPriceTemplate = $(this.options.tierPriceTemplateSelector).html();
            },
    
            /**
             * @private
             */
            _sortAttributes: function () {
                this.options.jsonConfig.attributes = _.sortBy(this.options.jsonConfig.attributes, function (attribute) {
                    return parseInt(attribute.position, 10);
                });
            },
        
        /**
         * Render select by part of config
         *
         * @param {Object} config
         * @param {String} chooseText
         * @returns {String}
         * @private
         */
        _RenderSwatchSelect: function (config, chooseText) {
            var html;
            var jsonConfig = this.options.jsonConfig;
            if (this.options.jsonSwatchConfig.hasOwnProperty(config.id)) {
                return '';
            }
            html =
                '<select class="' + this.options.classes.selectClass + ' ' + config.code + '">' +
                '<option value="0" option-id="0">' + chooseText + '</option>';

            $.each(config.options, function () {
                var label = this.label,
                    attr = ' value="' + this.id + '" option-id="' + this.id + '"';

                if (!this.hasOwnProperty('products') || this.products.length <= 0) {
                    attr += ' option-empty="true"';
                }
                    html += '<option ' + attr + '>'+ label + '</option>';
                
            });
            html += '</select>';

            return html;
        },    
        /**
       * Emulate mouse click on all swatches that should be selected
       *
       * @private
       */
            _EmulateSelected: function (selectedAttributes) {
                $.each(selectedAttributes, $.proxy(function (attributeCode, optionId) {
                    var elem = this.element.find('.' + this.options.classes.attributeClass +
                        '[attribute-code="' + attributeCode + '"] [option-id="' + optionId + '"]'),
                        parentInput = elem.parent();

                    if (elem.hasClass('selected')) {
                        return;
                    }

                    if (parentInput.hasClass(this.options.classes.selectClass)) {
                        parentInput.val(optionId);
                        parentInput.trigger('change');
                    } else {
                        elem.trigger('click');
                    }
                }, this));
            },
            _resetToMainProductUrl: function () {
                var productUrl = this.options.spConfig.productUrl;
                history.pushState({}, '', productUrl);
            },

            _preselectUniqueOption: function(){
                //this is additional code for select first attribute value
                if (this.options.jsonConfig.attributes.length > 0) {
                    var selectswatch = this.element.find('.' + this.options.classes.attributeClass + ' .' + this.options.classes.attributeOptionsWrapper);
                    $.each(selectswatch, function (index, item) {
                        var swatchOption = $(item).find('div.swatch-option').first();
                        var swatchOptions = $(item).find('div.swatch-option');
                        if (swatchOption.length && !$(item).find('div.swatch-option').hasClass('selected') && swatchOptions.length == 1) {
                            swatchOption.trigger('click');
                        } else {
                            if($(item).find('select').length > 0) {
                               $(item).find('select').val($("option:eq(1)").val()).trigger('change');
                            }
                        }
                    });
                }
            },

            _updateProductSku: function (childProductId) {
                var sku = this.options.jsonConfig.sku;
                var product_sku_markup = this.options.jsonConfig.product_sku_markup;
                if (childProductId && this.options.jsonConfig.skus[childProductId].sku) {
                    sku = this.options.jsonConfig.skus[childProductId].sku;
                }
                $(product_sku_markup).html(sku);
            },
            /**
             * Update configurable product name with the name of the selected simple product. 
             * @param Integer childProductId
             * @returns {undefined}
             */

            _updateProductName: function (childProductId) {
                var name = this.options.jsonConfig.name;
                var product_name_markup = this.options.jsonConfig.product_name_markup;
                if (childProductId && this.options.jsonConfig.names[childProductId].name) {
                    name = this.options.jsonConfig.names[childProductId].name;
                }
                $(product_name_markup).html(name);
            },
            _updateProductShortDescription: function (childProductId) {
                var short_description = this.options.jsonConfig.short_description;
                var product_short_description_markup = this.options.jsonConfig.product_short_description_markup;
                if (childProductId && this.options.jsonConfig.short_descriptions[childProductId].short_description) {
                    short_description = this.options.jsonConfig.short_descriptions[childProductId].short_description;
                }
                $(product_short_description_markup).html(short_description);
            },
            _updateProductDescription: function (childProductId) {
                var description = this.options.jsonConfig.description;
                var product_description_markup = this.options.jsonConfig.product_description_markup;
                if (childProductId && this.options.jsonConfig.descriptions[childProductId].description) {
                    description = this.options.jsonConfig.descriptions[childProductId].description;
                }
                $(product_description_markup).html(description);
            },

            /**
             * update product attributes. 
             * @param {type} childProductId
             * @returns {undefined}
             */
            _updateAdditionalInformation: function (childProductId) {
                var product_additional_markup = this.options.jsonConfig.product_additional_markup;
                var coUrl = this.options.jsonConfig + "additional/?id=" + this.options.jsonConfig.productId;
                if (childProductId) {
                    coUrl = this.options.jsonConfig.ajaxBaseUrl + "additional/?id=" + childProductId;
                }
                function updateAdditionalInformation(data) {
                    $(product_additional_markup).html(data.responseText);
                }
                $.ajax({
                    url: coUrl,
                    cache: true,
                    beforeSend: function () {
                        if (coUrl in localCache.data) {
                            updateAdditionalInformation(localCache.data[coUrl].data);
                            return false;
                        }
                        return true;
                    },
                    complete: function (jqXHR, textStatus) {
                        localCache.set(coUrl, jqXHR, updateAdditionalInformation);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                    }
                });
            },

            /**
             * display price block from the selected simple product
             * @param {type} productId
             * @param {type} showPriceFromLabel
             * @returns {undefined}
             */

            _showPriceBlock: function (productId, showPriceFromLabel) {
                var coUrl = this.options.jsonConfig.ajaxBaseUrl + "price/?id=" + productId;
                var fromPrice = this.options.jsonConfig.priceFromLabel;
                var priceBlock = '';
                var cProductId = this.options.jsonConfig.productId;


                function showPriceBlock(data) {
                    var parser = new DOMParser();
                    var priceDoc = parser.parseFromString(data.responseText, "text/html");
                    var x = priceDoc.getElementsByClassName('price-box');
                    var productPrice = x[0].innerHTML;
                    var toReplace = 'data-product-id="' + productId + '"';
                    var replacement = 'data-product-id="' + cProductId + '"';
                    var re = new RegExp(toReplace, "g");
                    productPrice = productPrice.replace(re, replacement);
                    var toReplace2 = "price-" + productId;
                    var replacement2 = "price-" + cProductId;
                    var re2 = new RegExp(toReplace2, "g");
                    productPrice = productPrice.replace(re2, replacement2);
                    if (showPriceFromLabel) {
                        priceBlock = '<span class="configurable-price-from-label">' + fromPrice + '</span>' + productPrice;
                    } else {
                        priceBlock = productPrice;
                    }
                    $(".product-box").html(priceBlock);

                }

                $.ajax({
                    url: coUrl,
                    cache: true,
                    beforeSend: function () {
                        if (coUrl in localCache.data) {
                            showPriceBlock(localCache.data[coUrl].data);
                            return false;
                        }
                        return true;
                    },
                    complete: function (jqXHR, textStatus) {
                        localCache.set(coUrl, jqXHR, showPriceBlock);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                    }
                });
            },

            /**
             * Update product image with images from the selected simple
             * product
             * @param Integer productId
             * @param Integer parentId
             * @returns {undefined}
             */
            _changeProductImage: function (productId) {
                var product_image_markup = this.options.jsonConfig.product_image_markup;
                var coUrl = this.options.jsonConfig.ajaxBaseUrl + "image/?id=" + productId;
                function imageSwap(data) {
                    $(product_image_markup).html(data.responseText);
                    $(product_image_markup).trigger('contentUpdated');
                }
                $.ajax({
                    url: coUrl,
                    cache: true,
                    beforeSend: function () {
                        if (coUrl in localCache.data) {
                            imageSwap(localCache.get(coUrl));
                            return false;
                        }
                        return true;
                    },
                    complete: function (jqXHR, textStatus) {
                        localCache.set(coUrl, jqXHR, imageSwap);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        //
                    }
                });
            },
            _showTierPricingBlock: function (productId) {
                var coUrl = this.options.jsonConfig.ajaxBaseUrl + "co/" + productId;;
                function showTierPricingBlock(data) {
                    $("div[data-role='tier-price-block']").html(data);
                }
                $.ajax({
                    url: coUrl,
                    cache: true,
                    beforeSend: function () {
                        if (coUrl in localCache.data) {
                            showTierPricingBlock(localCache.get(coUrl));
                            return false;
                        }
                        return true;
                    },
                    complete: function (jqXHR, textStatus) {
                        localCache.set(coUrl, jqXHR, showTierPricingBlock);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        //
                    }
                });
            },
            _defaultValues: function (fullUrl, coUrl) {
                var result = null;
                $.ajax({
                    url: coUrl,
                    type: "GET",
                    dataType: "json",
                    data: "url=" + fullUrl,
                    async: false,
                    success: function (data) {
                        result = data;
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        //
                    }
                });
                return result;
            }

        });
        return $.mage.SwatchRenderer;
    }
});