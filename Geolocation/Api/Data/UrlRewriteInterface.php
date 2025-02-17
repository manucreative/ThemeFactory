<?php
/**
 * Manwiks_Geolocation UrlRerite Interface.
 *
 * @category    Manwiks
 *
 * @author      Emmanuel Kirui (System admin)
 */
namespace Manwiks\Geolocation\Api\Data;

interface UrlRewriteInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    const ID = 'id';
    const STORE_ID = 'store_id';
    const LATITUDE = 'lat';
    const LONGITUDE = 'lng';
    const NAME = 'name';
    const LOCATION = 'location';
    const IS_ACTIVE = 'is_active';
    const URL = 'url';
    const UPDATE_TIME = 'update_time';
    const CREATED_AT = 'created_at';

    /**
     * Get Id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set Id.
     */
    public function setId($id);

     /**
     * Get Store Id.
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set Store Id.
     */
    public function setStoreId($store_id);

    /**
     * Get Lat.
     *
     * @return varchar
     */
    public function getLat();

    /**
     * Set Lat.
     */
    public function setLat($lat);

    /**
     * Get Lng.
     *
     * @return varchar
     */
    public function getLng();

    /**
     * Set Lng.
     */
    public function setLng($lng);

    /**
     * Get Name.
     *
     * @return varchar
     */
    public function getName();

    /**
     * Set Name.
     */
    public function setName($name);

    /**
     * Get Location.
     *
     * @return varchar
     */
    public function getLocation();

    /**
     * Set Location.
     */
    public function setLocation($location);

    /**
     * Get Url.
     *
     * @return varchar
     */
    public function getUrl();

    /**
     * Set Url.
     */
    public function setUrl($url);

        /**
     * Get IsActive.
     *
     * @return varchar
     */
    public function getIsActive();
    /**
     * Set IsActive.
     */
    public function setIsActive($isActive);

    /**
     * Get UpdateTime.
     *
     * @return varchar
     */
    public function getUpdateTime();

    /**
     * Set UpdateTime.
     */
    public function setUpdateTime($updateTime);

    /**
     * Get CreatedAt.
     *
     * @return varchar
     */
    public function getCreatedAt();

    /**
     * Set CreatedAt.
     */
    public function setCreatedAt($createdAt);
}