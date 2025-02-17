<?php

namespace ThemeFactory\Mpesastk\Controller\Stkpush;
use Magento\Framework\Controller\ResultFactory;


class Complete extends \Magento\Framework\App\Action\Action {


    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote = false;

    protected $_mpesastkModel;

    protected $_mpesaHelper;

    protected $_stkpushFactory;
    
     protected $pageFactory;

public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \ThemeFactory\Mpesastk\Model\Payment\Payment $mpesastkModel,
        \ThemeFactory\Mpesastk\Helper\Data $mpesaHelper,
        \ThemeFactory\Mpesastk\Model\StkpushFactory $stkpushFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_stkpushFactory = $stkpushFactory;
        $this->_coreRegistry = $coreRegistry;

        $this->logger = $logger;
        $this->_mpesastkModel = $mpesastkModel;
        $this->_mpesaHelper = $mpesaHelper;
        $this->pageFactory = $pageFactory;

        parent::__construct($context);
    }

    public function execute() {

$writer = new \Zend_Log_Writer_Stream(BP . '/var/log/CompleteLog.log');
    $logger = new \Zend_Log();
    $logger->addWriter($writer);

    $order_id = $this->getRequest()->getParam('coid');
    $order = $this->getOrder();
    $price = round($order->getGrandTotal(), 2);

    $this->_coreRegistry->register('transId', 21);
    $this->_coreRegistry->register('custom_parameter', 215456325);
    $this->_coreRegistry->register('custom_parameter1', 250);

    $page = $this->pageFactory->create();
    $page->getConfig()->getTitle()->set('Mpesa payment completed');
    $block = $page->getLayout()->getBlock('payment_display');
        if ($block) {
            $block->setData('topic', 'Thank you for shopping with us. Note that your payment has been paid through "Lipa na Mpesa stk Push"');
        }
        return $page;

    // $block = $pageFactory->getLayout()
    //     ->createBlock('ThemeFactory\Mpesastk\Block\CompletePayment')
    //     ->setTemplate('ThemeFactory_Mpesastk::completePay.phtml');

    // Add the block to the layout
    // $layout = $this->pageFactory->create()->getLayout();
    // $layout->setChild('content', $block);

    // Return the page
   
        
    //      $block = $this->pageFactory->create()->getLayout()->createBlock(
    //         \ThemeFactory\Mpesastk\Block\CompletePayment::class, 'payment_info'
    //     );

    //   $block->setOrderId(215456325)
    // ->setOrderPrice(250)
    // ->setTransId(21);
    //     $logger->info('OrderId:- ' . $order->getRealOrderId(), true);

    //     $resultPage = $this->pageFactory->create();
    //     $resultPage->getLayout()
    //         ->setTemplate('ThemeFactory_Mpesastk::completePay.phtml')
    //         ->toHtml();
    //     $resultPage->getLayout()->setChild('content', 'payment_info', $block);

        // $block->setData('custom_parameter', 215456325);
        // $block->setData('custom_parameter1', 250);
        // $block->setData('transId', 21);
        // $block->setData('orderId', $order->getRealOrderId());
		// $block->setData('orderPrice', $price);
        // $block->setData('transId', $this->getMpesaTransId());
        // Add an alias for the block


    
        
        // return $resultPage;
    }

    protected function getOrder() {
        return $this->_orderFactory->create()->loadByIncrementId(
            $this->_checkoutSession->getLastRealOrderId()
        );
    }

}