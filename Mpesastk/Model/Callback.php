<?php
namespace ThemeFactory\Mpesastk\Model;

use ThemeFactory\Mpesastk\Api\CallbackInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Webapi\Rest\Request;

class Callback implements CallbackInterface
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    protected $request;
    protected $_stkpushFactory;
    protected $_stkpush;

    public function __construct(
        JsonFactory $resultJsonFactory,
        Request $request,
        \ThemeFactory\Mpesastk\Model\StkpushFactory $stkpushFactory,
        \ThemeFactory\Mpesastk\Model\Stkpush $stkpush
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->_stkpushFactory = $stkpushFactory;
        $this->_stkpush = $stkpush;
    }

    /**
     * {@inheritdoc}
     */
    public function stkCallback()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $data = $this->request->getBodyParams();

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/stkpush.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('Received callback data: ' . print_r($data, true));
            
            // Accessing values one by one
                    $merchantRequestId = $data['Body']['stkCallback']['MerchantRequestID'];
                    $checkoutRequestId = $data['Body']['stkCallback']['CheckoutRequestID'];
                    $resultCode = $data['Body']['stkCallback']['ResultCode'];
                    $resultDesc = $data['Body']['stkCallback']['ResultDesc'];

            $collection = $this->_stkpush->getCollection()
            ->addFieldToFilter('merchant_request_id',['eq'=>$merchantRequestId])
            ->addFieldToFilter('checkout_request_id',['eq'=>$checkoutRequestId])
            ->addFieldToFilter('result_desc',['neq' => 'NULL']);

            if ($collection->getSize() > 0) 
            {
                foreach($collection as $mpesa)
            {

                $mpesa->setResultCode($resultCode);
                $mpesa->setResultDesc($resultDesc);
                $mpesa->save();
            }
            $collection->save();

            }

            $mpesa = $this->_stkpushFactory->create()->load($merchantRequestId,'merchant_request_id');
                $mpesa->setResultCode($resultCode);
                $mpesa->setResultDesc($resultDesc);
                $mpesa->save();

                    // Accessing CallbackMetadata items
                $myData = 'no data';


                if($resultCode == 0){
                        $items = $data["Body"]["stkCallback"]["CallbackMetadata"]['Item'];

                        if(is_array($items))
                        {
                            $receipt = $amount = $Balance = $TransactionDate = $PhoneNumber = null;
                            foreach($items as $item)
                            {
                                $item['Name'] == 'Amount' ? isset($item['Value']) ? $amount = $item['Value'] : '' : '';
                                $item['Name'] == 'MpesaReceiptNumber' ? isset($item['Value']) ? $receipt = $item['Value'] : '' : '';
                                $item['Name'] == 'Balance' ? isset($item['Value']) ? $Balance = $item['Value'] : '' : '';
                                $item['Name'] == 'TransactionDate' ? isset($item['Value']) ? $TransactionDate = $item['Value'] : '' : '';
                                $item['Name'] == 'PhoneNumber' ? isset($item['Value']) ? $PhoneNumber = $item['Value'] : '' : '';

                            }
                            $mpesa->setTransId($receipt);
                            $mpesa->setCallbackTime($TransactionDate);
                            $mpesa->setTransAmount($amount);
                            $mpesa->setMsisdn($PhoneNumber);
                            $mpesa->setRequestTime($TransactionDate);
                            $mpesa->save();
                        }

                    }
                    

            
                return $myData;
        } catch (\Exception $e) {

            $logger->error('Error processing callback data: ' . $e->getMessage());

            return $e->getMessage();
        }
    }
}