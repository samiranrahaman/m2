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
use Custom\Chharo\Api\Data\BannerimageInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class BannerimageRepository implements \Custom\Chharo\Api\BannerimageRepositoryInterface
{
    /**
     * @var BannerimageFactory
     */
    protected $_bannerimageFactory;

    /**
     * @var Bannerimage[]
     */
    protected $_instances = [];

    /**
     * @var Bannerimage[]
     */
    protected $_instancesById = [];

    /**
     * @var \Custom\Chharo\Model\ResourceModel\Bannerimage\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Custom\Chharo\Model\ResourceModel\Bannerimage
     */
    protected $_resourceModel;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /**
     * @param BannerimageFactory                                   $bannerimageFactory
     * @param ResourceModel\Bannerimage\CollectionFactory          $collectionFactory
     * @param ResourceModel\Bannerimage                            $resourceModel
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        BannerimageFactory $bannerimageFactory,
        ResourceModel\Bannerimage\CollectionFactory $collectionFactory,
        ResourceModel\Bannerimage $resourceModel,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->_resourceModel = $resourceModel;
        $this->_bannerimageFactory = $bannerimageFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function save(BannerimageInterface $bannerimage)
    {
        $bannerimageId = $bannerimage->getId();
        try {
            $this->_resourceModel->save($bannerimage);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                $e->getMessage()
            );
        }
        unset($this->_instancesById[$bannerimage->getId()]);

        return $this->getById($bannerimage->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function getById($bannerimageId)
    {
        $bannerimageData = $this->_bannerimageFactory->create();
        /* @var \Custom\Chharo\Model\ResourceModel\Bannerimage\Collection $bannerimageData */
        $bannerimageData->load($bannerimageId);
        if (!$bannerimageData->getId()) {
            // banner does not exist
            //throw new NoSuchEntityException(__('Requested banner doesn\'t exist'));
        }
        $this->_instancesById[$bannerimageId] = $bannerimageData;

        return $this->_instancesById[$bannerimageId];
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /**
 * @var \Custom\Chharo\Model\ResourceModel\Bannerimage\Collection $collection 
*/
        $collection = $this->_collectionFactory->create();
        $collection->load();

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(BannerimageInterface $bannerimage)
    {
        $bannerimageId = $bannerimage->getId();
        try {
            $this->_resourceModel->delete($bannerimage);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove banner image with id %1', $bannerimageId)
            );
        }
        unset($this->_instancesById[$bannerimageId]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($bannerimageId)
    {
        $bannerimage = $this->getById($bannerimageId);

        return $this->delete($bannerimage);
    }
}
