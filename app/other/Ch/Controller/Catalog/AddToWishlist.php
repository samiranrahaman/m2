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
class AddToWishlist extends \Custom\Chharo\Controller\ApiController
{

    /**
     * $_dir
     *
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
    
        $this->_dir = $dir;
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
            $customerId = $this->getRequest()->getPost("customerId");
            $storeId = $this->getRequest()->getPost("storeId");
            $productId = $this->getRequest()->getPost("productId");
            $returnArray = [];
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                try {
                    $wishlist = $this->_objectManager
                        ->create("\Magento\Wishlist\Model\Wishlist")
                        ->loadByCustomerId($customerId, true);

                    $product = $this->_objectManager
                        ->create("\Magento\Catalog\Model\Product")
                        ->load($productId);
                    $paramOptionsArray = [];

                    $options = $this->getRequest()->getPost("options");

                    if ($options != "") {
                        $paramOption = [];
                        $productOptions = json_decode($options);
                        foreach ($productOptions as $optionId => $values) {
                            $_option = $this->_objectManager
                                ->create("\Magento\Catalog\Model\Product\Option")
                                ->load($optionId);
                            $_optionType = $_option->getType();
                            if (in_array($_optionType, ["multiple", "checkbox"])) {
                                foreach ($values as $optionValue) {
                                    $paramOption[$optionId][] = $optionValue;
                                }
                            } elseif (in_array(
                                $_optionType,
                                ["radio", "drop_down", "area", "field"]
                            )
                            ) {
                                $paramOption[$optionId] = $values;
                            } elseif ($_optionType == "file") {
                                //downloading file
                                $base64String = $productOptions
                                    ->$optionId->encodeImage;
                                $fileName = time().$productOptions->$optionId->name;
                                $fileType = $productOptions->$optionId->type;
                                $fileWithPath = $this->_dir->getPath("media").$fileName;
                                $ifp = fopen($fileWithPath, "wb");

                                fwrite($ifp, base64_decode($base64String));
                                //assigning file to option
                                $fileOption = [
                                    "type" => $fileType,
                                    "title" => $fileName,
                                    "quote_path" => DS."media".DS.$fileName,
                                    "fullpath" => $fileWithPath,
                                    "secret_key" => substr(md5(file_get_contents($fileWithPath)), 0, 20)
                                ];
                                $filesToDelete[] = $fileWithPath;
                                $paramOption[$optionId] = $fileOption;
                            } elseif ($_optionType == "date") {
                                $paramOption[$optionId]["month"] = $values->month;
                                $paramOption[$optionId]["day"] = $values->day;
                                $paramOption[$optionId]["year"] = $values->year;
                            } elseif ($_optionType == "date_time") {
                                $paramOption[$optionId]["month"] = $values->month;
                                $paramOption[$optionId]["day"] = $values->day;
                                $paramOption[$optionId]["year"] = $values->year;
                                $paramOption[$optionId]["hour"] = $values->hour;
                                $paramOption[$optionId]["minute"] = $values->minute;
                                $paramOption[$optionId]["dayPart"] = $values->dayPart;
                            } elseif ($_optionType == "time") {
                                $paramOption[$optionId]["hour"] = $values->hour;
                                $paramOption[$optionId]["minute"] = $values->minute;
                                $paramOption[$optionId]["dayPart"] = $values->dayPart;
                            }
                        }
                        if (count($paramOption) > 0) {
                            $paramOptionsArray["options"] = $paramOption;
                        }
                    }
                    if ($product->getTypeId() == "downloadable") {
                        $links = $this->getRequest()->getPost("links");
                        if ($links != "") {
                            $links = json_decode($links);
                            $paramOptionsArray["links"] = $links;
                        }
                    } elseif ($product->getTypeId() == "grouped") {
                        $superGroup = $this->getRequest()->getPost("superGroup");
                        if ($superGroup != "") {
                            $superGroup = json_decode($superGroup);
                            $superGroupArray = [];
                            foreach ($superGroup as $key => $value) {
                                $superGroupArray[$key] = $value;
                            }
                            $paramOptionsArray["superGroup"] = $superGroupArray;
                        }
                    } elseif ($product->getTypeId() == "configurable") {
                        $superAttribute = $this->getRequest()->getPost("superAttribute");
                        if ($superAttribute != "") {
                            $superAttribute = json_decode($superAttribute);
                            $superAttributeArray = [];
                            foreach ($superAttribute as $key => $value) {
                                $superAttributeArray[$key] = $value;
                            }
                            $paramOptionsArray["superAttribute"] = $superAttributeArray;
                        }
                    } elseif ($product->getTypeId() == "bundle") {
                        $bundleOption = $this->getRequest()->getPost("bundleOption");
                        $bundleOption = json_decode($bundleOption);
                        $bundleOptionArray = [];
                        foreach ($bundleOption as $key => $value) {
                            $bundleOptionArray[$key] = $value;
                        }
                        $paramOptionsArray["bundleOption"] = $bundleOptionArray;

                        $bundleOptionQty = $this->getRequest()->getPost("bundleOptionQty");
                        $bundleOptionQty = json_decode($bundleOptionQty);
                        $bundleOptionQtyArray = [];
                        foreach ($bundleOptionQty as $key => $value) {
                            $bundleOptionQtyArray[$key] = $value;
                        }
                        $paramOptionsArray["bundleOptionQty"] = $bundleOptionQtyArray;
                    }
                    if (count($paramOptionsArray) > 0) {
                        $buyRequest = new \Magento\Framework\DataObject($paramOptionsArray);
                    } else {
                        $buyRequest = new \Magento\Framework\DataObject();
                    }
                    if (!$product->getId() || !$product->isVisibleInCatalog()) {
                        $returnArray["success"] = 0;
                        $returnArray["message"] = __("Cannot specify product.");
                        $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                        return $this->getJsonResponse($returnArray);
                    }
                    $result = $wishlist->addNewItem($product, $buyRequest);
                    if (is_string($result)) {
                        throw new \Magento\Framework\Exception\LocalizedException(__($result));
                    }

                    $wishlist->save();

                    $this->_eventManager->dispatch(
                        'wishlist_add_product',
                        ['wishlist' => $wishlist, 'product' => $product, 'item' => $result]
                    );

                    /**
                     * stop store emulation
                     */
                    $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                    return $this->getJsonResponse(["success" => 1]);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                    $returnArray["success"] = 0;
                    $returnArray["message"] =
                    __("An error occurred while adding item to wishlist: %1", $e->getMessage());
                    /**
                     * stop store emulation
                     */
                    $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                    return $this->getJsonResponse($returnArray);
                } catch (\Exception $e) {
                    $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                    $returnArray["success"] = 0;
                    $returnArray["message"] = __("An error occurred while adding item to wishlist.");
                    /**
                     * stop store emulation
                     */
                    $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                    return $this->getJsonResponse($returnArray);
                }
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
