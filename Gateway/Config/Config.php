<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Gateway\Config;

use Magento\Payment\Model\MethodInterface;

/**
 * Description of Config
 *
 * @author Kananinj
 */
class Config extends \Magento\Payment\Gateway\Config\Config {
    
    public function getMerchantKey($storeId = null) {
        return $this->getValue('merchant_key', $storeId);
    }

    public function getMerchantSalt($storeId = null) {
        return $this->getValue('merchant_salt', $storeId);
    }
}