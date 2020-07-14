define([
    'jquery',
    'Amasty_Base/vendor/slick/slick.min'
], function ($) {
    $.widget('amasty.postSlider', {
        options: {
            slidesToShow: 3,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2
                    }
                }
            ]
        },

        _create: function () {
            $(this.element).slick(this.options);
        }
    });

    return $.amasty.postSlider;
});
