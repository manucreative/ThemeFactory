<?php
namespace Manwiks\Geolocation\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $tableName = $installer->getTable('manwiks_geo_ip');

            if ($installer->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'location' => [
                        'type' => Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => 255,
                        'comment' => 'Shop Location',
                    ]
                ];

                foreach ($columns as $name => $definition) {
                    $connection = $installer->getConnection();
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        $installer->endSetup();
    }
}