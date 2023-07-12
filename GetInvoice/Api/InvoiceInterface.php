<?php

namespace ThemeFactory\GetInvoice\Api;

interface InvoiceInterface
{
    /**
     * Get invoice number by order number
     *
     * @param string $orderNumber
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getInvoiceNumberByOrderNumber($orderNumber);
}