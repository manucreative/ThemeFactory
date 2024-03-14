<?php

namespace ThemeFactory\Tims\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    // protected $deployService;

    /**
     * UpgradeSchema constructor.
     * @param \Magento\Framework\Module\Manager $moduleManager
    //  * @param \ThemeFactory\Tims\Model\Deploy\DeployService $deployService
     */
    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager
        // \ThemeFactory\Tims\Model\Deploy\DeployService $deployService
    ) {
        $this->moduleManager = $moduleManager;
        // $this->deployService = $deployService;
    }
    /**
     * @inheritdoc
     *
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /* deploy pos app into pub */
        // $this->deployService->execute();

        $installer = $setup;
        $installer->startSetup();

        // Sales Invoice Grid
        // if (version_compare($context->getVersion(), '0.1.4.3', '>')) {
        $connection = $setup->getConnection();
        $connection->addColumn(
            // Get the table
            $setup->getTable('sales_invoice_grid'),
            // Add the control_unit_serial_no column
            'control_unit_serial_no',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'control unit serial no'
            ]
        );
        $connection->addColumn(
            // Get the table
            $setup->getTable('sales_invoice_grid'),
            // Add the control_unit_serial_no column
            'control_unit_invoice_no',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'control unit invoice no'
            ]
        );
        $connection->addColumn(
            // Get the table
            $setup->getTable('sales_invoice_grid'),
            // Add the control_unit_serial_no column
            'control_unit_date_and_time',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'control unit date and time'
            ]
        );
        $connection->addColumn(
            // Get the table
            $setup->getTable('sales_invoice_grid'),
            // Add the control_unit_serial_no column
            'qr_code_link',
            [
                'type' => Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'comment' => 'qr code link'
            ]
        );
        // }
        // End of Sales Invoice Grid

        // Sales invoice
        if (version_compare($context->getVersion(), '0.1.4.3', '>')) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_invoice'),
                // Add the control_unit_serial_no column
                'control_unit_serial_no',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'control unit serial no'
                ]
            );
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_invoice'),
                // Add the control_unit_serial_no column
                'control_unit_invoice_no',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'control unit invoice no'
                ]
            );
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_invoice'),
                // Add the control_unit_serial_no column
                'control_unit_date_and_time',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'control unit date and time'
                ]
            );
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_invoice'),
                // Add the control_unit_serial_no column
                'qr_code_link',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'qr code link'
                ]
            );
        }
        // End of Sales Invoices

        // Start of Sales Flat Credit Memo
        if (version_compare($context->getVersion(), '0.1.4.3', '>')) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_creditmemo'),
                // Add the control_unit_serial_no column
                'control_unit_serial_no',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'control unit serial no'
                ]
            );
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_creditmemo'),
                // Add the control_unit_serial_no column
                'control_unit_credit_memo_no',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'control unit Credit Memo no'
                ]
            );
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_creditmemo'),
                // Add the control_unit_serial_no column
                'control_unit_date_and_time',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'control unit date and time'
                ]
            );
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_creditmemo'),
                // Add the control_unit_serial_no column
                'qr_code_link',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'qr code link'
                ]
            );
        }
        //End of Sales Start Creid Memo

        // Credit Memo Sales Flat Credit Memo Grid

        if (version_compare($context->getVersion(), '0.1.4.3', '>')) {
            $connection = $setup->getConnection();
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_creditmemo_grid'),
                // Add the control_unit_serial_no column
                'control_unit_serial_no',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'control unit serial no'
                ]
            );
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_creditmemo_grid'),
                // Add the control_unit_serial_no column
                'control_unit_credit_memo_no',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'control unit Credit Memo no'
                ]
            );
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_creditmemo_grid'),
                // Add the control_unit_serial_no column
                'control_unit_date_and_time',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'control unit date and time'
                ]
            );
            $connection->addColumn(
                // Get the table
                $setup->getTable('sales_creditmemo_grid'),
                // Add the control_unit_serial_no column
                'qr_code_link',
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'qr code link'
                ]
            );
        }
        // End of Credit Memo Sales Flat Grid

        $setup->endSetup();
    }
}
