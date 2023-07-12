<?php

/**
 * Copyright 2022 Steve Celebrates. All rights reserved.
 */

namespace ThemeFactory\Tims\Model;

use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use ThemeFactory\Tims\Api\Data\KraInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Defines a data structure representing. Demonstrates passing
 * more complex types in and out of a function call.
 */

class TimsFetch implements KraInterface
{

    //Connect Device Definitions

    const XML_PATH_ADDRESS = 'tims_section_id/server_group_id/server_address_id';
    const XML_PATH_PORT = 'tims_section_id/server_group_id/server_port_id';
    const XML_PATH_IP = 'tims_section_id/tcp_group_id/tcp_ip_id';
    const XML_PATH_TCP_PORT = 'tims_section_id/tcp_group_id/tcp_port_id';
    const XML_PATH_ZFP_PASS = 'tims_section_id/tcp_group_id/tcp_pass_id';
    const XML_PATH_SERIALPORT = 'tims_section_id/device_serial_id/device_Serial_port';
    const XML_PATH_BOUD = 'tims_section_id/device_serial_id/device_serial_baud';
    private $op_pass;

    //End of Connect Device Defintions

    /**
     * Constructor.
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        // Old hard codded code
        // $this->address = "127.0.0.1"; //127.0.0.1 //192.168.2.29
        // $this->port = "4444"; //4444
        // $this->ip = "196.207.27.42";
        // $this->tcp_port = "8000";
        // $this->zfp_pass = "Password";
        // $this->dev_serial_port = "COM3"; //COM3 //USB001
        // $this->dev_boud = "115200";
        $this->op_pass = "Password";
        // End of Old hard codded code
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    //Get the store id
    public function getStoreid()
    {
        return $this->_storeManager->getStore()->getId();
    }
    // End of get store id

    //**************************Don't change anything below this************************** */

    //Begining of Initialization device

    //********************************/

    /**
     * Get the Address.
     *
     * @api
     * @return string The Address.
     */
    public function getAddress()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADDRESS,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
        //return $this->address;
    }

    /**
     * Set Address.
     *
     * @api
     * @param $value1 string The Address.
     * @return null
     */
    public function setAddress($value1)
    {
        $this->address = $value1;
    }

    /**
     * Get the Port.
     *
     * @api
     * @return string The Port.
     */
    public function getPort()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PORT,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
        //return $this->port;
    }

    /**
     * Set Port.
     *
     * @api
     * @param $value2 string The Port.
     * @return null
     */
    public function setPort($value2)
    {
        $this->port = $value2;
    }

    /**
     * Get the ip.
     *
     * @api
     * @return string The ip.
     */
    public function getIp()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_IP,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
        //return $this->ip;
    }

    /**
     * Set ip.
     *
     * @api
     * @param $value3 string The ip.
     * @return null
     */
    public function setIp($value3)
    {
        $this->ip = $value3;
    }

    /**
     * Get the tcp_port.
     *
     * @api
     * @return string The tcp_port.
     */
    public function getTcp_port()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TCP_PORT,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
        // return $this->tcp_port;
    }

    /**
     * Set tcp_port.
     *
     * @api
     * @param $value4 string The tcp_port.
     * @return null
     */
    public function setTcp_port($value4)
    {
        $this->tcp_port = $value4;
    }

    /**
     * Get the zfp_pass.
     *
     * @api
     * @return string The zfp_pass.
     */
    public function getFp_pass()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ZFP_PASS,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
        //return $this->zfp_pass;
    }

    /**
     * Set zfp_pass.
     *
     * @api
     * @param $value5 string The zfp_pass.
     * @return null
     */
    public function setFp_pass($value5)
    {
        $this->zfp_pass = $value5;
    }

    /**
     * Get the dev_serial_port.
     *
     * @api
     * @return string The dev_serial_port.
     */
    public function getDev_serial_port()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SERIALPORT,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
        // return $this->dev_serial_port;
    }

    /**
     * Set dev_serial_port.
     *
     * @api
     * @param $value6 string The dev_serial_port.
     * @return null
     */
    public function setDev_serial_port($value6)
    {
        $this->dev_serial_port = $value6;
    }

    /**
     * Get the dev_boud.
     *
     * @api
     * @return string The dev_boud.
     */
    public function getDev_boud()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_BOUD,
            ScopeInterface::SCOPE_STORE,
            $this->getStoreid()
        );
        //return $this->dev_boud;
    }

    /**
     * Set dev_boud.
     *
     * @api
     * @param $value7 string The dev_boud.
     * @return null
     */
    public function setDev_boud($value7)
    {
        $this->dev_boud = $value7;
    }

    /**
     * Get the op_pass.
     *
     * @api
     * @return string The op_pass.
     */
    public function getOp_pass()
    {
        return $this->op_pass;
    }

    /**
     * Set op_pass.
     *
     * @api
     * @param $value8 string The op_pass.
     * @return null
     */
    public function setOp_pass($value8)
    {
        $this->op_pass = $value8;
    }

    //**********************************/

    //End of Initialization Device

    //**************************Don't change anything above this ************************ */

}