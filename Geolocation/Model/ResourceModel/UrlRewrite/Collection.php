<?php
namespace Manwiks\Geolocation\Model\ResourceModel\UrlRewrite;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Manwiks\Geolocation\Model\UrlRewrite as UrlRewriteModel;
use Manwiks\Geolocation\Model\ResourceModel\UrlRewrite as UrlRewriteResourceModel;

class Collection extends AbstractCollection
{
    // protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(UrlRewriteModel::class, UrlRewriteResourceModel::class);
    }
}

