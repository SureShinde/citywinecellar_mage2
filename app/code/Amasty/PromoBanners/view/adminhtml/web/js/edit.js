define([
    "jquery",
    "Magento_Ui/js/modal/modal"
], function ($) {
    $.widget('mage.ampromobannerAddAttribute', {
        _create: function () {
            var edit = this;

            if ($('#ampromobanners_attributes')) {
                if (!$('#add_new_attribute')[0]) {
                    var attributesFieldset = $('#ampromobanners_attributes')[0];
                    $(attributesFieldset).append('<button title=\"Save\" type=\"button\" class=\"scalable add\" id=\"add_new_attribute\" onclick=\"return false;\">Add New</button>');
                }

                $("#add_new_attribute").click(function () {
                    edit.landingNewField();
                });

                this.showHideContainer('attributestmp', false);
                this.showHideContainer('attributes', false);
            }

            if ($('#ampromobanners_banner_type')) {
                $("#ampromobanners_banner_type").change(function () {
                    edit.changeBannerType();
                });
                this.changeBannerType();
            }

            //Show always, because banner_positions is multiselect now
            if ($('#ampromobanners_banner_position')) {
                $("#ampromobanners_banner_position").change(function () {
                    edit.changePosition();
                });
                this.changePosition();
            }

            if ($('#ampromobanners_show_products')) {
                $("#ampromobanners_show_products").change(function () {
                    edit.showHideProductsContainer();
                });
                this.showHideProductsContainer();
            }

        },

        landingNewField: function () {
            if ($('#ampromobanners_attributes')) {

                var hasAttributeElement = $("#ampromobanners_attributestmp div:eq(1)").clone();
                var attributeValueElement = $("#ampromobanners_attributestmp div:eq(3)").clone();

                $('#add_new_attribute').before(hasAttributeElement);
                $('#add_new_attribute').before(attributeValueElement);
            }
        },

        landingRemove: function (element) {
            if ($(element)) {
                $(element).parent().parent().parent().parent().next('div').remove();
                $(element).parent().parent().parent().parent().remove();
            }
        },

        /*
         * Listen for chaning banner type field
         */
        changeBannerType: function () {
            var value = $("#ampromobanners_banner_type").val()
            var list = ['cms', 'html', 'image', 'products'];
            var editForm = this;

            list.each(function (item) {
                if (item == value) {
                    editForm.showHideContainer(item, true);
                } else {
                    editForm.showHideContainer(item, false);
                }
            });
        },

        changePosition: function () {
            var value = $("#ampromobanners_banner_position").val();
            var list = ['cats', 'show_on_products'];
            var editForm = this;

            list.each(function (item) {
                editForm.showHideField(item, false);
            });

            if (value != null &&
                    (value.indexOf('3') != -1
                    || value.indexOf('6') != -1
                    || value.indexOf('7') != -1
                    || value.indexOf('10') != -1)
            ) {
                this.showHideField('show_on_products', true);
                this.showHideContainer('banner_productcond_fieldset', true);
            } else {
                this.showHideContainer('banner_productcond_fieldset', false);
            }

            if (value != null && (value.indexOf('1') != -1
                || value.indexOf('2') != -1)) {
                $('#note-sidebar').show();
            } else {
                $('#note-sidebar').hide();
            }

            if (value != 0) {
                this.showHideField('cats', true);
            }
        },

        showHideField: function (id, show) {
            if ($(id)) {
                var field = $('#ampromobanners_' + id).parent().parent();

                if (show) {
                    field.show();
                } else {
                    field.hide();
                }
            }
        },

        /*
         * Show Or Hide Field with Its container
         */
        showHideContainer: function (id, show) {
            if ($(id)) {
                var container = $('#ampromobanners_' + id);
                // var header = container.previous(0);

                if (show) {
                    container.show();
                    // header.show();
                } else {
                    container.hide();
                    // header.hide();
                }
            }
        },

        /*
         * Show Or Hide Field with Its container
         */
        showHideProductsContainer: function () {
            var a = $('#ampromobanners_banners_edit_tabs_products_section');
            // var b = $(a).parentNode;

            var show = false;
            if ($("#ampromobanners_show_products").val() == 1) {
                show = true;
            }

            if (show) {
                // b.show();
                a.show();
            } else {
                // b.hide();
                a.hide();
            }
        }

    });

    return $.mage.ampromobannerAddAttribute;

});
