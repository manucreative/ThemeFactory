define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    'use strict';

    return function (stores) {
        var storeOptions = stores.map(function (store) {
            return '<button class="store-option" data-url="' + store.url + '">' + store.url + '</button>';
        }).join('');

        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: 'Select Your Preferred Store',
            buttons: []
        };

        var popup = $('<div/>').html(storeOptions).modal(options);
        popup.modal('openModal');

        $('.store-option').on('click', function () {
            window.location.href = $(this).data('url');
        });
    };
});
