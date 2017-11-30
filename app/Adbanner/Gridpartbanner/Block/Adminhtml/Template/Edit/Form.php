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
namespace Adbanner\Gridpartbanner\Block\Adminhtml\Template\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Adbanner\Gridpartbanner\Helper\Option
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
        \Adbanner\Gridpartbanner\Helper\Option $optionData,
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
        return $this->_coreRegistry->registry('_gridpartbanner_template');
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
		//echo $model->getGridpart2servicesbannerId();
		//echo $model->getId();
       // echo $model->getSss();
		//echo $model->getStatus();
		//exit;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Banner Information'), 'class' => 'fieldset-wide']
        );
        $fieldset->addType('background', 'Adbanner\Gridpartbanner\Block\Adminhtml\Template\Helper\Background');
        
       
        /* if ($model->getGridpart2templateId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getGridpart2templateId()]);
        } */
		if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getId()]);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'value' => $model->getName()
            ]
        );

        $fieldset->addField(
            'background',
            'background',
            [
                'name' => 'background',
                'label' => __('Image'),
                'title' => __('Image'),
                'required' => true,
                'value' => $model->getBackground()
            ]
        );
        


        $fieldset->addField(
            'imagecaption',
            'text',
            [
                'name' => 'imagecaption',
                'label' => __('Image Caption'),
                'title' => __('Image Caption'),
                'required' => false,
                'value' => $model->getImagecaption()
            ]
        );
         $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('Url'),
                'title' => __('Url'),
                'required' => false,
                'value' => $model->getUrl()
            ]
        );
        /* $fieldset->addField(
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
 */
      $fieldset->addField(
            'showname',
            'select',
            [
                'name' => 'showname',
                'label' => __('Show title'),
                'title' => __('Show title'),
				'value'  => $model->getShowname(),
                'required' => true,
                'values' => array(''=>'Please Select..','yes' => 'Yes','no' => 'No'),
            ]
        );
      $fieldset->addField(
            'type',
            'select',
            [
                'name' => 'type',
                'label' => __('Ad Type'),
                'title' => __('Ad Type'),
				'value'  => $model->getType(),
                'required' => true,
                'values' => array(''=>'Please Select..','Product' => 'Product','Services' => 'Services'),
            ]
        );
        $fieldset->addField( 
            'status',
            'select',
            [
                'label' => __('Status'),
                'required' => true,
				'value' => $model->getStatus(),
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
