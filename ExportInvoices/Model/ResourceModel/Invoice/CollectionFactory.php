<?php
namespace ThemeFactory\ExportInvoices\Model\ResourceModel\Invoice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class CollectionFactory
{
    /**
     * @var string
     */
    protected $instanceName;

    /**
     * @param string $instanceName
     */
    public function __construct($instanceName = \ThemeFactory\ExportInvoices\Model\ResourceModel\Invoice\Collection::class)
    {
        $this->instanceName = $instanceName;
    }

    /**
     * @param array $data
     * @return AbstractCollection
     */
    public function create(array $data = [])
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->create($this->instanceName, $data);
    }
}
