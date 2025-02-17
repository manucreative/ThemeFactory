<?php
namespace Manwiks\Geolocation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Request\Http;
use Manwiks\Geolocation\Helper\Data;
use Manwiks\Geolocation\Helper\ConfigHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Data\Form\FormKey;
use Manwiks\Geolocation\Model\ResourceModel\UrlRewrite\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class GeoIPRedirectObserver implements ObserverInterface
{
    protected $responseFactory;
    protected $url;
    protected $request;
    protected $geoIPHelper;
    protected $configHelper;
    protected $resultJsonFactory;
    protected $session;
    protected $formKey;
    protected $_collectionFactory;
    protected $storeManager;


    public function __construct(
        ResponseFactory $responseFactory,
        UrlInterface $url,
        Http $request,
        Data $geoIPHelper,
        ConfigHelper $configHelper,
        JsonFactory $resultJsonFactory,
        SessionManagerInterface $session,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        FormKey $formKey
    ) {
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->request = $request;
        $this->geoIPHelper = $geoIPHelper;
        $this->configHelper = $configHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->session = $session;
        $this->formKey = $formKey;
        $this->_collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
    }

    public function execute(Observer $observer)
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/GeoDataObserver.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        if (!$this->configHelper->isEnabled() || !$this->configHelper->isActive()) {
            return;
        }

        $store = $this->storeManager->getStore();
        if($store->getCode() !== 'default'){
            return;
        }

        $ip = $this->request->getClientIp();

        $blocked_ips = $this->configHelper->blockedIPs();

        if (in_array($ip, $blocked_ips)) {
            // $logger->info('Blocked IPs: ' . print_r($ip, true));
            return;
        }

        // Check if redirection has already been done in this session
        if ($this->session->getGeoIPRedirectDone()) {
            return;
        }



        $geoData = $this->geoIPHelper->getGeoIPData($ip);

        $userLat = $geoData['latitude'];
        $userLng = $geoData['longitude'];

        $redirectionRules = $this->_collectionFactory->create();
        $redirectionRulesData = $redirectionRules->getData();
        // $this->configHelper->getRedirectionRules();
        // $logger->info('Data: ' . print_r($redirectionRulesData, true));

        $nearbyStores = $this->findNearbyStores($userLat, $userLng, $redirectionRulesData);

         // $logger->info('Shops: ' . print_r($nearbyStores, true));

        if (count($nearbyStores) === 1) {
            $redirectUrl = $nearbyStores[0]['url'];
            $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
            $this->session->setGeoIPRedirectDone(true);
            exit;
        } elseif (count($nearbyStores) > 1) {
            $this->session->setNearbyStores($nearbyStores);
            $this->session->setShowStoreSelectionPopup(true);
            $this->session->setGeoIPRedirectDone(true);
           
        }
    }

    private function findNearbyStores($userLat, $userLng, $stores, $radius = 50)
    {
        $nearbyStores = [];

        foreach ($stores as $store) {
            if ($store['is_active'] != 1) {
                continue;
            }

            $storeLat = $store['lat'];
            $storeLng = $store['lng'];
            $distance = $this->haversineGreatCircleDistance($userLat, $userLng, $storeLat, $storeLng);

            if ($distance <= $radius) {
                $nearbyStores[] = $store;
            }
        }

        return $nearbyStores;
    }

    private function haversineGreatCircleDistance($lat1, $lng1, $lat2, $lng2, $earthRadius = 6371)
    {
        $latFrom = deg2rad($lat1);
        $lngFrom = deg2rad($lng1);
        $latTo = deg2rad($lat2);
        $lngTo = deg2rad($lng2);

        $latDelta = $latTo - $latFrom;
        $lngDelta = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}