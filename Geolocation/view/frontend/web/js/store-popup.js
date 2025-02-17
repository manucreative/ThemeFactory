define(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {
    'use strict';

    function checkLocation(lat, lng) {
        var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        var screenHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
        var popupWidth = 800;
        var popupHeight = 600;
        var left = (screenWidth - popupWidth) / 2;
        var top = (screenHeight - popupHeight) / 2;

        var googleMapsUrl = 'https://www.google.com/maps?q=' + lat + ',' + lng + '&z=15';
        window.open(googleMapsUrl, '_blank', 'width=800,height=600,left=' + left + ',top=' + top);
    }

return function (config) {
        if (!config.shouldDisplayPopup) {
            return;
        }
    $(window).on('load', function () {
    // $(document).ready(function () {
        var options = {
            type: 'popup',
                responsive: true,
                innerScroll: true,
                clickableOverlay: false,
            title: 'Welcome to '+ config.storeName,
            buttons: [{
                text: $.mage.__('Close'),
                class: '',
                click: function () {
                    this.closeModal();
                }
            }]
        };

        var popup = modal(options, $('#store-popup'));
            $('#store-popup').modal('openModal').css('z-index', '9999');

            $('#get-directions').on('click', function () {
            checkLocation(config.lat, config.lng);
        });

    });
}
});