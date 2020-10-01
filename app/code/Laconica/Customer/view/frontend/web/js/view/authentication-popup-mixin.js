define([
    'uiComponent',
    'jquery',
    'Magento_Ui/js/modal/modal',
    'ko'
], function (Component, $, modal, ko) {
    'use strict';

    return function (Component) {
        return Component.extend({
            defaults: {
                template: 'Laconica_Customer/authentication-popup'
            },
            amastySocialsEnabled: function () {
                if (typeof window.authenticationSocials === "undefined" || !window.authenticationSocials.hasOwnProperty('socials')) {
                    return false;
                }
                let elementCount = Object.keys(window.authenticationSocials['socials']).length;
                return (elementCount > 0);
            },
            getAmastySocials: function () {
                let resultArray = [];

                if (typeof window.authenticationSocials === "undefined" || !window.authenticationSocials.hasOwnProperty('socials')) {
                    return ko.observableArray(resultArray);
                }

                let object = window.authenticationSocials['socials'];

                for (let property in object) {

                    if (!object.hasOwnProperty(property)) {
                        continue;
                    }

                    let item = object[property];

                    if (!item.hasOwnProperty('label') || !item.hasOwnProperty('type') || !item.hasOwnProperty('url')) {
                        continue;
                    }

                    resultArray.push({
                        'label': object[property]['label'],
                        'type': object[property]['type'],
                        'url': object[property]['url']
                    });
                }

                return ko.observableArray(resultArray);
            }
        });
    };
});