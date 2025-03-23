<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Kananinj\Razorpay\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Kananinj\Razorpay\Gateway\Config\Config;
use Magento\Payment\Model\Method\Logger;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

/**
 * Description of ConfigProvider
 *
 * @author Kananinj
 */
class ConfigProvider implements ConfigProviderInterface {

    const CODE = 'razorpay';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     *
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * it store id
     * @var int
     */
    private $storeId;

    public function __construct(
        Config $config, 
        Logger $logger, 
        StoreManagerInterface $storeManager, 
        UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
    }

    public function getConfig() {

        $this->storeId = $this->storeManager->getStore()->getId();

        return [
            'payment' => [
                self::CODE => [
                    'code' => self::CODE,
                    'title' => $this->config->getValue('title', $this->storeId),
                    'isActive' => $this->config->getValue('active', $this->storeId),
                    'merchant_name' => $this->config->getValue('merchant_name', $this->storeId),
                ]
            ]
        ];
    }
}
