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

namespace Custom\Chharo\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SearchCriteriaInterface;
use Custom\Chharo\Api\Data\NotificationInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class NotificationRepository implements \Custom\Chharo\Api\NotificationRepositoryInterface
{
    /**
     * @var NotificationFactory
     */
    protected $_notificationFactory;

    /**
     * @var Notification[]
     */
    protected $_instances = [];

    /**
     * @var Notification[]
     */
    protected $_instancesById = [];

    /**
     * @var \Custom\Chharo\Model\ResourceModel\Notification\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Custom\Chharo\Model\ResourceModel\Notification
     */
    protected $_resourceModel;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /**
     * @param NotificationFactory                                  $notificationFactory
     * @param ResourceModel\Notification\CollectionFactory         $collectionFactory
     * @param ResourceModel\Notification                           $resourceModel
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        NotificationFactory $notificationFactory,
        ResourceModel\Notification\CollectionFactory $collectionFactory,
        ResourceModel\Notification $resourceModel,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_notificationFactory = $notificationFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save(NotificationInterface $notification)
    {
        $notificationId = $notification->getId();
        try {
            $this->_resourceModel->save($notification);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->_instancesById[$notification->getId()]);

        return $this->getById($notification->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getById($notificationId)
    {
        $notificationData = $this->_notificationFactory->create();
        /* @var \Custom\Chharo\Model\ResourceModel\Notification\Collection $notificationData */
        $notificationData->load($notificationId);
        if (!$notificationData->getId()) {
            // notification record does not exist
            //throw new NoSuchEntityException(__('Requested notification record doesn\'t exist'));
        }
        $this->_instancesById[$notificationId] = $notificationData;

        return $this->_instancesById[$notificationId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /**
 * @var \Custom\Chharo\Model\ResourceModel\Notification\Collection $collection 
*/
        $collection = $this->_collectionFactory->create();
        $collection->load();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(NotificationInterface $notification)
    {
        $notificationId = $notification->getId();
        try {
            $this->_resourceModel->delete($notification);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove notification record with id %1', $notificationId)
            );
        }
        unset($this->_instancesById[$notificationId]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($notificationId)
    {
        $notification = $this->getById($notificationId);

        return $this->delete($notification);
    }
}
