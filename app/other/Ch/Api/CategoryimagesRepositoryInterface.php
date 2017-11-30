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
use Custom\Chharo\Api\Data\CategoryimagesInterface;

/**
 * Categoryimages CRUD interface.
 *
 * @api
 */
interface CategoryimagesRepositoryInterface
{
    /**
     * Create or update a Categoryimages.
     *
     * @param  CategoryimagesInterface $categoryimages
     * @return CategoryimagesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CategoryimagesInterface $categoryimages);

    /**
     * Get categoryimages by categoryimages ID.
     *
     * @param  int $categoryimagesId
     * @return CategoryimagesInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * If categoryimages with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($categoryimagesId);

    /**
     * Retrieve categoryimagess which match a specified criteria.
     *
     * @param  \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Custom\Chharo\Api\Data\CategoryimagesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete categoryimages.
     *
     * @param  CategoryimagesInterface $categoryimages
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CategoryimagesInterface $categoryimages);

    /**
     * Delete categoryimages by ID.
     *
     * @param  int $categoryimagesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($categoryimagesId);
}
