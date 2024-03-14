<?php

/**
 * Copyright 2022 Steve Celebrates. All rights reserved.
 */

namespace ThemeFactory\Tims\Api\Data;

/**
 * Defines a data structure representing variables defined, demonstrating passing
 * more complex types in and out of a function call.
 */

interface KraInterface
{

    //Server Initialization Settings Functions Start here

    //**************************************/
    /**
     * Get the Server Address.
     *
     * @api
     * @return string The server Address.
     */
    public function getAddress();


    /**
     * Set the Server Address.
     *
     * @api
     * @param $value1 string The server Address.
     * @return null
     */
    public function setAddress($value1);



    //****************************************/
    /**
     * Get the Server Port.
     *
     * @api
     * @return string The server Port.
     */
    public function getPort();


    /**
     * Set the Server Port.
     *
     * @api
     * @param $value2 string The server Port.
     * @return null
     */
    public function setPort($value2);


    // ************************************************/
    /**
     * Get the ip.
     *
     * @api
     * @return string The ip.
     */
    public function getIp();

    /**
     * Set ip.
     *
     * @api
     * @param $value3 string The ip.
     * @return null
     */
    public function setIp($value3);


    //***************************************************** */

    /**
     * Get the tcp_port.
     *
     * @api
     * @return string The tcp_port.
     */
    public function getTcp_port();

    /**
     * Set tcp_port.
     *
     * @api
     * @param $value4 string The tcp_port.
     * @return null
     */
    public function setTcp_port($value4);


    //***************************************************** */
    /**
     * Get the fp_pass.
     *
     * @api
     * @return string The fp_pass.
     */
    public function getFp_pass();

    /**
     * Set fp_pass.
     *
     * @api
     * @param $value5 string The fp_pass.
     * @return null
     */
    public function setFp_pass($value5);


    //********************************************************** */

    /**
     * Get the dev_serial_port.
     *
     * @api
     * @return string The dev_serial_port.
     */
    public function getDev_serial_port();

    /**
     * Set dev_serial_port.
     *
     * @api
     * @param $value6 string The dev_serial_port.
     * @return null
     */
    public function setDev_serial_port($value6);

    //***************************************************** */

    /**
     * Get the dev_boud.
     *
     * @api
     * @return string The dev_boud.
     */
    public function getDev_boud();

    /**
     * Set dev_boud.
     *
     * @api
     * @param $value7 string The dev_boud.
     * @return null
     */
    public function setDev_boud($value7);


    //************************************************* */
    /**
     * Get the op_pass.
     *
     * @api
     * @return string The op_pass.
     */
    public function getOp_pass();

    /**
     * Set op_pass.
     *
     * @api
     * @param $value8 string The op_pass.
     * @return null
     */
    public function setOp_pass($value8);
    //****************************************/

    //Server Initialization settings Functions End here
}