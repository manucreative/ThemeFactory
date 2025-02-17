<?php
namespace ThemeFactory\Mpesastk\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Stkpush extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('themeFactory_mpesastk_stkpush', 'stkpush_id');
    }
}