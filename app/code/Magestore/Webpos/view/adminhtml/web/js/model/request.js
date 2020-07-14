/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/storage'
    ],
    function ($, storage) {
        "use strict";
        var Request = {
            initialize: function () {
                return this;
            },
            send: function (url, method, params) {
                return new Promise((resolve, reject) => {
                    switch (method) {
                        case 'post':
                            storage.post(
                                url, JSON.stringify(params)
                            ).done(
                                function (response) {
                                    resolve(response);
                                }
                            ).fail(
                                function (response) {
                                    reject(response);
                                }
                            );
                            break;
                        case 'get':
                            storage.get(
                                url, JSON.stringify(params)
                            ).done(
                                function (response) {
                                    resolve(response);
                                }
                            ).fail(
                                function (response) {
                                    reject(response);
                                }
                            );
                            break;
                        case 'delete':
                            url = this.addParamsToUrl(url, params);
                            storage.delete(
                                url, JSON.stringify(params)
                            ).done(
                                function (response) {
                                    resolve(response);
                                }
                            ).fail(
                                function (response) {
                                    reject(response);
                                }
                            );
                            break;
                        default:
                            break;
                        }
                });
            },
            addParamsToUrl: function(url, params){
                $.each(params, function(key, value){
                    if(key){
                        if (url.indexOf("?") != -1) {
                            url = url + '&'+key+'=' + value;
                        }
                        else {
                            url = url + '?'+key+'=' + value;
                        }
                    }
                });
                return url;
            }
        };
        return Request.initialize();
    }
);
