<?php
        namespace ThemeFactory\Mpesastk\Controller\Stkpush;

        use Magento\Framework\App\Action\Action;
        use Magento\Framework\App\Action\Context;
        use Magento\Framework\Controller\Result\JsonFactory;
        use ThemeFactory\Mpesastk\Helper\Data;
        use Magento\Framework\UrlInterface;
        use ThemeFactory\Mpesastk\Controller\Stkpush\Callback;

        class Recheckpayment extends \Magento\Framework\App\Action\Action
        {

            protected $_stkpush;
            protected $_mpesaFactory;
            protected $cart;
            protected $_myHelper;


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
        $this->_myHelper = $myHelper;
        $this->urlBuilder = $urlBuilder;
        $this->callback = $callback;
        $this->_stkpush = $stkpush;
        $this->cart = $cart;
    }


            public function execute(){

                $accessTokenObj = $this->_myHelper->generateToken();
        if(isset($accessTokenObj->access_token) && !empty($accessTokenObj->access_token)) {
        $accessToken = $accessTokenObj->access_token;

            $checkout_r_id = $this->getRequest()->getParam('checkout_r_id');

        $currentTimestamp = time();
        $shortcode = '174379';
        $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';

        $timestamp = date("YmdHis", $currentTimestamp);

                $combined = $shortcode . $passkey . $timestamp;
                $base64Password = base64_encode($combined);
                $password = $base64Password;
                        $requestPayload = [
                            "BusinessShortCode" => $shortcode,
                            "Password" => $password,
                            "Timestamp" => $timestamp,
                            "CheckoutRequestID" => $checkout_r_id
                        ];

                        $payload = json_encode($requestPayload);

                        // Initialize cURL session
                        $ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query');

                        // Set cURL options
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Authorization: Bearer ' . $accessToken,
                            'Content-Type: application/json'
                        ]);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        // Execute cURL session and get the response
                        $response = curl_exec($ch);

                        // Check for cURL errors
                        if (curl_errno($ch)) {
                            echo 'cURL Error: ' . curl_error($ch);
                        }

                        // Close cURL session
                        curl_close($ch);
                        $my_response = json_decode($response, true);


                        $code = null;
                        $success = false;
                        $message = 'Transaction Faild';

                                    if ($my_response['ResultCode'] !== null) {
                                        $success = true;
                                            $code = $my_response['ResultCode'];

                                        if (!empty($my_response['ResultDesc'])) {
                                            $message = $my_response['ResultDesc'];
                                        } else {
                                            $message = 'Transaction Faild';
                                        }

                                        // $record->setStatus(1);
                                        // $record->save();
                                    }

                echo json_encode(['success' => $success, 'message' => $message, 'code' => $code]);


                    }else{
                    echo json_encode(['success'=> 'error','message'=>"Token generatio problem please try again"]);
            }
               
                 }
                
        }