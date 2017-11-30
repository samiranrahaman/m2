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
namespace Custom\MpChharo\Controller\Catalog;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * MpChharo API Catalog controller.
 */
class GetProductDetails extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_coreRegistry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * $_priceFormat.
     *
     * @var string
     */
    protected $_priceFormat;

    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    protected $_layoutFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        \Magento\Framework\Registry $coreRegistry,
        DateTime $date,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_date = $date;
        $this->_layoutFactory = $layoutFactory;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
        $this->_priceFormat = $this->_objectManager
        ->create('Magento\Framework\Pricing\Helper\Data');
    }

    /**
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $storeId = $this->getRequest()->getPost('storeId');
            $productId = $this->getRequest()->getPost('productId');
            $width = $this->getRequest()->getPost('width');
            $quoteId = $this->getRequest()->getPost('quoteId');
            $customerId = $this->getRequest()->getPost('customerId');
            $allReviews = [];
            $imageGallery = [];
            $returnArray = [];
            $relatedProductData = [];
            $additionalInformation = [];
            $allOptions = [];
            $ratingArray = [];

            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $_product = $this->_objectManager
                ->create("\Magento\Catalog\Model\Product")
                ->load($productId);

                $this->_coreRegistry->register('current_product', $_product);
                $this->_coreRegistry->register('product', $_product);

                $returnArray['id'] = $productId;
                $returnArray['productUrl'] = $_product->getProductUrl();
                $returnArray['name'] = $_product->getName();
                $returnArray['formatedPrice'] = $this->_helperCatalog
                ->stripTags(
                    $this->_priceFormat->currency(
                        $_product->getPrice()
                    )
                );
                $returnArray['price'] = $_product->getPrice();
                $returnArray['formatedFinalPrice'] = $this->_helperCatalog
                ->stripTags(
                    $this->_priceFormat->currency(
                        $_product->getFinalPrice()
                    )
                );
                $returnArray['finalPrice'] = $_product->getFinalPrice();
                if ($_product->getTypeId() == 'bundle') {
                    $bundlePriceModel = $this->_objectManager
                    ->create("\Magento\Bundle\Model\Product\Price");
                    $returnArray['formatedMinPrice'] = $this->_helperCatalog
                    ->stripTags(
                        $this->_priceFormat->currency(
                            $bundlePriceModel->getTotalPrices(
                                $_product,
                                'min',
                                1
                            )
                        )
                    );
                    $returnArray['minPrice'] = $bundlePriceModel
                    ->getTotalPrices($_product, 'min', 1);
                    $returnArray['formatedMaxPrice'] = $this->_helperCatalog
                    ->stripTags(
                        $this->_priceFormat->currency(
                            $bundlePriceModel->getTotalPrices(
                                $_product,
                                'max',
                                1
                            )
                        )
                    );
                    $returnArray['maxPrice'] = $bundlePriceModel
                    ->getTotalPrices($_product, 'max', 1);
                } else {
                    $returnArray['formatedMinPrice'] = $this->_helperCatalog
                    ->stripTags(
                        $this->_priceFormat->currency(
                            $_product->getMinPrice()
                        )
                    );
                    $returnArray['minPrice'] = $_product->getMinPrice();
                    $returnArray['formatedMaxPrice'] = $this->_helperCatalog
                    ->stripTags(
                        $this->_priceFormat->currency(
                            $_product->getMaxPrice()
                        )
                    );
                    $returnArray['maxPrice'] = $_product->getMaxPrice();
                }
                $returnArray['formatedSpecialPrice'] = $this->_helperCatalog
                ->stripTags(
                    $this->_priceFormat->currency(
                        $_product->getSpecialPrice()
                    )
                );
                $returnArray['specialPrice'] = $_product->getSpecialPrice();
                $returnArray['typeId'] = $_product->getTypeId();
                if (!$_product->getMsrpEnabled()) {
                    $returnArray["msrpEnabled"] = 2;
                } else {
                    $returnArray["msrpEnabled"] = $_product->getMsrpEnabled();
                }
                $returnArray['msrpDisplayActualPriceType'] = $_product->getMsrpDisplayActualPriceType();
                $returnArray['msrp'] = $_product->getMsrp();
                $returnArray['formatedMsrp'] = $this->_helperCatalog
                ->stripTags(
                    $this->_priceFormat->currency(
                        $_product->getMsrp()
                    )
                );
                $returnArray['shortDescription'] = html_entity_decode(
                    $this->_helperCatalog->stripTags(
                        $_product->getShortDescription()
                    )
                );
                $returnArray['description'] = html_entity_decode(
                    $this->_helperCatalog->stripTags(
                        $_product->getDescription()
                    )
                );
                $fromdate = $_product->getSpecialFromDate();
                $todate = $_product->getSpecialToDate();
                $isInRange = 0;
                if (isset($fromdate) && isset($todate)) {
                    $today = $this->_date->date('Y-m-d H:i:s');
                    $todayTime = $this->_date->timestamp($today);
                    $fromTime = $this->_date->timestamp($fromdate);
                    $toTime = $this->_date->timestamp($todate);
                    if ($todayTime >= $fromTime && $todayTime <= $toTime) {
                        $isInRange = 1;
                    }
                }
                if (isset($fromdate) && !isset($todate)) {
                    $today = $this->_date->date('Y-m-d H:i:s');
                    $todayTime = $this->_date->timestamp($today);
                    $fromTime = $this->_date->timestamp($fromdate);
                    if ($todayTime >= $fromTime) {
                        $isInRange = 1;
                    }
                }

                $returnArray['isInRange'] = $isInRange;
                if ($_product->isAvailable()) {
                    $returnArray['availability'] = __('In stock');
                    $returnArray['isAvailable'] = 1;
                } else {
                    $returnArray['availability'] = __('Out of stock');
                    $returnArray['isAvailable'] = 0;
                }
            // getting price format
                $returnArray['priceFormat'] = $this->_objectManager
                ->get('Magento\Framework\Locale\Format')
                ->getPriceFormat();
            // getting image galleries
                $galleryCollection = $_product->getMediaGalleryImages();
                $imageGallery[0]['smallImage'] =
                $this->_helperCatalog
                ->getImageUrl($_product, $width / 3, 'product_page_image_small', false);

                $imageGallery[0]['largeImage'] =
                $this->_helperCatalog
                ->getImageUrl($_product, $width, 'product_page_image_large', false);

                $imageCount = 0;
                foreach ($galleryCollection as $_image) {
                    ++$imageCount;
                    if ($imageCount == 1) {
                        continue;
                    }
                    $eachImage = [];

                    $eachImage['smallImage'] =

                    $this->_objectManager
                    ->create('\Magento\Catalog\Helper\Image')
                    ->init($_product, 'product_page_image_small')
                    ->keepFrame(false)
                    ->resize($width / 3)
                    ->setImageFile($_image->getFile())
                    ->getUrl();

                    $eachImage['largeImage'] = $this->_objectManager
                    ->create('\Magento\Catalog\Helper\Image')
                    ->init($_product, 'product_page_image_large')
                    ->keepFrame(false)
                    ->resize($width)
                    ->setImageFile($_image->getFile())
                    ->getUrl();

                    $imageGallery[] = $eachImage;
                }
                $returnArray['imageGallery'] = $imageGallery;

                //getting additional information
                foreach ($_product->getAttributes() as $attribute) {
                    if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), [])) {
                        $value = $attribute->getFrontend()->getValue($_product);
                        if (!$_product->hasData($attribute->getAttributeCode())) {
                            $value = __('N/A');
                        } elseif ((string) $value == '') {
                            $value = __('No');
                        } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                            $value = $this->_objectManager
                            ->create("\Magento\Checkout\Helper\Data")
                            ->convertPrice($value, true);
                        }

                        if (is_string($value) && strlen($value)) {
                            $eachAttribute = [];
                            $eachAttribute['label'] = $attribute->getStoreLabel();
                            $eachAttribute['value'] = $value;
                            $additionalInformation[] = $eachAttribute;
                        }
                    }
                }
                $returnArray['additionalInformation'] = $additionalInformation;
            //getting rating list
                $ratingCollection = $this->_objectManager
                    ->create("\Magento\Review\Model\Rating")
                    ->getResourceCollection()
                    ->addEntityFilter('product')
                    ->setPositionOrder()
                    ->setStoreFilter($storeId)
                    ->addRatingPerStoreName($storeId)
                    ->load();
                $ratingCollection->addEntitySummaryToItem($productId, $storeId);
                foreach ($ratingCollection as $_rating) {
                    if ($_rating->getSummary()) {
                        $eachRating = [];
                        $eachRating['ratingCode'] = $this->_helperCatalog->
                        stripTags($_rating->getRatingCode());
                        $eachRating['ratingValue'] = number_format((5 * $_rating->getSummary()) / 100, 2, '.', '');
                        $ratingArray[] = $eachRating;
                    }
                }
                $returnArray['ratingData'] = $ratingArray;
            //getting review list
                $reviewCollection = $this->_objectManager
                    ->create("\Magento\Review\Model\Review")
                    ->getResourceCollection()->addStoreFilter($storeId)
                        ->addEntityFilter('product', $productId)
                        ->addStatusFilter(
                            \Magento\Review\Model\Review::STATUS_APPROVED
                        )
                        ->setDateOrder()->addRateVotes();
                foreach ($reviewCollection as $_review) {
                    $oneReview = [];
                    $ratings = [];
                    $oneReview['title'] = $this->_helperCatalog->stripTags($_review->getTitle());
                    $oneReview['details'] = $this->_helperCatalog->stripTags($_review->getDetail());
                    $_votes = $_review->getRatingVotes();
                    if (count($_votes)) {
                        foreach ($_votes as $_vote) {
                            $oneVote = [];
                            $oneVote['label'] = $this->_helperCatalog->stripTags($_vote->getRatingCode());
                            $oneVote['value'] = number_format($_vote->getValue(), 2, '.', '');
                            $ratings[] = $oneVote;
                        }
                    }
                    $oneReview['ratings'] = $ratings;
                    $oneReview['reviewBy'] = __(
                        'Review by %1',
                        $this->_helperCatalog->stripTags(
                            $_review->getNickname()
                        )
                    );
                    $oneReview['reviewOn'] = __(
                        '(Posted on %1)',
                        $this->_helperCatalog->formatDate(
                            $_review->getCreatedAt()
                        ),
                        'long'
                    );
                    $allReviews[] = $oneReview;
                }
                $returnArray['reviewList'] = $allReviews;

                //getting custom options
                $optionBlock = $this->_objectManager
                ->create("\Magento\Catalog\Block\Product\View\Options");
                $_options = $optionBlock->decorateArray($optionBlock->getOptions());
                if (count($_options)) {
                    $eachOption = [];
                    foreach ($_options as $_option) {
                        $eachOption = $_option->getData();
                        $eachOption['unformated_default_price'] = $this->_priceFormat->currency(
                            $_option->getDefaultPrice(),
                            false,
                            false
                        );
                        $eachOption['formated_default_price'] =
                        $this->_helperCatalog
                        ->stripTags(
                            $this->_priceFormat->currency(
                                $_option->getDefaultPrice()
                            )
                        );
                        $eachOption['unformated_price'] = $this->_priceFormat->currency($_option->getPrice(), false, false);
                        $eachOption['formated_price'] = $this->_helperCatalog
                        ->stripTags(
                            $this->_priceFormat->currency(
                                $_option->getPrice()
                            )
                        );
                        $optionValueCollection = $_option->getValues();
                        $eachOptionValue = [];
                        foreach ($optionValueCollection as $optionValue) {
                            $eachOptionValue[$optionValue->getId()] = $optionValue->getData();
                            $eachOptionValue[
                                $optionValue->getId()
                            ]['formated_price'] = $this->_helperCatalog
                            ->stripTags(
                                $this->_priceFormat->currency(
                                    $optionValue->getPrice()
                                )
                            );
                            $eachOptionValue[
                                $optionValue->getId()
                            ]['formated_default_price'] =
                            $this->_helperCatalog->stripTags(
                                $this->_priceFormat->currency(
                                    $optionValue->getDefaultPrice()
                                )
                            );
                        }
                        $eachOption['optionValues'] = $eachOptionValue;
                        $allOptions[] = $eachOption;
                    }
                }
                $returnArray['customOptions'] = $allOptions;

                // getting downloadable product data
                if ($_product->getTypeId() == 'downloadable') {
                    $linkArray = [];
                    $downloadableBlock = $this->_objectManager
                    ->create("\Magento\Downloadable\Block\Catalog\Product\Links");
                    $linkArray['title'] = $downloadableBlock->getLinksTitle();

                    $linkArray['linksPurchasedSeparately'] = $downloadableBlock->getLinksPurchasedSeparately();

                    $_links = $downloadableBlock->getLinks();

                    $linkData = [];
                    foreach ($_links as $_link) {
                        $eachLink = [];
                        $eachLink['id'] = $linkId = $_link->getId();
                        $eachLink['linkTitle'] = $_link->getTitle() ? $_link->getTitle() : '';
                        $eachLink['price'] = $this->_priceFormat->currency($_link->getPrice(), false, false);
                        $eachLink['formatedPrice'] = $this->_helperCatalog
                        ->stripTags(
                            $this->_priceFormat->currency(
                                $_link->getPrice()
                            )
                        );
                        if ($_link->getSampleFile() || $_link->getSampleUrl()) {
                            $link = $this->_objectManager
                            ->create("\Magento\Downloadable\Model\Link")
                            ->load($linkId);
                            if ($link->getId()) {
                                if ($link->getSampleType() == \Magento\Downloadable\Helper\Download::LINK_TYPE_URL) {
                                    $eachLink['url'] = $link->getSampleUrl();
                                    $fileArray = explode(DS, $link->getSampleUrl());
                                    $eachLink['fileName'] = end($fileArray);
                                } elseif ($link->getSampleType() ==
                                    \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE) {
                                    $sampleLinkFilePath = $this
                                    ->_objectManager
                                    ->create("\Magento\Downloadable\Helper\File")
                                    ->getFilePath(
                                        \Magento\Downloadable\Model\Link::getBaseSamplePath(),
                                        $link->getSampleFile()
                                    );
                                    $eachLink['url'] = $this->_helper
                                    ->getUrl(
                                        'chharohttp/download/downloadlinksample',
                                        ['linkId' => $linkId]
                                    );
                                    $fileArray = explode(DS, $sampleLinkFilePath);
                                    $eachLink['fileName'] = end($fileArray);
                                }
                            }
                            $eachLink['haveLinkSample'] = 1;
                            $eachLink['linkSampleTitle'] = __('sample');
                        }
                        $linkData[] = $eachLink;
                    }
                    $linkArray['linkData'] = $linkData;
                    $returnArray['links'] = $linkArray;
                    $linkSampleArray = [];

                    $downloadableSampleBlock = $this->_objectManager
                    ->create(
                        "\Magento\Downloadable\Block\Catalog\Product\Samples"
                    );
                    $linkSampleArray['hasSample'] = $downloadableSampleBlock->hasSamples();
                    $linkSampleArray['title'] = $downloadableSampleBlock->getSamplesTitle();
                    $_linkSamples = $downloadableSampleBlock->getSamples();
                    $linkSampleData = [];
                    foreach ($_linkSamples as $_linkSample) {
                        $eachSample = [];
                        $sampleId = $_linkSample->getId();
                        $eachSample['sampleTitle'] = $this->_helperCatalog
                        ->stripTags($_linkSample->getTitle());
                        $sample = $this->_objectManager
                            ->create("\Magento\Downloadable\Model\Sample")
                            ->load($sampleId);
                        if ($sample->getId()) {
                            if ($sample->getSampleType() ==
                                \Magento\Downloadable\Helper\Download::LINK_TYPE_URL
                            ) {
                                $eachSample['url'] = $sample->getSampleUrl();
                                $fileArray = explode(DS, $sample->getSampleUrl());
                                $eachSample['fileName'] = end($fileArray);
                            } elseif ($sample->getSampleType() ==
                                \Magento\Downloadable\Helper\Download::LINK_TYPE_FILE
                            ) {
                                $sampleFilePath = $this
                                    ->_objectManager
                                    ->create("\Magento\Downloadable\Helper\File")
                                    ->getFilePath(
                                        $sample->getBasePath(),
                                        $sample->getSampleFile()
                                    );
                                $eachSample['url'] = $this->_url
                                ->getUrl(
                                    'chharohttp/download/downloadsample',
                                    ['sampleId' => $sampleId]
                                );
                                $fileArray = explode(DS, $sampleFilePath);
                                $eachSample['fileName'] = end($fileArray);
                            }
                        }
                        $linkSampleData[] = $eachSample;
                    }
                    $linkSampleArray['linkSampleData'] = $linkSampleData;
                    $returnArray['samples'] = $linkSampleArray;
                }

                // getting grouped product data
                if ($_product->getTypeId() == 'grouped') {
                    $groupedParentId = $this->_objectManager
                    ->create(
                        "\Magento\GroupedProduct\Model\Product\Type\Grouped"
                    )
                    ->getParentIdsByChild($_product->getId());

                    $_associatedProducts = $_product->getTypeInstance(true)->getAssociatedProducts($_product);

                    $minPrice = [];
                    $groupedData = [];

                    foreach ($_associatedProducts as $_associatedProduct) {
                        $_associatedProduct = $this->_objectManager
                        ->create("\Magento\Catalog\Model\Product")
                        ->load($_associatedProduct->getId());
                        $eachAssociatedProduct = [];
                        $eachAssociatedProduct['name'] = $this->_helperCatalog
                        ->stripTags(
                            $_associatedProduct->getName()
                        );
                        $eachAssociatedProduct['id'] = $_associatedProduct->getId();
                        if ($_associatedProduct->isAvailable()) {
                            $eachAssociatedProduct['isAvailable'] = $_associatedProduct->isAvailable();
                        } else {
                            $eachAssociatedProduct['isAvailable'] = 0;
                        }
                        $fromdate = $_associatedProduct->getSpecialFromDate();
                        $todate = $_associatedProduct->getSpecialToDate();
                        $isInRange = 0;
                        if (isset($fromdate) && isset($todate)) {
                            $today = $this->_date->date('Y-m-d H:i:s');
                            $todayTime = $this->_date->timestamp($today);
                            $fromTime = $this->_date->timestamp($fromdate);
                            $toTime = $this->_date->timestamp($todate);
                            if ($todayTime >= $fromTime && $todayTime <= $toTime) {
                                $isInRange = 1;
                            }
                        }
                        if (isset($fromdate) && !isset($todate)) {
                            $today = $this->_date->date('Y-m-d H:i:s');
                            $todayTime = $this->_date->timestamp($today);
                            $fromTime = $this->_date->timestamp($fromdate);
                            if ($todayTime >= $fromTime) {
                                $isInRange = 1;
                            }
                        }
                        if (!isset($fromdate) && isset($todate)) {
                            $today = $this->_date->date('Y-m-d H:i:s');
                            $todayTime = $this->_date->timestamp($today);
                            $fromTime = $this->_date->timestamp($fromdate);
                            if ($todayTime <= $fromTime) {
                                $isInRange = 1;
                            }
                        }
                        $eachAssociatedProduct['isInRange'] = $isInRange;
                        $eachAssociatedProduct['specialPrice'] = $this
                        ->_helperCatalog->stripTags(
                            $this->_priceFormat->currency(
                                $_associatedProduct->getSpecialPrice()
                            )
                        );
                        $eachAssociatedProduct['foramtedPrice'] = $this
                        ->_helperCatalog->stripTags(
                            $this->_priceFormat->currency(
                                $_associatedProduct->getPrice()
                            )
                        );
                        $eachAssociatedProduct['thumbNail'] =
                        $this->_helperCatalog
                        ->getImageUrl($_associatedProduct, $width / 5);
                        $groupedData[] = $eachAssociatedProduct;
                    }
                    $returnArray['groupedData'] = $groupedData;
                }
            // getting bundle product options
                if ($_product->getTypeId() == 'bundle') {
                    $typeInstance = $_product->getTypeInstance(true);
                    $typeInstance->setStoreFilter($_product->getStoreId(), $_product);
                    $optionCollection = $typeInstance->getOptionsCollection($_product);
                    $selectionCollection = $typeInstance
                    ->getSelectionsCollection(
                        $typeInstance->getOptionsIds($_product),
                        $_product
                    );
                    $bundleOptionCollection = $optionCollection
                    ->appendSelections(
                        $selectionCollection,
                        false,
                        $this->_objectManager->create("\Magento\Catalog\Model\Product")
                        ->getSkipSaleableCheck()
                    );

                    $bundleOptions = [];
                    foreach ($bundleOptionCollection as $bundleOption) {
                        $_oneOption = [];
                        if (!$bundleOption->getSelections()) {
                            continue;
                        }
                        $_oneOption = $bundleOption->getData();
                        $_selections = $bundleOption->getSelections();
                        unset($_oneOption['selections']);
                        $bundleOptionValues = [];
                        foreach ($_selections as $_selection) {
                            $eachBundleOptionValues = [];
                            if ($_selection->isSaleable()) {
                                $coreHelper = $this->_priceFormat;
                                $taxHelper = $this->_objectManager
                                ->create('\Magento\Catalog\Helper\Data');
                                $price = $_product->getPriceModel()
                                ->getSelectionPreFinalPrice(
                                    $_product,
                                    $_selection,
                                    1
                                );
                                $priceTax = $taxHelper->getTaxPrice($_product, $price);
                                if ($_oneOption['type'] == 'checkbox' || $_oneOption['type'] == 'multi') {
                                    $eachBundleOptionValues['title'] = str_replace(
                                        '&nbsp;',
                                        ' ',
                                        $this
                                        ->_helperCatalog
                                        ->stripTags(
                                            $this->getSelectionQtyTitlePrice(
                                                $priceTax,
                                                $_selection,
                                                true
                                            )
                                        )
                                    );
                                }
                                if ($_oneOption['type'] == 'radio' || $_oneOption['type'] == 'select') {
                                    $eachBundleOptionValues['title'] = str_replace(
                                        '&nbsp;',
                                        ' ',
                                        $this->_helperCatalog->stripTags(
                                            $this->getSelectionTitlePrice(
                                                $priceTax,
                                                $_selection,
                                                false
                                            )
                                        )
                                    );
                                }
                                $eachBundleOptionValues['isQtyUserDefined'] = $_selection->getSelectionCanChangeQty();
                                $eachBundleOptionValues['isDefault'] = $_selection->getIsDefault();
                                $eachBundleOptionValues['optionValueId'] = $_selection->getSelectionId();
                                $eachBundleOptionValues['foramtedPrice'] =
                                $coreHelper->currencyByStore(
                                    $priceTax,
                                    $_product->getStore(),
                                    true,
                                    true
                                );
                                $eachBundleOptionValues['price'] = $coreHelper
                                ->currencyByStore(
                                    $priceTax,
                                    $_product->getStore(),
                                    false,
                                    false
                                );
                                $eachBundleOptionValues['isSingle'] =
                                (
                                    count($_selections) == 1 &&
                                    $bundleOption->getRequired()
                                );
                                $eachBundleOptionValues['defaultQty'] = $_selection->getSelectionQty();
                                $bundleOptionValues[$_selection->getId()] = $eachBundleOptionValues;
                            }
                        }
                        $_oneOption['optionValues'] = $bundleOptionValues;
                        $bundleOptions[] = $_oneOption;
                    }
                    $returnArray['bundleOptions'] = $bundleOptions;
                    $returnArray['priceView'] = $_product->getPriceView();
                }
            //getting bundle product options
                if ($_product->getTypeId() == 'configurable') {
                    $configurableBlock = $this->_objectManager
                    ->create(
                        "\Custom\Chharo\Block\Configurable"
                    );
                    $returnArray['configurableData'] = $configurableBlock->getJsonConfig();
                }
            // getting tier prices
                $allTierPrices = [];
                $tierBlock = $this->_objectManager
                ->create("\Magento\Catalog\Block\Product\Price");
                $_tierPrices = $tierBlock->getTierPrices();
                if ($_tierPrices && count($_tierPrices) > 0) {
                    foreach ($_tierPrices as $_index => $_price) {
                        $allTierPrices[] = __(
                            'Buy %1 for %2 each',
                            $_price['price_qty'],
                            $this->_helperCatalog->stripTags(
                                $_price['formated_price_incl_tax']
                            )
                        ).' '.__('and').' '.__('save').' '.$_price['savePercent'].'%';
                    }

                    $returnArray['tierPrices'] = $allTierPrices;
                }

                // seller data
                $helper = $this->_objectManager
                ->create("\Custom\Marketplace\Helper\Data");

                $collection = $this->_objectManager
                ->create("\Custom\Marketplace\Model\Product")
                ->getCollection()
                ->addFieldToFilter('mageproduct_id', [$productId]);
                $userid = '';
                $status = '';
                foreach ($collection as $record) {
                    $userid = $record->getSellerId();
                }
                $collection = $this->_objectManager
                ->create("\Custom\Marketplace\Model\Seller")
                ->getCollection()->addFieldToFilter('seller_id', $userid);
                $profileUrl = '';
                foreach ($collection as $record) {
                    $status = $record->getIsSeller();
                    $profileUrl = $record->getShopUrl();
                }
                if ($status != 1) {
                    $userid = '';
                }
                $productowner = [
                    'productid' => $productId,
                    'userid' => $userid,
                ];

                if ($productowner['userid'] != '') {
                    $captchenable = $this->_helper
                    ->getConfigData('marketplace/general_settings/captcha');

                    $rowsocial =
                    $helper->getSellerDataBySellerId($productowner['userid'])
                    ->getFirstItem()->getData();

                    $sellerId = $productowner['userid'];
                    $shoptitle = $rowsocial['shop_title'];

                    $seller = $this->_objectManager
                    ->get("\Magento\Customer\Model\Customer")
                    ->load($sellerId);

                    if (!$shoptitle) {
                        $shoptitle = $seller->getName();
                    }

                    $feeds = $helper->getFeedTotal($sellerId);

                    $returnArray['seller']['label'] = __('Sold By');
                    $returnArray['seller']['title'] = $shoptitle;
                    $returnArray['seller']['rating'] = $helper->getSelleRating($sellerId).' / 5';
                    $reviewPercentage = (($helper->getSelleRating($sellerId) * 100) / 5);
                    $returnArray['seller']['averageRatingTitle'] =
                    $reviewPercentage
                    .'% '.__('positive feedback')
                    .' ('.__('%1 ratings', number_format($feeds['feedcount']))
                    .') ';

                    $returnArray['seller']['averageRating'][] =
                    [
                        'label' => __('Price'),
                        'value' => round(
                            (
                                $feeds['price'] / 20
                            ),
                            1,
                            PHP_ROUND_HALF_UP
                        ),
                    ];

                    $returnArray['seller']['averageRating'][] =
                    [
                        'label' => __('Value'),
                        'value' => round(
                            (
                                $feeds['value'] / 20
                            ),
                            1,
                            PHP_ROUND_HALF_UP
                        ),
                    ];
                    $returnArray['seller']['averageRating'][] =
                    [
                        'label' => __('Quality'),
                        'value' => round(
                            (
                                $feeds['quality'] / 20
                            ),
                            1,
                            PHP_ROUND_HALF_UP
                        ),
                    ];
                    $returnArray['seller']['id'] = $sellerId;
                    $returnArray['seller']['profileUrl'] = $profileUrl;
                }

                if ($customerId != '') {
                    $quoteCollection = $this->_objectManager
                    ->create("\Magento\Quote\Model\Quote")->getCollection();
                    $quoteCollection->addFieldToFilter('customer_id', $customerId);
                    $quoteCollection->addOrder('updated_at', 'desc');
                    $quote = $quoteCollection->getFirstItem();
                }

                if ($quoteId != '') {
                    $quote = $this->_objectManager
                    ->create("\Magento\Quote\Model\Quote")
                    ->setStoreId($storeId)
                    ->load($quoteId);
                }

                $relatedProductCollection = $this->_objectManager
                ->create('\Magento\Catalog\Model\Product\Link')
                ->getCollection()
                ->addFieldToFilter(
                    'product_id',
                    $productId
                )->addFieldToFilter('link_type_id', '1');

                foreach ($relatedProductCollection as $_product) {
                    $_product = $this->_objectManager
                    ->create("\Magento\Catalog\Model\Product")
                    ->load($_product->getId());
                    $relatedProductData[] = $this->_helperCatalog
                    ->getOneProductRelevantData(
                        $_product,
                        $storeId,
                        $width
                    );
                }
                $returnArray['relatedProductData'] = $relatedProductData;
                if ($customerId != '' or $quoteId != '') {
                    $returnArray['cartCount'] = $quote->getItemsQty() * 1;
                }

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
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

    /**
     * Get title price for selection product.
     *
     * @param \Magento\Catalog\Model\Product $selection
     * @param bool                           $includeContainer
     *
     * @return string
     */
    public function getSelectionTitlePrice($amount, $selection, $includeContainer = true)
    {
        $priceTitle = '<span class="product-name">'.$this->_helperCatalog->escapeHtml($selection->getName()).'</span>';
        $priceTitle .= ' &nbsp; '.($includeContainer ? '<span class="price-notice">' : '').'+'
            .$this->_priceFormat->currency($amount).($includeContainer ? '</span>' : '');

        return $priceTitle;
    }

    /**
     * @param \Magento\Catalog\Model\Product $selection
     * @param bool                           $includeContainer
     *
     * @return string
     */
    public function getSelectionQtyTitlePrice($amount, $selection, $includeContainer = true)
    {
        $amount = $priceOption->getOptionSelectionAmount($selection);
        $priceTitle = '<span class="product-name">'.$selection->getSelectionQty() * 1 .' x '.$this->_helperCatalog->escapeHtml($selection->getName()).'</span>';

        $priceTitle .= ' &nbsp; '.($includeContainer ? '<span class="price-notice">' : '').'+'.
            $this->_priceFormat->currency($amount).($includeContainer ? '</span>' : '');

        return $priceTitle;
    }
}
