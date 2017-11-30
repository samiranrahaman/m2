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
 * Chharo Device Token interface.
 *
 * @api
 */
interface DeviceTokenInterface
{
    

    const CREATED_TIME = "created_at";


    const ENTITY_ID = 'id';

    const TOKEN = 'token';

    const UPDATED_TIME = 'updated_at';

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

    /**
     * Get updated date.
     *
     * @return string|null
     */
    public function getUpdatedTime();

    /**
     * Set updated date.
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedTime($updatedAt);

    /**
     * Get token
     *
     * @return string
     */
    public function getToken();

    /**
     * Set token
     *
     * @param  string $token
     * @return \Custom\Chharo\Api\Data\DeviceTokenInterface
     */
    public function setToken($token);
}
