<?php
/**
 * List Controller.
 * @category  GeoIp
 * @package   Manwiks_Geolocation
 * @author    Manwiks
 */
namespace Manwiks\Geolocation\Controller\Adminhtml\StoreUrlRewrite;

use Magento\Framework\Controller\ResultFactory;

class AddWeb extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Manwiks\Geolocation\Model\GridFactory
     */
    private $urlFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Manwiks\Geolocation\Model\UrlRewriteFactory $urlFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Manwiks\Geolocation\Model\UrlRewriteFactory $urlFactory
    ) {
        parent::__construct($context);
        $this->coreRegistry = $coreRegistry;
        $this->urlFactory = $urlFactory;
    }

    /**
     * Mapped Grid List page.
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $rowId = (int) $this->getRequest()->getParam('id');
        $storeData = $this->urlFactory->create();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        if ($rowId) {
           $storeData = $storeData->load($rowId);
           $storeName = $storeData->getName();
           if (!$storeData->getId()) {
               $this->messageManager->addError(__('Shop data no longer exist.'));
               $this->_redirect('urlgeolocation/storeurlrewrite/rowdata');
               return;
           }
       }

       $this->coreRegistry->register('shop_data', $storeData);
       $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
       $title = $rowId ? __('Edit ').$storeName : __('Add New Website Url');
       $resultPage->getConfig()->getTitle()->prepend($title);
       return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Manwiks_Geolocation::add_web');
    }
}