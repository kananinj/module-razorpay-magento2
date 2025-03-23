<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Api\Data;

/**
 * @author Kananinj
 */
interface RazorpayTransactionInterface {

    const ID = 'id';
    const STORE_ID = 'store_id';
    const QUOTE_ID = 'quote_id';
    const ORDER_ID = 'order_id';
    const MERCHANT_KEY = 'merchant_key';
    const MERCHANT_TXN_ID = 'merchant_txn_id';
    const PAYMENT_TXN_ID = 'payment_txn_id';
    const PAYMENT_MODE = 'payment_mode';
    const TXN_STATUS = 'txn_status';
    const AMOUNT = 'amount';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_EMAIL = 'customer_email';
    const CUSTOMER_MOBILE = 'customer_mobile';

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return $this
     */
    public function setStoreId($id);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $id
     * @return $this
     */
    public function setQuoteId($id);

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @param int $id
     * @return $this
     */
    public function setOrderId($id);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param string $id
     * @return $this
     */
    public function setMerchantKey($id);

    /**
     * @return string
     */
    public function getMerchantKey();

    /**
     * @param string $id
     * @return $this
     */
    public function setMerchantTxnId($id);

    /**
     * @return string
     */
    public function getMerchantTxnId();

    /**
     * @param string $id
     * @return $this
     */
    public function setPaymentTxnId($id);

    /**
     * @return string
     */
    public function getPaymentTxnId();

    /**
     * @param string $mode
     * @return $this
     */
    public function setPaymentMode($mode);

    /**
     * @return string
     */
    public function getPaymentMode();

    /**
     * @param string $status
     * @return $this
     */
    public function setTxnStatus($status);

    /**
     * @return string
     */
    public function getTxnStatus();

    /**
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param string $name
     * @return $this
     */
    public function setCustomerName($name);

    /**
     * @return string
     */
    public function getCustomerName();

    /**
     * @param string $email
     * @return $this
     */
    public function setCustomerEmail($email);

    /**
     * @return string
     */
    public function getCustomerEmail();

    /**
     * @param string $mobile
     * @return $this
     */
    public function setCustomerMobile($mobile);

    /**
     * @return string
     */
    public function getCustomerMobile();

    /**
     * @return string
     */
    public function getEmbeddedUrl();

    /**
     * @return string
     */
    public function getBrandLogo();

    /**
     * @return string
     */
    public function getCurrency();
}
