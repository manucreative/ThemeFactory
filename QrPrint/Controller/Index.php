<?php

namespace ThemeFactory\QrPrint\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use ThemeFactory\QrPrint\Api\InvoiceDataInterface;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\InvoiceRepositoryInterface;

class GetInvoiceData extends Action
{
    private $invoiceRepository;
    private $resultJsonFactory;
    protected $invoiceInterface;

    public function __construct(
        Context $context,
        InvoiceRepositoryInterface $invoiceRepository,
        InvoiceDataInterface $invoiceInterface,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->invoiceRepository = $invoiceRepository;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->invoiceInterface = $invoiceInterface;
    }

    public function execute()
    {
        $invoiceId = $this->getRequest()->getParam('transaction_id');
        $invoiceData = $this->invoiceInterface->getInvoiceData($invoiceId);
        // $invoice = $this->invoiceRepository->getByTransactionId($transactionId);
        // $invoiceData = $invoice->getData();
        $result = $this->resultJsonFactory->create();
        return $result->setData($invoiceData);
    }
}
