<?php
namespace Manwiks\Geolocation\Model;

use Magento\Framework\Model\AbstractModel;
use Manwiks\Geolocation\Api\Data\UrlRewriteInterface;

class UrlRewrite extends AbstractModel implements UrlRewriteInterface
{
        /**
     * CMS page cache tag.
     */
    const CACHE_TAG = 'manwiks_geo_ip';
    /**
     * @var string
     */
    protected $_cacheTag = 'manwiks_geo_ip';
    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'manwiks_geo_ip';


    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Manwiks\Geolocation\Model\ResourceModel\UrlRewrite');
    }

     /**
     * Get Id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set Id.
     */
    public function setId($id)
    {

        return $this->setData(self::ID, $id);
    }

    /**
     * Get Store Id.
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Set Store Id.
     */
    public function setStoreId($store_id)
    {

        return $this->setData(self::STORE_ID, $store_id);
    }

    /**
     * Get Lat.
     *
     * @return varchar
     */
    public function getLat()
    {
        return $this->getData(self::LATITUDE);
    }

    /**
     * Set Lat.
     */
    public function setLat($lat)
    {
        return $this->setData(self::LATITUDE, $lat);
    }

    /**
     * Get Lng.
     *
     * @return varchar
     */
    public function getLng()
    {
        return $this->getData(self::LONGITUDE);
    }

    /**
     * Set Lng.
     */
    public function setLng($lng)
    {
        return $this->setData(self::LONGITUDE, $lng);
    }

    /**
     * Get Name.
     *
     * @return varchar
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set Name.
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get Location.
     *
     * @return varchar
     */
    public function getLocation()
    {
        return $this->getData(self::LOCATION);
    }

    /**
     * Set Location.
     */
    public function setLocation($location)
    {
        return $this->setData(self::LOCATION, $location);
    }

    /**
     * Get Url.
     *
     * @return varchar
     */
    public function getUrl()
    {
        return $this->getData(self::URL);
    }

    /**
     * Set Url.
     */
    public function setUrl($url)
     {
        return $this->setData(self::URL, $url);
     }

         /**
     * Get IsActive.
     *
     * @return varchar
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }
    /**
     * Set IsActive.
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }
         /**
     * Get UpdateTime.
     *
     * @return varchar
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }
    /**
     * Set UpdateTime.
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }
    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }
    /**
     * Set CreatedAt.
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
