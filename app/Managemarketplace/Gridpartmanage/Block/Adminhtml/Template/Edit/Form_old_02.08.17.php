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
          if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getId()]);
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

		 $fieldset->addField('name', 'hidden', ['name' => 'name', 'value' => $model->getName()]);
		
       /*  $fieldset->addField(
            'background',
            'background',
            [
                'name' => 'background',
                'label' => __('App Image'),
                'title' => __('App Image'),
                'required' => true,
                'value' => $model->getBackground()
            ]
        ); */
        


        /* $fieldset->addField(
            'stylecolor',
            'text',
            [
                'name' => 'stylecolor',
                'label' => __('Android Url'),
                'title' => __('Android Url'),
                'required' => false,
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
                'required' => false,
                'value' => $model->getTextcolor()
            ]
        );
		
       $fieldset->addField(
            'type',
            'select',
            [
                'name' => 'type',
                'label' => __('App Type'),
                'title' => __('App Type'),
				'value'  => $model->getType(),
                'required' => true,
                'values' => array(''=>'Please Select..','Product' => 'Product','Services' => 'Services'),
            ]
        );
		
		$fieldset->addField(
            'featured',
            'select',
            [
                'name' => 'featured',
                'label' => __('Featured'),
                'title' => __('Featured'),
				'value'  => $model->getFeatured(),
                'required' => false,
                'values' => array(''=>'Please Select..','1' => 'yes','0' => 'No'),
            ]
        );
		
		$fieldset->addField(
            'top',
            'select',
            [
                'name' => 'top',
                'label' => __('Top'),
                'title' => __('Top'),
				'value'  => $model->getTop(),
                'required' => false,
                'values' => array(''=>'Please Select..','1' => 'yes','0' => 'No'),
            ]
        ); */
       $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Productbanner'),
                'required' => true,
                'name' => 'productbanner',
				'value' => $model->getProductbanner(),
                'values' => $this->_statusOption->getStatusesOptionArray()
            ]
        ); 

        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
