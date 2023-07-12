<?php

namespace ThemeFactory\QrInvoice\Api;

interface InvoiceNumberByOrderNumberInterface
{
    /**
     * Get invoice number by order number
     *
     * @param int $orderNumber
     * @return string|null
     */
    public function getInvoiceNumberByOrderNumber($orderNumber);
}
