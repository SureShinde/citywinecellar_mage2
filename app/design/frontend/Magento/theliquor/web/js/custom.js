require([
    'jquery',
    'domReady',
    'slick'
], function ($, domReady) {
    domReady(function () {
        let navbar = document.querySelector(".panel.wrapper");


        if (navbar) {
            window.onscroll = function() {headSticky()};

            let body = document.querySelector("body");
            let sticky = navbar.offsetTop;

            function headSticky() {
                if (window.pageYOffset >= sticky) {
                    body.classList.add("sticky")
                } else {
                    body.classList.remove("sticky");
                }
            }
        }



        $(".mobile-mnu").click(function(){
            $(this).parents().toggleClass('open');
        });

        $(".js-filter").click(function(){
            $(".columns").toggleClass('hide-filter');
        });


        // newsletter Popup

        $(".newsletter a, .newsletter-overlay, .newsletter-close").click(function(event){
            event.preventDefault();
            $("body").toggleClass('open-newsletter');
        });

        });
});
