<?php

/**
 * Grid Admin Cagegory Map Record Save Controller.
 * @category  Manwiks
 * @package   Manwiks_Geolocation
 * @author    Emmanuel Kiri
 */
namespace Manwiks\Geolocation\Controller\Adminhtml\StoreUrlRewrite;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Manwiks\Geolocation\Model\UrlRewriteFactory
     */
    var $urlFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Manwiks\Geolocation\Model\UrlRewriteFactory $urlFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Manwiks\Geolocation\Model\UrlRewriteFactory $urlFactory
    ) {
        parent::__construct($context);
        $this->urlFactory = $urlFactory;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('urlgeolocation/storeurlrewrite/addweb');
            return;
        }
        try {
            $webData = $this->urlFactory->create();
            $data['update_time'] = date('Y-m-d H:i:s');
            $webData->setData($data);
            if (isset($data['id'])) {
                $webData->setId($data['id']);
            }
            $webData->save();
            $this->messageManager->addSuccess(__('Website data has been successfully saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('urlgeolocation/storeurlrewrite/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Manwiks_Geolocation::save');
    }
}