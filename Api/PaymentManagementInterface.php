<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Api;

/**
 * @api
 * @author Kananinj
 */
interface PaymentManagementInterface {

    /**
     * @param string $return_url
     * @param string $cartId The guest cart ID.
     * @return Kananinj\Razorpay\Api\Data\RazorpayTransactionInterface
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException     
     */
    public function guestCart($cartId);

    /**
     * @param string $customerId The customer id.
     * @return Kananinj\Razorpay\Api\Data\RazorpayTransactionInterface
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException     
     */
    public function customerCart($customerId);
}
