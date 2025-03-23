<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Model\ResourceModel;

/**
 * Description of PaymentTransaction
 *
 * @author Kananinj
 */
class PaymentTransaction extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected function _construct(): void {
        $this->_init('Kananinj_payment_transaction', 'id');
    }

}
