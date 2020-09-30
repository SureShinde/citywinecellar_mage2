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

        let filterOpen = parseInt(localStorage.getItem('hide-filter'));
        if(filterOpen === 1){
            $(".columns").removeClass('hide-filter');
        }


        $(".mobile-mnu").click(function(){
            $(this).parents().toggleClass('open');
        });

        $(".js-filter").click(function(){
            let filterStatus = ($(".columns").hasClass('hide-filter')) ? "1" : "0";
            localStorage.setItem('hide-filter', filterStatus);
            $(".columns").toggleClass('hide-filter');
        });


        $('#minusQty').click(function () {
            $('#qty').val(parseInt($('#qty').val())-1);
            if($('#qty').val() < 0) $('#qty').val("0");
        });

        $('#addQty').click(function () {
            $('#qty').val(parseInt($('#qty').val())+1);
        });



        // newsletter Popup

        $(".newsletter a, .newsletter-overlay, .newsletter-close").click(function(event){
            event.preventDefault();
            $("body").toggleClass('open-newsletter');
        });

    });
});
