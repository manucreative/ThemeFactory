<?php 

namespace Manwiks\Geolocation\Model;

use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as SalesRuleCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Action\Context;

class CartRulePromo
{
    protected $salesRuleCollectionFactory;
    protected $storeManager;

    public function __construct(
        Context $context,
        SalesRuleCollectionFactory $salesRuleCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->salesRuleCollectionFactory = $salesRuleCollectionFactory;
        $this->storeManager = $storeManager;
    }

    public function getCartPromotions()
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerGroupId = 0;

        $rules = $this->salesRuleCollectionFactory->create()
            ->addWebsiteFilter($websiteId)
            ->addFieldToFilter('is_active', 1)
            ->addCustomerGroupFilter($customerGroupId);

        $promotions = [];
        foreach ($rules as $rule) {
            $promotions[] = [
                'name' => $rule->getName(),
                'description' => $rule->getDescription()
            ];
        }

        return $promotions;
    }
}
