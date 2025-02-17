define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push(
            {
                type: 'themeFactory_mpesastk',
                component: 'ThemeFactory_Mpesastk/js/view/payment/method-renderer/mpesa_stk_push'
            }
        );

        /** Add view logic here if needed */

        return Component.extend({});
    }
);