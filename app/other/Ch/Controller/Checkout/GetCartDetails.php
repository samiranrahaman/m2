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

namespace Custom\Chharo\Controller\Checkout;

/**
 * Chharo API Checkout controller.
 */
class GetCartDetails extends AbstractCheckout
{


    /**
     * execute category list
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $storeId = $this->getRequest()->getPost("storeId");
            $width = $this->getRequest()->getPost("width");
            $customerId = $this->getRequest()->getPost("customerId");
            $quoteId = $this->getRequest()->getPost("quoteId");
            $returnArray = [];
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                $discountAmount = 0;
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                if ($customerId != "") {
                    $quoteCollection = $this->_quoteFactory
                        ->create()
                        ->getCollection();
                    $quoteCollection->addFieldToFilter("customer_id", $customerId);
                    $quoteCollection->addOrder("updated_at", "desc");
                    $quote = $quoteCollection->getFirstItem();
                }
               
                if ($quoteId != "") {
                    $quote = $this->_quoteFactory
                        ->create()
                        ->setStoreId(
                            $storeId
                        )->load($quoteId);
                }
                if ($customerId != "" || $quoteId != "") {
                    $quote->collectTotals()->save();
                    $itemCollection = $quote->getAllVisibleItems();
                    foreach ($itemCollection as $item) {
                       
                        $eachItem = [];
                        if ($item->getDiscountAmount()) {
                            $discountAmount += $item->getDiscountAmount();
                        }
                        $_product = $this->_productFactory
                            ->create()
                            ->load($item->getProductId());
                        $eachItem["image"] =
                        $this->_helperCatalog->getImageUrl(
                            $_product,
                            $width/2.5
                        );
                        $eachItem["name"] = $this->_helperCatalog
                            ->stripTags($item->getName());

                        $options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                        if ($item->getProduct()->getTypeId() == "configurable") {
                            $configurableOptions = $options["attributes_info"];
                            foreach ($configurableOptions as $configurableOption) {
                                $eachConfigurableOption = [];
                                $eachConfigurableOption["label"] = $configurableOption["label"];
                                $eachConfigurableOption["value"][] = $configurableOption["value"];
                                $eachItem["options"][] = $eachConfigurableOption;
                            }
                        }
                        if ($item->getProduct()->getTypeId() == "bundle") {
                            $bundleOptions = $options["bundle_options"];
                            foreach ($bundleOptions as $bundleOption) {
                                $eachBundleOption = [];
                                $eachBundleOption["label"] = $bundleOption["label"];
                                foreach ($bundleOption["value"] as $bundleOptionValue) {
                                    $price = 0;
                                    if ($bundleOptionValue["price"] > 0) {
                                        $price = $bundleOptionValue["price"]/$bundleOptionValue["qty"];
                                    }
                                    $price = $this->_helperCatalog
                                        ->stripTags($this->_priceHelper->currency($price));
                                    $eachBundleOptionValue =
                                    $bundleOptionValue["qty"]." x ".$bundleOptionValue["title"]." ".$price;
                                    $eachBundleOption["value"][] = $eachBundleOptionValue;
                                }
                                $eachItem["options"][] = $eachBundleOption;
                            }
                        }
                        if ($item->getProduct()->getTypeId() == "downloadable") {
                            $links = $this->_downloadableConfiguration
                                ->getLinks($item);
                            if (count($links) > 0) {
                                $downloadOption = [];
                                $titles = [];
                                foreach ($links as $linkId) {
                                    $titles[] = $linkId->getTitle();
                                }
                                $downloadOption["label"] = $this->_downloadableConfiguration
                                    ->getLinksTitle($item->getProduct());
                                $downloadOption["value"] = $titles;
                                $eachItem["options"][] = $downloadOption;
                            }
                        }
                        if (isset($options["options"])) {
                            $customOptions = $options["options"];
                            foreach ($customOptions as $customOption) {
                                $eachCustomOption = [];
                                $eachCustomOption["label"] = $customOption["label"];
                                $eachCustomOption["value"][] = $customOption["print_value"];
                                $eachItem["options"][] = $eachCustomOption;
                            }
                        }
                        $eachItem["sku"] = $this->_helperCatalog->stripTags(
                            $item->getSku()
                        );
                        $eachItem["price"] = $this->_helperCatalog
                            ->stripTags(
                                $this->_priceHelper->currency($item->getPrice())
                            );
                        $eachItem["qty"] = $item->getQty()*1;
                        $eachItem["productId"] = $item->getProductId();
                        $eachItem["typeId"] = $item->getProductType();
                        $eachItem["subTotal"] = $this->_helperCatalog
                            ->stripTags(
                                $this->_priceHelper->currency(
                                    $item->getRowTotal()
                                )
                            );
                        $eachItem["id"] = $item->getId();
                        $baseMessages = $item->getMessage(false);
                        if ($baseMessages) {
                            foreach ($baseMessages as $message) {
                                $messages = [];
                                $messages[] = [
                                    "text" => $message,
                                    "type" => $item->getHasError() ? "error" : "notice"
                                ];
                                $eachItem["messages"] = $messages;
                            }
                        }
                        $returnArray["items"][] = $eachItem;
                    }
                    $returnArray["couponCode"] = $quote->getCouponCode();
                    if ($customerId != "" || $quoteId != "") {
                        $returnArray["cartCount"] = $quote->getItemsQty()*1;
                    }
                    $returnArray['isVirtual'] =  ($quote->getIsVirtual() == 1)?"true":"false"; 

                    if ($quote->getItemsQty()*1 > 0) {
                        $totals = $quote->getTotals();
                        $this->createLog("Chharo cart totals: ".get_class($this)." : ", (array)$totals);
                        $subtotal = "";
                        $discount = "";
                        $grandtotal = "";
                        if (isset($totals["subtotal"])) {
                            $subtotal = $totals["subtotal"];
                            $returnArray["subtotal"]["title"] = $subtotal->getTitle();
                            $returnArray["subtotal"]["value"] = $this->_helperCatalog
                                ->stripTags(
                                    $this->_priceHelper->currency(
                                        $subtotal->getValue()
                                    )
                                );
                        }
                        if (isset($totals["discount"])) {
                            $discount = $totals["discount"];
                            $returnArray["discount"]["title"] = $discount->getTitle();
                            $returnArray["discount"]["value"] =
                            $this->_helperCatalog
                                ->stripTags(
                                    $this->_priceHelper->currency(
                                        $discount->getValue()
                                    )
                                );
                        } elseif($discountAmount > 0) {
                            $discount = $discountAmount;
                            $returnArray["discount"]["title"] = __('Discount');
                            $returnArray["discount"]["value"] =
                            $this->_helperCatalog
                                ->stripTags(
                                    $this->_priceHelper->currency(
                                        $discountAmount
                                    )
                                );
                        }
                        if (isset($totals["shipping"])) {
                            $shipping = $totals["shipping"];
                            $returnArray["shipping"]["title"] = $shipping->getTitle();
                            $returnArray["shipping"]["value"] =
                            $this->_helperCatalog->stripTags(
                                $this->_priceHelper->currency(
                                    $shipping->getValue()
                                )
                            );
                        }
                        if (isset($totals["tax"])) {
                            $tax = $totals["tax"];
                            $returnArray["tax"]["title"] = $tax->getTitle();
                            $returnArray["tax"]["value"] =
                            $this->_helperCatalog->stripTags(
                                $this->_priceHelper->currency(
                                    $tax->getValue()
                                )
                            );
                        }
                        if (isset($totals["grand_total"])) {
                            $grandtotal = $totals["grand_total"];
                            $returnArray["grandtotal"]["title"] = $grandtotal->getTitle();
                            $returnArray["grandtotal"]["value"] =
                            $this->_helperCatalog->stripTags(
                                $this->_priceHelper->currency(
                                    $grandtotal->getValue()
                                )
                            );
                        }
                    }
                }
               
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
}
