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

use Custom\Chharo\Api\Data\DeviceTokenInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Chharo DeviceToken Model
 *
 * @method \Custom\Chharo\Model\ResourceModel\DeviceToken _getResource()
 * @method \Custom\Chharo\Model\ResourceModel\DeviceToken getResource()
 */
class DeviceToken extends \Magento\Framework\Model\AbstractModel implements DeviceTokenInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * chharo api cache tag
     */
    const CACHE_TAG = 'chharo_devicetoken';

    /**
     * @var string
     */
    protected $_cacheTag = 'chharo_devicetoken';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'chharo_devicetoken';
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Custom\Chharo\Model\ResourceModel\DeviceToken');
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
     * @return \Custom\Chharo\Model\DeviceToken
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
     * @return \Custom\Chharo\Api\Data\DeviceTokenInterface
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
    public function getupdatedTime()
    {
        return parent::getData(self::Updated_TIME);
    }

    /**
     * Set Updated Time
     *
     * @param  string $updatedAt
     * @return \Custom\Chharo\Api\Data\DeviceTokenInterface
     */
    public function setUpdatedTime($updatedAt)
    {
        return $this->setData(self::Updated_TIME, $updatedAt);
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
     * @return \Custom\Chharo\Api\Data\DeviceTokenInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get Token
     *
     * @return string
     */
    public function getToken()
    {
        return parent::getData(self::TOKEN);
    }

    /**
     * Set Token
     *
     * @param  string $token
     * @return \Custom\Chharo\Api\Data\DeviceTokenInterface
     */
    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }
}
