<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Newsletter Category Edit Form Block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Appstore\Gridpart2\Block\Adminhtml\Category\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Appstore\Gridpart2\Helper\Option
     */
    protected $_statusOption;

    /**
     * @param \Magento\Backend\Block\Category\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Category\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Appstore\Gridpart2\Helper\Option $optionData,
        array $data = []
    ) {
       
        $this->_statusOption = $optionData;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Retrieve template object
     *
     * @return \Magento\Newsletter\Model\Template
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_gridpart2_category');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();
        

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('App Information'), 'class' => 'fieldset-wide']
        );
        $fieldset->addType('background', 'Appstore\Gridpart2\Block\Adminhtml\Template\Helper\Background');
        
       
        if ($model->getGridpart2templateId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getGridpart2templateId()]);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('App Name'),
                'title' => __('App Name'),
                'required' => true,
                'value' => $model->getName()
            ]
        );

        $fieldset->addField(
            'background',
            'background',
            [
                'name' => 'background',
                'label' => __('App Image'),
                'title' => __('App Image'),
                'required' => true,
                'value' => $model->getBackground()
            ]
        );
        


        $fieldset->addField(
            'stylecolor',
            'text',
            [
                'name' => 'stylecolor',
                'label' => __('Android Url'),
                'title' => __('Android Url'),
                'required' => true,
                'value' => $model->getStylecolor()
            ]
        );
        
        $fieldset->addField(
            'textcolor',
            'text',
            [
                'name' => 'textcolor',
                'label' => __('Ios Url'),
                'title' => __('Ios Url'),
                'required' => true,
                'value' => $model->getIsoroidurl()
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'required' => true,
                'name' => 'status',
                'values' => $this->_statusOption->getStatusesOptionArray()
            ]
        );

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
