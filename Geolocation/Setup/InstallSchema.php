<?php
namespace Manwiks\Geolocation\Setup;

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

        $tableName = $installer->getTable('manwiks_geo_ip');
        if ($installer->getConnection()->isTableExists($tableName) != true) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'store_id',
                    Table::TYPE_SMALLINT,
                        null,
                        [],
                    'STORE ID'
                )
                ->addColumn(
                    'lat', 
                    Table::TYPE_DECIMAL, 
                    '10,6', 
                    ['nullable' => false], 
                    'Latitude')
                ->addColumn(
                    'lng', 
                    Table::TYPE_DECIMAL, 
                    '10,6', 
                    ['nullable' => false], 
                    'Longitude')
                ->addColumn(
                    'name', 
                    Table::TYPE_TEXT, 
                    255, 
                    ['nullable' => false], 
                    'Name')
                ->addColumn(
                    'url', 
                    Table::TYPE_TEXT, 
                    255, 
                    ['nullable' => false], 
                    'URL')
                ->addColumn(
                        'is_active',
                        Table::TYPE_SMALLINT,
                        null,
                        [],
                        'Active Status'
                    )
                ->addColumn(
                    'created_at',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => false,
                        'default' => Table::TIMESTAMP_INIT,
                    ],
                    'Creation Time'
                )
                ->addColumn(
                    'update_time',
                    Table::TYPE_TIMESTAMP,
                    null,
                    [
                        'nullable' => true,
                        'default' => Table::TIMESTAMP_INIT,
                    ],
                    'Modification Time'
                )
                ->setComment('Store URL Rewrite Table');
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}
