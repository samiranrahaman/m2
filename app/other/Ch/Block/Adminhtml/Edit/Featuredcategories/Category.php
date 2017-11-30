<?php
/**
 * Custom Software.
 *
 * @category Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\Chharo\Block\Adminhtml\Edit\Featuredcategories;

use Custom\Chharo\Api\FeaturedcategoriesRepositoryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Category extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'featuredcategories/categories.phtml';

    /**
     * @var FeaturedcategoriesRepositoryInterface
     */
    protected $_featuredcategoriesRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepositoryInterface;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $_category;

    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param FeaturedcategoriesRepositoryInterface   $featuredcategoriesRepository
     * @param CategoryRepositoryInterface             $categoryRepositoryInterface
     * @param \Magento\Catalog\Model\Category         $category
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        FeaturedcategoriesRepositoryInterface $featuredcategoriesRepository,
        CategoryRepositoryInterface $categoryRepositoryInterface,
        \Magento\Catalog\Model\Category $category,
        array $data = []
    ) {
        $this->_featuredcategoriesRepository = $featuredcategoriesRepository;
        $this->categoryRepositoryInterface = $categoryRepositoryInterface;
        $this->_category = $category;
        parent::__construct($context, $data);
    }

    /**
     * Featuredcategories initialization
     *
     * @return string featuredcategories id
     */
    protected function initCurrentFeaturedcategories()
    {
        $featuredcategoriesId = (int)$this->getRequest()->getParam('id');
        return $featuredcategoriesId;
    }

    /**
     * Return array with category IDs which the product is assigned to.
     *
     * @return array
     */
    public function getCategoryIds()
    {
        $featuredcategoriesId = $this->initCurrentFeaturedcategories();
        if ($featuredcategoriesId) {
            return [
                $this->_featuredcategoriesRepository->getById(
                    $featuredcategoriesId
                )->getCategoryId()
            ];
        } else {
            return [];
        }
    }

    public function getCategory()
    {
        return $this->_category;
    }

    public function getCategoryData($categoryId)
    {
        return $this->categoryRepositoryInterface->get($categoryId);
    }
}
