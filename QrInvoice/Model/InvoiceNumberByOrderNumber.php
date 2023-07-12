<?php

namespace ThemeFactory\QrInvoice\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class InvoiceNumberByOrderNumber
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    /**
     * Constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param InvoiceRepositoryInterface $invoiceRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        InvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * Retrieve invoice number by order number.
     *
     * @param string $orderNumber
     * @return string
     * @throws LocalizedException
     */
    public function getInvoiceNumberByOrderNumber($orderNumber)
    {
        try {
            $order = $this->orderRepository->get($orderNumber);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Order not found'));
        }

        $invoiceCollection = $order->getInvoiceCollection();
        if ($invoiceCollection->getSize() > 0) {
            foreach ($invoiceCollection as $invoice) {
                return $invoice->getIncrementId();
            }
        }
        return "There's no invoice for this order";
    }
}
