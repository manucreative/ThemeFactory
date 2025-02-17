<?php
/**
 * Manwiks_Geolocation Stores Options Model.
 * @category    Manwiks
 * @author      Emmanuel Kirui
 */
namespace Manwiks\Geolocation\Model;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class Stores implements OptionSourceInterface
{

  protected $storeManager;

  public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

  public function getOptionArray(){
    $stores = $this->storeManager->getStores();
        $storeOptions = [];

        foreach ($stores as $store) {
            $storeOptions[] = [
                'value' => $store->getId(),
                'label' => $store->getName()
            ];
        }

        return $storeOptions;

  }

 public function getAllOptions()
    {
        $res = $this->getOptionArray();
        array_unshift($res, ['value' => '', 'label' => __('Please select a store')]);
        return $res;
    }

    /**
     * Get Grid row type array for option element.
     * @return array
     */
    public function getOptions()
    {
        return $this->getOptionArray();
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}