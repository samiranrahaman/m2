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
use Custom\Chharo\Api\Data\BannerimageInterface;

/**
 * Bannerimage CRUD interface.
 *
 * @api
 */
interface BannerimageRepositoryInterface
{
    /**
     * Create or update a Bannerimage.
     *
     * @param  BannerimageInterface $bannerimage
     * @return BannerimageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(BannerimageInterface $bannerimage);

    /**
     * Get bannerimage by bannerimage ID.
     *
     * @param  int $bannerimageId
     * @return BannerimageInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * If bannerimage with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($bannerimageId);

    /**
     * Retrieve bannerimages which match a specified criteria.
     *
     * @param  \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Custom\Chharo\Api\Data\BannerimageSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete bannerimage.
     *
     * @param  BannerimageInterface $bannerimage
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(BannerimageInterface $bannerimage);

    /**
     * Delete bannerimage by ID.
     *
     * @param  int $bannerimageId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($bannerimageId);
}
