define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'mage/url',
        'mage/translate',
        'mage/template',
        'jquery/ui',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/checkout-data'
    ],
    function ($, ko, quote, url, $t, mageTemplate, newMap , navigator, selectBillingAddress, checkoutData) {
        var liststoreJson = window.liststoreJson;

        function getSelectListStoreHtml() {
            var $wrapperelectHtml = $('<div class="list-store-to-pickup"><label>' + $t('Select Store:') + '</label></div>');
            var $selectStoreHtml = $('<select class="list-store-select disable-ul"></select>');
            $selectStoreHtml.append('<option class="show-tag-li store-item" value="">' + $t('Select a store to pickup') + '</option>');
            $.each(liststoreJson, function (index, el) {
                $selectStoreHtml.append('<option class="show-tag-li store-item" value="' + el.storepickup_id + '">' + el.store_name + '</option>');
            });
            $wrapperelectHtml.append($selectStoreHtml);

            return $wrapperelectHtml;
        }
        quote.paymentMethod.subscribe(function () {

            if(window.checkoutConfig.checkout_title){
                return;
            }
            

            if (quote.shippingMethod() && quote.shippingMethod().carrier_code == 'storepickup') {

                // set billing address
                selectBillingAddress(quote.shippingAddress());

                // if (navigator.getActiveItemIndex() == 0) {
                //     quote.setPaymentMethod(null);
                // }

                var mapImage= '<br/><img src="http://maps.google.com/maps/api/staticmap?center=' + storePikcuplatitude + ',' + storePikcuplongitude + '&zoom=15&size=200x200&markers=color:red|label:S|' + storePikcuplatitude + ',' + storePikcuplongitude + '&sensor=false" />';
                // $('.payment-method-billing-address').html($t("Pickup at Store:")+'<br/>'+$('.info-store-checkout').html());
                // $('.ship-to .shipping-information-content').html($('.info-store-checkout').html());

                //if(!($('.storePickupreview').length>0)) {
                //    $('.ship-via .shipping-information-content').append(mapImage);
                //}

                if(isDisplayPickuptime && !($('.storePickupreview').length>0)){
                    storePickupreview= '<div class="storePickupreview">'+'Pickup Date:'+$('#shipping_date').val()+"<br/>"+ 'Pickup Time:'+$('#shipping_time').val()+'</div>';
                    $('.ship-via .shipping-information-content').append(storePickupreview);
                }
            }
        }, this);

        quote.shippingMethod.subscribe(function (value) {
            var storePickupInformation = "<div class ='storepickup-information'></div>";
            if((!$('.storepickup-information').length > 0)) $('#checkout-shipping-method-load').append(storePickupInformation);
            if (quote.shippingMethod()&&quote.shippingMethod().carrier_code == 'storepickup') {
                isShowShippingField(false);
                if($('#checkout-shipping-method-load').length === 0) {
                    quote.shippingMethod(null);
                    checkoutData.setSelectedShippingRate('');
                    return;
                }
                if(!($('.info-store-checkout').length>0) || $('.list-store-select').val()=="" || ((isDisplayPickuptime) && (($('#shipping_date').val() == '') || ($('#shipping_time').val() == '-1')))) {
                    $('#shipping-method-buttons-container').hide();
                }
                if (!($('.list-store-to-pickup').length > 0)) {
                    $('.storepickup-information').append(getSelectListStoreHtml());
                    $('.storepickup-information').append(select_store_by_map);
                    $('#select_store_by_map').click(function () {
                        $('#popup-mpdal').modal('openModal');
                        //if(typeof newMap != 'undefined'){
                        //var   map = newMap.getNewMap();
                        google.maps.event.trigger(newmap, "resize");  //Silver and Richard
                        //}
                    });
                    //change Store function
                    $('.list-store-select').change(function () {
                        $('#shipping-method-buttons-container').hide();
                        if ($('#shipping_date_div').length > 0) {
                            $('#shipping_date_div').show();
                            $('#shipping_date').hide();
                            $('#shipping_time_div').hide();
                        } else if(isDisplayPickuptime) {
                            $('.storepickup-information').append(storepickup_date_box);
                            $("#shipping_date").change(function()
                            {
                                $('#shipping-method-buttons-container').hide();
                                $('.overlay-bg-checkout').show();
                                showTimeBox($('#shipping_date').val(),$('.list-store-select').val());
                            });
                        }
                        $('.overlay-bg-checkout').show();
                        $.each(liststoreJson, function (index, el) {
                            if (el.storepickup_id == $('.list-store-select').val()) {
                                storePikcuplatitude= el.latitude;
                                storePikcuplongitude=el.longitude;
                                var store_information = '<h3>' + el.store_name + '</h3><br/>' + '<p>' + $t('Store address: ') + el.address + '</p>'+'<p>' + $t('Store Phone: ') + el.phone + '</p>';
                                if ($('.info-store-checkout').length > 0) {
                                    $('.info-store-checkout').html(store_information);
                                    $('.info-store-checkout').show();
                                } else {
                                    var info_store = '<div class ="info-store-checkout">' + '<h3>'+ el.store_name + '</h3><br/>' + '<p>' + $t('Store address: ') + el.address + '</p>'+'<p>' + $t('Store Phone: ') + el.phone + '</p>' + '</div>';
                                    $('#select_store_by_map').after(info_store);
                                }
                                $.ajax(
                                    {
                                        url: url.build("storepickup/checkout/changestore"),
                                        type: "post",
                                        dateType: "text",
                                        data: {
                                            store_id: $('.list-store-select').val()
                                        },
                                        success: function (result) {
                                            if(isDisplayPickuptime) showDateBox(); else {
                                                $('.overlay-bg-checkout').hide();
                                                $('#shipping-method-buttons-container').show();
                                            }
                                            var resultJson = JSON.parse(result);
                                            var storePickUpAddress = resultJson.dataShipping;
                                            fillStorePickUpAddressToField(storePickUpAddress);
                                        }
                                    });
                            }
                        });
                        if($('.list-store-select').val()==""){
                            $('.overlay-bg-checkout').hide();
                            $('.info-store-checkout').hide();
                            if ($('#shipping_date_div').length > 0) $('#shipping_date_div').hide();
                            if ($('#shipping_time_div').length > 0) $('#shipping_time_div').hide();
                        }
                    });
                }
                $.each(liststoreJson, function (index, el) {
                    if (el.storepickup_id == defaultStore && !($('.info-store-checkout').length>0 )) {
                        $('.list-store-select').val(defaultStore).trigger('change');
                    }
                });
                $('.storepickup-information').show();
            } else {
                $('.list-store-select').val('');
                $('.overlay-bg-checkout').hide();
                $('.info-store-checkout').hide();
                if ($('#shipping_date_div').length > 0) $('#shipping_date_div').hide();
                if ($('#shipping_time_div').length > 0) $('#shipping_time_div').hide();
                $('#shipping-method-buttons-container').show();
                $('.storepickup-information').hide();
                isShowShippingField(true);
            }
        });

        function isShowShippingField(isShow) {
            var componentsToHide = [
                'shippingAddress.firstname',
                'shippingAddress.lastname',
                'shippingAddress.company',
                'shippingAddress.telephone',
                'shippingAddress.country_id',
                'shippingAddress.postcode',
                'shippingAddress.region',
                'shippingAddress.region_id',
                'shippingAddress.city'
            ];
            $.each(componentsToHide, function (index, value) {
                var componentField = $('#shipping-new-address-form [name="' + value + '"]');
                var component = '';

                if (componentField.length) {
                    component = ko.dataFor(componentField.get(0));
                }
                if (!isShow && component) {
                    component.visible(false);
                } else if (component) {
                    if (value === 'shippingAddress.region_id' && !component.value()) {
                        component.visible(false);
                    } else if (value === 'shippingAddress.region') {
                        var regionIdField = $('#shipping-new-address-form [name="shippingAddress.region_id"]');
                        if (regionIdField.length) {
                            componentRegion = ko.dataFor(regionIdField.get(0));
                            if (componentRegion.value()) {
                                component.visible(false);
                            } else {
                                component.visible(true);
                            }
                        }
                    } else {
                        component.visible(true);
                    }
                }
            });
            var streetFieldSet = $('#shipping-new-address-form .street');
            if (!isShow && streetFieldSet.length) {
                streetFieldSet.hide();
            } else if (streetFieldSet.length) {
                streetFieldSet.show();
            }
        }

        function fillStorePickUpAddressToField(data) {
            var componentsToFill = [
                'shippingAddress.firstname',
                'shippingAddress.lastname',
                'shippingAddress.company',
                'shippingAddress.telephone',
                'shippingAddress.country_id',
                'shippingAddress.postcode',
                'shippingAddress.region',
                'shippingAddress.region_id',
                'shippingAddress.city',
                'street[0]',
                'street[1]',
                'street[2]',
                'street[3]',
            ];
            var mapComponentToData = {
                'shippingAddress.firstname': data.firstname,
                'shippingAddress.lastname': data.lastname,
                'shippingAddress.company': data.company,
                'shippingAddress.fax': data.fax,
                'shippingAddress.telephone': data.telephone,
                'shippingAddress.country_id': data.country_id,
                'shippingAddress.postcode': data.postcode,
                'shippingAddress.region': data.region,
                'shippingAddress.region_id': data.region_id,
                'shippingAddress.city': data.city,
                'street[0]': data.street,
                'street[1]': '',
                'street[2]': '',
                'street[3]': '',
            };

            $.each(componentsToFill, function (index, value) {
                var componentField = $('#shipping-new-address-form [name="' + value + '"]');
                if (componentField.length) {
                    var component = ko.dataFor(componentField.get(0));
                    component.value(mapComponentToData[value]);
                }
            });
        }

        function showDateBox(){

            $.ajax( {
                url: url.build("storepickup/checkout/disabledate"),
                type: "post",
                dateType: "json",
                data: {
                    store_id: $('.list-store-select').val()
                },
                success: function (result) {
                    result = $.parseJSON(result);
                    $("#shipping_date").val("");
                    $("#shipping_date").datepicker("destroy");
                    $("#shipping_date").datepicker( {
                        minDate: -0,
                        dateFormat: 'mm/dd/yy',
                        beforeShowDay: function(day) {
                            var formatdate = $.datepicker.formatDate('mm/dd/yy', day);
                            return [ ($.inArray(formatdate,result.special) != -1)||($.inArray(formatdate,result.holiday) == -1)&&($.inArray(day.getDay(),result.schedule) == -1) ];
                        }
                    });
                    $('#shipping_date').show();
                    $('.overlay-bg-checkout').hide();
                }
            });
        }
        function showTimeBox(shipping_date_val,store_id_val) {
            if (!($('#shipping_time_div').length > 0)) {
                $('.storepickup-information').append(storepickup_time_box);
                //change Time function
                $("#shipping_time").change(function() {
                    if(($("#shipping_time").val()!='-1')&&($('#shipping_date_div').length > 0)) {
                        $('.overlay-bg-checkout').show();
                        $.ajax( {
                            url: url.build("storepickup/checkout/changetime"),
                            type: "post",
                            dateType: "json",
                            data: {
                                store_id: $('.list-store-select').val(),
                                shipping_date: $("#shipping_date").val(),
                                shipping_time: $("#shipping_time").val()
                            },
                            success: function (result) {
                                $('#shipping-method-buttons-container').show();
                                $('.overlay-bg-checkout').hide();
                            }
                        });
                    } else $('#shipping-method-buttons-container').hide();
                });
            }
            $('#shipping_time_div').show();
            $('#shipping_time').hide();

            $.ajax( {
                url: url.build("storepickup/checkout/changedate"),
                type: "post",
                dateType: "json",
                data: {
                    shipping_date: shipping_date_val,
                    store_id:store_id_val
                },
                success: function (result) {
                    result = $.parseJSON(result);
                    $('#shipping_time').html("");
                    var selecttime='<option value="-1">Select time to pickup</option>';
                    if(!result.error)
                    {
                        $('#shipping_time').append(selecttime+result.html);
                        $('#shipping_time').show();
                        $('.overlay-bg-checkout').hide();
                    } else alert(result.error);
                }
            });
        }
    });