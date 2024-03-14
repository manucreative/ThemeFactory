<?php
namespace ThemeFactory\ExportInvoices\Controller\Adminhtml\Invoice;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use ThemeFactory\ExportInvoices\Model\ResourceModel\Invoice\CollectionFactory;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Customer\Model\GroupFactory;
use Zend\Log\Logger;
use Zend\Log\Writer\Stream;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Catalog\Model\ResourceModel\Product;
use Magento\Eav\Model\Config;

class Export extends Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $invoiceCollectionFactory;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var Logger
     */
    protected $logger;

    protected $invoiceFactory;

    protected $_orderFactory;

    protected $_groupFactory;

    protected $dateTime;

    protected $productResource;

    protected $_productRepository;

    protected $eavConfig;


    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $invoiceCollectionFactory,
        FileFactory $fileFactory,
        Logger $logger,
        \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        OrderFactory $orderFactory,
        GroupFactory $groupFactory,
        DateTime $dateTime,
        Product $productResource,
        Config $eavConfig
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->invoiceCollectionFactory = $invoiceCollectionFactory;
        $this->fileFactory = $fileFactory;
        $this->logger = $logger;
        $this->invoiceFactory = $invoiceFactory;
        $this->_orderFactory = $orderFactory;
        $this->_groupFactory = $groupFactory;
        $this->dateTime = $dateTime;
        $this->productResource = $productResource;
        $this->_productRepository = $productRepository;
        $this->eavConfig = $eavConfig;
    }

    public function execute()
{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/InvoicingExport.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        try {


         $selected = $this->getRequest()->getParam('selected');
        $exportedItems = [];

         $filters = $this->getRequest()->getParam('filters');

        // Check if 'created_at' filter exists
        if (isset($filters['created_at']) && is_array($filters['created_at'])) {
            // Get 'from' and 'to' dates
            $fromDate = $filters['created_at']['from'];
            $toDate = $filters['created_at']['to'];

            // Log the dates for debugging
            // $logger->info('From Date: ' . print_r($fromDate, true));
            // $logger->info('To Date: ' . print_r($toDate, true));
        } else {
            // Default values or error handling if needed
            $fromDate = date('Y-m-d'); // Set default to today
            $toDate = date('Y-m-d'); // Set default to today

            // Log error or default values
            $logger->info('No date filters found, using default dates.');
        }

        foreach ($selected as $invoiceId) {
            $invoice = $this->invoiceFactory->create()->load($invoiceId);
            if ($invoice->getId()) {
                $items = [];
            
                // $logger->info('Controll units: ' . print_r($invoice->getControlUnitInvoiceNo(), true));
                foreach ($invoice->getAllItems() as $item) {
                    $productId = $item->getProductId();
                     
                    if ($productId) {
                       $product = $this->_productRepository->getById($productId);
            
            // var_dump($product->getData());
                            // Get specific attribute value
                            $vendorCats = $product->getData('vendor_cats');
                            if ($vendorCats !== null) {
                                $vendorCategories = $this->getAttributeValue($vendorCats);
                            } else {
                                $vendorCategories = "N/A";
                            }

                            $vendor = $product->getData('vendor_seller');
                            if ($vendor !== null) {
                                $vendorData = $this->getVendorValue($vendor);
                            } else {
                                $vendorData = "N/A";
                            }
                    }else{

                    }
                    $customColumnValue = $item->getData('vendor');
                    $rowTotal = $item->getRowTotal() + $item->getTaxAmount();
                    
                        // $logger->info('My attribute: ' . print_r($vendorCategories, true));
                    
                    $items[] = [
                        'item_name' => $item->getName(),
                        'item_price' => $item->getPrice(),
                        'item_qty' => $item->getQty(),
                        'vendorCategories' => $vendorCategories,
                        'vendor' => $vendorData,
                        'subTotal' => $item->getRowTotal(), ////Subtotal excluding tax
                        'taxAmount' => $item->getTaxAmount(),
                        'rowTotal' => $rowTotal,
                        'taxPercentage' => $item->getOrderItem()->getTaxPercent(),
                        'fromDate' => $fromDate,
                        'toDate' => $toDate,
                    ];

                }

                $order = $this->_orderFactory->create()->load($invoice->getOrderId());

                // Get customer group ID
                $customerGroupId = $order->getCustomerGroupId();

                // Get customer group name
                $group = $this->_groupFactory->create()->load($customerGroupId);
                $customerGroupName = $group->getCustomerGroupCode();


                $BillingInformation = $invoice->getBillingAddress();
                $FirstName = $BillingInformation->getFirstname();
                $LastName = $BillingInformation->getLastname();
                $customerName = $FirstName . ' ' . $LastName;

                $exportedItems[] = [
                    'invoice_id' => $invoice->getIncrementId(),
                    'customer_name' => $customerName,
                    'customer_group' => $customerGroupName,
                    'total' => $invoice->getGrandTotal(),
                    'status' => $invoice->getOrder()->getStatus(),
                    'items' => $items,
                    'cuInvoiceNo' => $invoice->getControlUnitInvoiceNo(),
                    'invoiceDate' => $invoice->getCreatedAt()
                ];
            }
            

        }

        // Generate CSV content
        $content = $this->generateCsvContent($exportedItems);

        // Send file to download
        $fileName = 'exported_invoices.csv';

        $this->getResponse()->setHeader('Content-Type', 'application/csv');
        $this->getResponse()->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $this->getResponse()->setBody($content);
        return $this->getResponse();

    } catch (\Exception $e) {
        $logger->info('Error exporting invoices: ' . print_r($e->getMessage(), true));
        $this->messageManager->addError(__('An error occurred while exporting invoices.'));
        return $this->_redirect('*/*/');
    }
}

