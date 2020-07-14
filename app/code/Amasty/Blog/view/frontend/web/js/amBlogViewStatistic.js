define([
    'jquery',
], function ($) {
    'use strict';

    $.widget('amasty_blog.amBlogViewStatistic', {
        options: {
            update_url: '',
        },

        _create: function () {
            $.ajax({
                type: 'GET',
                url: this.options.update_url,
            });
        },
    });
    return $.amasty_blog.amBlogViewStatistic;
});
