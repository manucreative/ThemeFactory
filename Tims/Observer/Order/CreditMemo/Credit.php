<?php

namespace ThemeFactory\Tims\Observer\Order\CreditMemo;

require_once(__DIR__ . '../../../../Tremol/fp.php');
require_once(__DIR__ . '../../../../Tremol/FP_Core.php');

use Psr\Log\LoggerInterface; //
use Magento\Framework\Json\Decoder;
use ThemeFactory\Tims\Tremol\FP;
use ThemeFactory\Tims\Tremol\FP_Core;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\App\Filesystem\DirectoryList; //
use Magento\Framework\Filesystem\Directory\WriteFactory; //
use Magento\Framework\Filesystem\Io\File; //




class Credit implements ObserverInterface
{
    /**
     * @var \Magento\Framework\HTTP\Adapter\CurlFactory
     * CurlFactory
     */
    protected $_curlFactory;
    protected $_newJsonDecoder;

    public function __construct(
        FP_Core $fp,
        FP $fpLibrary,
        OrderInterface $NewOrder,
        CurlFactory $curlFactory,
        Decoder $newJsonDecoder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone, //
        DirectoryList $directoryList, //
        WriteFactory $WriteFactory, //
        File $io, //
        LoggerInterface $logger, //
        \Magento\Store\Model\StoreManagerInterface $storeManager, //
        \Magento\Framework\HTTP\Client\Curl $curl //

    ) {
        $this->FP = $fpLibrary;
        $this->_storeManager = $storeManager;
        $this->fp = $fp;
        $this->logger = $logger; //
        $this->_curlFactory = $curlFactory;
        $this->_newJsonDecoder = $newJsonDecoder;
        $this->_timezone = $timezone; //
        $this->NewOrder = $NewOrder;
        $this->_directoryList = $directoryList; //
        $this->_fileWriteFactory = $WriteFactory; //
        $this->_io = $io; //
        $this->_curl = $curl; //
    }
    /**
     * @param EventObserver $observer
     */

