
/**
 * Copyright   2018 Ayasoftware (http://www.ayasoftware.com).
 * See COPYING.txt for license details.
 * author      EL HASSAN MATAR <support@ayasoftware.com>
 */
define([
    'jquery',
    'underscore',
    'priceUtils',
], function ($, _, priceUtils) {
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
        $.widget('mage.configurable', widget, {
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
            },
            /**
             * Override default options values settings with either URL query parameters or
             * initialized inputs values.
             * @private
             */
            _overrideDefaults: function () {
                var hashIndex = window.location.href.indexOf('#');
                if (hashIndex !== -1) {
                    this._parseQueryParams(window.location.href.substr(hashIndex + 1));
                }
                if (this.options.spConfig.inputsInitialized) {
                    this._setValuesByAttribute();
                }
                var coUrl = this.options.spConfig.ajaxBaseUrl + "options/";
                var url = window.location.pathname;
                var data = this._defaultValues(url, coUrl);
                var obj = $.parseJSON(JSON.stringify(data));
                this.options.spConfig.defaultValues = obj;
            },
            _getMatchingSimpleProduct: function () {
                var inScopeProductIds = this.getInScopeProductIds();
                if ((typeof inScopeProductIds != 'undefined') && (inScopeProductIds.length == 1)) {
                    return inScopeProductIds[0];
                }
                return false;
            },

            /**
          * Change displayed product image according to chosen options of configurable product
          *
          * @private
          */
            _changeProductImage: function () {
                if (this.options.spConfig.image) {
                    return;
                }
                var images,
                    initialImages = this.options.mediaGalleryInitial,
                    galleryObject = $(this.options.mediaGallerySelector).data('gallery');

                if (!galleryObject) {
                    return;
                }

                images = this.options.spConfig.images[this.simpleProduct];

                if (images) {
                    images = this._sortImages(images);

                    if (this.options.gallerySwitchStrategy === 'prepend') {
                        images = images.concat(initialImages);
                    }

                    images = $.extend(true, [], images);
                    images = this._setImageIndex(images);

                    galleryObject.updateData(images);

                    $(this.options.mediaGallerySelector).AddFotoramaVideoEvents({
                        selectedOption: this.simpleProduct,
                        dataMergeStrategy: this.options.gallerySwitchStrategy
                    });
                } else {
                    galleryObject.updateData(initialImages);
                    $(this.options.mediaGallerySelector).AddFotoramaVideoEvents();
                }

            },
            /**
             * Reload the price of the configurable product incorporating the prices of all of the
             * configurable product's option selections.
             */
            _reloadPrice: function () {
                var childProductId = this._getMatchingSimpleProduct();
                if (childProductId) {
                    // this._showPriceBlock(childProductId, false);
                    if (this.options.spConfig.updateurl) {
                        this._changeUrl(childProductId);
                    }

                    if (this.options.spConfig.updateSku) {
                        this._updateProductSku(childProductId);
                    }
                    if (this.options.spConfig.updateName) {
                        this._updateProductName(childProductId);
                    }
                    if (this.options.spConfig.updateShortDescription) {
                        this._updateProductShortDescription(childProductId);
                    }

                    if (this.options.spConfig.updateDescription) {
                        this._updateProductDescription(childProductId);
                    }
                    this._showTierPricingBlock(childProductId, this.options.spConfig.productId);
                    if (this.options.spConfig.image) {
                        this._updateProductImage(childProductId);
                    }
                    if (this.options.spConfig.additional) {
                        this._updateAdditionalInformation(childProductId);
                    }
                    if (this.options.spConfig.customStockDisplay) {
                        this._updateProductAvailability(childProductId);
                    }
                    this.options.spConfig.defaultValues = {};
                } else {
                    // var cheapestPid = this.getProductIdOfCheapestProductInScope("finalPrice");
                    // this._showPriceBlock(cheapestPid, true);
                    if (this.options.spConfig.updateSku) {
                        this._updateProductSku(false);
                    }
                    if (this.options.spConfig.updateName) {
                        this._updateProductName(false);
                    }
                    if (this.options.spConfig.updateShortDescription) {
                        this._updateProductShortDescription(false);
                    }
                    if (this.options.spConfig.updateDescription) {
                        this._updateProductDescription(false);
                    }
                    if (this.options.spConfig.image) {
                        //  var inScopeProductIds = this.getInScopeProductIds();
                        //  var id = this._findFirstItemWithAvailableImages(inScopeProductIds);
                        // this._updateProductImage(this.options.spConfig.productId);
                        // this._updateProductImage(id);
                    }
                    if (this.options.spConfig.customStockDisplay) {
                        this._updateProductAvailability(childProductId);
                    }
                }
                return this._super();
            },
            /**
             * Populates an option's selectable choices.
             * @private
             * @param {*} element - Element associated with a configurable option.
             */
            _fillSelect: function (element) {
                console.log("using _fillSelect");
                var defaultValues = this.options.spConfig.defaultValues;
                var attributeId = element.id.replace(/[a-z]*/, ''),
                    options = this._getAttributeOptions(attributeId),
                    prevConfig,
                    index = 1,
                    allowedProducts,
                    i,
                    j;
                var disable_out_of_stock_option = this.options.spConfig.disable_out_of_stock_option;
                this._clearSelect(element);
                element.options[0] = new Option('', '');
                element.options[0].innerHTML = this.options.spConfig.chooseText;

                prevConfig = false;

                if (element.prevSetting) {
                    prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
                }

                if (options) {
                    for (i = 0; i < options.length; i++) {
                        allowedProducts = [];

                        if (prevConfig) {
                            for (j = 0; j < options[i].products.length; j++) {
                                // prevConfig.config can be undefined
                                if (prevConfig.config &&
                                    prevConfig.config.allowedProducts &&
                                    prevConfig.config.allowedProducts.indexOf(options[i].products[j]) > -1) {
                                    allowedProducts.push(options[i].products[j]);
                                }
                            }
                        } else {
                            allowedProducts = options[i].products.slice(0);
                        }

                        if (allowedProducts.length > 0) {

                            options[i].allowedProducts = allowedProducts;

                            var childProducts = this.options.spConfig.childProducts;
                            var stockInfo = this.options.spConfig.stockInfo;

                            var cheapestPid = this.getProductIdOfCheapestProductInScope("finalPrice", allowedProducts);
                            var mostExpensivePid = this.getProductIdOfMostExpensiveProductInScope("finalPrice", allowedProducts);
                            var cheapestFinalPrice = childProducts[cheapestPid]["finalPrice"];
                            var mostExpensiveFinalPrice = childProducts[mostExpensivePid]["finalPrice"];

                            var stock = '';
                            var disable_option = false;

                            if (cheapestPid == mostExpensivePid && typeof stockInfo[cheapestPid] != 'undefined') {
                                if (!stockInfo[cheapestPid]["is_in_stock"]) {
                                    stock = '( ' + stockInfo[cheapestPid]["stockLabel"] + ' )';
                                } 
                                if (disable_out_of_stock_option) {
                                    if (!stockInfo[cheapestPid]["is_in_stock"]) {
                                        disable_option = true;
                                    }
                                }
                            }
                            var tierpricing = childProducts[mostExpensivePid]["tierpricing"];
                            var optionLabel = this.getOptionLabel(options[i], cheapestFinalPrice, mostExpensiveFinalPrice, stock, tierpricing);
                            element.options[index] = new Option(optionLabel, options[i].id);

                            if (typeof options[i].price !== 'undefined') {
                                element.options[index].setAttribute('price', options[i].prices);
                            }

                            if (disable_option) {
                                element.options[index].setAttribute('disabled', true);
                            }

                            element.options[index].config = options[i];
                            index++;
                        }

                        // Code added to select option
                        $.each(defaultValues, $.proxy(function (attributeCode, optionId) {
                            if (defaultValues[attributeCode] == options[i].id) {
                                 this.options.values[attributeId] = options[i].id;
                            }
                        }, this));
                        if (options.length == 1) {
                            $('select.super-attribute-select option[value="' + options[0].id + '"]')
                                .prop('selected', 'selected')
                                .trigger('change');
                        }

                    }
                }
            },
            getOptionLabel: function (options, lowPrice, highPrice, stock, tierpricing) {
                if (this.options.spConfig.hideprices) {
                    return this._getOptionLabel(options);
                }
                var tierpricinglowestprice = '';
                var optionLabel = this._getOptionLabel(options);
                if (tierpricing > 0) {
                    tierpricinglowestprice = ': As low as (' + priceUtils.formatPrice(tierpricing) + ')';
                }
                var separator = ': ( ';
                if (lowPrice && highPrice) {
                    if (lowPrice != highPrice) {
                        optionLabel += separator + priceUtils.formatPrice(lowPrice);
                        optionLabel += ' - ' + priceUtils.formatPrice(highPrice);
                        optionLabel += " ) ";
                    } else {
                        if (tierpricing == 0) {
                            optionLabel += separator + priceUtils.formatPrice(lowPrice);
                            optionLabel += " ) ";
                        }
                        optionLabel += tierpricinglowestprice;
                        optionLabel += '  ' + stock;
                    }
                }

                return optionLabel;
            },
            getInScopeProductIds: function (optionalAllowedProducts) {
                var productIds;
                var childProducts = this.options.spConfig.childProducts;
                var allowedProducts = [];
                if ((typeof optionalAllowedProducts != 'undefined') && (optionalAllowedProducts.length > 0)) {
                    allowedProducts = optionalAllowedProducts;
                }

                for (var s = 0, len = this.options.settings.length - 1; s <= len; s++) {
                    if (this.options.settings[s].selectedIndex <= 0) {
                        break;
                    }
                    var selected = this.options.settings[s].options[this.options.settings[s].selectedIndex];
                    if (s == 0 && allowedProducts.length == 0) {
                        allowedProducts = selected.config.allowedProducts;
                    } else {
                        allowedProducts = $(allowedProducts).filter(selected.config.allowedProducts);
                    }
                }

                if ((typeof allowedProducts == 'undefined') || (allowedProducts.length == 0)) {
                    productIds = Object.keys(childProducts);
                } else {
                    productIds = allowedProducts;
                }
                return productIds;
            },
            getProductIdOfCheapestProductInScope: function (priceType, optionalAllowedProducts) {
                var childProducts = this.options.spConfig.childProducts;
                var productIds = this.getInScopeProductIds(optionalAllowedProducts);

                var minPrice = Infinity;
                var lowestPricedProdId = false;

                //Get lowest price from product ids.
                for (var x = 0, len = productIds.length; x < len; ++x) {
                    var thisPrice = Number(childProducts[productIds[x]][priceType]);
                    if (thisPrice < minPrice) {
                        minPrice = thisPrice;
                        lowestPricedProdId = productIds[x];
                    }
                }

                return lowestPricedProdId;
            },
            getProductIdOfMostExpensiveProductInScope: function (priceType, optionalAllowedProducts) {

                var childProducts = this.options.spConfig.childProducts;
                var productIds = this.getInScopeProductIds(optionalAllowedProducts);

                var maxPrice = 0;
                var highestPricedProdId = false;

                //Get highest price from product ids.
                for (var x = 0, len = productIds.length; x < len; ++x) {
                    var thisPrice = Number(childProducts[productIds[x]][priceType]);
                    if (thisPrice >= maxPrice) {
                        maxPrice = thisPrice;
                        highestPricedProdId = productIds[x];
                    }
                }
                return highestPricedProdId;
            },

            _changeUrl: function (productId) {
                var childProducts = this.options.spConfig.childProducts;
                var productUrl = childProducts[productId].productUrl;
                history.pushState({}, '', productUrl);

            },
            /**
             * Update configurable product SKU with the sku of the selected simple product. 
             * @param Integer childProductId
             * @returns {undefined}
             */

            _updateProductSku: function (childProductId) {
                var sku = this.options.spConfig.sku;
                var product_sku_markup = this.options.spConfig.product_sku_markup;
                if (childProductId && this.options.spConfig.skus[childProductId].sku) {
                    sku = this.options.spConfig.skus[childProductId].sku;
                }
                $(product_sku_markup).html(sku);
            },
            /**
             * Update configurable product name with the name of the selected simple product. 
             * @param Integer childProductId
             * @returns {undefined}
             */

            _updateProductName: function (childProductId) {
                var name = this.options.spConfig.name;
                var product_name_markup = this.options.spConfig.product_name_markup;
                if (childProductId && this.options.spConfig.names[childProductId].name) {
                    name = this.options.spConfig.names[childProductId].name;
                }
                $(product_name_markup).html(name);
            },
            _updateProductShortDescription: function (childProductId) {
                var short_description = this.options.spConfig.short_description;
                var product_short_description_markup = this.options.spConfig.product_short_description_markup;
                if (childProductId && this.options.spConfig.short_descriptions[childProductId].short_description) {
                    short_description = this.options.spConfig.short_descriptions[childProductId].short_description;
                }
                $(product_short_description_markup).html(short_description);
            },
            _updateProductDescription: function (childProductId) {
                var description = this.options.spConfig.description;
                var product_description_markup = this.options.spConfig.product_description_markup;
                if (childProductId && this.options.spConfig.descriptions[childProductId].description) {
                    description = this.options.spConfig.descriptions[childProductId].description;
                }
                $(product_description_markup).html(description);
            },
            /**
             * Update product image with images from the selected simple 
             * product
             * @param Integer productId
             * @param Integer parentId
             * @returns {undefined}
             */
            _updateProductImage: function (productId) {
                var product_image_markup = this.options.spConfig.product_image_markup;
                function imageSwap(data) {
                    $(product_image_markup).html(data.responseText);
                    $(product_image_markup).trigger('contentUpdated');
                }
                var coUrl = this.options.spConfig.ajaxBaseUrl + "image/?id=" + productId;
                if (productId) {
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
                }
            },
            _showTierPricingBlock: function (productId, parentId) {
                var coUrl = this.options.spConfig.ajaxBaseUrl + "co/";
                $("#sppTierPricingDiv").html("");
                if (productId) {
                    $.ajax({
                        url: coUrl,
                        type: "POST",
                        data: "id=" + productId + '&pid=' + parentId,
                        success: function (data) {
                            $("#sppTierPricingDiv").html(data);
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            //
                        }
                    });
                }
            },

            _getPrices: function () {
                var prices = {},
                    elements = _.toArray(this.options.settings),
                    allowedProduct;

                _.each(elements, function (element) {
                    var selected = element.options[element.selectedIndex],
                        config = selected && selected.config,
                        priceValue = {};

                    if (config && config.allowedProducts.length === 1) {
                        priceValue = this._calculatePrice(config);
                    } else if (element.value) {
                        allowedProduct = this._getAllowedProductWithMinPrice(config.allowedProducts);
                        priceValue = this._calculatePrice({
                            'allowedProducts': [
                                allowedProduct
                            ]
                        });
                    }

                    if (!_.isEmpty(priceValue)) {
                        prices.prices = priceValue;
                    }
                }, this);
                return prices;
            },
            _showPriceBlock: function (productId, showPriceFromLabel) {
                var coUrl = this.options.spConfig.ajaxBaseUrl + "price/?id=" + productId;
                var fromPrice = this.options.spConfig.priceFromLabel;
                var priceBlock = '';
                var cProductId = this.options.spConfig.productId;

                function showPriceBlock(data) {
                    var response = data.responseText;
                    var f = ["product-price-" + productId, "old-price-" + productId, 'data-product-id="' + productId + '"'];
                    var r = ["product-price-" + cProductId, "old-price-" + cProductId, 'data-product-id="' + cProductId + '"'];
                    $.each(f, function (i, v) {
                        response = response.replace(new RegExp(v, 'g'), r[i]);
                    });
                    if (showPriceFromLabel != false) {
                        priceBlock = '<span class="configurable-price-from-label">' + fromPrice + '</span>' + response;
                    } else {
                        priceBlock = response;
                    }
                    $(".product-info-price").html(priceBlock);
                    $(".price-box").trigger('contentUpdated');

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
            _updateAdditionalInformation: function (childProductId) {
                var product_additional_markup = this.options.spConfig.product_additional_markup;
                var coUrl = this.options.spConfig + "additional/?id=" + this.options.spConfig.productId;
                if (childProductId) {
                    coUrl = this.options.spConfig.ajaxBaseUrl + "additional/?id=" + childProductId + '&pid=' + this.options.spConfig.productId;
                }
                $.ajax({
                    url: coUrl,
                    type: "POST",
                    success: function (data) {
                        $(product_additional_markup).html(data);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        //
                    }
                });
            },
            /**
             * Find simple product in scope with available images
             * @param {type} inScopeProductIds
             * @returns int childProductId 
             */

            _findFirstItemWithAvailableImages: function (inScopeProductIds) {
                var childProducts = this.options.spConfig.childProducts;
                for (var s = 0, len = inScopeProductIds.length - 1; s <= len; s++) {
                    if (childProducts[inScopeProductIds[s]]["has_image"]) {
                        return inScopeProductIds[s];
                    }
                }
                return this.options.spConfig.productId;
            },
            _updateProductAvailability: function (childProductId) {
                var stockInfo = this.options.spConfig.stockInfo;
                var product_customstockdisplay_markup = this.options.spConfig.product_customstockdisplay_markup;
                var stockLabel = '';
                var stockQty = '';
                if (childProductId && stockInfo[childProductId]["stockLabel"]) {
                    stockLabel = stockInfo[childProductId]["stockLabel"];
                    stockQty = stockInfo[childProductId]["stockQty"];
                    if (stockQty > 0) {
                        $(product_customstockdisplay_markup + ' span').html(stockQty + '  ' + stockLabel);
                    } else {
                        $(product_customstockdisplay_markup + ' span').html(stockLabel);
                    }
                }
            }
        });
        return $.mage.configurable;
    }
});
