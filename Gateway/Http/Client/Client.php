<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\HTTP\LaminasClientFactory;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Description of Client
 *
 * @author Kananinj
 */
class Client implements ClientInterface {


    /**
     * @var LaminasClientFactory
     */
    private $_httpClientFactory;

    /**
     * @var SerializerInterface
     */
    private $serialize;

    public function __construct(
        LaminasClientFactory $httpClientFactory,
        SerializerInterface $serialize
    ) {
        $this->_httpClientFactory = $httpClientFactory;
        $this->serialize = $serialize;
    }

    public function placeRequest(TransferInterface $transferObject) {

        $client = $this->_httpClientFactory->create();
        $client->setOptions(['adapter' => \Laminas\Http\Client\Adapter\Curl::class])    ;
        $client->setUri($transferObject->getUri());
        $client->setMethod($transferObject->getMethod());
        $client->setHeaders($transferObject->getHeaders());
        $client->setRawBody($this->serialize->serialize($transferObject->getBody())); //json

        $response = $client->send();

        // if ($response->getStatusCode() != 200) {
        //     throw new \InvalidArgumentException('Something went wrong on payment gateway');
        // }

        return $this->serialize->unserialize($response->getBody());
    }

}
