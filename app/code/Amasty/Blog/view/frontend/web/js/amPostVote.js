define([
    "jquery",
    "pageCache",
    "mage/cookies"
], function ($) {
    'use strict';

    $.widget('mage.amPostVote', {
        options: {},

        _create: function (options) {
            this.url = this.options['url'];
            this.postId = this.element.attr('data-helpful-js').replace(/[^\d]/gi, '');
            this.plus = this.element.find('.amblog-plus');
            this.minus = this.element.find('.amblog-minus');

            var self = this;
            this.plus.on('click', function (item) {
                self.clickPlus();
            });

            this.minus.on('click', function (item) {
                self.clickMinus();
            });

            this.initializeBlock();
        },

        initializeBlock: function () {
            // initalize form key into cookie before domReady
            this.element.formKey();

            var self = this,
                key = $.mage.cookies.get('form_key'),
                data = 'type=update&form_key=' + key + '&post=' + this.postId;

            $.ajax({
                url: self.url,
                data: data,
                type: 'post',
                dataType: 'json',
                success: function (response) {
                    if (response && response.success) {
                        self.updateVote(response);
                    }
                }
            });
        },

        clickPlus: function () {
            if (!this.element.hasClass('disabled')) {
                this.element.addClass('disabled');
                this.sendAjax('plus');
            }
        },

        clickMinus: function () {
            if (!this.element.hasClass('disabled')) {
                this.element.addClass('disabled');
                this.sendAjax('minus');
            }
        },

        updateVote: function (response) {
            this.plus.find('.amblog-count').text(response.data.plus);
            this.minus.find('.amblog-count').text(response.data.minus);

            if (response.voted.plus > 0) {
                this.plus.addClass('-voted');
            } else {
                this.plus.removeClass('-voted');
            }

            if (response.voted.minus > 0) {
                this.minus.addClass('-voted');
            } else {
                this.minus.removeClass('-voted');
            }
        },

        sendAjax: function ($type) {
            var self = this,
                key = $.mage.cookies.get('form_key'),
                data = 'type=' + $type + '&form_key=' + key + '&post=' + this.postId;

            $.ajax({
                url: self.url,
                data: data,
                type: 'post',
                dataType: 'json',
                success: function (response) {
                    var result = $('<div/>', {
                            class: 'message'
                        }),
                        html = $('<div/>');
                    if (response && response.success) {
                        html.html(response.success).appendTo(result);
                        result.addClass('success');
                        self.updateVote(response);
                    }
                    if (response && response.error) {
                        html.html(response.error).appendTo(result);
                        result.addClass('error');
                    }

                    self.element.removeClass('disabled');
                },
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        }
    });

    return $.mage.amPostVote;
});
