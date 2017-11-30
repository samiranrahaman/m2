<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Newsletter Template Edit Form Block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Managemarketplace\Gridpartmanage\Block\Adminhtml\Template\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Appstore\Gridpart2\Helper\Option
     */
    protected $_statusOption;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
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
        return $this->_coreRegistry->registry('_gridpartmanage_template');
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
            ['legend' => __('Marketplace Setting'), 'class' => 'fieldset-wide']
        );
       // $fieldset->addType('background', 'Managemarketplace\Gridpartmanage\Block\Adminhtml\Template\Helper\Background');
        
       
        /* if ($model->getGridpart2templateId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getGridpart2templateId()]);
        } */
		
		 /* if ($model->getGridpart2template_id()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getGridpart2template_id()]);
        } */ 
          if ($model->getManageId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getManageId()]);
        } 
      /*  $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'value' => $model->getName()
            ]
        );  */

		 //$fieldset->addField('name', 'hidden', ['name' => 'name', 'value' => $model->getName()]);
		$fieldset->addField('name', 'hidden', ['name' => 'name', 'value' =>'magento setting']);
       
       $fieldset->addField(
            'productbanner',
            'select',
            [
                'label' => __('Product Banner'),
                'required' => true,
                'name' => 'productbanner',
				'value' => $model->getProductbanner(),
                'values' => $this->_statusOption->getStatusesOptionArray()
            ]
        ); 

		
		$fieldset->addField(
            'servicebanner',
            'select',
            [
                'label' => __('Service Banner'),
                'required' => true,
                'name' => 'servicebanner',
				'value' => $model->getServicebanner(),
                'values' => $this->_statusOption->getStatusesOptionArray()
            ]
        ); 
		
		$fieldset->addField(
            'homeproductcategory',
            'select',
            [
                'label' => __('Home product category'),
                'required' => true,
                'name' => 'homeproductcategory',
				'value' => $model->getHomeproductcategory(),
                'values' => $this->_statusOption->getStatusesOptionArray()
            ]
        ); 

		
		
		$fieldset->addField(
            'featuredproduct',
            'select',
            [
                'label' => __('Home Featured Product'),
                'required' => true,
                'name' => 'featuredproduct',
				'value' => $model->getFeaturedproduct(),
                'values' => $this->_statusOption->getStatusesOptionArray()
            ]
        );
		$fieldset->addField(
            'categorymenu',
            'select',
            [
                'label' => __('Category Menu'),
                'required' => true,
                'name' => 'categorymenu',
				'value' => $model->getCategorymenu(),
                'values' => $this->_statusOption->getStatusesOptionArray()
            ]
        );
		$fieldset->addField(
            'featuredservices',
            'select',
            [
                'label' => __('Featured Services'),
                'required' => true,
                'name' => 'featuredservices',
				'value' => $model->getFeaturedservices(),
                'values' => $this->_statusOption->getStatusesOptionArray()
            ]
        );
		$fieldset->addField(
            'headeraddtocart',
            'select',
            [
                'label' => __('Header Addtocart'),
                'required' => true,
                'name' => 'headeraddtocart',
				'value' => $model->getHeaderaddtocart(),
                'values' => $this->_statusOption->getStatusesOptionArray()
            ]
        );
		
		
        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
