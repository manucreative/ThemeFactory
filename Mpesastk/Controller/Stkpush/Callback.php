<?php
namespace ThemeFactory\Mpesastk\Controller\Stkpush;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Request\Http;

class Callback extends Action
{
    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    protected $request;

    protected $_stkpush;


    /**
     * Constructor
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        Http $request,
        \ThemeFactory\Mpesastk\Model\StkpushFactory $stkpush
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->request = $request;
        $this->_stkpush = $stkpush;
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
public function execute()
{
    $result = $this->resultJsonFactory->create();

    try {
        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new \Exception('Invalid request method');
        }

        $recievedData = $this->getRequest()->getPost();

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/stkpush.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('stkpush:- ' . $recievedData, true);

        $data = json_decode($recievedData, true);

        if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to decode JSON data');
        }

        return $result->setData(['success' => true, 'message' => 'Received POST data successfully']);
    } catch (\Exception $e) {
        // Log the error
        $logger->error('Error occurred: ' . $e->getMessage());

        return $result->setData(['success' => false, 'message' => $e->getMessage()]);
    }
}


}

        // $ResultCode = $data["Body"]["stkCallback"]["ResultCode"];
        // $ResultDesc = $data["Body"]["stkCallback"]["ResultDesc"];
        // $MerchantRequestID = $data["Body"]["stkCallback"]["MerchantRequestID"];
        // $CheckoutRequestID = $data["Body"]["stkCallback"]["CheckoutRequestID"];
        
        // $collection = $this->_stkpush->getCollection()->addFieldToFilter('merchant_request_id',['eq'=>$MerchantRequestID])
        //     ->addFieldToFilter('checkout_request_id',['eq'=>$CheckoutRequestID]);
        // if($collection)
        // {
        //     foreach($collection as $mpesa)
        //     {

        //         $mpesa->setResultCode($ResultCode);
        //         $mpesa->setResultDesc($ResultDesc);
        //         $mpesa->save();

        //     }
        //     $collection->save();
        // }

        // $mpesa = $this->_stkpush->create()->load($MerchantRequestID,'merchant_request_id');
        // $mpesa->setResultCode($ResultCode);
        // $mpesa->setResultDesc($ResultDesc);
        // $mpesa->save();

        // if($ResultCode == 0){
        //     $items = $data["Body"]["stkCallback"]["CallbackMetadata"]['Item'];

        //     if(is_array($items))
        //     {
        //         $receipt = $amount = $Balance = $TransactionDate = $PhoneNumber = null;
        //         foreach($items as $item)
        //         {
        //             $item['Name'] == 'Amount' ? isset($item['Value']) ? $amount = $item['Value'] : '' : '';
        //             $item['Name'] == 'MpesaReceiptNumber' ? isset($item['Value']) ? $receipt = $item['Value'] : '' : '';
        //             $item['Name'] == 'Balance' ? isset($item['Value']) ? $Balance = $item['Value'] : '' : '';
        //             $item['Name'] == 'TransactionDate' ? isset($item['Value']) ? $TransactionDate = $item['Value'] : '' : '';
        //             $item['Name'] == 'PhoneNumber' ? isset($item['Value']) ? $PhoneNumber = $item['Value'] : '' : '';

        //         }
        //         $data = ['trans_id'=>$receipt,'callback_time'=>$TransactionDate,'trans_amount'=>$amount,
        //             'msisdn'=>$PhoneNumber,'invoice_number'=>$mpesa->getStkpushId(),'trans_time'=>$TransactionDate
        //             // 'business_shortcode'=> $this->helper->getGeneralConfig('my_paybill'), 'bill_ref_number'=>$mpesa->getAccountId()
        //         ];
        //         // $this->_mpesa->setData($data)->save();
        //     }

        // }


