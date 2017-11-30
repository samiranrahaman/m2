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

use Custom\Chharo\Api\Data\BannerimageInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Chharo Bannerimage Model
 *
 * @method \Custom\Chharo\Model\ResourceModel\Bannerimage _getResource()
 * @method \Custom\Chharo\Model\ResourceModel\Bannerimage getResource()
 */
class Bannerimage extends AbstractModel implements BannerimageInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ID = 'no-route';

    /**#@+
     * Bannerimage's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**#@+
     * Bannerimage's Types
     */
    const TYPE_PRODUCT = 'product';
    const TYPE_CATEGORY = 'category';
    /**#@-*/

    /**
     * Chharo Bannerimage cache tag
     */
    const CACHE_TAG = 'chharo_bannerimage';

    /**
     * @var string
     */
    protected $_cacheTag = 'chharo_bannerimage';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'chharo_bannerimage';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Custom\Chharo\Model\ResourceModel\Bannerimage');
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
            return $this->noRouteBannerimage();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Bannerimage
     *
     * @return \Custom\Chharo\Model\Bannerimage
     */
    public function noRouteBannerimage()
    {
        return $this->load(self::NOROUTE_ID, $this->getIdFieldName());
    }

    /**
     * Prepare bannerimage's statuses.
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
     * Prepare bannerimage's types.
     *
     * @return array
     */
    public function getAvailableTypes()
    {
        return [
            self::TYPE_PRODUCT => __('Product'),
            self::TYPE_CATEGORY => __('Category')
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
     * @return \Custom\Chharo\Api\Data\BannerimageInterface
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
     * @return \Custom\Chharo\Api\Data\BannerimageInterface
     */
    public function setFilename($filename)
    {
        return $this->setData(self::FILENAME, $filename);
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
     * @return \Custom\Chharo\Api\Data\BannerimageInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
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
     * @return \Custom\Chharo\Api\Data\BannerimageInterface
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
     * @return \Custom\Chharo\Api\Data\BannerimageInterface
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
     * @return \Custom\Chharo\Api\Data\BannerimageInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
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
     * @return \Custom\Chharo\Api\Data\BannerimageInterface
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
