<?php

namespace Kananinj\Razorpay\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;

class ResponseCodeValidator extends AbstractValidator {

    /**
     * Performs validation of result code
     *
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject) {

        if (!isset($validationSubject['response']) || !is_array($validationSubject['response'])) {
            return $this->createResult(false, [__('Response does not exist')]);
        }

        $response = $validationSubject['response'];

        if (array_key_exists('status', $response) &&  (string) $response['status'] === 'captured') {
            return $this->createResult(true, []);
        }
        
        return $this->createResult(false, [$response['error']['description']],[$response['error']['code']]);
    }

}

