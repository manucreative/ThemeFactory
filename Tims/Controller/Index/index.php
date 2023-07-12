<?php

namespace ThemeFactory\Tims\Controller\Index;

require_once(__DIR__ . '../../../Tremol/fp.php');
require_once(__DIR__ . '../../../Tremol/FP_Core.php');

// if (file_exists('fp_ext.php')) {
//     require_once('fp_ext.php');
// }
//session_start();



// use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use ThemeFactory\Tims\Tremol\FP;
use ThemeFactory\Tims\Tremol\FP_Core;
// use Magento\Framework\Json\Helper\Data;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Json\Decoder;

class Index extends Action
{
    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     * CurlFactory
     */
    protected $_curlFactory;

    /**
     * Tims Library Init
     * ThemeFactory\Tims\Tremol\FP
     *
     */
    protected $_fpLibrary;

    // /**
    //  * Simple Xml Init
    //  * Magento\Framework\Simplexml\Element
    //  *
    //  */

    // protected $_simpleXml;

    // /**
    //  *
    //  * @var \Magento\Framework\Serialize\Serializer\Json
    //  */

    // protected $_jsonSerializer;
    // protected $_jsonDataHelper;
    protected $_newJsonDecoder;
    protected $_serverAddress;
    protected $_serverTcpPort;
    protected $_devIp;
    protected $_devTcpPort;
    protected $_devTcpPassword;
    protected $_devSerialPort;
    protected $_devBoudRate;




