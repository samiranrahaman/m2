<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Addsubscriptionplans\Gridpartsubscriptionplans\Controller\Adminhtml\Template;

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
        $resultPage->setActiveMenu('Addsubscriptionplans_Gridpartsubscriptionplans::gridpartsubscriptionplans_template_ui');
        $resultPage->addBreadcrumb(__('Add New subscriptionplan'), __('|Add New subscriptionplan'));
        $resultPage->addBreadcrumb(__('subscriptionplan'), __('Add New subscriptionplan'));
        $resultPage->getConfig()->getTitle()->prepend(__('Add New subscriptionplan '));

        return $resultPage;
    }
    
     /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Addsubscriptionplans_Gridpartsubscriptionplans::gridpartsubscriptionplans_template');
    }
}