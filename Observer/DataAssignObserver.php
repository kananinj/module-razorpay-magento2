<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Payment\Model\Method\Logger;

/**
 * Description of DataAssignObserver
 *
 * @author Kananinj
 */
class DataAssignObserver extends AbstractDataAssignObserver {

    const RAZORPAY_ORDER_ID = 'rzp_order_id';

    const RAZORPAY_PAYMENT_ID = 'rzp_payment_id';

    const MERCHANT_TXN_ID = 'txnid';

    /**
     * @var array
     */
    protected $additionalInformationList = [
        self::RAZORPAY_ORDER_ID,
        self::RAZORPAY_PAYMENT_ID,
        self::MERCHANT_TXN_ID
    ];

    public function execute(Observer $observer) {

        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);

        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                        $additionalInformationKey,
                        $additionalData[$additionalInformationKey]
                );
            }
        }
    }

}
