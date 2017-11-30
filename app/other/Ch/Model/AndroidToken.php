<?php
/**
 * Custom Software.
 *
 * @category Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\Chharo\Model;

use Custom\Chharo\Api\Data\AndroidTokenInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Chharo AndroidToken Model.
 *
 * @method \Custom\Chharo\Model\ResourceModel\AndroidToken _getResource()
 * @method \Custom\Chharo\Model\ResourceModel\AndroidToken getResource()
 */
class AndroidToken extends \Magento\Framework\Model\AbstractModel implements AndroidTokenInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * chharo api cache tag.
     */
    const CACHE_TAG = 'chharo_android_token';

    /**
     * @var string
     */
    protected $_cacheTag = 'chharo_android_token';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    protected $_eventPrefix = 'chharo_android_token';
    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('Custom\Chharo\Model\ResourceModel\AndroidToken');
    }

    /**
     * Load object data.
     *
     * @param string $id
     * @param string $field
     *
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
     * Load No-Route.
     *
     * @return \Custom\Chharo\Model\DeviceToken
     */
    public function noRouteProduct()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return string
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param string $id
     *
     * @return \Custom\Chharo\Api\Data\DeviceTokenInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get Token.
     *
     * @return string
     */
    public function getToken()
    {
        return parent::getData(self::TOKEN);
    }

    /**
     * Set Token.
     *
     * @param string $token
     *
     * @return \Custom\Chharo\Api\Data\DeviceTokenInterface
     */
    public function setToken($token)
    {
        return $this->setData(self::TOKEN, $token);
    }

    /**
     * Get customer ID.
     *
     * @return int
     */
    public function getCustomerId()
    {
        return parent::getData(self::CUSTOMER_ID);
    }

    /**
     * Set customer ID.
     *
     * @param int $customerId
     *
     * @return \Custom\Chharo\Api\Data\AndroidTokenInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }
}
