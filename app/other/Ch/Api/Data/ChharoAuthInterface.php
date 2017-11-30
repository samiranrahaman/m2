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
namespace Custom\Chharo\Api\Data;

/**
 * Chharo Notification interface.
 *
 * @api
 */
interface ChharoAuthInterface
{
    

    const CREATED_TIME = "created_at";

    const API_KEY = "api_key";

    const ENTITY_ID = 'id';

    /**#@-*/

    /**
     * Get ID
     *
     * @return string|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param  string $id
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setId($id);


    /**
     * Get api key
     *
     * @return string|null
     */
    public function getApiKey();

    /**
     * Set api key
     *
     * @param  string $apiKey
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setApiKey($apiKey);

    /**
     * Get created date.
     *
     * @return string|null
     */
    public function getCreatedTime();

    /**
     * Set created date.
     *
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedTime($createdAt);
}
