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
interface UserImageInterface
{
    

    const CREATED_TIME = "created_at";


    const ENTITY_ID = 'id';

    const PROFILE = 'profile';

    const BANNER = 'banner';

    const CUSTOMER_ID = 'customer_id';

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
     * Get profile
     *
     * @return string
     */
    public function getProfile();

    /**
     * Set profile
     *
     * @param  string $profile
     * @return \Custom\Chharo\Api\Data\UserImageInterface
     */
    public function setProfile($profile);
    /**
     * Get banner
     *
     * @return string
     */
    public function getBanner();

    /**
     * Set banner
     *
     * @param  string $banner
     * @return \Custom\Chharo\Api\Data\UserImageInterface
     */
    public function setBanner($banner);

    /**
     * Get customerId
     *
     * @return string
     */
    public function getCustomerId();

    /**
     * Set customerId
     *
     * @param  int $customerId
     * @return \Custom\Chharo\Api\Data\UserImageInterface
     */
    public function setCustomerId($customerId);
}
