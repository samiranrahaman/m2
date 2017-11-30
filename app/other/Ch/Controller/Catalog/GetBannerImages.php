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
class GetBannerImages extends \Custom\Chharo\Controller\ApiController
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
            $rootId = $this->getRequest()->getPost('rootId');
            $width = $this->getRequest()->getPost('width');
            //$themeCode = $this->_helper->getThemeCode();
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

                /*
                 * create banner data
                 */
                $imageBanner = $this->_objectManager->create('\Custom\Chharo\Model\Bannerimage');

                $collection = $imageBanner
                    ->getCollection()
                    ->addFieldToFilter('status', 1)
                    // ->addFieldToFilter(
                    //     'store_id',
                    //     [
                    //         [
                    //             'finset' => [$storeId],
                    //         ],
                    //     ]
                    // )
                    ->setOrder('sort_order', 'ASC');
                $height = $width / 2;
                foreach ($collection as $eachBanner) {
                    $oneBanner = [];
                    $newUrl = '';
                    $basePath = $this->_baseDir.
                    'chharo'
                    .DS.
                    'bannerimages'
                    .DS.
                    $eachBanner->getFilename();

                    if (file_exists($basePath)) {
                        $newPath = $this->_baseDir
                        .'chharo'
                        .DS.
                        'bannerimages'
                        .DS.
                        'chharoresized'
                        .DS.
                        $width
                        .'x'.
                        $height
                        .DS.
                        $eachBanner->getFilename();

                        $newUrl = $this->_helper->getUrl('media')
                        .'chharo'
                        .DS.
                        'bannerimages'
                        .DS.
                        'chharoresized'
                        .DS.
                        $width
                        .'x'.
                        $height
                        .DS.
                        $eachBanner->getFilename();

                        if (!file_exists($newPath)) {
                            $this->_helperCatalog->imageUpload($basePath, $newPath, $width, $height);
                        }
                    }

                    $oneBanner['url'] = $newUrl;
                    $oneBanner['bannerType'] = $eachBanner->getType();
                    if ($eachBanner->getType() == 'category') {
                        $categoryModel = $this->_objectManager
                            ->create('\Magento\Catalog\Model\Category')
                            ->load($eachBanner->getProCatId());
                        //for category
                        if ($categoryModel->getName()) {
                            $oneBanner['error'] = 0;
                        } else {
                            $oneBanner['error'] = 1;
                        }
                        $oneBanner['categoryName'] = $categoryModel->getName();
                        $oneBanner['categoryId'] = $eachBanner->getProCatId();
                    } elseif ($eachBanner->getType() == 'product') {
                        $productModel = $this->_objectManager
                            ->create('\Magento\Catalog\Model\Product')
                            ->load($eachBanner->getProCatId());
                        //for product
                        if ($productModel->getName()) {
                            $oneBanner['error'] = 0;
                        } else {
                            $oneBanner['error'] = 1;
                        }
                        $oneBanner['productName'] = $productModel->getName();
                        $oneBanner['productType'] = $productModel->getTypeId();
                        $oneBanner['productId'] = $eachBanner->getProCatId();
                    }
                    $bannerImages[] = $oneBanner;
                }
                $returnArray['bannerImages'] = $bannerImages;

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