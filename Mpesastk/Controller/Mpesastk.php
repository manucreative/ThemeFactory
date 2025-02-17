<?php

namespace ThemeFactory\Mpesastk\Controller;

use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;

abstract class Mpesastk extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface {

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

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \ThemeFactory\Mpesastk\Model\Payment\Payment $mpesastkModel
     * @param \ThemeFactory\Mpesastk\Helper\Mpesa $mpesaHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \ThemeFactory\Mpesastk\Model\Payment\Payment $mpesastkModel,
        \ThemeFactory\Mpesastk\Helper\Data $mpesaHelper,
        \ThemeFactory\Mpesastk\Model\StkpushFactory $stkpushFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_stkpushFactory = $stkpushFactory;

        $this->logger = $logger;
        $this->_mpesastkModel = $mpesastkModel;
        $this->_mpesaHelper = $mpesaHelper;
        $this->pageFactory = $pageFactory;

        parent::__construct($context);
    }

    /**
     * Cancel order, return quote to customer
     *
     * @param string $errorMsg
     * @return false|string
     */

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
    
    protected function _cancelPayment($errorMsg = '') {
        $gotoSection = false;
        $this->_mpesaHelper->cancelCurrentOrder($errorMsg);
        if ($this->_checkoutSession->restoreQuote()) {
            //Redirect to payment step
            $gotoSection = 'paymentMethod';
        }
        return $gotoSection;
    }

    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrderById($order_id) {
        $order=$this->_orderFactory->create()->load($order_id);
		return $order;
    }

    /**
     * Get order object
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function getOrder() {
        return $this->_orderFactory->create()->loadByIncrementId(
            $this->_checkoutSession->getLastRealOrderId()
        );
    }

    public function getMpesaTransId(){
        $order = $this->getOrder();
        $record = $this->_stkpushFactory->create()->load($order->getRealOrderId(), 'order_id');

        return $record->getTransId();
    }

    protected function getQuote() {
        if (!$this->_quote) {
            $this->_quote = $this->_getCheckoutSession()->getQuote();
        }
        return $this->_quote;
    }

    protected function getCheckoutSession() {
        return $this->_checkoutSession;
    }

    protected function getCustomerSession() {
        return $this->_customerSession;
    }

}