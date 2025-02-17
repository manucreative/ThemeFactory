<?php
namespace ThemeFactory\Mpesastk\Observer\Payment;

use \ThemeFactory\Mpesastk\Model\Stkpush;

class Mpesa implements \Magento\Framework\Event\ObserverInterface
{
    protected $_stkpus;
    protected $_stkpushFactory;

    public function __construct(
        Stkpush $_stkpus,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \ThemeFactory\Mpesastk\Model\StkpushFactory $stkpushFactory,
        \Magento\Framework\DB\Transaction $transaction)
    {
        $this->__stkpus = $_stkpus;
        $this->_invoiceService = $invoiceService;
        $this->_stkpushFactory = $stkpushFactory;
        $this->_transaction = $transaction;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $order = $observer->getData('order');

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/ObserverData.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

       

        if($order->getPayment()->getMethod()=='themeFactory_mpesastk') {

            $logger->info('Order_id: ' . print_r($order->getIncrementId(), true));

            $record = $this->_stkpushFactory->create()->load($order->getQuoteId(), 'account_id');

             $logger->info('Record: ' . print_r($record->getResultCode(), true));

            $record->setTransAmount($order->getGrandTotal());
            $record->setOrderId($order->getIncrementId());
            $record->save();

            $order->setState('processing');
            $order->setStatus('processing');
            $order->save();


        }
    }
}
