<?php

namespace Kananinj\Razorpay\Controller\Payment;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Controller\Result\JsonFactory;
use Kananinj\Razorpay\Model\PaymentTransactionFactory;
use Kananinj\Razorpay\Gateway\Config\Config;
use Razorpay\Api\Api;
use Psr\Log\LoggerInterface;


class Validate implements ActionInterface, CsrfAwareActionInterface {

    public function __construct(
        private RequestInterface $request,
        private SerializerInterface $serializer,
        private ManagerInterface $messageManager,
        private PaymentTransactionFactory $paymentTransactionFactory,
        private CheckoutSession $checkoutSession,
        private CustomerSession $customerSession,
        private CartManagementInterface $cartManagement,
        private CartRepositoryInterface $quote,
        private JsonFactory $resultJsonFactory,
        private LoggerInterface $logger,
        private Config $config
    ) {

    }
    


    public function execute() {

        $result = $this->resultJsonFactory->create();

        $params = $this->serializer->unserialize($this->request->getContent());
        
        $rzp_payment_id = $params['razorpay_payment_id'];
        $rzp_order_id = $params['razorpay_order_id'];

        $payment = $this->paymentTransactionFactory->create()->load($rzp_order_id,'payment_txn_id');
        if (!empty($payment->getOrderIncrementId())) {
            $msg = __('Order already placed with this online transaction.');
            $this->messageManager->addErrorMessage($msg);
            return $result->setData([
                'status' => '401',
                'message' => $msg
            ]);
        }
        $storeId = $payment->getStoreId(); 
                
        $rzp = new Api($this->config->getMerchantKey($storeId), $this->config->getMerchantSalt($storeId));

        try {
           
            $rzp->utility->verifyPaymentSignature($params);

           $quote = $this->quote->get($payment->getQuoteId());
           $quotePayment = $quote->getPayment();
           $paymentAdditionalInformation = $quotePayment->getAdditionalInformation();

            $paymentAdditionalInformation['txnid'] = $payment->getMerchantTxnId();
            $paymentAdditionalInformation['rzp_order_id'] = $payment->getPaymentTxnId(); //rzp order id
            $paymentAdditionalInformation['rzp_payment_id'] = $rzp_payment_id;
            $quotePayment->setAdditionalInformation($paymentAdditionalInformation);
            $quotePayment->save();

            if (!$this->customerSession->isLoggedIn()) {
                $quote->setCheckoutMethod($this->cartManagement::METHOD_GUEST);
                $quote->setCustomerEmail($this->customerSession->getCustomerEmailAddress());
            }

            try{
                $this->cartManagement->placeOrder($quote->getId());

                $msg = __('order processed successfully');
                $this->messageManager->addSuccessMessage($msg);
                return $result->setData([
                    'status' => 200,
                    'message' => $msg,
                ]);
            }catch(\Exception $e) {
                $msg = $e->getMessage();
                $this->messageManager->addErrorMessage($msg);
                return $result->setData([
                    'status' => 401,
                    'message' => $msg,
                ]);
            }
        }catch(\Razorpay\Api\Errors\Error $e) {
            $this->logger->critical("Validate: Razorpay Error message:" . $e->getMessage());

            $msg = $e->getMessage();
            $this->messageManager->addErrorMessage($msg);
            return $result->setData([
                'status' => 401,
                'message' => $msg,
            ]);
        }
    }

    /**
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }
}