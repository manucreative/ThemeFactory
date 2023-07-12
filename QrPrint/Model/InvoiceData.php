<?php

namespace ThemeFactory\QrPrint\Model;

use ThemeFactory\QrPrint\Api\InvoiceDataInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\InvoiceInterfaceFactory;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\InvoiceRepository;
use Magento\Framework\App\ResourceConnection;

class InvoiceData implements InvoiceDataInterface
{
    private $invoiceRepository;
    private $searchCriteriaBuilder;
    private $invoiceInterfaceFactory;
    private $connection;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        InvoiceInterfaceFactory $invoiceInterfaceFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->invoiceInterfaceFactory = $invoiceInterfaceFactory;
        $this->connection = $resourceConnection->getConnection();
    }

    /**
     * @inheritdoc
     */
    public function getInvoiceData($invoiceNumber)
    {
        $invoiceData = [];

        try {
            $select = $this->connection->select()->from(
                ['sales_invoice' => $this->connection->getTableName('sales_invoice')],
                ['increment_id', 'created_at', 'grand_total', 'order_id']
            )->joinLeft(
                ['sales_invoice_grid' => $this->connection->getTableName('sales_invoice_grid')],
                'sales_invoice.entity_id = sales_invoice_grid.entity_id',
                ['control_unit_serial_no', 'control_unit_invoice_no', 'qr_code_link']
            )->where('sales_invoice.increment_id = ?', $invoiceNumber);

            $data = $this->connection->fetchRow($select);

            if ($data) {
                $invoiceData['increment_id'] = $data['increment_id'];
                $invoiceData['created_at'] = $data['created_at'];
                $invoiceData['grand_total'] = $data['grand_total'];
                $invoiceData['order_id'] = $data['order_id'];
                $invoiceData['control_unit_serial_no'] = $data['control_unit_serial_no'];
                $invoiceData['control_unit_invoice_no'] = $data['control_unit_invoice_no'];
                $invoiceData['qr_code_link'] = $data['qr_code_link'];
            } else {
                throw new NoSuchEntityException(__('Invoice with increment_id "%1" does not exist.', $invoiceNumber));
            }
        } catch (NoSuchEntityException $e) {
            throw $e;
        }

        return $invoiceData;
    }
}