    public function execute(EventObserver $observer)
    {
        // Testing data on log file
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/crediting.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        //Get the Values from the APi Trigger
        try {
            $data_array = $this->getValues();

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

            // This fetches data from array and passes it to function
            $ServerAddress = $data_values["ServerAddress"];
            $ServerTcpPort = $data_values["ServerPort"];

            $this->fp->ServerSetSettings($ServerAddress, $ServerTcpPort);

            // Now set the device tcp settings
            $ipaddress = $data_values["DevIp"];
            $tcpport = $data_values["DevTcpPort"];
            $password = $data_values["DevPassword"];

            //We open a connection to the TCP device This is available in the FP_Core file.

            $this->fp->ServerSetDeviceTcpSettings($ipaddress, $tcpport, $password);

            // //Lets Read the Connection Status

            $this->ReadState();

            // $logger->info('Testing Elements:- ' . print_r($data_values, true));
            // $logger->info('Testing Elements:- ' . print_r("Testing this element", true));
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }


        try {
            // Get the event data

            $creditMemo = $observer->getEvent()->getCreditmemo();
            $order = $creditMemo->getOrder();

            // // Get the Order Number
            $orderId = $order->getIncrementId();
            // $TraderSystemInvNum = $orderId;
            $TraderSystemInvNum = $creditMemo->getIncrementId();

            //Get the invoice id from the order number
            // $orderNewDetails = $this->NewOrder->load($orderId);
            $orderNewDetails = $this->NewOrder->loadByIncrementId($orderId);

            // // Check if the order has invoices
            if ($orderNewDetails->hasInvoices()) {
                foreach ($orderNewDetails->getInvoiceCollection() as $InvoiceNewNum) {

                    // $InvoiceIncrementId = $InvoiceNewNum->getIncrementId();
                    $RelatedInvNo = $InvoiceNewNum->getControlUnitInvoiceNo();
                }
                // $TraderSystemInvNum1 = $InvoiceIncrementId;
                // $TraderSystemInvNum = $TraderSystemInvNum1;
                $RelatedInvoiceNum = $RelatedInvNo;
            } else {
                $logger->info('This order has no invoices: File Credit line 123');
            }
            // Get the Billing information/Company information
            //CompanyName=,ClientPINnum=,HeadQuarters=,Address=,PostalCodeAndCity=,ExemptionNum=,RelatedInvoiceNum=,TraderSystemInvNum=
            $BillingInformation = $orderNewDetails->getBillingAddress();
            $FirstName = $BillingInformation->getFirstname();
            $SecondName = $BillingInformation->getLastname();
            $CompanyName = $FirstName . ' ' . $SecondName;
            $HeadQuarters = $BillingInformation->getRegion();
            $Address = $BillingInformation->getStreet();
            $PostalCodeAndCity = $BillingInformation->getCity();

            ////////////////////////////////////////////////////////////////////////////////////
            // Figure this shit later (Client Pin and Exemption Group)
            //Get Customer Id and Load Data Using Customer Id
            $orderForPinExc = $this->NewOrder->load($orderNewDetails->getId());
            $CustomerId = $orderForPinExc->getCustomerId();

            //$ClientPinNumber = $orderNewDetails->getVatId();
            $ClientPinNumber = $orderForPinExc->getData('taxvat');
            //$ClientPinNumber = $BillingInformation->getCustomerTaxvat();
            $ExemptionNumber = $orderForPinExc->getCustomerGroupId();
            // End of Figure this shit later (Client Pin and Exemption Group)
            /////////////////////////////////////////////////////////////////////////////////////

            //Now lets open the invoice with the customer data

            $this->OpenCreditMemo($CompanyName, $ClientPinNumber, $HeadQuarters, $Address, $PostalCodeAndCity, $ExemptionNumber, $RelatedInvoiceNum,  $TraderSystemInvNum);


            // Write Open Credit Memo Function here

            //End of open credit memo function

            $OrderItems = $creditMemo->getAllItems();
            foreach ($OrderItems as $item) {
                $NamePLU = $item->getName();
                $VATGrRate = $item->getOrderItem()->getTaxPercent();
                if (
                    $VATGrRate == 16.0000
                ) {
                    $OptionVATClass = 'A';
                } elseif ($VATGrRate == 8.0000) {
                    $OptionVATClass = 'B';
                } elseif ($VATGrRate == 0.0000) {
                    $OptionVATClass = 'C';
                } elseif ($VATGrRate == 0.0000) {
                    $OptionVATClass = 'D';
                } elseif ($VATGrRate == 0.0000) {
                    $OptionVATClass = 'E';
                }
                $Price = $item->getPrice();
                $MeasureUnit = "kg";
                $HSCode = "";
                $HSName = $item->getName();
                $Quantity = $item->getQty(); // Get the invoiced quantity
                $DiscAddP = $item->getOrderItem()->getDiscountPercent();

                $this->SellPluNow($NamePLU, $OptionVATClass, $Price, $MeasureUnit, $HSCode, $HSName, $VATGrRate, $Quantity, $DiscAddP);

                //Then we read the VAT rate for the Products
                $this->FP->ReadVATrates();

                /////////////////////////////////////////////////////////////////////////////
                // $logger->info('Get the Order Items:- ' . print_r($item->getData(), true));
                $logger->info('Get the Order Items:- ' . print_r($NamePLU, true));
                $logger->info('Get the tax rate:- ' . print_r($VATGrRate, true));
                $logger->info('Get the tax Class:- ' . print_r($OptionVATClass, true));
                $logger->info('Get the Product Price:- ' . print_r($Price, true));
                $logger->info('Get the Product Measurement Unit:- ' . print_r($MeasureUnit, true));
                $logger->info('Get the Product HSCode:- ' . print_r($HSCode, true));
                $logger->info('Get the Product HS Name:- ' . print_r($HSName, true));
                $logger->info('Get Quantity invoiced:- ' . print_r($Quantity, true));
                $logger->info('Get the Product Discount Percent:- ' . print_r($DiscAddP, true));
                $logger->info('.........................................' . print_r('.........................................', true));
                /////////////////////////////////////////////////////////////////////////////
            }
            // End of credit Memo items

            // Now we close the receipt and 
            $ReturnedReceiptInfoOld = $this->FP->CloseReceipt();

            // Now we read the date and time of the invoice creation
            $ReceiptCreationDateOld = $this->FP->ReadDateTime();

            //Then we close the Device Connection
            $this->fp->ServerCloseDeviceConnection();

            //Convert the objects to Array
            // $ReturnedReceiptInfo = $ReturnedReceiptInfoOld;

            $ReturnedReceiptInfo = $this->ProtectedToUnrotected($ReturnedReceiptInfoOld);
            $InvoiceNum = $ReturnedReceiptInfo["InvoiceNum"];
            $QRcode = $ReturnedReceiptInfo["QRcode"];

            // $logger->info('Testing the qr code link:' . print_r($QRcode, true));

            //Receipt Creation Date and Time
            $ReceiptCreationDate = (array)$ReceiptCreationDateOld;
            $dateOne = $ReceiptCreationDate["date"];
            $date = $this->_timezone->date(new \DateTime($dateOne))->format('m/d/y H:i:s');
            // $date = $dateOne;


            $SerialNo = "KRAMW011202201016397";

            //Save Data against invoice

            $creditMemo->setControlUnitDateAndTime($date);
            $creditMemo->getResource()->saveAttribute($creditMemo, "control_unit_date_and_time");

            $creditMemo->setControlUnitCreditMemoNo($InvoiceNum);
            $creditMemo->getResource()->saveAttribute($creditMemo, "control_unit_credit_memo_no");

            $creditMemo->setControlUnitSerialNo($SerialNo);
            $creditMemo->getResource()->saveAttribute($creditMemo, "control_unit_serial_no");

            // Converting the Kra Link to QR Code
            // $newQrCode = $this->generateQrCode($QRcode, $TraderSystemInvNum);
            $newQrCode = $this->generateQrCode($QRcode, $InvoiceNum);

            $logger->info('Testing the qr code link:' . print_r($newQrCode, true));

            $creditMemo->setQrCodeLink($newQrCode);
            $creditMemo->getResource()->saveAttribute($creditMemo, "qr_code_link");
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        // //Testing the Credit Memo Details
        $logger->info('Get the Order Number:- ' . print_r($orderId, true));
        // $logger->info('Get the Invoice Number:- ' . print_r($TraderSystemInvNum, true));
        $logger->info('Get Company Name:- ' . print_r($CompanyName, true));
        $logger->info('Get Client Pin Number:- ' . print_r($ClientPinNumber, true));
        $logger->info('Get HeadQuarters:- ' . print_r($HeadQuarters, true));
        $logger->info('Get Address:- ' . print_r($Address, true));
        $logger->info('Get Postal Code and City:- ' . print_r($PostalCodeAndCity, true));
        $logger->info('Get Client Exemption Number:- ' . print_r($ExemptionNumber, true));
        $logger->info('Get Related Invoice Number:- ' . print_r($RelatedInvoiceNum, true));
        $logger->info('Get Trader System Invoice Number:- ' . print_r($TraderSystemInvNum, true));
        $logger->info('Date and Time:- ' . print_r($ReceiptCreationDateOld, true));
        $logger->info('Url Link:- ' . print_r($ReturnedReceiptInfo, true));
        // Testing the credit Memo Details

        // // Return

        // return $this;
    }
    //Generate QR Code for the KRa url link
    public function generateQrCode($QRcode, $TraderSystemInvNum)
    {

        $url = $QRcode; // get the qr link
        $QR_DIR = $this->_directoryList->getPath('media') . '/qr/img/' . $TraderSystemInvNum . "/"; // name the the qr images
        $this->_io->checkAndCreateFolder($QR_DIR); // Check and create the directory
        $CleanedQrCode = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $TraderSystemInvNum);

        $size = '250x250';
        //$content = $url;
        $content = urlencode($url);
        $correction = 'L';
        $encoding = 'UTF-8';
        $filename = $CleanedQrCode . '.png';

        // Generate QR code using Google Api
        $rootUrl = "http://chart.googleapis.com/chart?cht=qr&chs=$size&chl=$content&choe=$encoding&chld=$correction";

        //Function to write Image files in Specified Directory
        if (function_exists("curl_init")) {
            $this->_curl->setOptions(array(CURLOPT_CONNECTTIMEOUT => 30, CURLOPT_SSL_VERIFYPEER => 0, CURLOPT_RETURNTRANSFER => 1));
            $this->_curl->get($rootUrl);
            $get_image = $this->_curl->getBody();
            $image_to_fetch = $get_image;
            $image_path_qr = $QR_DIR . $filename;

            $local_image_file = fopen($image_path_qr, 'w');
            $fpImage = fwrite($local_image_file, $image_to_fetch);
            fclose($local_image_file);
        }
        return $filename;
    }

