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

namespace Custom\Chharo\Api\Data;

/**
 * Chharo Device Token interface.
 *
 * @api
 */
interface AndroidTokenInterface
{
    const CUSTOMER_ID = 'customer_id';

    const ENTITY_ID = 'id';

    const TOKEN = 'token';

    /**#@-*/

    /**
     * Get ID.
     *
     * @return string|null
     */
    public function getId();

    /**
     * Set ID.
     *
     * @param string $id
     *
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setId($id);

    /**
     * Get customer Id.
     *
     * @return string|null
     */
    public function getCustomerId();

    /**
     * Set customer id.
     *
     * @param string $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken();

    /**
     * Set token.
     *
     * @param string $token
     *
     * @return \Custom\Chharo\Api\Data\DeviceTokenInterface
     */
    public function setToken($token);
}
