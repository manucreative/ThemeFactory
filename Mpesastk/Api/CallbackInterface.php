<?php
namespace ThemeFactory\Mpesastk\Api;

interface CallbackInterface
{
   /**
     * Callback endpoint for Safaricom Mpesa.
     * @api
     * @param string $jsonData JSON data received in the request body
     * @return array
     */
    public function stkCallback();
}