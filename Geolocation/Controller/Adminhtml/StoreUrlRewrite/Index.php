<?php
namespace Manwiks\Geolocation\Controller\Adminhtml\StoreUrlRewrite;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $resultPageFactory;

    public function __construct(Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Manwiks_Geolocation::main_menu');
        $resultPage->getConfig()->getTitle()->prepend(__('Alladin Shops URL Rewrite'));

        return $resultPage;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Manwiks_Geolocation::main_menu');
    }
}
