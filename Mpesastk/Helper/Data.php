<?php

namespace ThemeFactory\Mpesastk\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;

class Data extends AbstractHelper
{

	protected $_storeManager;
    protected $objectManager;
    protected $urlBuilder;
    protected $session;

    const XML_PATH_Mpesa_Stk = 'payment/themeFactory_mpesastk/';



    public function __construct(Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        UrlInterface $urlBuilder,
        \Magento\Checkout\Model\Session $session
    ) {
        $this->objectManager = $objectManager;
        parent::__construct($context);
        $this->urlBuilder = $urlBuilder;
        $this->_storeManager = $storeManager;
        $this->session = $session;
    }

public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }


    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_Mpesa_Stk . $code, $storeId);
    }


    public function getTillNumber(){
    	return $this->getGeneralConfig('tillNumber');
    }

        public function cancelCurrentOrder($comment) {
        $order = $this->session->getLastRealOrder();
        if ($order->getId() && $order->getState() != Order::STATE_CANCELED) {
            $order->registerCancellation($comment)->save();
            return true;
        }
        return false;
    }

    // public function processMpesaStk($data){
   
	// }

      

        public function getUrl()
			{
			    $linkpath = "rest/V1/stkpush/stkcallback";
                return $this->_storeManager->getStore()->getBaseUrl() . $linkpath;
			}
        
        public function registerUrl(){

            $accessTokenObj = $this->generateToken();
            if(isset($accessTokenObj->access_token) && !empty($accessTokenObj->access_token)) {
                $accessToken = $accessTokenObj->access_token;

                $endpoint = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v2/registerurl';

                $requestPayload = [
                    "ShortCode" => "174379",
                    "ResponseType" => "Completed",
                    "ConfirmationURL" => $this->getUrl(),
                    "ValidationURL" => "https://magento-553389-2363723.cloudwaysapps.com/stkcallback/stkpush/logcallback"
                ];

                $payload = json_encode($requestPayload);

                $ch = curl_init($endpoint);

                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Authorization: Bearer '. $accessToken,
                        'Content-Type: application/json'
                    ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    echo 'cURL Error: ' . curl_error($ch);
                }

                curl_close($ch);

                if ($response) {
                    $result = json_decode($response, true);
                    if (isset($result['ResponseDescription'])) {
                        return 'Registration successful: ' . $result['ResponseDescription'];
                    } else {
                        return 'Registration failed: ' . $response;
                    }
                } else {
                    return 'No response received.';
                }
            }
            else{
                return 'Registration failed: Invalid Access Token' ;
            }
}

public function generateToken(){

            $clientID = 'RAg6vFwBOZTyCj8vnsTUCT9BVtiWQRUJ5fNPDz6JhUEJzT9O';
            $clientSecret = 'bk5h10BdhAD0t8mBx7K7x9Q1IISzuGAvoh3uug1xXsl03CAiEnFjsHOztPGmVOHg';
            $credentials = base64_encode($clientID . ':' . $clientSecret);
            $ch = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            return json_decode($response);
            }

}