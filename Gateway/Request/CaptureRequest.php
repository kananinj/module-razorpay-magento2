<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kananinj\Razorpay\Gateway\Request;

use Laminas\Http\Request;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Kananinj\Razorpay\Gateway\Config\Config;

class CaptureRequest implements BuilderInterface, RequestInterface {

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
            Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject) {

        if (!isset($buildSubject['payment']) || !$buildSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];

        $order = $paymentDO->getOrder();

        $payment = $paymentDO->getPayment();

        if (!$payment instanceof OrderPaymentInterface) {
            throw new \LogicException('Order payment should be provided.');
        }

        $paymentAdditionalInformation = $payment->getAdditionalInformation();

        $rzp_payment_id = $paymentAdditionalInformation['rzp_payment_id'];

        $requestData = [            
            'amount' => ($order->getGrandTotalAmount() * 100),
            'currency' => $order->getCurrencyCode()
        ];

        $store_id = $order->getStoreId();

        return [
            self::RZP_KEY_ID => $this->config->getMerchantKey($store_id),
            self::RZP_KEY_SECRET => $this->config->getMerchantSalt($store_id),
            self::BODY => $requestData, 
            self::METHOD => Request::METHOD_POST,
            self::URL => 'https://api.razorpay.com/v1/payments/'.$rzp_payment_id.'/capture'
        ];
    }

}

