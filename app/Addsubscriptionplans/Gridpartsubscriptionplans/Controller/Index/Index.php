<?php
 
namespace Addsubscriptionplans\Gridpartsubscriptionplans\Controller\Index;
 
use Magento\Framework\App\Action\Context;
 
class Index extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
 
    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
 
    public function execute()
    {
        /* $resultPage = $this->_resultPageFactory->create();
        return $resultPage; */
		
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        /* $resultPage->setActiveMenu('Addsubscriptionplans_Gridpartsubscriptionplans::gridpartsubscriptionplans_template_ui');
        $resultPage->addBreadcrumb(__('Sbscriptionplan'), __('Subscriptionplan'));
        $resultPage->addBreadcrumb(__('subscriptionplan'), __('Subscriptionplan')); */
        $resultPage->getConfig()->getTitle()->prepend(__('Subscriptionplan '));

        return $resultPage;
    }
}