<?php

namespace Kananinj\Razorpay\Cron;

use Razorpay\Api\Api;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Kananinj\Razorpay\Gateway\Config\Config;
use Kananinj\Razorpay\Model\ResourceModel\PaymentTransaction\CollectionFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\Status\HistoryFactory;
use Magento\Sales\Api\OrderRepositoryInterface;


class VerifiyOrders {
    
    /**
     * @var Api; 
     */
    private $rzp;

    public function __construct(
        private DateTime $dateTime,
        private Config $config,
        private OrderFactory $orderFactory,
        private HistoryFactory $orderStatusHistoryFactory,
        private OrderRepositoryInterface $orderRepository,
        private CollectionFactory $collectionFactory
    ) {
        
    }

    public function execute() {
        $toDate = date('Y-m-d H:i:s', strtotime($this->dateTime->date() . '-5 minutes'));
        $page = 1;

        do {
            $collections = $this->collectionFactory->create()
            ->addFieldToSelect(['id','store_id','quote_id','order_increment_id','merchant_txn_id','payment_txn_id','txn_status'])
            ->addFieldToFilter('created_at', ['lteq' => $toDate])
            ->addFieldToFilter('payment_method',['eq' => 'razorpay'])
            ->addFieldToFilter('order_increment_id', ['notnull' => true])
            ->addFieldToFilter('verified',['eq'=> 0])
            ->setCurPage($page)
            ->setPageSize(10);

            if($collections->count() == 0) {
                exit;
            }

            foreach ($collections as $key => $transaction) {
                $store_id = $transaction->getStoreId();

                if(!$this->config->getValue('active', $store_id)) {
                    continue;
                };

                $rzp_order_id = $transaction->getPaymentTxnId();

                $rzp = new Api($this->config->getMerchantKey($store_id), $this->config->getMerchantSalt($store_id));

                $rzp_order = $rzp->order->fetch($rzp_order_id);

                $comment = __("The payment for Razorpay Order ID %1 has been marked as '%2'.",[$rzp_order_id, strtoupper($rzp_order['status'])]);

                if (($rzp_order['status'] == 'paid' && $rzp_order['amount'] != $rzp_order['amount_paid'])) {
                    $collectable = $rzp_order['currency'].' '.$rzp_order['amount'];
                    $paid = $rzp_order['currency'].' '.$rzp_order['amount_paid'];
                    $comment .= __(' However, the collectable amount is %1, but only %2 has been received.',[$collectable, $paid]);
                }
                
                $order = $this->orderFactory->create()->loadByIncrementId($transaction->getOrderIncrementId());
                $statusHistory = $this->orderStatusHistoryFactory->create();
                $statusHistory->setComment($comment);
                $statusHistory->setEntityName(\Magento\Sales\Model\Order::ENTITY);
                $statusHistory->setStatus($order->getStatus());
                $statusHistory->setIsCustomerNotified(false)->setIsVisibleOnFront(false);
                $order->addStatusHistory($statusHistory);
                $this->orderRepository->save($order);

                $transaction->setTxnStatus($rzp_order['status']);
                $transaction->setVerified(1);
                $transaction->save();
            }
            $page++;
        } while($collections->count() > 0);
    }
}