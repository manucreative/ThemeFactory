<?php
namespace Manwiks\Geolocation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Manwiks\Geolocation\Helper\ConfigHelper;

class Data extends AbstractHelper
{
    protected $httpClient;
    protected $configHelper;

    public function __construct(
        Context $context,
        ConfigHelper $configHelper,
        \Magento\Framework\HTTP\Client\Curl $httpClient
    ) {
        $this->httpClient = $httpClient;
        $this->configHelper = $configHelper;
        parent::__construct($context);
    }

    public function getGeoIPData($ip)
    {
        $apiKey = $this->configHelper->getApiKey(); // Getting API key from the configuration
        $url = "https://api.ipgeolocation.io/ipgeo?apiKey=$apiKey&ip=$ip";

        $this->httpClient->get($url);
        $response = $this->httpClient->getBody();

        return json_decode($response, true);
    }

    public function getGeoIpDataCloudways()
    {
        $geoIpData = [
            'country' => getenv('HTTP_X_FORWARDED_COUNTRY') ?? null,
            'Continent' => getenv('HTTP_X_FORWARDED_CONTINENT') ?? null,
            'region' => getenv('HTTP_X_FORWARDED_REGION') ?? null,
            'latitude' => getenv('HTTP_X_FORWARDED_LATITUDE') ?? null,
            'longitude' => getenv('HTTP_X_FORWARDED_LONGITUDE') ?? null,
        ];

        return $geoIpData;
    }
}