// Function to get the attribute value based on the attribute ID
private function getAttributeValue($vendorCats)
{
    $attribute = $this->eavConfig->getAttribute('catalog_product', 'vendor_cats');
    $options = $attribute->getSource()->getAllOptions();

    foreach ($options as $option) {
        if ($option['value'] == $vendorCats) {
            return $option['label'];
        }
    }

    return null;
}

private function getVendorValue($vendor)
{
    $attribute = $this->eavConfig->getAttribute('catalog_product', 'vendor_seller');
    $options = $attribute->getSource()->getAllOptions();

    foreach ($options as $option) {
        if ($option['value'] == $vendor) {
            return $option['label'];
        }
    }

    return null;
}


protected function generateCsvContent($data)
{

    $csv = '';

foreach ($data as $myData) {
        if (!empty($myData['items'])) {
            // Get the first item's vendor name
            $firstItem = reset($myData['items']); // Get the first element
            $vendor = $firstItem['vendor'];

            // Dates
            foreach($myData['items'] as $myDates){
                $fromDate = $myDates['fromDate'];
                $toDate = $myDates['toDate'];
            }

           
            // Calculate the number of empty columns needed before and after the center
            $totalColumns = 15;
            $centerColumns = 7; // The number of columns in the middle section
            $emptyColumnsBefore = floor(($totalColumns - $centerColumns) / 2);
            $emptyColumnsAfter = $totalColumns - $centerColumns - $emptyColumnsBefore;

            // Add the first part of the header with empty columns before the center
        
    }
}
    
            // Add the middle part of the header with the actual content
            $csv .= '"' . implode('","', ['', '', '', '', '', '', '', $vendor . ' Report', '', '', '', '']) . '"' . "\n";
            $csv .= '"' . implode('","', ['', '', '', '', '', '', '','From date: ' . $fromDate, '', '', '', '']) . '"' . "\n";
            $csv .= '"' . implode('","', ['', '', '', '', '', '', '','To date:  ' . $toDate, '', '', '', '']) . '"' . "\n";
            // Add a new line for the next set of data
            $csv .= "\n";
   
    // Set the headers
    $headers = [
        'Invoice ID',
        'CU Invoice Number',
        'Customer Name',
        'Sales Group',
        'Vendor',
        'Status',
        'Invoice Date',
        'Item Name',
        'Identity',
        'Price',
        'Item Quantity',
        'Subtotal',
        'Tax Amount',
        'Tax Percentage',
        'Row Total',
        'Grand Total'
    ];
    $csv .= '"' . implode('","', $headers) . '"' . "\n";

$totalGrand = 0;
    foreach ($data as $invoice) {
        // Get invoice details
        $invoiceFields = [
            $invoice['invoice_id'],
            $invoice['cuInvoiceNo'],
            $invoice['customer_name'],
            $invoice['customer_group'],
            $invoice['total'],
            $invoice['status'],
            $invoice['invoiceDate'],
        ];

        // If invoice has items
        if (!empty($invoice['items'])) {
            // Flag to check if it's the first item in the invoice
            $firstItem = true;
            // Iterate through items
            $totalGrand += $invoice['total'];
            foreach ($invoice['items'] as $item) {
                // Set invoice fields empty for subsequent rows
                $itemFields = [
                    $invoice['invoice_id'], 
                    $invoice['cuInvoiceNo'],
                    ($firstItem) ? $invoice['customer_name'] : '', 
                    ($firstItem) ? $invoice['customer_group'] : '', 
                    ($firstItem) ? $item['vendor'] : '', 
                    ($firstItem) ? $invoice['status'] : '',
                    ($firstItem) ? $invoice['invoiceDate'] : '',
                    $item['item_name'],
                    $item['vendorCategories'],
                    'Ksh. '. $item['item_price'],
                    $item['item_qty'],
                    'Ksh. '. $item['subTotal'],
                    'Ksh. '. $item['taxAmount'],
                    $item['taxPercentage'] . '%',
                    'Ksh. '. $item['rowTotal'],
                    ($firstItem) ? 'Ksh. '.$invoice['total'] : '', 
                ];
                $csv .= '"' . implode('","', $itemFields) . '"' . "\n";
                // Set the flag to false after the first item
               $firstItem = false;
                }
                $csv .= "\n"; // Skip one line after the loop
        } else {
            // If no items, still output invoice details
            $csv .= '"' . implode('","', $invoiceFields) . '","","",""' . "\n";
        }
    }
    $csv .= ',"","","","","","","","","","","","","","","Total Grand: Ksh. '.$totalGrand.'"'."\n";

    return $csv;
}


}


