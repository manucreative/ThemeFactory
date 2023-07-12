<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace ThemeFactory\Tims\Model\Order\Pdf;;

class Creditmemo extends \Magento\Sales\Model\Order\Pdf\Creditmemo
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Sales\Model\Order\Pdf\Config $pdfConfig
     * @param \Magento\Sales\Model\Order\Pdf\Total\Factory $pdfTotalFactory
     * @param \Magento\Sales\Model\Order\Pdf\ItemsFactory $pdfItemsFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @internal param \Magento\Framework\TranslateInterface $translate
     */
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
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_localeResolver = $localeResolver;
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
    }

    /**
     * Draw table header for product items
     *
     * @param  \Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(\Zend_Pdf_Page $page)
    {
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 30);
        $this->y -= 10;
        $page->setFillColor(new \Zend_Pdf_Color_Rgb(0, 0, 0));

        //columns headers
        $lines[0][] = ['text' => __('Products'), 'feed' => 35];

        $lines[0][] = [
            'text' => $this->string->split(__('SKU'), 12, true, true),
            'feed' => 255,
            'align' => 'right',
        ];

        $lines[0][] = [
            'text' => $this->string->split(__('Total (ex)'), 12, true, true),
            'feed' => 330,
            'align' => 'right',
        ];

        $lines[0][] = [
            'text' => $this->string->split(__('Discount'), 12, true, true),
            'feed' => 380,
            'align' => 'right',
        ];

        $lines[0][] = [
            'text' => $this->string->split(__('Qty'), 12, true, true),
            'feed' => 445,
            'align' => 'right',
        ];

        $lines[0][] = [
            'text' => $this->string->split(__('Tax'), 12, true, true),
            'feed' => 495,
            'align' => 'right',
        ];

        $lines[0][] = [
            'text' => $this->string->split(__('Total (inc)'), 12, true, true),
            'feed' => 565,
            'align' => 'right',
        ];

        $lineBlock = ['lines' => $lines, 'height' => 10];

        $this->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }

    /**
     * Return PDF document
     *
     * @param  array $creditmemos
     * @return \Zend_Pdf
     */
    public function getPdf($creditmemos = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('creditmemo');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($creditmemos as $creditmemo) {
            if ($creditmemo->getStoreId()) {
                $this->_localeResolver->emulate($creditmemo->getStoreId());
                $this->_storeManager->setCurrentStore($creditmemo->getStoreId());
            }
            $page = $this->newPage();
            $order = $creditmemo->getOrder();
            /* Add image */
            $this->insertLogo($page, $creditmemo->getStore());
            /* Add address */
            $this->insertAddress($page, $creditmemo->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_CREDITMEMO_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Credit Memo # ') . $creditmemo->getIncrementId());
            /* Add table head */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($creditmemo->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $creditmemo);

            // Get the Data links
            $newInvoiceNumber = $creditmemo->getIncrementId();
            $controlunitSerialNumber = $creditmemo->getControlUnitSerialNo();
            $ControlUnitDateTime = $creditmemo->getControlUnitDateAndTime();
            $ControlUnitInvoiceNo = $creditmemo->getControlUnitCreditMemoNo();
            $ContolUnitQr = $creditmemo->getQrCodeLink();
            $ContolUnitQrCodeLink = basename($ContolUnitQr, '.png');

            // Get the QR Code Image and the Invoice Details

        }
        $this->_afterGetPdf();
        if ($creditmemo->getStoreId()) {
            $this->_localeResolver->revert();
        }

        $this->_drawFooter($page, $controlunitSerialNumber, $ControlUnitDateTime, $ControlUnitInvoiceNo, $ContolUnitQrCodeLink);
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
        $page->drawText("Control Unit Credit Memo Number :" . $ControlUnitInvoiceNo . " | " . "Control Unit Date and Time :" . $ControlUnitDateTime, 150, $this->y -= 10, 'UTF-8');
        // $page->drawText("Control Unit Date and Time :" . $ControlUnitDateTime, 240, $this->y -= 20, 'UTF-8');

        // $page->drawText($ContolUnitQrCodeLink . "Information - E.O.D. De Huesr Mester, Postcode 89, 7895 AA Genet", 180, $this->y, 'UTF-8');
        // $page->drawText("ABC: 12345678 (ABC Corporation) - BTW nummer: AB1234567890 - IBAN: AA78 JHOA 1234 56789 00", 120, $this->y -= 15, 'UTF-8');
        //$page->drawText("Registered in Countryname", 430, $this->y, 'UTF-8');

    }

    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return \Zend_Pdf_Page
     */
    public function newPage(array $settings = [])
    {
        $page = parent::newPage($settings);
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }
}