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
namespace Addsubscriptionplans\Gridpartsubscriptionplans\Block\Adminhtml\Template\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Addsubscriptionplans\Gridpartsubscriptionplans\Helper\Option
     */
    protected $_statusOption;
	  protected $_ongoingfeeOption;

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
        \Addsubscriptionplans\Gridpartsubscriptionplans\Helper\Option $optionData,
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
        return $this->_coreRegistry->registry('_gridpartscriptionplans_template');
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
            ['legend' => __('subscriptionplans Information'), 'class' => 'fieldset-wide']
        );
       // $fieldset->addType('price', 'Addsubscriptionplans\Gridpartsubscriptionplans\Block\Adminhtml\Template\Helper\Background');
        
       
        /* if ($model->getGridpart2templateId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getGridpart2templateId()]);
        } */
		if ($model->getSubscriptionplanid()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id', 'value' => $model->getSubscriptionplanid()]);
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
            'price',
            'text',
            [
                'name' => 'price',
                'label' => __('One-Off SetUp Fee'),
                'title' => __('Price'),
                'required' => true,
                'value' => $model->getPrice()
            ]
        );
        
		
		$fieldset->addField(
		     'ongoingfee',
			 'select',
			 [
			  'label' => __('On Going fee'),
                'required' => true,
				'value' => $model->getOngoingfee(),
                'name' => 'ongoingfee',
                'values' => $this->_statusOption->getOngoingfeeOptionArray()
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
		
		
		 $fieldset->addField(
            'perannually',
            'text',
            [
                'name' => 'perannually',
                'label' => __('Paid  Annually'),
                'title' => __('Paid  Annually'),
                'required' => false,
                'value' => $model->getPerannually()
            ]
        );
		
		
		$fieldset->addField(
            'permonth',
            'text',
            [
                'name' => 'permonth',
                'label' => __('Paid Monthly'),
                'title' => __('Paid Monthly'),
                'required' => false,
                'value' => $model->getPermonth()
            ]
        );
		
		
		$fieldset->addField(
		     'ba',
			 'select',
			 [
			  'label' => __('Business App'),
                'required' => true,
				'value' => $model->getBa(),
                'name' => 'bA',
                'values' => $this->_statusOption->getBusinessAppOptionArray()
			 ]
		);
		$fieldset->addField(
		     'onba',
			 'select',
			 [
			  'label' => __('Own Name &  Branded App'),
                'required' => true,
				'value' => $model->getOnba(),
                'name' => 'oNBA',
                'values' => $this->_statusOption->getOwnNameBrandedAppArray()
			 ]
		);
		
			
			
		$fieldset->addField(
		     'ed',
			 'select',
			 [
			  'label' => __('Extension Domain'),
                'required' => true,
				'value' => $model->getEd(),
                'name' => 'ed',
                'values' => $this->_statusOption->getExtensionDomainAppArray()
			 ]
		);
			$fieldset->addField(
		     'paa',
			 'select',
			 [
			  'label' => __('Published To Apple And Android'),
                'required' => true,
				'value' => $model->getPaa(),
                'name' => 'paa',
                'values' => $this->_statusOption->getPublishedToAppleAndAndroidArray()
			 ]
		);
		
			
			$fieldset->addField(
		     'ct',
			 'select',
			 [
			  'label' => __('Customise Your Themes And Colours'),
                'required' => true,
				'value' => $model->getCt(),
                'name' => 'ct',
                'values' => $this->_statusOption->getCustomiseYourThemesAndColoursArray()
			 ]
		);
			
		
		
		$fieldset->addField(
		     'alogo',
			 'select',
			 [
			  'label' => __('Add Your Logo'),
                'required' => true,
				'value' => $model->getAlogo(),
                'name' => 'alogo',
                'values' => $this->_statusOption->getAddYourLogoArray()
			 ]
		);
			
			
			$fieldset->addField(
		     'aypsp',
			 'select',
			 [
			  'label' => __('Add your products or services incl. Description photo and price (up to 100)'),
                'required' => true,
				'value' => $model->getAypsp(),
                'name' => 'aypsp',
                'values' => $this->_statusOption->getAddyourproductsservicesArray()
			 ]
		);
		
		$fieldset->addField(
		     'elc',
			 'select',
			 [
			  'label' => __('eCommerce and logistics capability'),
                'required' => true,
				'value' => $model->getElc(),
                'name' => 'elc',
                'values' => $this->_statusOption->geteCommerceandlogisticscapabilityArray()
			 ]
		);
		
		
		
		
		
$fieldset->addField(
		     'dmt',
			 'select',
			 [
			  'label' => __('Dashboard To Monitor Transaction'),
                'required' => true,
				'value' => $model->getDmt(),
                'name' => 'dmt',
                'values' => $this->_statusOption->getDashboardToMonitorTransactionArray()
			 ]
		);
$fieldset->addField(
		     'cam',
			 'select',
			 [
			  'label' => __('Customer Appointment Management'),
                'required' => true,
				'value' => $model->getCam(),
                'name' => 'cam',
                'values' => $this->_statusOption->getCustomerAppointmentManagementArray()
			 ]
		);

       $fieldset->addField(
		     'cvvc',
			 'select',
			 [
			  'label' => __('Customer text, voice or video communication'),
                'required' => true,
				'value' => $model->getCvvc(),
                'name' => 'cvvc',
                'values' => $this->_statusOption->getCustomertextvoiceorvideocommunicationArray()
			 ]
		);

		$fieldset->addField(
		     'crc',
			 'select',
			 [
			  'label' => __('Customer Reviews Capture '),
                'required' => true,
				'value' => $model->getCrc(),
                'name' => 'crc',
                'values' => $this->_statusOption->getCustomerArray()
			 ]
		);
		
		
		$fieldset->addField(
		     'cd',
			 'select',
			 [
			  'label' => __('Customer Data Base '),
                'required' => true,
				'value' => $model->getCd(),
                'name' => 'cd',
                'values' => $this->_statusOption->getCustomerdatabaseArray()
			 ]
		);
		
	 
	

		
        
        
        $form->setAction($this->getUrl('*/*/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
