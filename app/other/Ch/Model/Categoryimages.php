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

use Custom\Chharo\Api\Data\CategoryimagesInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Chharo Categoryimages Model
 *
 * @method \Custom\Chharo\Model\ResourceModel\Categoryimages _getResource()
 * @method \Custom\Chharo\Model\ResourceModel\Categoryimages getResource()
 */
class Categoryimages extends AbstractModel implements CategoryimagesInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ID = 'no-route';

    /**
     * Chharo Categoryimages cache tag
     */
    const CACHE_TAG = 'chharo_categoryimages';

    /**
     * @var string
     */
    protected $_cacheTag = 'chharo_categoryimages';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'chharo_categoryimages';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Custom\Chharo\Model\ResourceModel\Categoryimages');
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
            return $this->noRouteCategoryimages();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Categoryimages
     *
     * @return \Custom\Chharo\Model\Categoryimages
     */
    public function noRouteCategoryimages()
    {
        return $this->load(self::NOROUTE_ID, $this->getIdFieldName());
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
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Icon
     *
     * @return string|null
     */
    public function getIcon()
    {
        return parent::getData(self::ICON);
    }

    /**
     * Set Icon
     *
     * @param  string $icon
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface
     */
    public function setIcon($icon)
    {
        return $this->setData(self::ICON, $icon);
    }

    /**
     * Get Banner
     *
     * @return string|null
     */
    public function getBanner()
    {
        return parent::getData(self::BANNER);
    }

    /**
     * Set Banner
     *
     * @param  string $banner
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface
     */
    public function setBanner($banner)
    {
        return $this->setData(self::BANNER, $banner);
    }

    /**
     * Get Category ID
     *
     * @return int|null
     */
    public function getCategoryId()
    {
        return parent::getData(self::CATEGORY_ID);
    }

    /**
     * Set Category ID
     *
     * @param  int $categoryId
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface
     */
    public function setCategoryId($categoryId)
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
    }

    /**
     * Get Category Name
     *
     * @return int|null
     */
    public function getCategoryName()
    {
        return parent::getData(self::CATEGORY_NAME);
    }

    /**
     * Set Category Name
     *
     * @param  int $categoryName
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface
     */
    public function setCategoryName($categoryName)
    {
        return $this->setData(self::CATEGORY_NAME, $categoryName);
    }

    /**
     * Get created date.
     *
     * @return string|null
     */
    public function getCreatedAt()
    {
        return parent::getData(self::CREATED_AT);
    }

    /**
     * Set created date.
     *
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated date.
     *
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return parent::getData(self::UPDATED_AT);
    }

    /**
     * Set updated date.
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
