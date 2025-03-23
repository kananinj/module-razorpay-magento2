<?php

namespace Kananinj\Razorpay\Model\Adminhtml\Source;

/**
 * Class Environment
 * 
 * @author Kananinj
 */
class Environment implements \Magento\Framework\Data\OptionSourceInterface {

    const SANDBOX = 'sandbox';
    const PRODUCTION = 'production';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray() {
        return [
            [
                'value' => self::SANDBOX,
                'label' => __('Testing')
            ],
            [
                'value' => self::PRODUCTION,
                'label' => __('Production')
            ]
        ];
    }

}