    // We open the object conversion function

    /**
     * converting protected Objects to Array
     * 
     * @param ThemeFactory\Tims\Tremol\CloseReceiptRes $TheObject
     * @return array
     */

    public function ProtectedToUnrotected($TheObject)
    {
        $ObjectReflection = new \ReflectionClass(get_class($TheObject));
        $MyResults = array();
        foreach ($ObjectReflection->getProperties() as $property) {
            $property->setAccessible(true);
            $MyResults[$property->getName()] = $property->getValue($TheObject);
            $property->setAccessible(false);
        }
        return $MyResults;
    }

    public function SellPluNow($NamePLU, $OptionVATClass, $Price, $MeasureUnit, $HSCode, $HSName, $VATGrRate, $Quantity, $DiscAddP)
    {

        //Now we launch the Sell PLU

        $NamePLU = $NamePLU;
        $OptionVATClass = $OptionVATClass;
        $Price = $Price;
        $MeasureUnit = $MeasureUnit;
        $HSCode = $HSCode;
        $HSName = $HSName;
        $VATGrRate = $VATGrRate;
        $Quantity = $Quantity; // Not compulsory // Decimal
        $DiscAddP = $DiscAddP; // 1.00 // Not compulsory //Deciamal
        $this->FP->SellPLUfromExtDB($NamePLU, $OptionVATClass, $Price, $MeasureUnit, $HSCode, $HSName, $VATGrRate, $Quantity, $DiscAddP);
    }

