<?php

namespace ThemeFactory\GetInvoice\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as InvoiceCollectionFactory;
use Magento\Sales\Model\OrderFactory;

class Invoice extends AbstractModel
{
    protected $invoiceFactory;

    protected $invoiceCollectionFactory;

    protected $orderFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        InvoiceFactory $invoiceFactory,
        InvoiceCollectionFactory $invoiceCollectionFactory,
        OrderFactory $orderFactory
    ) {
        $this->invoiceFactory = $invoiceFactory;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->orderFactory = $orderFactory;
        parent::__construct($context, $registry);
    }

    // public function getInvoiceNumberByOrderNumber($orderNumber)
    // {
    //     $invoiceCollection = $this->invoiceCollectionFactory->create();
    //     // var_dump($invoiceCollection->getData());
    //     var_dump($orderNumber); //Let me make this test change
    //     $invoiceCollection->addFieldToFilter('order_id', $orderNumber);
    //     var_dump($invoiceCollection->getData());
    //     // $invoiceCollection->addFieldToFilter('increment_id', $orderNumber);
    //     $invoice = $invoiceCollection->getFirstItem();
    //     // var_dump($invoice->getData());

    //     if ($invoice && $invoice->getId()) {
    //         return $invoice->getIncrementId();
    //     }
    //     return null;
    // }

    public function getInvoiceNumberByOrderNumber($orderNumber)
    {
        $order = $this->orderFactory->create()->loadByIncrementId($orderNumber);
        if ($order && $order->getId()) {
            $order_id = $order->getId();
            $invoiceCollection = $this->invoiceCollectionFactory->create();
            $invoiceCollection->addFieldToFilter('order_id', $order_id);
            $invoice = $invoiceCollection->getFirstItem();
            if ($invoice && $invoice->getId()) {
                return $invoice->getIncrementId();
            }
        }
        return null;
    }
}