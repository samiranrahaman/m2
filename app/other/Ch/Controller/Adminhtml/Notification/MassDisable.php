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
 * Class MassDisable.
 */
class MassDisable extends \Magento\Backend\App\Action
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
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param Context                                     $context
     * @param Filter                                      $filter
     * @param CollectionFactory                           $collectionFactory
     * @param NotificationRepositoryInterface             $notificationRepository
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        NotificationRepositoryInterface $notificationRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_notificationRepository = $notificationRepository;
        $this->_date = $date;
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
        $notificationsUpdated = 0;
        $coditionArr = [];
        foreach ($collection->getAllIds() as $key => $notificationId) {
            $currentNotification = $this->_notificationRepository->getById($notificationId);
            $notificationData = $currentNotification->getData();
            if (count($notificationData)) {
                $condition = "`id`=".$notificationId;
                array_push($coditionArr, $condition);
                $notificationsUpdated++;
            }
        }
        $coditionData = implode(' OR ', $coditionArr);

        $collection->setNotificationData(
            $coditionData,
            ['status' => 0, 'updated_at' => $this->_date->gmtDate()]
        );

        if ($notificationsUpdated) {
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) were disabled.', $notificationsUpdated)
            );
        }
        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
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
