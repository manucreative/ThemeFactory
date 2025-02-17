<?php
namespace ThemeFactory\Mpesastk\Model\ResourceModel\Stkpush;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init('ThemeFactory\Mpesastk\Model\Stkpush', 'ThemeFactory\Mpesastk\Model\ResourceModel\Stkpush');
        parent::_construct();
    }

}