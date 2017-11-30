<?php
/**
 * Custom Software.
 *
 * @category  Custom
 * @package   Custom_Chharo
 * @author    Custom
 * @copyright Copyright (c) 2010-2016 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */
namespace Custom\Chharo\Model;

use Custom\Chharo\Api\Data\ChharoAuthInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Chharo ChharoAuth Model
 *
 * @method \Custom\Chharo\Model\ResourceModel\ChharoAuth _getResource()
 * @method \Custom\Chharo\Model\ResourceModel\ChharoAuth getResource()
 */
class ChharoAuth extends \Magento\Framework\Model\AbstractModel implements ChharoAuthInterface, IdentityInterface
{
    /**
     * No route page id
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * chharo api cache tag
     */
    const CACHE_TAG = 'chharo_api_authentication';

    /**
     * @var string
     */
    protected $_cacheTag = 'chharo_api_authentication';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'chharo_api_authentication';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Custom\Chharo\Model\ResourceModel\ChharoAuth');
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
     * @return \Custom\Chharo\Model\ChharoAuth
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
     * @return \Custom\Chharo\Api\Data\ChharoAuthInterface
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
     * @return \Custom\Chharo\Api\Data\ChharoAuthInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Get api key
     *
     * @return string
     */
    public function getApiKey()
    {
        return parent::getData(self::API_KEY);
    }

    /**
     * Set API KEY
     *
     * @param  string $apiKey
     * @return \Custom\Chharo\Api\Data\ChharoAuthInterface
     */
    public function setApiKey($apiKey)
    {
        return $this->setData(self::API_KEY, $apiKey);
    }
}
