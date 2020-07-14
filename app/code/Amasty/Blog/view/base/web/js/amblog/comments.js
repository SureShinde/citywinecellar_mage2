/*jshint browser:true jquery:true*/
define([
    'jquery',
    'mage/cookies',
    'mage/validation'
], function ($) {
    "use strict";

    $.widget('mage.amblogComments', {
        options: {
            'form' : '[data-amblog-js="form"]',
            'reply' : '[data-amblog-js="reply-to"]',
            'comments_wrapper' : '[data-amblog-js="comments_wrapper"]',
            'short_comments' : '[data-amblog-js="short_comments"]',
            'gdpr' : '[data-amblog-js="gdpr-agree"]'
        },

        _create: function () {
            var self = this;

            $(document).on('submit', self.options.form, function (e) {
                if ($(this).validation() && $(this).validation('isValid')) {
                    self.submitForm($(this).serializeArray(), $(this));
                }

                e.preventDefault();
                e.stopPropagation();
                return false;
            });

            $(document).on('click', self.options.reply, function () {
                self.showForm('am-comment-form-' + $(this).attr("data-id"), $(this).attr("data-id"));
            });

            this.loadCommentsByAjax();
        },

        initialize: function (params) {
            this.form = false;

            //Redirect to form after login
            if (window.location.hash) {
                if (window.location.hash === '#add-comment') {
                    this.showForm('amblog-comment-form', 0);
                } else if (window.location.hash.indexOf('#reply-to-') !== -1) {
                    var replyTo = window.location.hash.replace('#reply-to-', '');
                    this.showForm('am-comment-form-' + replyTo, replyTo);
                }
            }
        },

        loadCommentsByAjax: function () {
            var self = this;

            $.ajax({
                type: 'POST',
                url: this.options.update_url,
                data: {
                    'post_id': this.options.post_id,
                    'form_key': $.mage.cookies.get('form_key')
                },
            }).success(function (data) {
                try {
                    if (data.content) {
                        var tmpElement = $('<div/>').html(data.content).find(self.options.comments_wrapper),
                            element = $(self.options.comments_wrapper);

                        if (element && tmpElement && tmpElement.children().length) {
                            element.html(tmpElement.html());
                        }

                        if (data.short_content) {
                            var tmpElement = $('<div/>').html(data.short_content).find(self.options.short_comments),
                                element = $(self.options.short_comments);

                            if (element && tmpElement && tmpElement.children().length) {
                                element.html(tmpElement.html());
                            }
                        }
                    } else if (data.error) {
                        console.warn(data.error);
                    }
                } catch (e) {
                    var response = {};
                }
            });
        },

        hideForm: function (form_id, callback) {
            $(form_id).innerHTML = '';
            new Effect.Fade(form_id, {
                afterFinish: (typeof(callback) != 'undefined' ? callback() : function () {
                }),
                duration: 1.0
            });
        },

        showForm: function (container, id) {
            var formContainer = $('#' + container);
            if (formContainer && (formContainer.css('display') == 'none')) {
                $(this.form_selector).each(function (element) {
                    if (element.id !== container) {
                        element.innerHTML = '';
                        $(element).hide();
                    }
                });

                this.showLoader(formContainer);

                $(formContainer).show();
                this.loadFormToContainer(container, id);
            }

            return false;
        },

        showLoader: function (who) {
            $(who).append('<div class="amblog-loader"></div>');
        },

        loadFormToContainer: function (container, id) {
            var url = decodeURI(this.options.form_url.replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, ''))),
                self = this;

            $.ajax({
                type: 'GET',
                url: url.replace('{{post_id}}', this.options.post_id).replace('{{session_id}}', this.options.session_id).replace('{{reply_to}}', id).replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, '')),
            }).success(function (data) {
                try {
                    if (data.form) {
                        $('#' + container).html(data.form);
                    }
                } catch (e) {
                    var response = {};
                }
            });
        },

        submitForm: function (formValues, form) {
            var values = {},
                url = decodeURI(this.options.post_url),
                self = this;

            $.each(formValues, function (i, field) {
                values[field.name] = field.value;
            });

            if (form.find('[data-amblog-js="gdpr-agree"]').length
                && !form.find('[data-amblog-js="gdpr-agree"]:checked').length
            ) {
                values['email'] = '';
                values['name'] = '';
                values['customer_id'] = 0;
            }
            if (!('name' in values)) {
                values['name'] = '';
            }

            url = url.replace('{{post_id}}', this.options.post_id)
                .replace('{{session_id}}', this.options.session_id)
                .replace('{{reply_to}}', values.reply_to)
                .replace(/^http[s]{0,1}/, window.location.href.replace(/:[^:].*$/i, ''));
            $.ajax({
                type: 'POST',
                data: values,
                url: url,
                beforeSend: function () {
                    $('body').loader('show');
                },

                success: function (response) {
                    $('body').loader('hide');
                    if (response.error == 1) {
                        window.scrollTo(0, 0);
                        return false;
                    }

                    $(response.message).insertBefore(form).show();
                    form.replaceWith(response.form);
                },

                error: function () {
                    $('body').loader('hide');
                    self._scrollToTop();
                }
            });
        },

        _scrollToTop: function () {
            $('html,body').animate({
                scrollTop: 0
            }, 'slow');
        }
    });

    return $.mage.amblogComments;
});
