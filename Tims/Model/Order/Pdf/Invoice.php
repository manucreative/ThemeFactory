<?php

namespace ThemeFactory\Tims\Model\Order\Pdf;

class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{

    /*You can override function of 'Magento\Sales\Model\Order\Pdf\Invoice' file here based on your requirement. */

    /**
     * @var \RB\Vendor\Helper\Data
     */
    private $vendorHelper;
    /**
     * @var \RB\Vendor\Model\VendorFactory
     */
    private $vendorFactory;

    public function __construct(
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Sales\Model\Order\Pdf\Config $pdfConfig,
        \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory,
        \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        // \TimsKra\TimsModule\Model\VendorFactory $vendorFactory,
        // \TimsKra\TimsModule\Helper\Data $vendorHelper,
        array $data = []
    ) {
        $this->_dir = $dir;

        parent::__construct(
            $paymentData,
            $string,
            $scopeConfig,
            $filesystem,
            $pdfConfig,
            $pdfTotalFactory,
            $pdfItemsFactory,
            $localeDate,
            $inlineTranslation,
            $addressRenderer,
            $storeManager,
            $localeResolver,
            $data
        );
        //  $this->vendorHelper = $vendorHelper;
        // $this->vendorFactory = $vendorFactory->create();
    }



    /**
     * Return PDF document
     *
     * @param array|Collection $invoices
     * @return \Zend_Pdf
     */

    public function getPdf($invoices = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());

            /* Add table */

            $this->_drawHeader($page);

            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }

                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                $this->_localeResolver->revert();
            }

            // Get the Data links
            $newInvoiceNumber = $invoice->getIncrementId();
            $controlunitSerialNumber = $invoice->getControlUnitSerialNo();
            $ControlUnitDateTime = $invoice->getControlUnitDateAndTime();
            $ControlUnitInvoiceNo = $invoice->getControlUnitInvoiceNo();
            $ContolUnitQr = $invoice->getQrCodeLink();
            $ContolUnitQrCodeLink = basename($ContolUnitQr, '.png');

            // Get the QR Code Image and the Invoice Details

        }
        $this->_drawFooter($page, $controlunitSerialNumber, $ControlUnitDateTime, $ControlUnitInvoiceNo, $ContolUnitQrCodeLink);
        // $this->_afterGetPdf();
        return $pdf;
    }

    /* Add Your Custom Information you want to show in this function */
    protected function _drawFooter(\Zend_Pdf_Page $page, $controlunitSerialNumber, $ControlUnitDateTime, $ControlUnitInvoiceNo, $ContolUnitQrCodeLink)
    {
        $this->y = 50;
        $page->setFillColor(new \Zend_Pdf_Color_RGB(1, 1, 1));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(70, $this->y, 510, $this->y - 30);

        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.1, 0.1, 0.1));
        $page->setFont(\Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA), 7);
        $this->y -= 10;
        // Draw QR
        // $controlunitSerialNumber = $invoice->getControlUnitSerialNo();
        // $ControlUnitDateTime = $invoice->getControlUnitDateAndTime();
        // $ControlUnitInvoiceNo = $invoice->getControlUnitInvoiceNo();
        // $page->drawText("............................................ Tims Information ............................................ ", 180, $this->y, 'UTF-8');
        $page->drawText("Control Unit Serial Number :" . $controlunitSerialNumber, 240, $this->y, 'UTF-8');
        $QrImage = $this->_dir->getPath('media') . '/qr/img/' . $ContolUnitQrCodeLink . '/' . $ContolUnitQrCodeLink . '.png';
        $this->y = $this->y ? $this->y : 815;
        if (is_file($QrImage)) {
            $image       = \Zend_Pdf_Image::imageWithPath($QrImage);
            $width = 40;
            $height = 60;
            $y  =   $height / 3;
            // $page->drawImage($image, 10, $y, $y + $width / 2, $y + $height / 2);
            $page->drawImage($image, 20, $y, 55, 55);
        }
        // End of draw qr
        $page->drawText("Control Unit Invoice Number :" . $ControlUnitInvoiceNo . " | " . "Control Unit Date and Time :" . $ControlUnitDateTime, 150, $this->y -= 10, 'UTF-8');
        // $page->drawText("Control Unit Date and Time :" . $ControlUnitDateTime, 240, $this->y -= 20, 'UTF-8');

        // $page->drawText($ContolUnitQrCodeLink . "Information - E.O.D. De Huesr Mester, Postcode 89, 7895 AA Genet", 180, $this->y, 'UTF-8');
        // $page->drawText("ABC: 12345678 (ABC Corporation) - BTW nummer: AB1234567890 - IBAN: AA78 JHOA 1234 56789 00", 120, $this->y -= 15, 'UTF-8');
        //$page->drawText("Registered in Countryname", 430, $this->y, 'UTF-8');

    }

    protected function _afterGetPdf()
    {
        $pages = $this->_getPdf()->pages;
        $total = count($pages);

        $current = 1;
        foreach ($pages as $page) {

            $this->_initRenderer('invoice');
            $invoices2 = $this->getPdf();
            foreach ($invoices2 as $invoice) {

                // Get the Data links
                $newInvoiceNumber = $invoice->getIncrementId();
                $controlunitSerialNumber = $invoice->getControlUnitSerialNo();
                $ControlUnitDateTime = $invoice->getControlUnitDateAndTime();
                $ControlUnitInvoiceNo = $invoice->getControlUnitInvoiceNo();
                $ContolUnitQr = $invoice->getQrCodeLink();
                $ContolUnitQrCodeLink = basename($ContolUnitQr, '.png');

                // Get the QR Code Image and the Invoice Details
            }
            $this->_drawFooter($page, $controlunitSerialNumber, $ControlUnitDateTime, $ControlUnitInvoiceNo, $ContolUnitQrCodeLink); // This line is adding information on each pages of PDF
            $current++;
        }

        parent::_afterGetPdf();
    }
}