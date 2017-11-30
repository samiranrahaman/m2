<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Booking\Gridpart5\Controller\Adminhtml\Template;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		//$model = $this->_objectManager->create('Booking\Gridpart5\Model\Template');
		// $item = $model->load(6);
		//  var_dump($item->getData());exit;
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Booking_Gridpart5::gridpart5_template_ui');
        $resultPage->addBreadcrumb(__('Booking'), __('|Booking'));
        $resultPage->addBreadcrumb(__('Booking'), __('Booking'));
        $resultPage->getConfig()->getTitle()->prepend(__('Booking view '));

      return $resultPage;
	   //return $item;
    }
    
     /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Booking_Gridpart5::gridpart5_template');
    }
}