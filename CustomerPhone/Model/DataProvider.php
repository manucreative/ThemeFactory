<?php

namespace ThemeFactory\CustomerPhone\Model;
use Magento\Customer\Model\DataProvider as CustomerDataProvider;

class DataProvider extends CustomerDataProvider {
    protected function prepareEntityFormOptions(\Magento\Framework\DataObject $entity) {
        parent::prepareEntityFormOptions($entity);

        $entity->setTelephone($entity->getCustomAttribute('telephone'));
    }
}
