define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Kananinj_Razorpay/js/action/place-order',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/url',
    ],
    function ($, Component, additionalValidators, placeOrderAction, fullScreenLoader,url) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Kananinj_Razorpay/payment/razorpay',
                razorpayDataFrameLoaded: false,
                code: window.checkoutConfig.payment.razorpay.code
            },
            getCode: function () {
                return this.code;
            },

            initialize: function () {
                var self = this;
                self._super();
                return self;
            },
            initObservable: function () {
                var self = this._super();

                if(!self.razorpayDataFrameLoaded) {
                    $.getScript("https://checkout.razorpay.com/v1/checkout.js", function() {
                        self.razorpayDataFrameLoaded = true;
                    });
                }

                return self;
            },
            selectPaymentMethod: function () {
                var self = this;
                self._super();
                return true;
            },

            getData: function () {
                return {
                    'method': this.item.method,
                    additionalData: {
                        'merchant_name': window.checkoutConfig.payment.razorpay.merchant_name
                    }
                };
            },

            placeOrder: function (data, event) {
                var self = this;
                var placeOrder;

                if (event) {
                    event.preventDefault();
                }

                if (this.validate() && additionalValidators.validate()) {

                    this.isPaymentProcessing = $.Deferred();

                    $.when(this.isPaymentProcessing).fail(
                        function (result) {
                            console.log(result);
                            self.handleError(result);
                        }
                    );

                    this.isPlaceOrderActionAllowed(false);
                    placeOrder = placeOrderAction(this);
                    
                }

                return false;
            },
            validateOrder: function(data){

                var self = this;
                fullScreenLoader.startLoader();

                $.ajax({
                    type: 'POST',
                    url: url.build('razorpay/payment/validate'),
                    data: JSON.stringify(data),
                    dataType: 'json',
                    contentType: 'application/json',

                    /**
                     * Success callback
                     * @param {Object} response
                     */
                    success: function (response) {
                        if (response.status != 200) {
                            fullScreenLoader.stopLoader();
                            console.log("!response.status == 200",response);
                            self.isPaymentProcessing.reject(response.message);
                            self.handleError(response);
                            self.isPlaceOrderActionAllowed(true);
                        } else {
                            window.location.replace(url.build('checkout/onepage/success'));
                        }
                    },

                    /**
                     * Error callback
                     * @param {*} response
                     */
                    error: function (response) {
                        fullScreenLoader.stopLoader();
                        self.isPaymentProcessing.reject(response.message);
                        self.handleError(response);
                    }
                });

            },
            handleError: function (error) {
                if (_.isObject(error)) {
                    this.messageContainer.addErrorMessage(error);
                } else {
                    this.messageContainer.addErrorMessage({
                        message: error
                    });
                }
            }            
        });
    }
);
