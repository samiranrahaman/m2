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
class GetCatalogSearchResult extends \Custom\Chharo\Controller\ApiController
{

    /**
     * $_categoryTree
     *
     * @var Magento\Catalog\Model\Category\Tree
     */
    protected $_categoryTree;

    protected $_fullText;

    /**
     * $_productStatus
     *
     * @var \Magento\Catalog\Model\Product\Attribute\Source\Status
     */
    protected $_productStatus;

    /**
     * $_productVisibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_productVisibility;

    /**
     * $_toolbar
     *
     * @var \Magento\Catalog\Block\Product\ProductList\Toolbar
     */
    protected $_toolbar;

    /**
     * $_queryFactory
     *
     * @var Magento\Search\Model\Query
     */
    protected $_queryFactory;
    
    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        \Magento\CatalogSearch\Model\Fulltext $fullText,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Block\Product\ProductList\Toolbar $toolbar,
        \Magento\Search\Model\QueryFactory $queryFactory
    ) {
    
        $this->_fullText = $fullText;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        $this->_toolbar = $toolbar;
        $this->_queryFactory = $queryFactory;
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
            $returnArray = [];
            $productCollection = [];
            $sortingData = [];
            $sortData = $this->getRequest()->getPost("sortData");
            $searchQuery = $this->getRequest()->getPost("searchQuery");
            $this->getRequest()->setParam('q', $searchQuery);
            $storeId = $this->getRequest()->getPost("storeId");
            $width = $this->getRequest()->getPost("width");
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                
                /**
                 * $query search query
                 *
                 * @var Magento\Search\Model\Query
                 */
                $query = $this->_queryFactory
                    ->get();

                /**
                 * $collection get search result collection
                 *
                 * @var Magento\Catalog\Model\ResourceModel\Product\Collection
                 */
                $collection = $this->_objectManager
                    ->create('\Magento\CatalogSearch\Model\ResourceModel\Search\Collection')
                    ->addAttributeToSelect("*");

                //set search text
                $collection->addSearchFilter($query->getQueryText());
                
                $collection->setStoreId($storeId);
                $collection->addMinimalPrice();
                $collection->addFinalPrice();
                $collection->addTaxPercents();
                $collection->addStoreFilter();
                $collection->addUrlRewrite();
                $collection
                    ->addAttributeToFilter(
                        'status',
                        ['in' => $this->_productStatus->getVisibleStatusIds()]
                    );

                $collection->setVisibility($this->_productVisibility->getVisibleInSiteIds());
                $sortData = json_decode($sortData);

                //sorting product collection
                if (count($sortData) > 0) {
                    $sortBy = $sortData[0];
                    if ($sortData[1] == 0) {
                        $collection->setOrder($sortBy, "ASC");
                    } else {
                        $collection->setOrder($sortBy, "DESC");
                    }
                }
                $pageNumber = $this->getRequest()->getPost("pageNumber");
                if ($pageNumber != "") {
                    $pageNumber = $this->getRequest()->getPost("pageNumber");
                    $returnArray["totalCount"] = $collection->getSize();
                    $collection->setPageSize(16)->setCurPage($pageNumber);
                }
                foreach ($collection as $_product) {
                    $productCollection[] = $this->_helperCatalog
                        ->getOneProductRelevantData($_product, $storeId, $width);
                }
                $returnArray["productCollection"] = $productCollection;

                //getting sorting collection
                $toolbar = $this->_toolbar;
                $availableOrders = $toolbar->getAvailableOrders();
                unset($availableOrders["position"]);
                $availableOrders = array_merge(
                    ["relevance" => "Relevance"],
                    $availableOrders
                );
                foreach ($availableOrders as $_key => $_order) {
                    $each = [];
                    $each["code"] = $_key;
                    $each["label"] = $_order;
                    $sortingData[] = $each;
                }
                $returnArray["sortingData"] = $sortingData;
                $returnArray["storeData"] = $this->_helperCatalog->getStoreData();
                $returnArray["success"] = 1;

                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
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
