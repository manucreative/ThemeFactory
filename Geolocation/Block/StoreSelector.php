<?php
namespace Manwiks\Geolocation\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Session\SessionManagerInterface;
use Manwiks\Geolocation\Helper\ConfigHelper;

class StoreSelector extends Template
{
     protected $session;
     protected $configHelper;

    public function __construct(
        Template\Context $context,
        SessionManagerInterface $session,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        $this->session = $session;
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    public function shouldShowPopup()
    {
        return $this->session->getShowStoreSelectionPopup();
    }

    public function getPopupUrl()
    {
        return $this->getUrl('geolocation/redirect/index');
    }

    public function getPopupTitle()
    {
        return $this->configHelper->getPopupTitle();
    }
}
