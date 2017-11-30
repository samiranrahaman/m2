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
use Custom\Chharo\Api\Data\FeaturedcategoriesInterface;

/**
 * Featuredcategories CRUD interface.
 *
 * @api
 */
interface FeaturedcategoriesRepositoryInterface
{
    /**
     * Create or update a Featuredcategories.
     *
     * @param  FeaturedcategoriesInterface $featuredcategories
     * @return FeaturedcategoriesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(FeaturedcategoriesInterface $featuredcategories);

    /**
     * Get featuredcategories by featuredcategories ID.
     *
     * @param  int $featuredcategoriesId
     * @return FeaturedcategoriesInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * If featuredcategories with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($featuredcategoriesId);

    /**
     * Retrieve featuredcategoriess which match a specified criteria.
     *
     * @param  \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Custom\Chharo\Api\Data\FeaturedcategoriesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete featuredcategories.
     *
     * @param  FeaturedcategoriesInterface $featuredcategories
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(FeaturedcategoriesInterface $featuredcategories);

    /**
     * Delete featuredcategories by ID.
     *
     * @param  int $featuredcategoriesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($featuredcategoriesId);
}
