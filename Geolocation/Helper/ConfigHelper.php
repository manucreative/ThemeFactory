<?php
namespace Manwiks\Geolocation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class ConfigHelper extends AbstractHelper
{
    const XML_PATH_GEOIPREDIRECT = 'geolocation_section/general/';
    const XML_PATH_GEOIPREDIRECT_POPUP = 'geolocation_section/popup/';

    public function getGeneralValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GEOIPREDIRECT . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getPopupValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GEOIPREDIRECT_POPUP . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function isEnabled()
    {
        return $this->getGeneralValue('enable');

    }

    public function blockedIPs()
    {
        $blockedIps = $this->getGeneralValue('block_ip');
        if ($blockedIps) {
            $ipsArray = array_map('trim', explode(',', $blockedIps));
            return $ipsArray;
        }
        return [];
    }

    public function getApiKey()
    {
        return $this->getGeneralValue('api_key');
    }
    public function showFab()
    {
        return $this->getGeneralValue('fab');
    }
    public function showPopup()
    {
        return $this->getGeneralValue('store_popup');
    }
    public function showTime()
    {
        return $this->getGeneralValue('popup_show_time');
    }

    public function isActive()
    {
        return $this->getPopupValue('isActive');
    }

     public function getPopupTitle()
    {
        return $this->getPopupValue('popup_title');
    }



}