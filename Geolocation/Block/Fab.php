<?php
namespace Manwiks\Geolocation\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Session\SessionManagerInterface;
use Manwiks\Geolocation\Helper\ConfigHelper;

class Fab extends Template
{

    protected $configHelper;

    public function __construct(
        Template\Context $context,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
    }

    public function isEnabled()
    {
        return $this->configHelper->isEnabled();
    }

    public function showFab()
    {
        return $this->configHelper->showFab();
    }
    public function getMyUrl()
    {
        return $this->getUrl('geolocation/ajax/fetchstores');
    }
    public function getPopupTitle()
    {
        return $this->configHelper->getPopupTitle();
    }
}
