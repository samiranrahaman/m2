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
 * Chharo Featuredcategories interface.
 *
 * @api
 */
interface FeaturedcategoriesInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID    = 'id';

    const FILENAME = 'filename';

    const CATEGORY_ID = 'category_id';

    const STORE_ID = 'store_id';

    const SORT_ORDER = 'sort_order';

    const STATUS = 'status';

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
     * @return \Custom\Chharo\Api\Data\FeaturedcategoriesInterface
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
     * @return \Custom\Chharo\Api\Data\FeaturedcategoriesInterface
     */
    public function setFilename($filename);

    /**
     * Get Category ID
     *
     * @return int|null
     */
    public function getCategoryId();

    /**
     * Set Category ID
     *
     * @param  int $categoryId
     * @return \Custom\Chharo\Api\Data\FeaturedcategoriesInterface
     */
    public function setCategoryId($categoryId);

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
     * @return \Custom\Chharo\Api\Data\FeaturedcategoriesInterface
     */
    public function setStoreId($storeId);

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
     * @return \Custom\Chharo\Api\Data\FeaturedcategoriesInterface
     */
    public function setSortOrder($sortOrder);

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
     * @return \Custom\Chharo\Api\Data\FeaturedcategoriesInterface
     */
    public function setStatus($status);

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
