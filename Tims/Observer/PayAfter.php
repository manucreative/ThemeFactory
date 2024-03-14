<?php

namespace ThemeFactory\Tims\Observer;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Json\Decoder;
use ThemeFactory\Tims\Tremol\FP;
use ThemeFactory\Tims\Tremol\FP_Core;

class PayAfter // implements ObserverInterface
{
    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     * CurlFactory
     */
    protected $_curlFactory;
    protected $_newJsonDecoder;

    protected $_request;
    protected $_customerRepositoryInterface; //

    public function __construct(
        RequestInterface $request,
        OrderInterface $NewOrder,
        Invoice $invoice,
        CurlFactory $curlFactory,
        Decoder $newJsonDecoder,
        FP_Core $fp,
        FP $fpLibrary,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface //
    ) {
        $this->_curlFactory = $curlFactory;
        $this->_request = $request;
        $this->NewOrder = $NewOrder;
        $this->NewInvoice = $invoice;
        $this->_newJsonDecoder = $newJsonDecoder;
        $this->FP = $fpLibrary;
        $this->fp = $fp;
        $this->_customerRepositoryInterface = $customerRepositoryInterface; //

    }

    /**
     * Undocumented function
     *
     * @param EventObserver $observer
     * @return int QtyToInvoiceAfterPay;
     */
    public function sales_order_invoice_save_before(EventObserver $observer)
    {
        // // Get invoice data new (Working)

        $Order = $observer->getData('invoice');
        $orderDetails = $Order->getOrder();

        //Get the order number
        $orderid = $orderDetails->getIncrementId();

        //Get invoice id from order increament id

        $orderNewDetails = $this->NewOrder->load($orderid);
        //$orderDetails = $this->NewOrder->loadByIncrementId($orderid);

        //if order has invoice
        if ($orderNewDetails->hasInvoices()) {

            foreach ($orderNewDetails->getInvoiceCollection() as $invoice) {
                $invoiceIncrementID = $invoice->getIncrementId();

                foreach ($invoice->getAllItems() as $NewItems) { // get all the invoiced items of the particular invoice
                    $invItems =  $NewItems->getQty();
                }
            }

            $TraderSystemInvNum = $invoiceIncrementID;
        } else {

            $TraderSystemInvNum = $orderNewDetails->getIncrementId();
        }

        //Get Billing Information/Company information

        $BillingInformation = $orderNewDetails->getBillingAddress();


        $OrderItems = $orderNewDetails->getAllItems();
        // $OrderItems = $orderNewDetails->getInvoiceCollection();
        foreach ($OrderItems as $item) {

            //Begining of get quantity code     

            // Get Values After
            $QtyToInvoiceAfterPay =  $item->getQtyToInvoice(); // get the invoiced quantity for each item

            return $QtyToInvoiceAfterPay;
        }
    }
}