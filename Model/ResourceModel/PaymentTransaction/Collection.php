<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Model\ResourceModel\PaymentTransaction;

/**
 * Description of Collection
 *
 * @author Kananinj
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * Initialization here
     *
     * @return void
     */
    public function _construct() {
        $this->_init(
            'Kananinj\Razorpay\Model\PaymentTransaction',
            'Kananinj\Razorpay\Model\ResourceModel\PaymentTransaction'
        );
    }

}
