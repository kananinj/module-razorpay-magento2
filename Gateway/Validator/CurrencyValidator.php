<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Gateway\Validator;

use Magento\Payment\Gateway\ConfigInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

/**
 * Description of CurrencyValidator
 *
 * @author Kananinj
 */
class CurrencyValidator extends \Magento\Payment\Gateway\Validator\AbstractValidator {

    /**
     * @var \Magento\Payment\Gateway\ConfigInterface
     */
    private $config;

    /**
     * @var Allow Currencies 
     */
    private $_allowCurrencies = 'INR';

    /**
     * @param ResultInterfaceFactory $resultFactory
     * @param \Magento\Payment\Gateway\ConfigInterface $config
     */
    public function __construct(
            ResultInterfaceFactory $resultFactory,
            ConfigInterface $config
    ) {
        $this->config = $config;
        parent::__construct($resultFactory);
    }

    public function validate(array $validationSubject) {

        $isValid = in_array($validationSubject['currency'], explode(',', $this->_allowCurrencies));

        return $this->createResult($isValid);
    }

}
