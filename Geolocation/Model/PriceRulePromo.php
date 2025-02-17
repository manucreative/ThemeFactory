<?php

namespace Manwiks\Geolocation\Model;

use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory as CatalogRuleCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Action\Context;

class PriceRulePromo
{
    protected $catalogRuleCollectionFactory;
    protected $productCollectionFactory;
    protected $storeManager;

    public function __construct(
        Context $context,
        CatalogRuleCollectionFactory $catalogRuleCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->catalogRuleCollectionFactory = $catalogRuleCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->storeManager = $storeManager;
    }

    public function getCatalogPromotions()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customerGroupId = 0;

        $rules = $this->catalogRuleCollectionFactory->create()
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
