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
use Magento\Catalog\Model\Category\AttributeRepository;

/**
 * Chharo API Catalog controller.
 */
class GetFeaturedProducts extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_categoryTree.
     *
     * @var Magento\Catalog\Model\Category\Tree
     */
    protected $_categoryTree;

    /**
     * $_baseDir.
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * $_attributeRepository.
     *
     * @var Magento\Catalog\Model\Category\AttributeRepository
     */
    protected $_attributeRepository;

    /**
     * $_dir description.
     *
     * @var Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * $_imageFactory.
     *
     * @var Magento\Framework\Image\Factory
     */
    protected $_imageFactory;

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
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        CategoryTree $categoryTree,
        AttributeRepository $attributeRepository,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Catalog\Model\Product\Visibility $productVisibility
    ) {
        $this->_categoryTree = $categoryTree;
        $this->_attributeRepository = $attributeRepository;
        $this->_dir = $dir;
        $this->_imageFactory = $imageFactory;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
        $this->_baseDir = $this->_dir
            ->getPath('media').DS;
    }

    /**
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $returnArray = [];
            $categories = [];
            $bannerImages = [];
            $featuredCategories = [];
            $featuredProducts = [];
            $newProducts = [];
            $storeData = [];
            $limit = 0;
            $storeId = $this->getRequest()->getPost('storeId');
            $websiteId = $this->getRequest()->getPost('websiteId');
            $sessionId = $this->getRequest()->getPost('sessionId');
            $rootcat = $this->getRequest()->getPost('rootcat');
            $width = $this->getRequest()->getPost('width');
            $themeCode = $this->_helper->getThemeCode();
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $returnArray['storeId'] = $storeId;
                $returnArray['success'] = 1;
                //$returnArray['versionCode'] = parent::VERSION_CODE;
                //$returnArray['themeCode'] = $themeCode;

                //collecting featured products
                $featuredProductCollection = $this->_objectManager
                    ->create('\Magento\Catalog\Model\Product')
                    ->getCollection();

                /*
                 * added visibility and status filter and stock filter
                 */
                $featuredProductCollection
                    ->addAttributeToFilter(
                        'status',
                        ['eq' => 1]
                    );

                $this
                    ->_objectManager
                    ->create('\Magento\CatalogInventory\Helper\Stock')
                    ->addInStockFilterToCollection($featuredProductCollection);
                $featuredProductCollection->setVisibility($this->_productVisibility->getVisibleInSiteIds());
                $featuredProductCollection->addStoreFilter();
                $random = (int)rand(1, (int)$featuredProductCollection->getSize()/5);
                $featuredProductCollection->setPage($random, 5)->load();

                foreach ($featuredProductCollection as $eachProduct) {
                    $eachProduct = $this->_objectManager
                        ->create('\Magento\Catalog\Model\ProductFactory')
                        ->create()
                        ->load($eachProduct->getId());
                    $featuredProducts[] = $this->_helperCatalog
                        ->getOneProductRelevantData(
                            $eachProduct,
                            $storeId,
                            $width
                        );
                }
                $returnArray['featuredProducts'] = $featuredProducts;

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
}