<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Productbanner\Gridpart4\Controller\Adminhtml\Template;

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
        $resultPage->setActiveMenu('Productbanner_Gridpart4::gridpart3_template_ui');
        $resultPage->addBreadcrumb(__('Banner'), __('|Banner'));
        $resultPage->addBreadcrumb(__('Banner'), __('Banner'));
        $resultPage->getConfig()->getTitle()->prepend(__('Product Banner '));

        return $resultPage;
    }
    
     /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Productbanner_Gridpart4::gridpart4_template');
    }
}