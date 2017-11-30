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
 * Interface for bannerimage search results.
 *
 * @api
 */
interface BannerimageSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get bannerimage list.
     *
     * @return \Custom\Chharo\Api\Data\BannerimageInterface[]
     */
    public function getItems();

    /**
     * Set bannerimage list.
     *
     * @param  \Custom\Chharo\Api\Data\BannerimageInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