    // We read the connection status through this function
    public function ReadState()
    {
        $this->FP->ReadStatus();
    }

    // Open credi Memo with customer data

    public function OpenCreditMemo($CompanyName, $ClientPinNumber, $HeadQuarters, $Address, $PostalCodeAndCity, $ExemptionNumber, $RelatedInvoiceNum,  $TraderSystemInvNum)
    {
        //Now we open the Invoice function with customer data (The buying party details)


        $CompanyName = $CompanyName;
        $ClientPINnum = $ClientPinNumber; //P051609805L
        $HeadQuarters = $HeadQuarters;
        $Address = $Address;
        $PostalCodeAndCity = $PostalCodeAndCity;
        $ExemptionNum = $ExemptionNumber;
        $RelatedInvoiceNum = $RelatedInvoiceNum;
        //$RelatedInvoiceNum = "0000000034";
        $TraderSystemInvNum = $TraderSystemInvNum;

        //OptionInvoicePrintType:: ,

        $this->FP->OpenCreditNoteWithFreeCustomerData($CompanyName, $ClientPinNumber, $HeadQuarters, $Address, $PostalCodeAndCity, $ExemptionNumber, $RelatedInvoiceNum,  $TraderSystemInvNum);
    }

    // We trigger the api through this function
    public function getValues()
    {

        $linkpath = "index.php/rest/V2/kratest/connectdevice";
        $url = $this->_storeManager->getStore()->getBaseUrl() . $linkpath;
        // $url = 'http://localhost/magento8/index.php/rest/V2/kratest/connectdevice';

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
    }
}