define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('mage.amlandingProductsPager', {
        options: {
            'gridUrl': null,
            'varNamePage': null,
            'limitControl': '[data-role=page_limiter]',
            'prevControl': '[data-role=button_previous]',
            'nextControl': '[data-role=button_next]',
            'inputControl': '[data-role=input_page]'
        },

        _create: function () {
            this.url = this.options.gridUrl;
            this.initEvents();
        },

        initEvents: function () {
            $(this.options.limitControl).on('change', this.loadByElement.bindAsEventListener(this));
            $(this.options.prevControl).on('click', this.setPage.bindAsEventListener(this));
            $(this.options.nextControl).on('click', this.setPage.bindAsEventListener(this));
            $(this.options.inputControl).on('keypress', this.inputPage.bindAsEventListener(this));
        },

        reload: function (url, keepCurrentPage) {
            var ajaxRequest,
                ajaxSettings,
                reloadParams = {}
                keepCurrentPage = typeof keepCurrentPage == 'undefined' ? true : keepCurrentPage;

            reloadParams.form_key = FORM_KEY;

            reloadParams.limit = $(this.options.limitControl).val();
            if (keepCurrentPage !== false) {
                reloadParams.page = $(this.options.inputControl).val();
            }

            url = url || this.options.gridUrl;
            ajaxSettings = {
                url: url + (url.match(new RegExp('\\?')) ? '&ajax=true' : '?ajax=true'),
                showLoader: true,
                method: 'post',
                context: this.element,
                data: reloadParams,
                dataType: 'html',
                success: $.proxy(this.ajaxSuccess, this)
            };

            this.element.trigger('gridajaxsettings', ajaxSettings);
            ajaxRequest = $.ajax(ajaxSettings);
            this.element.trigger('gridajax', ajaxRequest);
        },

        setPage: function (event) {
            var pageNum = $(event.target).attr('data-value');

            this.reload(this.getUrlWithAdditionalValue(this.options.varNamePage, pageNum), false);
        },

        inputPage: function (event) {
            var keyCode = event.keyCode || event.which,
                element = $(Event.element(event));

            if (keyCode === Event.KEY_RETURN) {
                this.reload(this.getUrlWithAdditionalValue(this.options.varNamePage, element.val()), false);
            }
        },

        loadByElement: function (event) {
            var element = event.target;

            if (element && element.name) {
                this.reload(this.getUrlWithAdditionalValue(element.name, element.value), false);
            }
        },

        ajaxSuccess: function (data, textStatus, transport) {
            var html = $('<div />').append(transport.responseText).find('>div').html();

            this.element.html(html);
            this.element.trigger('contentUpdated');
            this.initEvents();
        },

        getUrlWithAdditionalValue: function (varName, varValue) {
            var re = new RegExp('\/(' + varName + '\/.*?\/)'),
                parts = this.url.split(new RegExp('\\?'));

            this.url = parts[0].replace(re, '/');
            this.url += varName + '/' + varValue + '/';

            if (parts.size() > 1) {
                this.url += '?' + parts[1];
            }
            return this.url;
        }
    });

    return $.mage.amlandingProductsPager;
});
