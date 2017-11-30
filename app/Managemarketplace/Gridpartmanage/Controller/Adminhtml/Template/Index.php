<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Managemarketplace\Gridpartmanage\Controller\Adminhtml\Template;

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
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Managemarketplace_Gridpartmanage::gridpartmanage_template_ui');
        $resultPage->addBreadcrumb(__('Manage Marketplace'), __('|Setting Marketplace'));
        $resultPage->addBreadcrumb(__('Management'), __('Manage Marketplace'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Marketplace '));

        return $resultPage;
    }
    
     /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Managemarketplace_Gridpartmanage::gridpartbanner_template');
    }
}