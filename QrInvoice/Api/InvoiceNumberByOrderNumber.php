<?php

namespace ThemeFactory\QrInvoice\Api;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class InvoiceNumberByOrderNumber implements InvoiceNumberByOrderNumberInterface
{
    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var InvoiceRepositoryInterface
     */
    protected $invoiceRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        InvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceNumberByOrderNumber($orderNumber)
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->get($orderNumber);

        if (!$order) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Order not found'));
        }

        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->getByOrderId($order->getEntityId());

        if (!$invoice) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Invoice not found'));
        }

        return $invoice->getIncrementId();
    }
}