    public function __construct(

        FP_Core $fp,
        FP $fpLibrary,
        Context $context,
        CurlFactory $curlFactory,
        // Data $jsonDataHelper,
        //     Json $jsonSerializer,
        Decoder $newJsonDecoder
        //     Element $simpleXml,

    ) {
        $this->FP = $fpLibrary;
        $this->fp = $fp;


        $this->_curlFactory = $curlFactory;
        // $this->_jsonDataHelper = $jsonDataHelper;
        //     $this->_jsonSerializer = $jsonSerializer;
        $this->_newJsonDecoder = $newJsonDecoder;
        //     $this->_simpleXml = $simpleXml;


        parent::__construct($context);
    }
    public function execute()
    {
        // session_unset();

        // echo "testing";
        // echo "<br/>";
        // echo gethostbyname('localhost');
        // echo "<br/>";

        // Getting invoice values from Observer

        // echo "<br/>";
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $observerData = $objectManager->get(\ThemeFactory\Tims\Observer\Order\Invoice\Pay::class);
        // print_r($objectManager);
        // echo "<br/>";
        // var_dump($objectManager);
        // exit();


        //End of getting invoice values from Observer


        $this->getValues();

        $data_array = $this->getValues();

        // print_r($data_array);
        // exit();

        // This is the definition of the data arrays
        $data_values = array(
            'ServerAddress' => $data_array["address"],
            'ServerPort' => $data_array["port"],
            'DevIp' => $data_array["ip"],
            'DevTcpPort' => $data_array["tcp_port"],
            'DevPassword' => $data_array["fp_pass"],
            'DevSerialPort' => $data_array["dev_serial_port"],
            'DevBaudRate' => $data_array["dev_boud"],
            'Operator1password' => $data_array["op_pass"]
        );


        // print_r($data_values);
        // exit();
        // try {
        // Fetching data from Array and storing in variable for using in function

        // This fetches data from array and passes it to function to create url for curl
        $ServerAddress = $data_values["ServerAddress"];
        $ServerTcpPort = $data_values["ServerPort"];

        $this->fp->ServerSetSettings($ServerAddress, $ServerTcpPort);

        // Now se set the device tcp settings
        $ipaddress = $data_values["DevIp"];
        $tcpport = $data_values["DevTcpPort"];
        $password = $data_values["DevPassword"];

        //We open a connection to the TCP device This is available in the FP_Core file.

        $this->fp->ServerSetDeviceTcpSettings($ipaddress, $tcpport, $password);

        //transmission
        $this->ReadState();
        $this->OpenInvoice();
        $this->SellPluNow();
        $this->FP->ReadVATrates();
        $ReturnedReceiptInfo = $this->FP->CloseReceipt();
        $this->FP->ReadDateTime();
        $this->fp->ServerCloseDeviceConnection();
        var_dump($ReturnedReceiptInfo);
        exit();
        //end of transmission


        // Now we set the device serial settings

        // $serialPort = $data_values["DevSerialPort"];
        // $baudRate = $data_values["DevBaudRate"];

        // $this->fp->ServerSetDeviceSerialSettings($serialPort, $baudRate, FALSE);

        //multiple executions
        $functions = array();
        foreach ($functions as $function) $function();

        // // Open the receipt
        // $functions[] = $this->OpenNewReceipt();

        // // Now we open the Invoice function with customer data
        // $functions[] = $this->OpenInvoice();

        // // Now we launch the Sell PLU
        // $functions[] = $this->SellPluNow();

        // // Now we close the receipt and return info

        // $functions[] = $this->FP->CloseReceipt();

        //end of multiple executions

        //We close the connection to the TCP device  and the receipt this is available in the fp file. (This returns the receipt info)
        //$ReturnedReceiptInfo = $this->FP->CloseReceipt();

        //This closes the connection to the CU device
        // $this->fp->ServerCloseDeviceConnection();

        // print_r($ReturnedReceiptInfo);
        // exit();

        // } catch (Exception $e) {
        //echo "There is a big error. Check it out";

        //print($functions[$this->OpenInvoice()]);
        //var_dump($this->$functions);
        //exit();

        //}




        //********************************************************************** */
    }
    public function ReadState()
    {
        $this->FP->ReadStatus();
    }
    public function OpenInvoice()
    {
        //Now we open the Invoice function with customer data (The buying party details)

        $CompanyName = "Alladin Client";
        $ClientPINnum = "P051609805L"; //P051609805L
        $HeadQuarters = "Thika Road Mall";
        $Address = "674, Unit 38A, 1st Floor";
        $PostalCodeAndCity = "00621 Nairobi";
        $ExemptionNum = "0";
        //$RelatedInvoiceNum = "0000000034";
        $TraderSystemInvNum = "0000000045";

        //OptionInvoicePrintType:: ,

        $this->FP->OpenInvoiceWithFreeCustomerData($CompanyName, $ClientPINnum, $HeadQuarters, $Address, $PostalCodeAndCity, $ExemptionNum, $TraderSystemInvNum);
    }
    public function OpenNewReceipt()
    {
        //Now we open the Invoice function with customer data

        $OptionReceiptFormat = "1";
        $TraderSystemInvNum = "0000000045";

        $this->FP->OpenReceipt(1, "0000", $OptionReceiptFormat, $TraderSystemInvNum);
    }
    public function SellPluNow()
    {

        //Now we launch the Sell PLU

        $NamePLU = "GAS OILS";
        $OptionVATClass = 'B';
        $Price = "1220.00";
        $MeasureUnit = "kg";
        $HSCode = "";
        $HSName = "gas oil automotive light amber for high speed engines";
        $VATGrRate = "16";
        $Quantity = "1"; // Not compulsory // Decimal
        $DiscAddP = "1"; // 1.00 // Not compulsory //Deciamal
        $this->FP->SellPLUfromExtDB($NamePLU, $OptionVATClass, $Price, $MeasureUnit, $HSCode, $HSName, $VATGrRate, $Quantity, $DiscAddP);
    }

    public function getValues()
    {

        $url = 'http://localhost/magento8/index.php/rest/V2/kratest/connectdevice';

        $requestBody =
            [
                "address" => "",
                "port" => "",
                "ip" => "",
                "tcp_port" => "",
                "zfp_pass" => "",
                "dev_serial_port" => "",
                "dev_boud" => "",
                "op_pass" => ""
            ];

        //create curl Factory
        $httpAdapter = $this->_curlFactory->create();

        $httpAdapter->write(\Zend_Http_Client::POST, $url, '1.1', ["Content-Type:application/json"], json_encode($requestBody));
        $results = $httpAdapter->read();
        $body = \Zend_Http_Response::extractBody($results);

        /* convert JSON to Array */
        $response = $this->_newJsonDecoder->decode($body);


        return $response;
        // print_r($response);
        // exit();
    }
}