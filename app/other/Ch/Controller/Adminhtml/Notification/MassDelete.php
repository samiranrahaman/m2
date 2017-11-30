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

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Custom\Chharo\Model\ResourceModel\Notification\CollectionFactory;
use Custom\Chharo\Api\NotificationRepositoryInterface;

/**
 * Class MassDelete.
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var NotificationRepositoryInterface
     */
    protected $_notificationRepository;

    /**
     * @param Context                         $context
     * @param Filter                          $filter
     * @param CollectionFactory               $collectionFactory
     * @param NotificationRepositoryInterface $notificationRepository
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        NotificationRepositoryInterface $notificationRepository
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_notificationRepository = $notificationRepository;
        parent::__construct($context);
    }

    /**
     * Execute action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     *
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $notificationsDeleted = 0;
        foreach ($collection->getAllIds() as $notificationId) {
            if (!empty($notificationId)) {
                try {
                    $this->_notificationRepository->deleteById($notificationId);
                    $this->messageManager->addSuccess(__('Notification has been deleted.'));
                    $notificationsDeleted++;
                } catch (\Exception $exception) {
                    $this->messageManager->addError($exception->getMessage());
                }
            }
        }

        if ($notificationsDeleted) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were deleted.', $notificationsDeleted)
            );
        }
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('chharo/notification/index');
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Custom_Chharo::notification');
    }
}
