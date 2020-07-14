/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


define(
    [
        'ko'
    ],
    function (ko) {
        'use strict';
        var tempAllRewardpointsData = window.rewardPointsInfo;
        return {
            allData: ko.observable(tempAllRewardpointsData),
            getData: function(){
                return this.allData;
            },

            setData: function(data){
                this.allData(data);
            }
        }
    }
);
