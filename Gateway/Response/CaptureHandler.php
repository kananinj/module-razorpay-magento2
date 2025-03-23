<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kananinj\Razorpay\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Kananinj\Razorpay\Model\PaymentTransactionFactory;

class CaptureHandler implements HandlerInterface {

    /**
     * List of additional details
     * @var array
     */
    protected $additionalInformationMapping = [
        'status',
        'method',        
        'card_id',
        'bank',
        'vpa',
        'wallet'
    ];

   
    /**
     * @var PaymentTransactionFactory
     */
    protected $paymentTransactionFactory;

    /**
     * 
     * @param PaymentTransactionFactory $paymentTransactionFactory
     */
    public function __construct(PaymentTransactionFactory $paymentTransactionFactory) {
        $this->paymentTransactionFactory = $paymentTransactionFactory;
    }

    /**
     * Handles transaction id
     *
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response) {

        if (!isset($handlingSubject['payment']) || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }


        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];

        $order = $paymentDO->getOrder();
        $payment = $paymentDO->getPayment();


        if($response['method'] == 'card') {
            /** @var $payment \Magento\Sales\Model\Order\Payment */
            $payment->setCcTransId($response['card_id']);
            $payment->setCcType($response['card']['type']);
            $payment->setCcLast4($response['card']['last4']);            
        }

        $payment->setLastTransId($response['order_id']);
        $payment->setTransactionId($response['order_id']);
        $payment->setIsTransactionClosed(true);

        $payment->setAdditionalInformation('amount', (float) ($response['amount'] / 100));

        foreach ($this->additionalInformationMapping as $item) {
            if (!isset($response[$item])) {
                continue;
            }
            $payment->setAdditionalInformation($item, $response[$item]);
        }

        $transaction = $this->paymentTransactionFactory->create()->load($response['order_id'], 'payment_txn_id');
        $transaction->setOrderIncrementId($order->getOrderIncrementId());
        $transaction->save();
    }

}
