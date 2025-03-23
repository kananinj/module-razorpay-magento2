<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Gateway\Http;

use Laminas\Http\Request;
use Magento\Payment\Gateway\Http\TransferBuilder;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Kananinj\Razorpay\Gateway\Request\RequestInterface;

/**
 * Description of TransferFactory
 *
 * @author Kananinj
 */
class TransferFactory implements TransferFactoryInterface, RequestInterface {

    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @param TransferBuilder $transferBuilder
     */
    public function __construct(
            TransferBuilder $transferBuilder
    ) {
        $this->transferBuilder = $transferBuilder;
    }

    public function create(array $request) {

        return $this->transferBuilder
                        ->setBody($request[self::BODY])
                        ->setMethod($request[self::METHOD])
                        ->setUri($request[self::URL])
                        ->setHeaders([
                            'Authorization' => 'Basic ' . base64_encode($request[self::RZP_KEY_ID] . ':' .$request[self::RZP_KEY_SECRET]),
                            'Content-Type' => 'application/json',
                            'Accept' => 'application/json'
                        ])
                        ->build();
    }

}
