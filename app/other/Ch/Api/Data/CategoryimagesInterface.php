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
 * Chharo Categoryimages interface.
 *
 * @api
 */
interface CategoryimagesInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID    = 'id';

    const ICON = 'icon';

    const BANNER = 'banner';

    const CATEGORY_ID = 'category_id';

    const CATEGORY_NAME = 'category_name';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';
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
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface
     */
    public function setId($id);

    /**
     * Get Icon
     *
     * @return string|null
     */
    public function getIcon();

    /**
     * Set Icon
     *
     * @param  string $icon
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface
     */
    public function setIcon($icon);

    /**
     * Get Banner
     *
     * @return string|null
     */
    public function getBanner();

    /**
     * Set Banner
     *
     * @param  string $banner
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface
     */
    public function setBanner($banner);

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
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface
     */
    public function setCategoryId($categoryId);

    /**
     * Get Category Name
     *
     * @return int|null
     */
    public function getCategoryName();

    /**
     * Set Category Name
     *
     * @param  int $categoryName
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface
     */
    public function setCategoryName($categoryName);

    /**
     * Get created date.
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created date.
     *
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated date.
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated date.
     *
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);
}
