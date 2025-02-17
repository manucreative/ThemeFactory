<?php
namespace Manwiks\Geolocation\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Manwiks\Geolocation\Model\ResourceModel\UrlRewrite\CollectionFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Manwiks\Geolocation\Helper\ConfigHelper;
use Manwiks\Geolocation\Model\PriceRulePromo;
use Manwiks\Geolocation\Model\CartRulePromo;

class StorePopup extends Template
{
    protected $storeManager;
    protected $_collectionFactory;
    protected $sessionManager;
    protected $configHelper;
    protected $pricePromotions;
    protected $cartPromotions;

    public function __construct(
        Template\Context $context,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory,
        SessionManagerInterface $sessionManager,
        ConfigHelper $configHelper,
        PriceRulePromo $pricePromotions,
        CartRulePromo $cartPromotions,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
        $this->_collectionFactory = $collectionFactory;
        $this->sessionManager = $sessionManager;
        $this->configHelper = $configHelper;
        $this->pricePromotions = $pricePromotions;
        $this->cartPromotions = $cartPromotions;
    }

    public function getStoreData()
    {
        $store = $this->storeManager->getStore();

        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('store_id', $store->getId());

        $currentStoreData = $collection->getFirstItem();
        return [
            'name' => $store->getName(),
            'location' => $currentStoreData->getLocation(),
            'lat' => $currentStoreData->getLat(),
            'lng' => $currentStoreData->getLng(),
        ];
    }

    public function isDefault(){
        $store = $this->storeManager->getStore();
        return $store->getId() == 1;
    }

    public function getStoreId(){
        $store = $this->storeManager->getStore();
        return $store->getId();
    }

   public function checkAndDisplayPopup()
    {
        $timeToShow = $this->configHelper->showTime();

        $lastVisit = $this->sessionManager->getData('last_visit');
        $currentTime = time();
        $timeSet = $timeToShow;

        if (!$lastVisit || ($currentTime - $lastVisit > $timeSet)) {
            $this->sessionManager->setData('last_visit', $currentTime);
            return true;
        }
        return false; 
    }

    public function showPopup()
    {
        return $this->configHelper->showPopup();
    }
    public function getCatalogPromotions()
    {
        return $this->pricePromotions->getCatalogPromotions();
    }

    public function getCartPromotions()
    {
        return $this->cartPromotions->getCartPromotions();
    }
    

    // private function getPromotions()
    // {
    //     return [
    //         [
    //             'title' => '10% off on all items', 
    //             'description' => 'Get 10% off on all items today!'
    //         ],
    //     ];
    // }
}
