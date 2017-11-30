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
 * Interface for notification search results.
 *
 * @api
 */
interface NotificationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get notification list.
     *
     * @return \Custom\Chharo\Api\Data\NotificationInterface[]
     */
    public function getItems();

    /**
     * Set notification list.
     *
     * @param  \Custom\Chharo\Api\Data\NotificationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
