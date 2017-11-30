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

namespace Custom\Chharo\Controller\Catalog;
//define('DS','/');

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Store\Model\App\Emulation;
use Magento\Catalog\Model\Category\Tree as CategoryTree;
use Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory;

/**
 * Chharo API Catalog controller.
 */
class GetCategoryProductList extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_categoryTree.
     *
     * @var Magento\Catalog\Model\Category\Tree
     */
    protected $_categoryTree;

    /**
     * $_productStatus.
     *
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_productStatus;

    /**
     * $_productVisibility.
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_productVisibility;

    /**
     * $_dir description.
     *
     * @var Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * $_baseDir.
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * $_coreRegistry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * $_listProduct.
     *
     * @var Magento\Catalog\Block\Product\ListProduct
     */
    protected $_listProduct;

    /**
     * $_eavConfig.
     *
     * @var Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * $_toolbar.
     *
     * @var
     */
    protected $_toolbar;

    /**
     * @var FilterableAttributeListInterface
     */
    protected $_filterableAttributes;

    protected $_categoryFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        CategoryTree $categoryTree,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar,
        \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $filterableAttributes,
        CategoryFactory $categoryFactory
    ) {
        $this->_categoryTree = $categoryTree;
        $this->_imageFactory = $imageFactory;
        $this->_dir = $dir;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        $this->_coreRegistry = $coreRegistry;
        $this->_listProduct = $listProduct;
        $this->_eavConfig = $eavConfig;
        $this->_toolbar = $toolbar;
        $this->_categoryFactory = $categoryFactory;
        $this->_filterableAttributes = $filterableAttributes;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
        $this->_baseDir = $this->_dir
            ->getPath('media');

        $this->_dataProvider = $this->_categoryFactory->create();
    }

    /**
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $categoryId = $this->getRequest()->getPost('categoryId');
            $storeId = $this->getRequest()->getPost('storeId');
            $width = $this->getRequest()->getpost('width');
            $sortData = $this->getRequest()->getPost('sortData');
            if ($sortData == '') {
                $sortData = [];
            } else {
                $sortData = json_decode($sortData);
            }
            $filterData = $this->getRequest()->getPost('filterData');
            if ($filterData == '') {
                $filterData = [];
            } else {
                $filterData = json_decode($filterData);
            }
            $returnArray = [];
            $categoryData = [];
            $layeredData = [];
            $sortingData = [];
            $stateData = [];

            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $returnArray['storeId'] = $storeId;
                $returnArray['success'] = 1;
                $returnArray['versionCode'] = self::VERSION_CODE;

                //start data creation
                $category = $this->_objectManager
                    ->create('\Magento\Catalog\Model\Category')
                    ->setStoreId($storeId)
                    ->load($categoryId);
                $this->_coreRegistry->register(
                    'current_category',
                    $category
                );
                $categoryBlock = $this->_listProduct;
                $productCollection = $categoryBlock->getLoadedProductCollection();
                if ($this->_helperCatalog->showOutOfStock() == 0) {
                    $this
                        ->_objectManager
                        ->create('\Magento\CatalogInventory\Helper\Stock')
                        ->addInStockFilterToCollection($productCollection);

                    //addSaleableFilterToCollection function deprecated
                    // Mage::getSingleton("catalog/product_status")->addSaleableFilterToCollection($productCollection);
                }
                //filtering product collection
                if (count($filterData) > 0) {
                    for ($i = 0; $i < count($filterData[0]); ++$i) {
                        if ($filterData[0][$i] != '') {
                            if ($filterData[1][$i] == 'price') {
                                $minPossiblePrice = .01;
                                $currencyRate = $productCollection->getCurrencyRate();
                                $priceRange = explode('-', $filterData[0][$i]);
                                $from = $priceRange[0];
                                $to = $priceRange[1];
                                $fromRange = ($from - ($minPossiblePrice / 2)) / $currencyRate;
                                $toRange = ($to - ($minPossiblePrice / 2)) / $currencyRate;
                                $select = $productCollection->getSelect();
                                if ($from !== '') {
                                    $select->where('price_index.min_price'.'>='.$fromRange);
                                }
                                if ($to !== '') {
                                    $select->where('price_index.min_price'.'<'.$toRange);
                                }
                            } elseif ($filterData[1][$i] == 'cat') {
                                $categoryToFilter = $this->_objectManager
                                    ->create('\Magento\Catalog\Model\Category')
                                    ->load($filterData[0][$i]);
                                $productCollection->setStoreId($storeId)->addCategoryFilter($categoryToFilter);
                            } else {
                                $attribute = $this->_eavConfig
                                    ->getAttribute(
                                        'catalog_product',
                                        $filterData[1][$i]
                                    );
                                $attributeModel = $this->_objectManager
                                    ->create('\Magento\Catalog\Model\Layer\Filter\Attribute');
                                $attributeModel->setAttributeModel($attribute);
                                $filterAtr = $this->_objectManager
                                    ->create('\Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute');

                                $connection = $filterAtr->getConnection();
                                $tableAlias = $attribute->getAttributeCode().'_idx';
                                $conditions = [
                                    "{$tableAlias}.entity_id = e.entity_id",
                                    $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
                                    $connection->quoteInto("{$tableAlias}.store_id = ?", $productCollection->getStoreId()),
                                    $connection->quoteInto("{$tableAlias}.value = ?", $filterData[0][$i]),
                                ];

                                $productCollection->getSelect()->join(
                                    [$tableAlias => $filterAtr->getMainTable()],
                                    implode(' AND ', $conditions),
                                    []
                                );
                            }
                        }
                    }
                }

                //sorting product collection
                if (count($sortData) > 0) {
                    $sortBy = $sortData[0];
                    if ($sortData[1] == 0) {
                        $productCollection->setOrder($sortBy, 'ASC');
                    } else {
                        $productCollection->setOrder($sortBy, 'DESC');
                    }
                }

                $pageNumber = $this->getRequest()->getPost('pageNumber');
                if ($pageNumber != '') {
                    $pageNumber = $this->getRequest()->getPost('pageNumber');
                    $returnArray['totalCount'] = $productCollection->getSize();
                    $productCollection->setPageSize(16)->setCurPage($pageNumber);
                }

                //creating product collection data
                foreach ($productCollection as $_product) {
                    $categoryData[] = $this->_helperCatalog
                        ->getOneProductRelevantData(
                            $_product,
                            $storeId,
                            $width
                        );
                }
                $doCategory = 1;
                if (count($filterData) > 0) {
                    if (in_array('cat', $filterData[1])) {
                        $doCategory = 0;
                    }
                }
                if ($doCategory == 1) {
                    $categoryFilterModel = $this->_objectManager
                        ->create('\Magento\Catalog\Model\Layer\Filter\Category');
                    if ($categoryFilterModel->getItemsCount()) {
                        $each = [];
                        $each['label'] = 'Category';
                        $each['code'] = 'cat';
                        $key = $categoryFilterModel->getLayer()->getStateKey().'_SUBCATEGORIES';
                        //var_dump($key);die;
                        ///$data = $this->getAggregator()->load($key);
                        //if ($data === null) {
                            $category = $this->_dataProvider->getCategory();
                            $categories = $category->getChildrenCategories();
                            $categoryFilterModel->getLayer()->getProductCollection()->addCountToCategories($categories);
                            $data = [];
                            foreach ($categories as $category) {
                                if ($category->getIsActive()
                                    && $category->getProductCount()
                                ) {
                                    $data[] = [
                                        'label' => str_replace(
                                            '&amp;',
                                            '&',
                                            $this->_helperCatalog->stripTags(
                                                $category->getName()
                                            )
                                        ),
                                        'id' => $category->getId(),
                                        'count' => $category->getProductCount(),
                                    ];
                                }
                            }
                            $tags = $categoryFilterModel->getLayer()->getStateTags();
                            //$this->getAggregator()->save($data, $key, $tags);
                        //}
                        if($data) {
                            $each['options'] = $data;
                        } else {
                            $each['options'] = [];
                        }
                        $layeredData[] = $each;
                    }
                }
                $doPrice = 1;
                if (count($filterData) > 0) {
                    if (in_array('price', $filterData[1])) {
                        $doPrice = 0;
                    }
                }
                $_filters = $this->_filterableAttributes->getList();
                foreach ($_filters as $_filter) {
                    if ($_filter->getFrontendInput() == 'price') {
                        if ($doPrice == 1) {
                            $priceFilterModel = $this->_objectManager
                                ->create('\Magento\Catalog\Model\Layer\Filter\DataProvider\Price');
                            if ($priceFilterModel) {
                                $each = [];
                                $each['label'] = $_filter->getFrontendLabel();
                                $each['code'] = $_filter->getAttributeCode();
                                $priceOptions = $this->_helperCatalog
                                    ->getPriceFilter($priceFilterModel, $storeId);
                                $each['options'] = $priceOptions;
                                $layeredData[] = $each;
                            }
                        }
                    } else {
                        $doAttribute = 1;
                        if (count($filterData) > 0) {
                            if (in_array(
                                $_filter->getAttributeCode(),
                                $filterData[1]
                            )
                            ) {
                                $doAttribute = 0;
                            }
                        }
                        if ($doAttribute == 1) {
                            $attributeFilterModel = $this->_objectManager
                                ->create('\Magento\Catalog\Model\Layer\Filter\Attribute')
                                ->setAttributeModel($_filter);
                            if ($attributeFilterModel->getItemsCount()) {
                                $each = [];
                                $each['label'] = $_filter->getFrontendLabel();
                                $each['code'] = $_filter->getAttributeCode();
                                $attributeOptions = $this->_helperCatalog
                                    ->getAttributeFilter(
                                        $attributeFilterModel,
                                        $_filter
                                    );
                                $each['options'] = $attributeOptions;
                                $layeredData[] = $each;
                            }
                        }
                    }
                }
                $toolbar = $this->_toolbar;
                foreach ($toolbar->getAvailableOrders() as $_key => $_order) {
                    $each = [];
                    $each['code'] = $_key;
                    $each['label'] = $_order;
                    $sortingData[] = $each;
                }
                $returnArray['categoryData'] = $categoryData;
                $returnArray['layeredData'] = $layeredData;
                $returnArray['sortingData'] = $sortingData;
                $customerId = $this->getRequest()->getPost('customerId');
                if ($customerId != '') {
                    $quoteCollection = $this->_objectManager
                        ->create("\Magento\Quote\Model\Quote")
                        ->getCollection();
                    $quoteCollection->addFieldToFilter('customer_id', $customerId);
                    $quoteCollection->addOrder('updated_at', 'desc');
                    $quote = $quoteCollection->getFirstItem();
                    $returnArray['cartCount'] = $quote->getItemsQty() * 1;
                }
                //End data cresation


                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {

                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __($e->getMessage());

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }

    private function getAggregator()
    {
        return $this->_objectManager
            ->get('\Magento\Framework\App\CacheInterface');
    }
}
