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

namespace Custom\Chharo\Controller\Extra;

/**
 * Chharo API Extra controller.
 */
class GetCustomCollection extends AbstractChharo
{


    /**
     * execute
     *
     * @return JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $storeId = $this->getRequest()->getPost("storeId");
            $width = $this->getRequest()->getPost("width");
            $notificationId = $this->getRequest()->getPost("notificationId");
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                $returnArray =[];
                $notification = $this->_chharoNotification
                    ->create()
                    ->load($notificationId);

                $filterData = unserialize($notification->getFilterData());

                $productCollection = $this->_productFactory
                    ->create()->getCollection();

                /**
                 * added visibility and status filter and stock filter
                 */
                $productCollection
                    ->addAttributeToFilter(
                        'status',
                        ['in' => $this->_productStatus->getVisibleStatusIds()]
                    );

                //visibility filter
                $productCollection->setVisibility($this->_productVisibility->getVisibleInSiteIds());

                //in stock filter
                $this
                    ->_objectManager
                    ->create('\Magento\CatalogInventory\Helper\Stock')
                    ->addInStockFilterToCollection($productCollection);



                if ($notification->getCollectionType() == "product_attribute") {
                    foreach ($filterData as $key => $filterValue) {
                        if ($key == "category_ids") {
                            foreach (explode(",", $filterValue) as $value) {
                                $productCollection->addCategoryFilter($this->_categoryFactory->create()->load($value));
                            }
                        } else {
                            $productCollection->addAttributeToSelect($key);
                            $productCollection->addAttributeToFilter($key, ["in" => $filterValue]);
                        }
                    }
                } elseif ($notification->getCollectionType() == "product_ids") {
                    $productCollection->addAttributeToFilter("entity_id", ["in" => explode(",", $filterData)]);
                } elseif ($notification->getCollectionType() == "product_new") {
                    $productCollection->getSelect()->limit($filterData);
                }
                $filteredProducts = [];
                foreach ($productCollection as $product) {
                    $product = $this->_productFactory
                        ->create()->load($product->getId());
                    $filteredProducts[] = $this->_helperCatalog
                        ->getOneProductRelevantData($product, $storeId, $width);
                }
                $returnArray["success"] = 1;
                $returnArray["products"] = $filteredProducts;
                $returnArray["totalCount"] = count($filteredProducts);

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray["success"] = 0;
                $returnArray["message"] = __("Invalid Request.");
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["success"] = 0;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }
}
