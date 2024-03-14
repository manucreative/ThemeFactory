<?php
namespace ThemeFactory\ExportInvoices\Model;

use Magento\Framework\Model\AbstractModel;

class Invoice extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\ThemeFactory\ExportInvoices\Model\ResourceModel\Invoice::class);
    }

      public function getInvoiceDataById($invoiceId)
    {
        $this->load($invoiceId);
        return $this->getData();
    }
}
