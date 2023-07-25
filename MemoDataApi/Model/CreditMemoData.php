<?php

namespace ThemeFactory\MemoDataApi\Model;

use ThemeFactory\MemoDataApi\Api\CreditMemoDataInterface;
use Magento\Framework\App\ResourceConnection;

class CreditMemoData implements CreditMemoDataInterface
{

    protected $connection;

    public function __construct(ResourceConnection $connection)
    {
        $this->connection = $connection->getConnection();
    }

    public function getCreditMemoDataByCreditMemoNumber($creditMemoNumber)
    {
        $creditMemoData = [];

    try {
        $select = $this->connection->select()->from(
            ['sales_creditmemo' => $this->connection->getTableName('sales_creditmemo')],
            ['increment_id', 'created_at', 'grand_total', 'order_id']
        )->joinLeft(
            ['sales_creditmemo_grid' => $this->connection->getTableName('sales_creditmemo_grid')],
            'sales_creditmemo.entity_id = sales_creditmemo_grid.entity_id',
            ['control_unit_serial_no', 'control_unit_invoice_no', 'qr_code_link']
        )->where('sales_creditmemo.increment_id = ?', $creditMemoNumber);

        $data = $this->connection->fetchRow($select);

        if ($data) {
            $creditMemoData['increment_id'] = $data['increment_id'];
            $creditMemoData['created_at'] = $data['created_at'];
            $creditMemoData['grand_total'] = $data['grand_total'];
            $creditMemoData['order_id'] = $data['order_id'];
            $creditMemoData['control_unit_serial_no'] = $data['control_unit_serial_no'];
            $creditMemoData['control_unit_invoice_no'] = $data['control_unit_invoice_no'];
            $creditMemoData['qr_code_link'] = $data['qr_code_link'];
        } else {
            throw new NoSuchEntityException(__('Credit memo with increment_id "%1" does not exist.', $creditMemoNumber));
        }
    } catch (NoSuchEntityException $e) {
        throw $e;
    }

    return $creditMemoData;
    }
}
