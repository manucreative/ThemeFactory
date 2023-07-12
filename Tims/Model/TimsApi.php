<?php

/**
 * Copyright 2022 Steve Celebrates. All rights reserved.
 */

namespace ThemeFactory\Tims\Model;

use Exception;
use ThemeFactory\Tims\Tremol\FP;
use ThemeFactory\Tims\Api\KraApiManagementInterface;
use ThemeFactory\Tims\Api\Data\KraInterface;
use ThemeFactory\Tims\Api\Data\KraInterfaceFactory;


/**
 * Defines the implementaiton class of the calculator and connect device service contract. 
 * Remember this implements the interface
 */

class TimsApi  implements KraApiManagementInterface
{
    //******************************Dont change anything below this**************************/
    /**
     * @var KraInterfaceFactory
     * Factory for creating new connection instances. This code will be automatically
     * generated because the type ends in "Factory".
     */
    private $kraFactory;

    public function __construct(
        KraInterfaceFactory $kraFactory
        // FP $fpLibrary
    ) {
        $this->kraFactory = $kraFactory;
        // $this->_fpLibrary = $fpLibrary;
    }
    //************************Don't change anything above this************************************/

    //********************Don't change anything below this*************************** */

    // Start of connectDevice


    /**
     * Return the the address and port.
     * @api
     * @param KraInterface $address The Address.
     * @param KraInterface $port The port.
     * @param KraInterface $ip The ip.
     * @param KraInterface $tcp_port The Tcp Port.
     * @param KraInterface $zfp_pass The zpf password.
     * @param KraInterface $dev_serial_port The device Serial Port.
     * @param KraInterface $dev_boud The device baud.
     * @param KraInterface $op_pass The device operators password.
     * @return KraInterface The Server Settings.
     * 
     */


    public function connectdevice($address, $port, $ip, $tcp_port, $zfp_pass, $dev_serial_port, $dev_boud, $op_pass)
    {
        $serverSetting = $this->kraFactory->create();
        $serverSetting->setAddress($address->getAddress());
        $serverSetting->setPort($port->getPort());
        $serverSetting->setIp($ip->getIp());
        $serverSetting->setTcp_port($tcp_port->getTcp_port());
        $serverSetting->setFp_pass($zfp_pass->getFp_pass());
        $serverSetting->setDev_serial_port($dev_serial_port->getDev_serial_port());
        $serverSetting->setDev_boud($dev_boud->getDev_boud());
        $serverSetting->setOp_pass($op_pass->getOp_pass());

        return $serverSetting;
    }
    // End of connectDevice
    //**********************Dont change anything above this *********************** */
}