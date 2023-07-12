<?php

namespace ThemeFactory\QrPrint\Api;

interface InvoiceDataInterface
{
    /**
     * Get invoice data
     *
     * @param string $invoiceId
     * @return \Vendor\Module\Api\Data\Magento\InvoiceDataInterface
     */
    public function getInvoiceData($invoiceId);
}
