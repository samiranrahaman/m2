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

use Custom\Chharo\Api\Data\UserImageInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Chharo UserImage Model
 *
 * @method \Custom\Chharo\Model\ResourceModel\UserImage _getResource()
 * @method \Custom\Chharo\Model\ResourceModel\UserImage getResource()
 */
class UserImage extends \Magento\Framework\Model\AbstractModel implements UserImageInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * chharo api cache tag
     */
    const CACHE_TAG = 'chharo_userimage';

    /**
     * @var string
     */
    protected $_cacheTag = 'chharo_userimage';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'chharo_userimage';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Custom\Chharo\Model\ResourceModel\UserImage');
    }

    /**
     * Load object data
     *
     * @param  string $id
     * @param  string $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteProduct();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route
     *
     * @return \Custom\Chharo\Model\UserImage
     */
    public function noRouteProduct()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
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
     * @return string
     */
    public function getCreatedTime()
    {
        return parent::getData(self::CREATED_TIME);
    }

    /**
     * Set Created Time
     *
     * @param  string $createdAt
     * @return \Custom\Chharo\Api\Data\UserImageInterface
     */
    public function setCreatedTime($createdAt)
    {
        return $this->setData(self::CREATED_TIME, $createdAt);
    }

    /**
     * Get ID
     *
     * @return string
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID
     *
     * @param  string $id
     * @return \Custom\Chharo\Api\Data\UserImageInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get profile
     *
     * @return string
     */
    public function getProfile()
    {
        return parent::getData(self::PROFILE);
    }

    /**
     * Set profile
     *
     * @param  string $profile
     * @return \Custom\Chharo\Api\Data\UserImageInterface
     */
    public function setProfile($profile)
    {
        return $this->setData(self::PROFILE, $profile);
    }

    /**
     * Get banner
     *
     * @return string
     */
    public function getBanner()
    {
        return parent::getData(self::BANNER);
    }

    /**
     * Set banner
     *
     * @param  string $banner
     * @return \Custom\Chharo\Api\Data\UserImageInterface
     */
    public function setBanner($banner)
    {
        return $this->setData(self::BANNER, $banner);
    }

    /**
     * Get customerId
     *
     * @return string
     */
    public function getCustomerId()
    {
        return parent::getData(self::CUSTOMER_ID);
    }

    /**
     * Set customerId
     *
     * @param  int $customerId
     * @return \Custom\Chharo\Api\Data\UserImageInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
}
