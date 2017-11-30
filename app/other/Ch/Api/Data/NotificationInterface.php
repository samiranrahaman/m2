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
interface NotificationInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID    = 'id';

    const FILENAME = 'filename';

    const TITLE = 'title';

    const CONTENT = 'content';

    const TYPE = 'type';

    const PRO_CAT_ID = 'pro_cat_id';

    const STORE_ID = 'store_id';

    const STATUS = 'status';

    const SORT_ORDER = 'sort_order';

    const CREATED_TIME = 'created_time';

    const UPDATE_TIME = 'update_time';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param  int $id
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setId($id);

    /**
     * Get Filename
     *
     * @return string|null
     */
    public function getFilename();

    /**
     * Set Filename
     *
     * @param  string $filename
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setFilename($filename);

    /**
     * Get Title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Set Title
     *
     * @param  string $title
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setTitle($title);

    /**
     * Get Content
     *
     * @return string|null
     */
    public function getContent();

    /**
     * Set Content
     *
     * @param  string $content
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setContent($content);

    /**
     * Get Type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Set Type
     *
     * @param  string $type
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setType($type);

    /**
     * Get Product Category ID
     *
     * @return int|null
     */
    public function getProCatId();

    /**
     * Set Product Category ID
     *
     * @param  int $proCatId
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setProCatId($proCatId);

    /**
     * Get Store ID
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Set Store ID
     *
     * @param  int $proCatId
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setStoreId($storeId);

    /**
     * Get Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Status
     *
     * @param  int $status
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setStatus($status);

    /**
     * Get Sort Order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Set Sort Order
     *
     * @param  int $sortOrder
     * @return \Custom\Chharo\Api\Data\NotificationInterface
     */
    public function setSortOrder($sortOrder);

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
    public function getUpdateTime();

    /**
     * Set updated date.
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdateTime($updatedAt);
}
