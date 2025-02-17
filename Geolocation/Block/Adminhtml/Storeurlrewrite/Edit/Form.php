<?php
namespace Manwiks\Geolocation\Block\Adminhtml\Storeurlrewrite\Edit;


/**
 * Adminhtml Add New Row Form.
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $_stores;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Manwiks\Geolocation\Model\Status $options,
        \Manwiks\Geolocation\Model\Stores $stores,
        array $data = []
    ) 
    {
        $this->_options = $options;
        $this->_stores = $stores;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $dateFormat = $this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
        $model = $this->_coreRegistry->registry('shop_data');
        $form = $this->_formFactory->create(
            ['data' => [
                            'id' => 'edit_form', 
                            'enctype' => 'multipart/form-data', 
                            'action' => $this->getData('action'), 
                            'method' => 'post'
                        ]
            ]
        );

        $form->setHtmlIdPrefix('mksgeoid_');
        if ($model->getId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Website Url'), 'class' => 'fieldset-wide']
            );
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Website Url'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Shop Name'),
                'id' => 'name',
                'title' => __('Shop Name'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'location',
            'text',
            [
                'name' => 'location',
                'label' => __('Shop Location'),
                'id' => 'location',
                'title' => __('Shop Location'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

         $fieldset->addField(
            'store_id',
            'select',
            [
                'name' => 'store_id',
                'label' => __('Select Store Name'),
                'id' => 'store_id',
                'title' => __('Select Store Name'),
                'values' => $this->_stores->getAllOptions(),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        // code for adding text area

        // $wysiwygConfig = $this->_wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        // $fieldset->addField(
        //     'content',
        //     'editor',
        //     [
        //         'name' => 'content',
        //         'label' => __('Content'),
        //         'style' => 'height:36em;',
        //         'required' => true,
        //         'config' => $wysiwygConfig
        //     ]
        // );
         $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('Shop Url'),
                'id' => 'url',
                'title' => __('Shop Url'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'lat',
            'text',
            [
                'name' => 'lat',
                'label' => __('Latitude'),
                'id' => 'lat',
                'title' => __('Latitude'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'lng',
            'text',
            [
                'name' => 'lng',
                'label' => __('Longitude'),
                'id' => 'lng',
                'title' => __('Longitude'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Status'),
                'id' => 'is_active',
                'title' => __('Status'),
                'values' => $this->_options->getOptionArray(),
                'class' => 'status',
                'required' => true,
            ]
        );
        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}