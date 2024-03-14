<?php
namespace ThemeFactory\ExportInvoices\Model\ResourceModel\Invoice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use ThemeFactory\ExportInvoices\Model\Invoice as InvoiceModel;
use ThemeFactory\ExportInvoices\Model\ResourceModel\Invoice as InvoiceResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'invoice_id';
    protected $_eventPrefix = 'themeFactory_exportInvoices_invoice_collection';
    protected $_eventObject = 'invoice_collection';

    protected function _construct()
    {
        $this->_init(InvoiceModel::class, InvoiceResourceModel::class);
    }
}
