<?php
// namespace ThemeFactory\ExportInvoices\Block\Adminhtml\Sales;

// use Magento\Backend\Block\Widget\Context;
// use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

// class ExportButton implements ButtonProviderInterface
// {
//     /**
//      * @var Context
//      */
//     protected $context;

//     /**
//      * ExportButton constructor.
//      *
//      * @param Context $context
//      */
//     public function __construct(
//         Context $context
//     ) {
//         $this->context = $context;
//     }

//     /**
//      * @return array
//      */
//     public function getButtonData()
//     {
//         return [
//             'label' => __('Export Invoiced'),
//             'class' => 'primary',
//             'on_click' => sprintf("location.href = '%s';", $this->getButtonUrl()),
//             'sort_order' => 10,
//         ];
//     }

//     /**
//      * Get URL for the button
//      *
//      * @return string
//      */
//     public function getButtonUrl()
//     {
//         return $this->context->getUrlBuilder()->getUrl('exportInvoices/invoice/exportItems');
//     }
// }
