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

namespace Custom\Chharo\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Custom\Chharo\Api\Data\NotificationInterface;

/**
 * Notification CRUD interface.
 *
 * @api
 */
interface NotificationRepositoryInterface
{
    /**
     * Create or update a Notification.
     *
     * @param  NotificationInterface $notification
     * @return NotificationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(NotificationInterface $notification);

    /**
     * Get notification by notification ID.
     *
     * @param  int $notificationId
     * @return NotificationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * If notification with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($notificationId);

    /**
     * Retrieve notifications which match a specified criteria.
     *
     * @param  \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Custom\Chharo\Api\Data\NotificationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete notification.
     *
     * @param  NotificationInterface $notification
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(NotificationInterface $notification);

    /**
     * Delete notification by ID.
     *
     * @param  int $notificationId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($notificationId);
}
