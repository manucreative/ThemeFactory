<?php
/** 
 * @package   Manwiks_Geolocation
 * @author    Emmanuel Kirui
 */
namespace Manwiks\Geolocation\Controller\Adminhtml\StoreUrlRewrite;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Manwiks\Geolocation\Model\ResourceModel\UrlRewrite\CollectionFactory;
use Manwiks\Geolocation\Model\UrlRewriteFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Massactions filter.​_
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;
    protected $_urlRewriteFactory;


    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        UrlRewriteFactory $urlRewriteFactory
    ) {

        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/DeletedData.log');
        // $logger = new \Zend_Log();
        // $logger->addWriter($writer);

        $collection = $this->_filter->getCollection($this->_collectionFactory->create());

        $recordDeleted = 0;
        foreach ($collection->getItems() as $record) {
            // $logger->info('Item data: ' . print_r($record->getData(), true));
            $id = $record->getId();
            
            // Load the actual model instance by ID
            $urlRewrite = $this->_urlRewriteFactory->create()->load($id);
            if ($urlRewrite->getId()) {
                $urlRewrite->delete();
                $recordDeleted++;
            } else {
                $logger->info('Item data: ' . print_r('Failed to load URL rewrite with ID '. $id, true));
            }
        }

         if ($recordDeleted > 0) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $recordDeleted));
        } else {
            $this->messageManager->addErrorMessage(__('No records were deleted.'));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Manwiks_Geolocation::shop_data_delete');
    }

    //     $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/DeletedData.log');
    //     $logger = new \Zend_Log();
    //     $logger->addWriter($writer);

    //     $collection = $this->_filter->getCollection($this->_collectionFactory->create());

         
    //     $recordDeleted = 0;
    //     foreach ($collection->getItems() as $record) {
    //             $logger->info('deleteData: ' . print_r($record->getData(), true));
    //         $record->getId();
    //         $record->delete();
    //         $recordDeleted++;
    //     }
    //     $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDeleted));

    //     return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    // }

    // /**
    //  * Check Category Map recode delete Permission.
    //  * @return bool
    //  */
    // protected function _isAllowed()
    // {
    //     return $this->_authorization->isAllowed('Manwiks_Geolocation::shop_data_delete');
    // }
}