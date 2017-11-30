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
 * Interface for categoryimages search results.
 *
 * @api
 */
interface CategoryimagesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get categoryimages list.
     *
     * @return \Custom\Chharo\Api\Data\CategoryimagesInterface[]
     */
    public function getItems();

    /**
     * Set categoryimages list.
     *
     * @param  \Custom\Chharo\Api\Data\CategoryimagesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
