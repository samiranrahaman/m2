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

namespace Custom\Chharo\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\SearchCriteriaInterface;
use Custom\Chharo\Api\Data\FeaturedcategoriesInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class FeaturedcategoriesRepository implements \Custom\Chharo\Api\FeaturedcategoriesRepositoryInterface
{
    /**
     * @var FeaturedcategoriesFactory
     */
    protected $_featuredcategoriesFactory;

    /**
     * @var Featuredcategories[]
     */
    protected $_instances = [];

    /**
     * @var Featuredcategories[]
     */
    protected $_instancesById = [];

    /**
     * @var \Custom\Chharo\Model\ResourceModel\Featuredcategories\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Custom\Chharo\Model\ResourceModel\Featuredcategories
     */
    protected $_resourceModel;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /**
     * @param FeaturedcategoriesFactory                            $featuredcategoriesFactory
     * @param ResourceModel\Featuredcategories\CollectionFactory   $collectionFactory
     * @param ResourceModel\Featuredcategories                     $resourceModel
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        FeaturedcategoriesFactory $featuredcategoriesFactory,
        ResourceModel\Featuredcategories\CollectionFactory $collectionFactory,
        ResourceModel\Featuredcategories $resourceModel,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_featuredcategoriesFactory = $featuredcategoriesFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save(FeaturedcategoriesInterface $featuredcategories)
    {
        $featuredcategoriesId = $featuredcategories->getId();
        try {
            $this->_resourceModel->save($featuredcategories);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->_instancesById[$featuredcategories->getId()]);

        return $this->getById($featuredcategories->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getById($featuredcategoriesId)
    {
        $featuredcategoriesData = $this->_featuredcategoriesFactory->create();
        /* @var \Custom\Chharo\Model\ResourceModel\Featuredcategories\Collection $featuredcategoriesData */
        $featuredcategoriesData->load($featuredcategoriesId);
        if (!$featuredcategoriesData->getId()) {
            // featuredcategories record does not exist
            //throw new NoSuchEntityException(__('Requested featuredcategories record doesn\'t exist'));
        }
        $this->_instancesById[$featuredcategoriesId] = $featuredcategoriesData;

        return $this->_instancesById[$featuredcategoriesId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /**
 * @var \Custom\Chharo\Model\ResourceModel\Featuredcategories\Collection $collection 
*/
        $collection = $this->_collectionFactory->create();
        $collection->load();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(FeaturedcategoriesInterface $featuredcategories)
    {
        $featuredcategoriesId = $featuredcategories->getId();
        try {
            $this->_resourceModel->delete($featuredcategories);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove featuredcategories record with id %1', $featuredcategoriesId)
            );
        }
        unset($this->_instancesById[$featuredcategoriesId]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($featuredcategoriesId)
    {
        $featuredcategories = $this->getById($featuredcategoriesId);

        return $this->delete($featuredcategories);
    }
}
