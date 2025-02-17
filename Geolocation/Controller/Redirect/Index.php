<?php
namespace Manwiks\Geolocation\Controller\Redirect;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Session\SessionManagerInterface;

class Index extends Action
{
    protected $resultJsonFactory;
    protected $session;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        SessionManagerInterface $session
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->session = $session;
        parent::__construct($context);
    }

    public function execute()
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/StoreHere.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);

        $result = $this->resultJsonFactory->create();
        $stores = $this->session->getNearbyStores();
        $this->session->unsNearbyStores();
        $this->session->unsShowStoreSelectionPopup();  // Clear the flag
        // $this->session->unsGeoIPRedirectDone();
        $result->setData(['stores' => $stores]);
        // $logger->info('StoresData: ' . print_r($result, true));
        return $result;
    }
}
 
