<?php

namespace ThemeFactory\QrCreditmemo\Api;

interface CreditMemoInterface
{
    /**
     * Get invoice number by order number
     *
     * @param int $orderNumber
     * @return string|null
     */
    public function getCreditMemoNumberByOrderNumber($orderNumber);
}
