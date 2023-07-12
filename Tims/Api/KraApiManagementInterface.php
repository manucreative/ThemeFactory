<?php

/**
 * Copyright 2022 Steve Celebrates. All rights reserved.
 */

namespace ThemeFactory\Tims\Api;

/*
    This will help you define all the Api methods you would like 
    to expose. all the methods must have
    doc-block defined with @api, @params and @return else it will not work.

    
*/

use ThemeFactory\Tims\Api\Data\KraInterface;


interface KraApiManagementInterface
{
    /**
     * Return the address and port on implementation.
     *
     * @api
     * @param ThemeFactory\Tims\Api\Data\KraInterface $address The Server address.
     * @param ThemeFactory\Tims\Api\Data\KraInterface $port The Server Port.
     * @param ThemeFactory\Tims\Api\Data\KraInterface $ip The device ip.
     * @param ThemeFactory\Tims\Api\Data\KraInterface $tcp_port the device tcp port.
     * @param ThemeFactory\Tims\Api\Data\KraInterface $zfp_pass ZFP password.
     * @param ThemeFactory\Tims\Api\Data\KraInterface $dev_serial_port The device serial port.
     * @param ThemeFactory\Tims\Api\Data\KraInterface $dev_boud the device boud rate.
     * @param ThemeFactory\Tims\Api\Data\KraInterface $op_pass the device password.
     * @return ThemeFactory\Tims\Api\Data\KraInterface array The Server Settings.
     * 
     *
     */
    public function connectdevice($address, $port, $ip, $tcp_port, $zfp_pass, $dev_serial_port, $dev_boud, $op_pass);
}