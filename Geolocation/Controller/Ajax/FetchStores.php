<?php
namespace Manwiks\Geolocation\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Request\Http;
use Manwiks\Geolocation\Helper\ConfigHelper;
use Manwiks\Geolocation\Helper\Data as GeoIPHelper;
use Manwiks\Geolocation\Model\ResourceModel\UrlRewrite\CollectionFactory;
use Magento\Framework\Session\SessionManagerInterface;

class FetchStores extends Action
{
    protected $resultJsonFactory;
    protected $url;
    protected $request;
    protected $geoIPHelper;
    protected $configHelper;
    protected $_collectionFactory;
    protected $session;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
         UrlInterface $url,
        Http $request,
        ConfigHelper $configHelper,
        CollectionFactory $collectionFactory,
        SessionManagerInterface $session,
        GeoIPHelper $geoIPHelper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->url = $url;
        $this->request = $request;
        $this->geoIPHelper = $geoIPHelper;
        $this->configHelper = $configHelper;
        $this->_collectionFactory = $collectionFactory;
        $this->session = $session;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/buttonGeoData.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        if (!$this->configHelper->isEnabled()) {
            return;
        }

        $ip = $this->request->getClientIp();

        $geoData = $this->geoIPHelper->getGeoIPData($ip);
        // $geoData = $this->geoIPHelper->getGeoIPDataCloudways();

      // $logger->info('GeoData2: ' . print_r($geoData, true));

        $userLat = $geoData['latitude'];
        $userLng = $geoData['longitude'];

        $redirectionRules = $this->_collectionFactory->create();
        $redirectionRulesData = $redirectionRules->getData();
        // $this->configHelper->getRedirectionRules();
        $this->session->unsetData('last_visit');
        $nearbyStores = $this->findNearbyStores($userLat, $userLng, $redirectionRulesData);

        if (count($nearbyStores) > 0) {

            return $result->setData(['stores' => $nearbyStores]);
           
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
