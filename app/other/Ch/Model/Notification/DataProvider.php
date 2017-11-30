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

namespace Custom\Chharo\Model\Notification;

use Magento\Eav\Model\Config;
use Custom\Chharo\Model\Notification;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Session\SessionManagerInterface;
use Custom\Chharo\Model\ResourceModel\Notification\Collection;
use Custom\Chharo\Model\ResourceModel\Notification\CollectionFactory as NotificationCollectionFactory;

/**
 * Class DataProvider.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var SessionManagerInterface
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param string                        $name
     * @param string                        $primaryFieldName
     * @param string                        $requestFieldName
     * @param NotificationCollectionFactory $notificationCollectionFactory
     * @param array                         $meta
     * @param array                         $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        NotificationCollectionFactory $notificationCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $notificationCollectionFactory->create();
        $this->collection->addFieldToSelect('*');
    }

    /**
     * Get session object.
     *
     * @return SessionManagerInterface
     */
    protected function getSession()
    {
        if ($this->session === null) {
            $this->session = ObjectManager::getInstance()->get(
                'Magento\Framework\Session\SessionManagerInterface'
            );
        }

        return $this->session;
    }

    /**
     * Get data.
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /**
 * @var Customer $notification 
*/
        foreach ($items as $notification) {
            $result['notification'] = $notification->getData();
            $this->loadedData[$notification->getId()] = $result;
        }

        $data = $this->getSession()->getNotificationFormData();
        if (!empty($data)) {
            $notificationId = isset($data['chharo_notification']['id'])
            ? $data['chharo_notification']['id'] : null;
            $this->loadedData[$notificationId] = $data;
            $this->getSession()->unsNotificationFormData();
        }

        return $this->loadedData;
    }
}
