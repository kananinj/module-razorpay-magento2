/**
 * Copyright © Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *  http://aws.amazon.com/apache2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'mage/url',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/action/set-payment-information',
    ],
    function (quote, urlBuilder, storage, url, errorProcessor, customer, fullScreenLoader, setPaymentInformationAction) {
        'use strict';

        return function (razorpay, redirectOnSuccess) {
            var serviceUrl, payload, paymentData;

            redirectOnSuccess = redirectOnSuccess !== false;
            
            paymentData = razorpay.getData();

            /** Checkout for guest and registered customer. */
            if (!customer.isLoggedIn()) {
                serviceUrl = urlBuilder.createUrl('/guest-carts/:quoteId/razorpay-payment-reference', {
                    quoteId: quote.getQuoteId()
                });

            } else {
                serviceUrl = urlBuilder.createUrl('/carts/mine/razorpay-payment-reference', {});
            }

            fullScreenLoader.startLoader();

            storage.post(
                    serviceUrl,
                    JSON.stringify(payload)
                    ).done(
                    function (transaction) {
                        
                        console.log("paymentData",paymentData);

                        paymentData.additionalData.txnid = transaction.merchant_txn_id;
                        paymentData.additionalData.rzp_order_id = transaction.payment_txn_id;

                        setPaymentInformationAction(this.messageContainer, paymentData).done(function (response) {
                            if (response === true) {

                                console.log("transaction",transaction);

                                this.merchant_order_id = transaction.quote_id;

                                var options = {
                                    key: transaction.merchant_key,
                                    name: paymentData.additionalData.merchant_name, //fixed
                                    amount: transaction.amount,
                                    timeout: 720,
                                    handler: function (data) {
                                        // @TODO 
                                        console.log("handle data",data);
                                        razorpay.isPlaceOrderActionAllowed(true);
                                        razorpay.validateOrder(data);
                                    },
                                    order_id: transaction.payment_txn_id,
                                    modal: {
                                        ondismiss: function(transaction) {
                                            console.log("dissmiss payment");
                                            //reset the cart
                                           // self.resetCart(transaction);
                                           fullScreenLoader.stopLoader();
                                           razorpay.isPaymentProcessing.reject("Payment Closed");
                                           razorpay.isPlaceOrderActionAllowed(true);
                                        }
                                    },
                                    notes: {
                                        merchant_order_id: transaction.quote_id,
                                    },
                                    prefill: {
                                        name: transaction.customer_name,
                                        contact: transaction.customer_mobile,
                                        email: transaction.customer_email
                                    },
                                    _: {
                                        integration: 'magento',
                                        integration_type: 'plugin',
                                    }
                                };

                                if (transaction.currency !== 'INR')
                                {
                                    options.display_currency = transaction.currency;
                                    options.display_amount = transaction.amount;
                                }

                                console.log("options",options);
                                this.rzp = new Razorpay(options);
                                this.rzp.open();
                                fullScreenLoader.stopLoader(true);
                            }
                        }).fail(function (response) {
                            errorProcessor.process(response);
                            console.log(response);
                            razorpay.isPlaceOrderActionAllowed(true);
                            fullScreenLoader.stopLoader(true);
                        });
                    }
            ).fail(function (transaction) {
                errorProcessor.process(transaction);
                razorpay.isPlaceOrderActionAllowed(true);
                console.log(transaction);
                fullScreenLoader.stopLoader(true);
            });
        };
    }
);