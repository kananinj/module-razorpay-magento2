<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Block;

use Magento\Payment\Block\ConfigurableInfo;

/**
 * Description of Info
 *
 * @author Kananinj
 */
class Info extends ConfigurableInfo {

    /**
     * Returns label
     *
     * @param string $field
     * @return Phrase
     */
    protected function getLabel($field) {
        return __($field);
    }

}