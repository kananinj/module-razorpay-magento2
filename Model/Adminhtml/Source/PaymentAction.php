<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Kananinj\Razorpay\Model\Adminhtml\Source;

use Magento\Payment\Model\MethodInterface;

/**
 * Class PaymentAction
 * 
 * @author Kananinj
 */
class PaymentAction implements \Magento\Framework\Data\OptionSourceInterface {

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() {
        return [
            [
                'value' => MethodInterface::ACTION_AUTHORIZE_CAPTURE,
                'label' => __('Authorize & Capture')
            ]
        ];
    }
}
