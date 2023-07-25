<?php

namespace ThemeFactory\MemoDataApi\Api;

interface CreditMemoDataInterface
{
    /**
     * Get invoice number by order number
     *
     * @param int $creditMemoNumber
     * @return string|null
     */
    public function getCreditMemoDataByCreditMemoNumber($creditMemoNumber);
}
