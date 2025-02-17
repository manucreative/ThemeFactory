<?php

namespace ThemeFactory\Mpesastk\Controller\Stkpush;

use \Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;
use \Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use \ThemeFactory\Mpesastk\Model\Stkpush;

class PaymentConfirmation extends \Magento\Framework\App\Action\Action
{

    protected $_stkpush;
    protected $_mpesaFactory;
    protected $cart;
    protected $_myHelper;
    protected $catalogSession;
    protected $checkoutSession;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Stkpush $stkpush,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \ThemeFactory\Mpesastk\Helper\Data $myHelper
    )
    {
        $this->_resultPageFactory = $resultPageFactory;
        $this->cart = $cart;
        $this->_stkpush = $stkpush;
        $this->_myHelper = $myHelper;
        $this->catalogSession = $catalogSession;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    public function execute()
    {

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/ConfirmationLog.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        $amount = $this->cart->getQuote()->getGrandTotal();
        $ref = $this->cart->getQuote()->getId();

        $merchant_r_id = $this->getRequest()->getParam('merchant_r_id');
        $checkout_r_id = $this->getRequest()->getParam('checkout_r_id');
        $account_id = $this->getRequest()->getParam('account_id');



        $code = null;
        $success = false;
        $message = 'Waiting for transaction';

        $collection = $this->_stkpush->getCollection()
            ->addFieldToFilter('merchant_request_id', ['eq' => $merchant_r_id])
            ->addFieldToFilter('checkout_request_id', ['eq' => $checkout_r_id])
            ->addFieldToFilter('result_desc', ['neq' => null]) 
            ->addFieldToFilter('result_desc', ['neq' => '']);  

        // Check if any records are found
        if ($collection->getSize() > 0) {
            foreach ($collection as $record) {
                if ($record->getResultCode() !== null) {
                    $success = true;
                    $code = $record->getResultCode();
                    $logger->info('Code: ' . print_r($code, true));

                    if (!empty($record->getResultDesc())) {
                        $message = $record->getResultDesc();
                    } else {
                        $message = 'Waiting for transaction';
                    }

                    if($record->getResultCode() == 0){
                         $record->setAccountId($account_id);
                        $record->save();
                    }

                    $record->setStatus(1);
                    $record->save();
                }
            }
        }

echo json_encode(['success' => $success, 'message' => $message, 'code' => $code]);

    }
}