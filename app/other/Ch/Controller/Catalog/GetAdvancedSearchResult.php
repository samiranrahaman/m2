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

namespace Custom\Chharo\Controller\Catalog;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API Catalog controller.
 */
class GetAdvancedSearchResult extends \Custom\Chharo\Controller\ApiController
{

    /**
     * $_toolbar
     *
     * @var \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    protected $_toolbar;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar
    ) {
    
        $this->_toolbar = $toolbar;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
    }

    /**
     * execute category list
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $queryString = $this->getRequest()->getPost("queryString");

            $sortData = $this->getRequest()->getPost("sortData");

            $queryStringArray = json_decode($queryString);
            $sortData = json_decode($sortData);
            $storeId = $this->getRequest()->getPost("storeId");
            $width = $this->getRequest()->getPost("width");
            $returnArray = [];
            $productCollectionArray = [];
            $criteriaArray = [];
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                //Getting Product Collection
                // $queryArray = [
                //     "name" => "hero",
                //     "sku" => "MH07"
                // ];
                $queryArray = $this->_helperCatalog->getQueryArray($queryStringArray);

                $advancedSearch = $this->_objectManager
                    ->create("\Magento\CatalogSearch\Model\Advanced")
                    ->addFilters($queryArray);

                //get search creterias
                $searchCriterias = $advancedSearch->getSearchCriterias();
                
                /**
                 * $productCollection product collection
                 *
                 * @var Magento\Catalog\Model\Product\ResourceModel\Product\Collection
                 */
                $productCollection = $advancedSearch->getProductCollection();



                //sorting product collection
                if (count($sortData) > 0) {
                    $sortBy = $sortData[0];
                    if ($sortData[1] == 0) {
                        $productCollection->setOrder($sortBy, "ASC");
                    } else {
                        $productCollection->setOrder($sortBy, "DESC");
                    }
                }
                $pageNumber = $this->getRequest()->getPost("pageNumber");
                if ($pageNumber != "") {
                    $pageNumber = $this->getRequest()->getPost("pageNumber");
                    $returnArray["totalCount"] = $productCollection->getSize();
                    $productCollection->setPageSize(16)->setCurPage($pageNumber);
                }
                foreach ($productCollection as $_product) {
                    $productCollectionArray[] = $this->_helperCatalog
                        ->getOneProductRelevantData($_product, $storeId, $width);
                }
                $returnArray["productCollection"] = $productCollectionArray;


                //Getting Sorating Collection
                $toolbar = $this->_toolbar;
                $availableOrders = $toolbar->getAvailableOrders();
                unset($availableOrders["position"]);
                $availableOrders = array_merge(["relevance" => "Relevance"], $availableOrders);
                foreach ($availableOrders as $_key => $_order) {
                    $each = [];
                    $each["code"] = $_key;
                    $each["label"] = $_order;
                    $sortingData[] = $each;
                }
                $returnArray["sortingData"] = $sortingData;

                //Getting Criteria
                $searchCriterias = $this->getSearchCriterias($searchCriterias);
                foreach (["left", "right"] as $side) {
                    if ($searchCriterias[$side]) {
                        foreach ($searchCriterias[$side] as $criteria) {
                            $criteriaArray[] = $this->_helperCatalog
                                ->stripTags($criteria["name"])." : ".$this->_helperCatalog->stripTags($criteria["value"]);
                        }
                    }
                }
                $returnArray["critariaData"] = $criteriaArray;

                //getting website data
                $returnArray["storeData"] = $this->_helperCatalog->getStoreData();
               
                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray["success"] = 1;
                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray["success"] = 0;
                $returnArray["message"] = __($e->getMessage());
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["success"] = 0;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }

    /**
     * @return array
     */
    public function getSearchCriterias($searchCriterias)
    {
        $middle = ceil(count($searchCriterias) / 2);
        $left = array_slice($searchCriterias, 0, $middle);
        $right = array_slice($searchCriterias, $middle);

        return ['left' => $left, 'right' => $right];
    }
}
