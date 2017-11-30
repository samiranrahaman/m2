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
 * Interface for featuredcategories search results.
 *
 * @api
 */
interface FeaturedcategoriesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get featuredcategories list.
     *
     * @return \Custom\Chharo\Api\Data\FeaturedcategoriesInterface[]
     */
    public function getItems();

    /**
     * Set featuredcategories list.
     *
     * @param  \Custom\Chharo\Api\Data\FeaturedcategoriesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
