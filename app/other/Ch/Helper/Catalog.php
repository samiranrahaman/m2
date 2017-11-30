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

namespace Custom\Chharo\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Custom Chharo Helper Catalog.
 */
class Catalog extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_sessionManager;

    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * $_storeManager.
     *
     * @var \Magento\Store\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * $_objectManager.
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * $_priceFormat.
     *
     * @var string
     */
    protected $_priceFormat;

    /**
     * $coreString.
     *
     * @var
     */
    public $coreString;

    /**
     * $_dateTime.
     *
     * @var Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * $directory.
     *
     * @var DirectoryList
     */
    public $directory;

    /**
     * $_imageFactory.
     *
     * @var Magento\Framework\Image\Factory
     */
    protected $_imageFactory;

    /**
     * __construct.
     *
     * @param \Magento\Framework\App\Helper\Context              $context
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\ObjectManagerInterface          $objectManager
     * @param DateTime                                           $date
     * @param \Magento\Framework\Filesystem\DirectoryList        $dir
     * @param \Magento\Framework\Image\Factory                   $imageFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        DateTime $date,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Image\Factory $imageFactory
    ) {
        $this->_sessionManager = $sessionManager;
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        $this->_date = $date;
        $this->directory = $dir;
        $this->_imageFactory = $imageFactory;
        parent::__construct($context);

        $this->_priceFormat = $this->_objectManager
            ->create('\Magento\Framework\Pricing\Helper\Data');

        $this->_storeSwitcher = $this->_objectManager
            ->create("\Magento\Store\Block\Switcher");

        $this->coreString = $this->_objectManager
            ->create('\Magento\Framework\Stdlib\StringUtils');

        $this->_dateTime = $this->_objectManager
            ->create("\Magento\Framework\Stdlib\DateTime");
    }

    /**
     * getCurrentStore get current store id.
     *
     * @return int
     */
    public function getCurrentStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }

    /**
     * getAttributeInputType.
     *
     * @param object $attribute
     *
     * @return string input type
     */
    public function getAttributeInputType($attribute)
    {
        $dataType = $attribute->getBackend()->getType();
        $inputType = $attribute->getFrontend()->getInputType();
        if ($inputType == 'select' || $inputType == 'multiselect') {
            return 'select';
        } elseif ($inputType == 'boolean') {
            return 'yesno';
        } elseif ($inputType == 'price') {
            return 'price';
        } elseif ($dataType == 'int' || $dataType == 'decimal') {
            return 'number';
        } elseif ($dataType == 'datetime') {
            return 'date';
        } else {
            return 'string';
        }
    }

    public function _renderRangeLabel($fromPrice, $toPrice, $storeId)
    {
        $formattedFromPrice = $this->stripTags($this->_priceFormat->currency($fromPrice));
        if ($toPrice === '') {
            return __('%1 and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice 
            && $this->scopeConfig->getValue(
                'catalog/layered_navigation/one_price_interval',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        ) {
            return $formattedFromPrice;
        } else {
            if ($fromPrice != $toPrice) {
                $toPrice -= .01;
            }

            return __('%1 - %2', $formattedFromPrice, $this->stripTags($this->_priceFormat->currency($toPrice)));
        }
    }

    /**
     * getPriceFilter get price filter data.
     *
     * @param object $priceFilterModel
     * @param int    $storeId
     *
     * @return array
     */
    public function getPriceFilter($priceFilterModel, $storeId)
    {
        if ($this->getPriceRangeCalculation() == 'improved'
        ) {
            $algorithmModel = $this->_objectManager->create("\Magento\Catalog\Model\Layer\Filter\Price\Algorithm");
            $collection = $priceFilterModel->getLayer()->getProductCollection();
            $appliedInterval = $priceFilterModel->getInterval();
            if ($appliedInterval && $collection->getPricesCount() <= $priceFilterModel->getIntervalDivisionLimit()) {
                return [];
            }
            $algorithmModel->setPricesModel($priceFilterModel)->setStatistics(
                $collection->getMinPrice(),
                $collection->getMaxPrice(),
                $collection->getPriceStandardDeviation(),
                $collection->getPricesCount()
            );
            if ($appliedInterval) {
                if ($appliedInterval[0] == $appliedInterval[1] || $appliedInterval[1] === '0') {
                    return [];
                }
                $algorithmModel->setLimits($appliedInterval[0], $appliedInterval[1]);
            }
            $items = [];
            foreach ($algorithmModel->calculateSeparators() as $separator) {
                $items[] = [
                        'label' => $this
                            ->stripTags(
                                $this->_renderRangeLabel(
                                    $separator['from'],
                                    $separator['to'],
                                    $storeId
                                )
                            ),
                    'id' => (
                        (
                            $separator['from'] == 0) ? '' : $separator['from']
                        ).'-'.$separator['to'].$priceFilterModel->_getAdditionalRequestData(),
                    'count' => $separator['count'],
                ];
            }
        } elseif ($priceFilterModel->getInterval()) {
            return [];
        }
        $range = $priceFilterModel->getPriceRange();
        $dbRanges = $priceFilterModel->getRangeItemCounts($range);
        $data = [];
        if (!empty($dbRanges)) {
            $lastIndex = array_keys($dbRanges);
            $lastIndex = $lastIndex[count($lastIndex) - 1];
            foreach ($dbRanges as $index => $count) {
                $fromPrice = ($index == 1) ? 0 : (($index - 1) * $range);
                $toPrice = ($index == $lastIndex) ? 0 : ($index * $range);
                $data[] = [
                    'label' => $this->stripTags(
                        $this->_renderRangeLabel(
                            $fromPrice,
                            $toPrice,
                            $storeId
                        )
                    ),
                    'id' => $fromPrice.'-'.$toPrice,
                    'count' => $count,
                ];
            }
        }

        return $data;
    }

    /**
     * getAttributeFilter get filter attributes array.
     *
     * @param object $attributeFilterModel
     * @param object $_filter
     *
     * @return array
     */
    public function getAttributeFilter($attributeFilterModel, $_filter)
    {
        $options = $_filter->getFrontend()->getSelectOptions();
        $optionsCount = $this->_objectManager
            ->create('\Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute')
            ->getCount($attributeFilterModel);
        $data = [];
        foreach ($options as $option) {
            if (is_array($option['value'])) {
                continue;
            }
            if ($this->coreString->strlen($option['value'])) {
                if ($_filter->getIsFilterable() == 1) {
                    if (!empty($optionsCount[$option['value']])) {
                        $data[] = [
                            'label' => $option['label'],
                            'id' => $option['value'],
                            'count' => $optionsCount[$option['value']],
                        ];
                    }
                } else {
                    $data[] = [
                        'label' => $option['label'],
                        'id' => $option['value'],
                        'count' => isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0,
                    ];
                }
            }
        }

        return $data;
    }

    /**
     * getQueryArray.
     *
     * @param array $queryStringArray
     *
     * @return array
     */
    public function getQueryArray($queryStringArray)
    {
        $queryArray = [];
        foreach ($queryStringArray as $each) {
            if ($each->inputType == 'string' || $each->inputType == 'yesno') {
                if ($each->value != '') {
                    if (is_object($each->value)) {
                        $selectedArray = [];
                        foreach ($each->value as $key => $value) {
                            if ($value == 'true') {
                                $selectedArray[] = $key;
                            }
                        }
                        if (count($selectedArray) > 0) {
                            $queryArray[$each->code] = $selectedArray;
                        }
                    } else {
                        $queryArray[$each->code] = $each->value;
                    }
                }
            } elseif ($each->inputType == 'price' || $each->inputType == 'date') {
                $valueArray = $each->value;
                if ($valueArray->from != '' && $valueArray->to != '') {
                    $queryArray[$each->code] = ['from' => $valueArray->from,'to' => $valueArray->to];
                }
            } elseif ($each->inputType == 'select') {
                if ($each->value == '') {
                    continue;
                }
                $valueArray = $each->value;

                $selectedArray = [];
                if (!is_object($valueArray)) {
                    $queryArray[$each->code] = $each->value;
                } else {
                    foreach ($valueArray as $key => $value) {
                        if ($value == 'true') {
                            $selectedArray[] = $key;
                        }
                    }
                    if (count($selectedArray) > 0) {
                        $queryArray[$each->code] = $selectedArray;
                    }
                }
            }
        }

        return $queryArray;
    }

    /**
     * getOneProductRelevantData get single product data.
     *
     * @param \Magento\Catalog\Model\Product $_product
     * @param int                            $storeId
     * @param float                          $width
     *
     * @return array
     */
    public function getOneProductRelevantData($_product, $storeId, $width)
    {
        $reviews = $this->_objectManager->create("\Magento\Review\Model\Review")
            ->getResourceCollection()
            ->addStoreFilter($storeId)
            ->addEntityFilter(
                'product',
                $_product->getId()
            )->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
            ->setDateOrder()->addRateVotes();
        $ratings = [];
        if (count($reviews) > 0) {
            foreach ($reviews->getItems() as $review) {
                foreach ($review->getRatingVotes() as $vote) {
                    $ratings[] = $vote->getPercent();
                }
            }
        }
        $eachProduct = [];
        $eachProduct['entityId'] = $_product->getId();
        $eachProduct['sku'] = $_product->getSku();
        $eachProduct['typeId'] = $_product->getTypeId();

        if ($_product->getTypeId() == 'downloadable') {
            $eachProduct['linksPurchasedSeparately'] = $_product->getLinksPurchasedSeparately();
        }

        if ($_product->getTypeId() == 'bundle') {
            $eachProduct['priceView'] = $_product->getPriceView();
            $selectionCollection = $_product
                ->getTypeInstance(true)
                ->getSelectionsCollection(
                    $_product->getTypeInstance(true)->getOptionsIds($_product),
                    $_product
                );
            $bundledPrices = [];
            foreach ($selectionCollection as $option) {
                $bundledPrices[] = $option->getPrice();
            }
            sort($bundledPrices);
            $minPrice = $bundledPrices[0];
            $maxPriceTmp = array_slice($bundledPrices, -1, 1, false);
            $maxPrice = $maxPriceTmp[0];
            $eachProduct['formatedMinPrice'] = $this->stripTags($this->_priceFormat->currency($minPrice));
            $eachProduct['minPrice'] = $minPrice;
            $eachProduct['formatedMaxPrice'] = $this->stripTags($this->_priceFormat->currency($maxPrice));
            $eachProduct['maxPrice'] = $maxPrice;
        }
        $tierPrice = $_product->getTierPrice();
        if (count($tierPrice) > 0) {
            $tierPrices = [];
            foreach ($tierPrice as $value) {
                $tierPrices[] = $value['price'];
            }
            sort($tierPrices);
            $eachProduct['tierPrice'] = $this->stripTags($this->_priceFormat->currency($tierPrices[0]));
            $eachProduct['hasTierPrice'] = 'true';
        } else {
            $eachProduct['hasTierPrice'] = 'false';
        }
        $eachProduct['shortDescription'] = $this->stripTags($_product->getShortDescription());
        $eachProduct['formatedPrice'] = $this->stripTags($this->_priceFormat->currency($_product->getPrice()));
        $eachProduct['price'] = $_product->getPrice();
        if (count($ratings) > 0) {
            $rating = number_format((5 * (array_sum($ratings) / count($ratings))) / 100, 2, '.', '');
        } else {
            $rating = 0;
        }
        if ($_product->isAvailable()) {
            $eachProduct['isAvailable'] = 1;
        } else {
            $eachProduct['isAvailable'] = 0;
        }
        $eachProduct['rating'] = $rating;
        $eachProduct['formatedFinalPrice'] = $this->stripTags(
            $this->_priceFormat->currency(
                $_product->getFinalPrice()
            )
        );
        $eachProduct['finalPrice'] = $_product->getFinalPrice();
        $eachProduct['hasOptions'] = $_product->getHasOptions();
        $eachProduct['requiredOptions'] = $_product->getRequiredOptions();
        $returnArray['msrpEnabled'] = $_product->getMsrpEnabled();
        $returnArray['msrpDisplayActualPriceType'] = $_product->getMsrpDisplayActualPriceType();
        $eachProduct['name'] = $_product->getName();
        $eachProduct['formatedSpecialPrice'] = $this->stripTags(
            $this->_priceFormat->currency(
                $_product->getSpecialPrice()
            )
        );
        $eachProduct['specialPrice'] = $_product->getSpecialPrice();
        if ($_product->getTypeId() == 'grouped') {
            $eachProduct['requiredOptions'] = 1;
            $groupedParentId = $this->_objectManager
                ->create('\Magento\GroupedProduct\Model\Product\Type\Grouped')
                ->getParentIdsByChild($_product->getId());
            $_associatedProducts = $_product->getTypeInstance(true)->getAssociatedProducts($_product);
            $minPrice = [];
            foreach ($_associatedProducts as $_associatedProduct) {
                if ($ogPrice = $_associatedProduct->getPrice()) {
                    $minPrice[] = $ogPrice;
                }
            }
            $eachProduct['groupedPrice'] = $this->stripTags($this->_priceFormat->currency(min($minPrice)));
        }
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
        if (!isset($fromdate) && isset($todate)) {
            $today = $this->_date->date('Y-m-d H:i:s');
            $todayTime = $this->_date->timestamp($today);
            $fromTime = $this->_date->timestamp($fromdate);
            if ($todayTime <= $fromTime) {
                $isInRange = 1;
            }
        }
        $eachProduct['isInRange'] = $isInRange;
        $eachProduct['thumbNail'] = $this->getImageUrl($_product, $width / 2.5);

        return $eachProduct;
    }

    /**
     * getStoreData get store data.
     *
     * @return array
     */
    public function getStoreData()
    {
        $storeData = [];
        $storeBlock = $this->_storeSwitcher;
        foreach ($storeBlock->getGroups() as $group) {
            $groupArr = [];
            $groupArr['id'] = $group->getGroupId();
            $groupArr['name'] = $group->getName();
            $stores = $group->getStores();
            foreach ($stores as $store) {
                if (!$store->isActive()) {
                    continue;
                }
                $storeArr = [];
                $storeArr['id'] = $store->getStoreId();
                $code = explode(
                    '_',
                    $this->getLocaleCodes(
                        $store->getId()
                    )
                );
                $storeArr['code'] = $code[0];
                $storeArr['name'] = $store->getName();
                $groupArr['stores'][] = $storeArr;
            }
            $storeData[] = $groupArr;
        }

        return $storeData;
    }

    /**
     * stripTags strip tags in string.
     *
     * @param string $data
     *
     * @return string
     */
    public function stripTags($data)
    {
        return strip_tags($data);
    }

    /**
     * getLocaleCodes get store wise locale code.
     *
     * @param int $store
     *
     * @return
     */
    public function getLocaleCodes($store)
    {
        return $this->scopeConfig->getValue(
            'general/locale/code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * showOutOfStock out of stock products on frontend.
     *
     * @return bool
     */
    public function showOutOfStock()
    {
        return $this->scopeConfig->getValue(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getPriceRangeCalculation price range calculation.
     *
     * @return bool
     */
    public function getPriceRangeCalculation()
    {
        return $this->scopeConfig->getValue(
            'catalog/layered_navigation/price_range_calculation',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * getMaxQueryLength max search query length.
     *
     * @return int
     */
    public function getMaxQueryLength()
    {
        return $this->scopeConfig->getValue(
            \Magento\Search\Model\Query::XML_PATH_MAX_QUERY_LENGTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * formatDate format date.
     *
     * @param string $date   date
     * @param string $format long|short
     *
     * @return string
     */
    public function formatDate($date, $format = null)
    {
        if ($format != null) {
            return $this->_dateTime->formatDate($date, $format);
        } else {
            return $this->_dateTime->formatDate($date);
        }
    }

    /**
     * escapeHtml escape html.
     *
     * @param string $text html text
     *
     * @return string
     */
    public function escapeHtml($text)
    {
        return $this->_objectManager->create("\Magento\Framework\Escaper")
            ->escapeHtml($text);
    }

    /**
     * getBasePath get directory path.
     *
     * @param string $folder
     *
     * @return string directory path
     */
    public function getBasePath($folder = 'media')
    {
        return $this->directory->getPath($folder);
    }

    /**
     * getImageUrl get Product Image.
     *
     * @param Magento\Catalog\Model\product $_product
     * @param float                         $resize
     * @param string                        $imageType
     * @param bool                          $keepFrame
     *
     * @return string
     */
    public function getImageUrl(
        $_product,
        $resize,
        $imageType = 'product_page_image_large',
        $keepFrame = true
    ) {
        return $this->_objectManager
            ->create('\Magento\Catalog\Helper\Image')
            ->init($_product, $imageType)
            ->keepFrame($keepFrame)
            ->resize($resize)
            ->getUrl();
    }

    /**
     * uploadPicture user profile picture upload.
     *
     * @param []     $files
     * @param int    $customerId
     * @param string $name
     * @param string $signal     Benner|Profile
     */
    public function uploadPicture($files, $customerId, $name, $signal)
    {
        $target = $this->getBasePath('media')
        .DS.'chharo'
        .DS.'customerpicture'
        .DS.$customerId.DS;
        if (isset($files) && count($files) > 0) {
            $file = $this->_objectManager->create("\Magento\Framework\Filesystem\Io\File");
            $file->mkdir($target);
            foreach ($files as $image) {
                if ($image['tmp_name'] != '') {
                    $splitname = explode('.', $image['name']);

                    $finalTarget = $target.$name.'.'.end($splitname);

                    move_uploaded_file($image['tmp_name'], $finalTarget);

                    $userImageModel = $this->_objectManager
                        ->create("\Custom\Chharo\Model\UserImageFactory")->create();

                    $collection = $userImageModel->getCollection()->addFieldToFilter('customer_id', $customerId);

                    if ($collection->getSize() > 0) {
                        foreach ($collection as $value) {
                            $loadedUserImageModel = $userImageModel->load($value->getId());
                            if ($signal == 'banner') {
                                $loadedUserImageModel->setBanner($name.'.'.end($splitname));
                            }
                            if ($signal == 'profile') {
                                $loadedUserImageModel->setProfile($name.'.'.end($splitname));
                            }
                            $loadedUserImageModel->save();
                        }
                    } else {
                        if ($signal == 'banner') {
                            $userImageModel->setBanner($name.'.'.end($splitname));
                        }
                        if ($signal == 'profile') {
                            $userImageModel->setProfile($name.'.'.end($splitname));
                        }
                        $userImageModel->setCustomerId($customerId)->save();
                    }
                }
            }
        }
    }

    /**
     * resizeAndCache.
     *
     * @param int $width
     * @param int $customerId
     */
    public function resizeAndCache($customerId, $width = 0)
    {
        if ($width > 0) {
            $height = $width / 2;
            $collection = $this->_objectManager
                ->create("\Custom\Chharo\Model\UserImage")
                ->getCollection()
                ->addFieldToFilter('customer_id', $customerId);

            if ($collection->getSize() > 0) {
                foreach ($collection as $value) {
                    if ($value->getBanner() != '') {
                        $basePath =
                        $this->getBasePath('media')
                        .DS.'chharo'
                        .DS.'customerpicture'
                        .DS.$customerId
                        .DS.$value->getBanner();
                        if (file_exists($basePath)) {
                            $newPath =
                            $this->getBasePath('media')
                            .DS.'chharo'
                            .DS.'customerpicture'
                            .DS.$customerId
                            .DS.$width.'x'.$height
                            .DS.$value->getBanner();
                            $this->imageUpload($basePath, $newPath, $width, $height);
                        }
                    }
                    if ($value->getProfile() != '') {
                        $basePath =
                        $this->getBasePath('media')
                        .DS.'chharo'
                        .DS.'customerpicture'
                        .DS.$customerId
                        .DS.$value->getProfile();
                        if (file_exists($basePath)) {
                            $newPath =
                            $this->getBasePath('media')
                            .DS.'chharo'
                            .DS.'customerpicture'
                            .DS.$customerId
                            .DS.'100x100'
                            .DS.$value->getProfile();

                            $this->imageUpload($basePath, $newPath, 70, 70);
                        }
                    }
                }
            }
        }
    }

    /**
     * imageUpload general image upload function.
     *
     * @param string $basePath
     * @param string $newPath
     * @param int    $width
     * @param int    $height
     */
    public function imageUpload($basePath, $newPath, $width, $height)
    {
        $imageObj = $this->_imageFactory->create($basePath);
        $imageObj->keepAspectRatio(false);
        $imageObj->backgroundColor([255, 255, 255]);
        $imageObj->keepFrame(false);
        $imageObj->resize($width, $height);
        $imageObj->save($newPath);
    }
}
