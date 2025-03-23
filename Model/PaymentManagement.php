<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Model;


use Razorpay\Api\Api;
use Kananinj\Razorpay\Gateway\Config\Config as RazorpayConfig;
use Kananinj\Razorpay\Model\Ui\ConfigProvider;
use Kananinj\Razorpay\Api\PaymentManagementInterface;
use Kananinj\Razorpay\Model\PaymentTransactionFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Psr\Log\LoggerInterface;

/**
 * Description of PaymentManagement
 *
 * @author Kananinj
 */
class PaymentManagement implements PaymentManagementInterface {

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var QuoteIdMaskFactory 
     */
    protected $quoteIdMaskFactory;

    /**
     * @var RazorpayConfig
     */
    protected $config;

    /**
     * @var PaymentTransactionFactory
     */
    protected $paymentTransactionFactory;

    private $rzp;

    public function __construct(
            CartRepositoryInterface $quoteRepository,
            QuoteIdMaskFactory $quoteIdMaskFactory,
            RazorpayConfig $config,
            PaymentTransactionFactory $paymentTransactionFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->config = $config;
        $this->paymentTransactionFactory = $paymentTransactionFactory;        
    }

    public function customerCart($customerId) {
        try {
            $quote = $this->quoteRepository->getActiveForCustomer($customerId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw $e;
        }

        try {
            return $this->transaction_reference($quote);
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    public function guestCart($cartId) {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        try {
            $quote = $this->quoteRepository->getActive($quoteIdMask->getQuoteId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                    __('Guest cart not found.')
            );
        }
        try {
            return $this->transaction_reference($quote);
        } catch (\Exception $exc) {
            throw $exc;
        }
    }

    private function transaction_reference($quote) {

        if (!$quote->isVirtual()) {
            $shipping_address = $quote->getShippingAddress();
            // check if shipping address is set
            if ($shipping_address->getCountryId() === null) {
                throw new \Magento\Framework\Exception\State\InvalidTransitionException(
                        __('The shipping address is missing. Set the address and try again.')
                );
            }
        }

        $storeId = $quote->getStoreId();
        $billing_address = $quote->getBillingAddress();

        $this->rzp = new Api($this->config->getMerchantKey($storeId), $this->config->getMerchantSalt($storeId));

        try {

            //@todo currency validation

            $merchant_txn_id = date('YmdHms') . sprintf("%03d", $quote->getId());
            $transaction_data = [
                'store_id' => $quote->getStoreId(),
                'quote_id' => $quote->getId(),
                'payment_method' => ConfigProvider::CODE,
                'merchant_key' => $this->config->getMerchantKey($storeId),
                'merchant_txn_id' => $merchant_txn_id,
                'amount' => $quote->getBaseGrandTotal(),
                'txn_status' => 'pending',
                'customer_name' => $billing_address->getFirstname() . ' ' . $billing_address->getLastName(),
                'customer_email' => $billing_address->getEmail(),
                'customer_mobile' => $billing_address->getTelephone(),
                'currency' => $quote->getBaseCurrencyCode()
            ];


            $rzpOrder = $this->rzp->order->create([
                'amount' => ($quote->getBaseGrandTotal() * 100),
                'currency' => $quote->getBaseCurrencyCode(),
                'payment_capture' => 0, //default authorize
                'notes' => [
                    'referrer'  => (isset($_SERVER['HTTP_REFERER']) === true) ? $_SERVER['HTTP_REFERER'] : null,
                    'quote_id' => $quote->getId(),
                    'merchant_txn_id' => $merchant_txn_id,
                ]
            ]);
            $transaction_data['payment_txn_id'] = $rzpOrder->id;
            $transaction = $this->paymentTransactionFactory->create()->addData($transaction_data)->save();

            $transaction->setEmbeddedUrl(Api::getFullUrl("checkout/embedded"));

            return $transaction;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw $e;
        }
    }

}