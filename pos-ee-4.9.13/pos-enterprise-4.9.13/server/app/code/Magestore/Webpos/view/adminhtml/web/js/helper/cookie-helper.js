/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';
        var Cookie = {
            data: null,
            denomination: ko.observableArray(),
            /**
             * Init Data
             * @param data
             */
            setCookie: function (cname, cvalue, exdays) {
                var d = new Date();
                d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
                var expires = "expires="+d.toUTCString();
                document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
            },
            getCookie : function (cname) {
                var name = cname + "=";
                var ca = document.cookie.split(';');
                for(var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            },
            removeCookie : function (cname){
                document.cookie = cname + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/';
            }
        };
        return Cookie;
    }
);
