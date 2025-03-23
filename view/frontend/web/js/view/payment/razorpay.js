define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
            Component,
            rendererList
            ) {
        'use strict';
        rendererList.push(
                {
                    type: 'razorpay',
                    component: 'Kananinj_Razorpay/js/view/payment/method-renderer/razorpay-method'
                },
                // other payment method renderers if required
                );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
