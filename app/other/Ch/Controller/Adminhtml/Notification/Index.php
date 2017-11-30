<?php
/**
 * Custom Software.
 *
 * @category  Custom
 * @package   Custom_Chharo
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */
namespace Custom\Chharo\Controller\Adminhtml\Notification;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Backend\Model\View\Result\Page
     */
    protected $_resultPage;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * Notification list page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /**
 * @var \Magento\Backend\Model\View\Result\Page $resultPage 
*/
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_Chharo::notification');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Notifications'));

        return $resultPage;
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Chharo::notification');
    }
}
