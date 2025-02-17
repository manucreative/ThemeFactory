<?php
namespace ThemeFactory\Mpesastk\Model\Payment;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;

class Payment extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Payment code
     *
     * @var string
     */
    const PAYMENT_MPESASTK_PUSH_CODE = 'themeFactory_mpesastk';

    protected $_code = self::PAYMENT_MPESASTK_PUSH_CODE;
    // protected $_order;
    // protected $orderSender;

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;
    
    // public function __construct(
    //     OrderSender $orderSender,
    //     \Magento\Payment\Model\Method\Logger $logger,
    //     \Magento\Checkout\Model\Session $checkoutSession
    // ){
    //     $this->orderSender = $orderSender;
    //     $this->_checkoutSession = $checkoutSession;

    //     }

    //     private function notifyOrder() 
    //     {
    //     $this->orderSender->send($this->_order);
    //     $this->order->addStatusHistoryComment('Customer email sent')->setIsCustomerNotified(true)->save();
    //     }

    //  public function getCheckoutSession() 
    //     {
    //     return $this->_checkoutSession;
    //     }
}