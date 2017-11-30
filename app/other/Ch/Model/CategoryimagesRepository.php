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
use Custom\Chharo\Api\Data\CategoryimagesInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class CategoryimagesRepository implements \Custom\Chharo\Api\CategoryimagesRepositoryInterface
{
    /**
     * @var CategoryimagesFactory
     */
    protected $_categoryimagesFactory;

    /**
     * @var Categoryimages[]
     */
    protected $_instances = [];

    /**
     * @var Categoryimages[]
     */
    protected $_instancesById = [];

    /**
     * @var \Custom\Chharo\Model\ResourceModel\Categoryimages\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Custom\Chharo\Model\ResourceModel\Categoryimages
     */
    protected $_resourceModel;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /**
     * @param CategoryimagesFactory                                $categoryimagesFactory
     * @param ResourceModel\Categoryimages\CollectionFactory       $collectionFactory
     * @param ResourceModel\Categoryimages                         $resourceModel
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        CategoryimagesFactory $categoryimagesFactory,
        ResourceModel\Categoryimages\CollectionFactory $collectionFactory,
        ResourceModel\Categoryimages $resourceModel,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_categoryimagesFactory = $categoryimagesFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save(CategoryimagesInterface $categoryimages)
    {
        $categoryimagesId = $categoryimages->getId();
        try {
            $this->_resourceModel->save($categoryimages);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->_instancesById[$categoryimages->getId()]);

        return $this->getById($categoryimages->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getById($categoryimagesId)
    {
        $categoryimagesData = $this->_categoryimagesFactory->create();
        /* @var \Custom\Chharo\Model\ResourceModel\Categoryimages\Collection $categoryimagesData */
        $categoryimagesData->load($categoryimagesId);
        if (!$categoryimagesData->getId()) {
            // categoryimages record does not exist
            //throw new NoSuchEntityException(__('Requested categoryimages record doesn\'t exist'));
        }
        $this->_instancesById[$categoryimagesId] = $categoryimagesData;

        return $this->_instancesById[$categoryimagesId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /**
 * @var \Custom\Chharo\Model\ResourceModel\Categoryimages\Collection $collection 
*/
        $collection = $this->_collectionFactory->create();
        $collection->load();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CategoryimagesInterface $categoryimages)
    {
        $categoryimagesId = $categoryimages->getId();
        try {
            $this->_resourceModel->delete($categoryimages);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove categoryimages record with id %1', $categoryimagesId)
            );
        }
        unset($this->_instancesById[$categoryimagesId]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($categoryimagesId)
    {
        $categoryimages = $this->getById($categoryimagesId);

        return $this->delete($categoryimages);
    }
}
