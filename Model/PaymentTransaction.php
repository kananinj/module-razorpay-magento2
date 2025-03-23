<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Model;

/**
 * Description of Payu Transaction
 *
 * @author Kananinj
 */
class PaymentTransaction extends \Magento\Framework\Model\AbstractModel {

    protected function _construct() {
        $this->_init('Kananinj\Razorpay\Model\ResourceModel\PaymentTransaction');
    }

}
