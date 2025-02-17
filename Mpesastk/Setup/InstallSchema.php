<?php
namespace ThemeFactory\Mpesastk\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('themeFactory_mpesastk_stkpush');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            // Create mpesa_paybill table
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'stkpush_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'uns
                        igned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Stkpush ID'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Customer ID'
                )
                ->addColumn(
                    'merchant_request_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Merchant Request ID'
                )
                ->addColumn(
                    'checkout_request_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Checkout Request ID'
                )
                ->addColumn(
                    'response_code',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Response Code'
                )
                ->addColumn(
                    'customer_message',
                    Table::TYPE_TEXT,
                    '2M',
                    ['nullable' => false, 'default' => ''],
                    'Customer Message'
                )
                ->addColumn(
                    'trans_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Transaction ID'
                )
                ->addColumn(
                    'trans_amount',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Order Amount'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Order ID'
                )
                ->addColumn(
                    'trans_type',
                    Table::TYPE_TEXT,
                    '2M',
                    ['nullable' => false, 'default' => ''],
                    'Transaction Type'
                )
                ->addColumn(
                    'response_description',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Response Description'
                )
                ->addColumn(
                    'result_code',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Result Code'
                )
                ->addColumn(
                    'result_desc',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Result Desc'
                )
                ->addColumn(
                    'account_id',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Account Id'
                )
                ->addColumn(
                    'phone',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Phone'
                )
                ->addColumn(
                    'msisdn',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'MSISDN'
                )
                ->addColumn(
                    'customer_name',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Customer Name'
                )
                ->addColumn(
                    'callback_time',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Callback Time'
                )
                ->addColumn(
                    'request_time',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Request Time'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_SMALLINT,
                    null,
                    ['nullable' => false, 'default' => '0'],
                    'Status'
                )

                ->setComment('M-PESA STK Push Table')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }


        $installer->endSetup();

    }
}