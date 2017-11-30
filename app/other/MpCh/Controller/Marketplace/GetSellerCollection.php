<?php
/**
 * Custom Software.
 *
 * @category  Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */
namespace Custom\MpChharo\Controller\Marketplace;

/**
 * MpChharo API .
 */
class GetSellerCollection extends AbstractMarketplace
{
    /**
     * execute.
     *
     * @return string JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $returnArray = [];
                $storeId = $this->getRequest()->getPost('storeId');
                $profileUrl = $this->getRequest()->getPost('profileUrl');
                $filterData = $this->getRequest()->getPost('filterData');
                $filterData = [];
                $sortData = $this->getRequest()->getPost('sortData');
                $sortData = json_decode($sortData);
                $width = $this->getRequest()->getPost('width');
                $sessionId = $this->getRequest()->getPost('sessionId');
                $pageNumber = $this->getRequest()->getPost('pageNumber');
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $thisSeller = new \Magento\Framework\DataObject();
                $returnArray = [];
                $sellerCollection = [];
                $layeredData = [];
                if ($profileUrl) {
                    $profileData = $this->_objectManager->create("\Custom\Marketplace\Model\SellerFactory")->create()->getCollection()->addFieldToFilter('shop_url', $profileUrl);
                    foreach ($profileData as $seller) {
                        $thisSeller = $seller;
                    }
                }
                $sellerId = $thisSeller->getSellerId();
                $querydata = $this->_objectManager->create("\Custom\Marketplace\Model\ProductFactory")
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('seller_id', $sellerId)
                    ->addFieldToFilter('status', ['neq' => 2])
                    ->setOrder('mageproduct_id');
                $rowdata = [];
                foreach ($querydata as $value) {
                    $stockItemDetails = $this->_objectManager
                    ->create("\Magento\CatalogInventory\Api\StockRegistryInterface")
                    ->getStockItem($value->getmageproduct_id());

                    $stockAvailability = $stockItemDetails->getIsInStock();
                    $stock_item_qty = $stockItemDetails->getQty() * 1;
                    $product = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")
                    ->create()->load($value->getmageproduct_id());

                    if ($product->getTypeId() == 'configurable' && $product['has_options']) {
                        $stock_item_qty = 1;
                    }
                    if ($stockAvailability && $product->isVisibleInCatalog() && $product->isVisibleInCatalog() && $product->isVisibleInSiteVisibility()) {
                        $rowdata[] = $value->getmageproduct_id();
                    }
                }
                $collection = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")
                    ->create()->getCollection()->addAttributeToSelect('*');
                $collection->addAttributeToFilter('entity_id', ['in' => $rowdata]);
                if (count($filterData) > 0) {
                    $category = $this->_objectManager->create("\Magento\Catalog\Model\CategoryFactory")
                    ->create()->load($filterData);
                    $collection->addCategoryFilter($category);
                }
                if (count($sortData) > 0) {
                    $sortBy = $sortData[0];
                    if ($sortData[1] == 0) {
                        $collection->setOrder($sortBy, 'ASC');
                    } else {
                        $collection->setOrder($sortBy, 'DESC');
                    }
                }
                $this
                    ->_objectManager
                    ->create('\Magento\CatalogInventory\Helper\Stock')
                    ->addInStockFilterToCollection($collection);
                // Mage::getSingleton("catalog/product_status")->addVisibleFilterToCollection($collection);
                $configurableCollection = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")
                    ->create()->getCollection()
                    ->addAttributeToFilter('type_id', 'configurable')
                    ->addAttributeToFilter('entity_id', ['in' => $querydata->getData()]);
                $outOfStockConfig = [];

                foreach ($configurableCollection as $_configurableproduct) {
                    $product = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")
                    ->create()->load($_configurableproduct->getId());

                    if (!$product->getData('is_salable')) {
                        $outOfStockConfig[] = $product->getId();
                    }
                }
                if (count($outOfStockConfig)) {
                    $collection->addAttributeToFilter('entity_id', ['nin' => $outOfStockConfig]);
                }

                $collectionBundle = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")
                    ->create()->getCollection()
                    ->addAttributeToFilter('type_id', 'bundle')
                    ->addAttributeToFilter('entity_id', ['in' => $querydata->getData()]);
                $outOfStockBundle = [];
                foreach ($collectionBundle as $_bundleproduct) {
                    $product = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")
                    ->create()->load($_bundleproduct->getId());
                    if (!$product->getData('is_salable')) {
                        $outOfStockBundle[] = $product->getId();
                    }
                }

                if (count($outOfStockBundle)) {
                    $collection->addAttributeToFilter(
                        'entity_id',
                        ['nin' => $outOfStockBundle,
                        ]
                    );
                }

                $collectionGrouped = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")
                    ->create()->getCollection()
                ->addAttributeToFilter('type_id', 'grouped')
                ->addAttributeToFilter('entity_id', ['in' => $querydata->getData()]);
                $outOfStockGrouped = [];

                foreach ($collectionGrouped as $_groupedproduct) {
                    $product = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")
                    ->create()->load($_groupedproduct->getId());

                    if (!$product->getData('is_salable')) {
                        $outOfStockGrouped[] = $product->getId();
                    }
                }
                if (count($outOfStockGrouped)) {
                    $collection->addAttributeToFilter('entity_id', ['nin' => $outOfStockGrouped]);
                }
                if ($pageNumber != '') {
                    $returnArray['totalCount'] = $collection->getSize();
                    $collection->setPageSize(9)->setCurPage($pageNumber);
                }
                foreach ($collection as $_product) {
                    $sellerCollection[] = $this->_helperCatalog->getOneProductRelevantData($_product, $storeId, $width);
                }
                $returnArray['categoryData'] = $sellerCollection;

                $returnArray['sortingData'] = [
                    [
                        'code' => 'price',
                        'label' => 'Price',
                    ], [
                    'code' => 'name',
                    'label' => 'Name',
                    ],
                ];

                if (count($filterData) == 0) {
                    $products = $this->_objectManager->create("\Custom\Marketplace\Model\ProductFactory")
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('seller_id', $sellerId)
                    ->addFieldToFilter('status', ['neq' => 2])
                    ->addFieldToSelect('mageproduct_id');
                    $rowdata = [];
                    foreach ($products as $value) {
                        $stockItemDetails = $this->_objectManager
                        ->create("\Magento\CatalogInventory\Api\StockRegistryInterface")
                        ->getStockItem($value->getMageproductId());

                        $stockAvailability = $stockItemDetails->getIsInStock();
                        $stock_item_qty = $stockItemDetails->getQty() * 1;
                        $product = $this->_objectManager->create("\Magento\Catalog\Model\ProductFactory")->create()
                        ->load($value->getMageproductId());

                        if ($product->getTypeId() == 'configurable' && $product['has_options']) {
                            $stock_item_qty = 1;
                        }
                        if ($stockAvailability && $product->isVisibleInCatalog() && $product->isVisibleInCatalog() && $product->isVisibleInSiteVisibility()) {
                            $rowdata[] = $value->getMageproductId();
                        }
                    }
                    $products = $this->_objectManager->create("\Custom\Marketplace\Model\ProductFactory")->create()
                    ->getCollection()
                    ->addFieldToFilter('mageproduct_id', ['in' => $rowdata])
                    ->addFieldToSelect('mageproduct_id');

                    $eavAttribute = $this->_objectManager
                    ->get("\Magento\Eav\Model\Config");
                    $proAttId = $eavAttribute->getAttribute('catalog_category', 'name')->getAttributeId();
                    $parentid = $this->_storeManager->getStore()->getRootCategoryId();

                    $products->getSelect()
                        ->join(
                            ['ccp' => $this->_resourceConnection->getTableName('catalog_category_product')],
                            'ccp.product_id = main_table.mageproduct_id',
                            ['category_id' => 'category_id']
                        )
                        ->join(
                            ['cce' => $this->_resourceConnection->getTableName('catalog_category_entity')],
                            'cce.entity_id = ccp.category_id',
                            ['parent_id' => 'parent_id']
                        )->where("cce.parent_id = '".$parentid."'")
                        ->columns('COUNT(*) AS countCategory')
                        ->group('category_id')
                        ->join(
                            ['ce1' => $this->_resourceConnection->getTableName('catalog_category_entity_varchar'),
                            ],
                            'ce1.entity_id = ccp.category_id',
                            ['name' => 'value']
                        )->where('ce1.attribute_id = '.$proAttId)
                        ->order('name');
                    $this->createLog("MpChharo Exception log for class: ".get_class($this)." : ", [(string)$products->getSelect()]);
                    $arrHaveChildCat = [];
                    $each = [];
                    $eachlayeredData = [];
                    $each['label'] = 'Category';
                    $each['code'] = 'cat';
                    foreach ($products as $value) {
                        array_push($arrHaveChildCat, $value['category_id']);
                        if ($value['category_id'] != 1) {
                            $eachFilter = [];
                            $eachFilter['label'] = $value['name'];
                            $eachFilter['id'] = $value['category_id'];
                            $eachFilter['count'] = $value['countCategory'];
                            $eachlayeredData[] = $eachFilter;
                        }
                    }
                    $each['options'] = $eachlayeredData;
                    $returnArray['layeredData'][] = $each;
                } else {
                    $returnArray['layeredData'] = '';
                }

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);

                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("MpChharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
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
}
