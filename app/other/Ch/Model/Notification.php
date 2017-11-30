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

use Custom\Chharo\Api\Data\NotificationInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Chharo Notification Model
 *
 * @method \Custom\Chharo\Model\ResourceModel\Notification _getResource()
 * @method \Custom\Chharo\Model\ResourceModel\Notification getResource()
 */
class Notification extends AbstractModel implements NotificationInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ID = 'no-route';

    /**#@+
     * Notification's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**#@+
     * Notification's Types
     */
    const TYPE_PRODUCT = 'product';
    const TYPE_CATEGORY = 'category';
    const TYPE_OTHERS = 'others';
    const TYPE_CUSTOM = 'custom';
    /**#@-*/

    /**
     * Chharo Notification cache tag
     */
    const CACHE_TAG = 'chharo_notification';

    /**
     * @var string
     */
    protected $_cacheTag = 'chharo_notification';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'chharo_notification';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Custom\Chharo\Model\ResourceModel\Notification');
    }

    /**
     * Load object data
     *
     * @param  int|null $id
     * @param  string   $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteNotification();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Notification
     *
     * @return \Custom\Chharo\Model\Notification
     */
    public function noRouteNotification()
    {
        return $this->load(self::NOROUTE_ID, $this->getIdFieldName());
    }

    /**
     * Prepare notification's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled')
        ];
    }

    /**
     * Prepare notification's types.
     *
     * @return array
     */
    public function getAvailableTypes()
    {
        return [
            self::TYPE_PRODUCT => __('Product'),
            self::TYPE_CATEGORY => __('Category'),
            self::TYPE_OTHERS => __('Others'),
            self::TYPE_CUSTOM => __('Custom Collection')
        ];
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ID);
    }

    /**
     * Set ID
     *
     * @param  int $id
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Filename
     *
     * @return string|null
     */
    public function getFilename()
    {
        return parent::getData(self::FILENAME);
    }

    /**
     * Set Filename
     *
     * @param  string $filename
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setFilename($filename)
    {
        return $this->setData(self::FILENAME, $filename);
    }

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return parent::getData(self::TITLE);
    }

    /**
     * Set Title
     *
     * @param  string $title
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get Content
     *
     * @return string|null
     */
    public function getContent()
    {
        return parent::getData(self::CONTENT);
    }

    /**
     * Set Content
     *
     * @param  string $content
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
    }

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType()
    {
        return parent::getData(self::TYPE);
    }

    /**
     * Set Type
     *
     * @param  string $type
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get Product Category ID
     *
     * @return int|null
     */
    public function getProCatId()
    {
        return parent::getData(self::PRO_CAT_ID);
    }

    /**
     * Set Product Category ID
     *
     * @param  int $proCatId
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setProCatId($proCatId)
    {
        return $this->setData(self::PRO_CAT_ID, $proCatId);
    }

    /**
     * Get Store ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return parent::getData(self::STORE_ID);
    }

    /**
     * Set Store ID
     *
     * @param  int $proCatId
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return parent::getData(self::STATUS);
    }
    
    /**
     * Set Status
     *
     * @param  int $status
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Sort Order
     *
     * @return int|null
     */
    public function getSortOrder()
    {
        return parent::getData(self::SORT_ORDER);
    }

    /**
     * Set Sort Order
     *
     * @param  int $sortOrder
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * Get created date.
     *
     * @return string|null
     */
    public function getCreatedTime()
    {
        return parent::getData(self::CREATED_TIME);
    }

    /**
     * Set created date.
     *
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedTime($createdAt)
    {
        return $this->setData(self::CREATED_TIME, $createdAt);
    }

    /**
     * Get updated date.
     *
     * @return string|null
     */
    public function getUpdateTime()
    {
        return parent::getData(self::UPDATE_TIME);
    }

    /**
     * Set updated date.
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdateTime($updatedAt)
    {
        return $this->setData(self::UPDATE_TIME, $updatedAt);
    }
}
