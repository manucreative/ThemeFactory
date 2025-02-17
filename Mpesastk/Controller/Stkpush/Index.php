<?php

namespace ThemeFactory\Mpesastk\Controller\Stkpush;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use ThemeFactory\Mpesastk\Helper\Data;
use Magento\Framework\UrlInterface;
use ThemeFactory\Mpesastk\Controller\Stkpush\Callback;


class Index extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;


    protected $myHelper;

    protected $urlBuilder;

    protected $callback;

    protected $_stkpush;

    protected $cart;

    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Registry $registry
     * @param Data $myHelper
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\Registry $registry,
        Data $myHelper,
        UrlInterface $urlBuilder,
        Callback $callback,
        \Magento\Checkout\Model\Cart $cart,
        \ThemeFactory\Mpesastk\Model\Stkpush $stkpush
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->registry = $registry;
        $this->myHelper = $myHelper;
        $this->urlBuilder = $urlBuilder;
        $this->callback = $callback;
        $this->_stkpush = $stkpush;
        $this->cart = $cart;
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/stkData.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);


        $tillNumber = $this->myHelper->getTillNumber();
        $phoneNumber = $this->getRequest()->getParam('phoneNumber');

        $amount = $this->cart->getQuote()->getGrandTotal();
        $account_id = $this->cart->getQuote()->getId();
        $firstName = $this->cart->getQuote()->getBillingAddress()->getFirstName();
        $lastName = $this->cart->getQuote()->getBillingAddress()->getLastname();

        $customerName = $firstName.' '.$lastName;

        $customerId = $this->cart->getQuote()->getCustomer()->getId();
        
        $registered = $this->myHelper->registerUrl();
        
        


        $logger->info('Data Phone: ' . print_r($phoneNumber, true));
        $logger->info('Data account: ' . print_r($account_id, true));
        $logger->info('Data cid: ' . print_r($customerId, true));
        $logger->info('Data amount: ' . print_r($amount, true));
        $logger->info('Break: ' . print_r('.....................................................', true));

        // $logger->info('Data url: ' . print_r($registered, true));
        $logger->info('Url: ' . print_r($this->myHelper->getUrl(), true));


         
            $accessTokenObj = $this->myHelper->generateToken();
        if(isset($accessTokenObj->access_token) && !empty($accessTokenObj->access_token)) {
        $accessToken = $accessTokenObj->access_token;

        $currentTimestamp = time();
        $shortcode = '174379';
        $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

        $callbackUrl = $this->myHelper->getUrl();
        // Format the timestamp as YYYYMMDDHHMMSS
        $timestamp = date("YmdHis", $currentTimestamp);

                $combined = $shortcode . $passkey . $timestamp;
                $base64Password = base64_encode($combined);
                $password = $base64Password;



            // "TransactionType" => "CustomerBuyGoodsOnline",
        $myData = [
            "BusinessShortCode" => $shortcode,
            "Password" => $password,
            "Timestamp" => $timestamp,
            "TransactionType" => "CustomerPayBillOnline",
            "Amount" => 1,
            "PartyA" => $this->formatPhone($phoneNumber),
            "PartyB" => $shortcode,
            "PhoneNumber" => $this->formatPhone($phoneNumber),
            "CallBackURL" => $callbackUrl,
            "AccountReference" => $account_id,
            "TransactionDesc" => "Magento Goods Payment",
        ];

            $postFields = json_encode($myData);

            $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer '. $accessToken,
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $server_output = curl_exec($ch);

            if (curl_errno($ch)) {
                $error_msg = curl_error($ch);
                curl_close ($ch);
                echo ['success' => 'error', 'message' => $error_msg];
            }

                $server_response = json_decode($server_output, true);

               if(isset($server_response['ResponseCode']) && $server_response['ResponseCode'] == '0'){

                        $this->_stkpush->setData(['merchant_request_id'=>$server_response['MerchantRequestID'],'checkout_request_id'=>$server_response['CheckoutRequestID'],'phone'=>$phoneNumber,'customer_id'=>$customerId, 'customer_name'=>$customerName])->save();

                        echo json_encode(['success'=> 'done','message'=>$server_response['CustomerMessage'],'merchant_r_id'=>$server_response['MerchantRequestID'],'checkout_r_id' =>$server_response['CheckoutRequestID'], 'account_id'=>$account_id,]);
                    }
                    elseif(isset($server_response['errorCode']))
                    {
                        echo json_encode(['success'=> 'error','message'=>$server_response['errorMessage']]);
                    }
                    else
                    {
                        echo json_encode(['success'=> 'error','message'=>"An error occured during request"]);
                    }

                }else{
                    echo json_encode(['success'=> 'error','message'=>"Token generatio problem please try again"]);
                }

    }
    public function formatPhone($phone)
    {
        $phone = str_replace ('', '',trim($phone));
        return "254".substr($phone, -9);
    }


          
}

