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

namespace Custom\Chharo\Controller\Checkout;

/**
 * Chharo API Checkout controller.
 */
class AddToCart extends AbstractCheckout
{
    /**
     * execute add to cart.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $quoteId = 0;
            $productId = $this->getRequest()->getPost('productId');
            $customerId = $this->getRequest()->getPost('customerId');
            $quoteId = $this->getRequest()->getPost('quoteId');
            $storeId = $this->getRequest()->getPost('storeId');
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $returnArray = [];
                $returnArray['error'] = 0;
                $returnArray['message'] = '';

                if ($customerId == '' && $quoteId == '') {
                    $quote = $this->_quoteFactory->create()
                        ->setStoreId($storeId)
                        ->setIsActive(true)
                        ->setIsMultiShipping(false)
                        ->save();
                    $quote->getBillingAddress();
                    $quote->getShippingAddress()->setCollectShippingRates(true);
                    $quote->collectTotals()->save();
                    $quoteId = (int) $quote->getId();
                    $returnArray['quoteId'] = $quoteId;
                }
                $qty = $this->getRequest()->getPost('qty');
                if ($qty == '') {
                    $qty = 1;
                }
                if ($customerId != '') {
                    $quoteCollection = $this->_quoteFactory->create()->getCollection();
                    $quoteCollection->addFieldToFilter('customer_id', $customerId);
                    $quoteCollection->addOrder('updated_at', 'desc');
                    $quote = $quoteCollection->getFirstItem();
                    $quoteId = $quote->getId();
                    if ($quote->getId() < 0 || !$quoteId) {
                        $quote = $this->_quoteFactory->create()
                            ->setStoreId($storeId)
                            ->setIsActive(true)
                            ->setIsMultiShipping(false)
                            ->save();

                        $quoteId = (int) $quote->getId();
                        $customer = $this->_customerRepository
                            ->getById($customerId);

                        $quote->assignCustomer($customer);
                        $quote->setCustomer($customer);
                        $quote->getBillingAddress();
                        $quote->getShippingAddress()->setCollectShippingRates(true);
                        $quote->collectTotals()->save();
                    }
                } else {
                    $quote = $this->_quoteFactory->create()
                        ->setStoreId($storeId)->load($quoteId);
                }

                $product = $this->_productFactory->create()
                    ->setStoreId($storeId)->load($productId);
                if ($qty && !($product->getTypeId() == 'downloadable')) {
                    $availableQty = $this->_stockRegistry
                        ->getStockItem($product->getId())
                        ->getQty();

                    if ($qty <= $availableQty) {
                        $filter = new \Magento\Framework\Filter\LocalizedToNormalized(
                            [
                                'locale' => $this->_objectManager
                                    ->get("\Magento\Framework\Locale\Resolver")
                                    ->getLocale(),
                            ]
                        );
                        $qty = $filter->filter($qty);
                    } else {
                        if (!in_array(
                            $product->getTypeId(),
                            ['grouped', 'configurable', 'bundle']
                        )
                        ) {
                            $returnArray['error'] = 1;
                            $returnArray['message'] =
                            __('The requested quantity is not available');

                            return $this->getJsonResponse($returnArray);
                        }
                    }
                }

                $filesToDelete = [];
                $paramOption = [];
                $params = $this->getRequest()->getPost('params');
                if ($params != '') {
                    $params = json_decode($params);
                }
                if (isset($params->options)) {
                    $productOptions = $params->options;
                    foreach ($productOptions as $optionId => $values) {
                        $_option = $this->_objectManager
                            ->create("\Magento\Catalog\Model\Product\Option")
                            ->load($optionId);

                        $_optionType = $_option->getType();
                        if (in_array(
                            $_optionType,
                            ['multiple', 'checkbox']
                        )
                        ) {
                            foreach ($values as $optionValue) {
                                $paramOption[$optionId][] = $optionValue;
                            }
                        } elseif (in_array(
                            $_optionType,
                            ['radio', 'drop_down', 'area', 'field']
                        )
                        ) {
                            $paramOption[$optionId] = $values;
                        } elseif ($_optionType == 'file') {
                            //downloading file
                            $base64String = $productOptions
                                ->$optionId->encodeImage;
                            $fileName = time().$productOptions
                                ->$optionId->name;
                            $fileType = $productOptions->$optionId->type;
                            $fileWithPath = $this->_helperCatalog
                                ->getBasePath()
                            .DS.$fileName;
                            $ifp = fopen($fileWithPath, 'wb');
                            fwrite($ifp, base64_decode($base64String));
                            //assigning file to option
                            $fileOption = [
                                'type' => $fileType,
                                'title' => $fileName,
                                'quote_path' => DS.'media'.DS.$fileName,
                                'fullpath' => $fileWithPath,
                                'secret_key' => substr(md5(file_get_contents($fileWithPath)), 0, 20),
                            ];
                            $filesToDelete[] = $fileWithPath;
                            $paramOption[$optionId] = $fileOption;
                        } elseif ($_optionType == 'date') {
                            $paramOption[$optionId]['month'] = $values->month;
                            $paramOption[$optionId]['day'] = $values->day;
                            $paramOption[$optionId]['year'] = $values->year;
                        } elseif ($_optionType == 'date_time') {
                            $paramOption[$optionId]['month'] = $values->month;
                            $paramOption[$optionId]['day'] = $values->day;
                            $paramOption[$optionId]['year'] = $values->year;
                            $paramOption[$optionId]['hour'] = $values->hour;
                            $paramOption[$optionId]['minute'] = $values->minute;
                            $paramOption[$optionId]['dayPart'] = $values->dayPart;
                        } elseif ($_optionType == 'time') {
                            $paramOption[$optionId]['hour'] = $values->hour;
                            $paramOption[$optionId]['minute'] = $values->minute;
                            $paramOption[$optionId]['dayPart'] = $values->dayPart;
                        }
                    }
                }
                if ($product->getTypeId() == 'downloadable') {
                    $links = [];
                    if (isset($params) && $params->links) {
                        foreach ($params->links as $key => $value) {
                            $links[] = $params->links->$key;
                        }
                        $params = [
                            'related_product' => null,
                            'links' => $links,
                            'options' => $paramOption,
                            'qty' => $qty,
                            'product' => $productId,
                        ];
                    } else {
                        $params = [
                        'related_product' => null,
                        'options' => $paramOption,
                        'qty' => $qty,
                        'product' => $productId,
                        ];
                    }
                } elseif ($product->getTypeId() == 'grouped') {
                    $superGroup = [];
                    foreach ($params->super_group as $key => $value) {
                        $superGroup[$key] = intval($params->super_group->$key);
                    }
                    $params = [
                        'related_product' => null,
                        'super_group' => $superGroup,
                        'product' => $productId,
                    ];
                } elseif ($product->getTypeId() == 'configurable') {
                    $superAttribute = [];
                    foreach ($params->super_attribute as $key => $value) {
                        $superAttribute[$key] = $params->super_attribute->$key;
                    }
                    $params = [
                        'related_product' => null,
                        'super_attribute' => $superAttribute,
                        'options' => $paramOption,
                        'qty' => $qty,
                        'product' => $productId,
                    ];
                } elseif ($product->getTypeId() == 'bundle') {
                    $this->_coreRegistry->register('product', $product);

                    $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
                        $product->getTypeInstance(true)->getOptionsIds($product),
                        $product
                    );
                 
                    foreach ($selectionCollection as $option) {

                        $selectionQty = $option->getSelectionQty() * 1;
                        $key = $option->getOptionId();
                        if (isset($params->bundle_option_qty->$key)) {
                            $probablyRequestedQty = $params->bundle_option_qty->$key;
                        }
                        if ($selectionQty > 1) {
                            $requestedQty = $selectionQty * $qty;
                        } elseif (isset($probablyRequestedQty)) {
                            $requestedQty = $probablyRequestedQty * $qty;
                        } else {
                            $requestedQty = 1;
                        }
                        $associateBundleProduct =
                        $this->_productFactory
                            ->create()
                            ->load($option->getProductId());

                        $availableQty = $this->_stockRegistry->getStockItem(
                            $associateBundleProduct->getId()
                        )->getQty();

                        if ($associateBundleProduct->getIsSalable()) {
                            if ($requestedQty > $availableQty) {
                                $returnArray['error'] = 1;
                                $returnArray['message'] = __('The requested quantity of ')
                                .$option->getName().
                                __(' is not available');

                                return $this->getJsonResponse($returnArray);
                            }
                        }
                    }
                    $bundleOption = [];
                    if ($params->bundle_option) {
                        foreach ($params->bundle_option as $key => $value) {
                            $bundleOption[$key] = $params->bundle_option->$key;
                        }
                    }
                    $bundleOptionQty = [];
                    if ($params->bundle_option_qty) {
                        foreach ($params->bundle_option_qty as $key => $value) {
                            $bundleOptionQty[$key] = intval($params->bundle_option_qty->$key);
                        }
                    }
                    $params = [
                        'related_product' => null,
                        'bundle_option' => $bundleOption,
                        'bundle_option_qty' => $bundleOptionQty,
                        'options' => $paramOption,
                        'qty' => $qty,
                        'product' => $productId,
                    ];
                } else {
                    $params = [
                        'related_product' => null,
                        'options' => $paramOption,
                        'qty' => $qty,
                        'product' => $productId,
                    ];
                }
                $params['quote_id'] = $quoteId;
                $params = new \Magento\Framework\DataObject($params);
                $quote = $this->_quoteFactory
                    ->create()
                    ->setStoreId($storeId)->load($quoteId);
                $allItems = $quote->getAllVisibleItems();
                $cartItemId = 0;
                foreach ($allItems as $item) {
                    if($item->getProductId() == $productId) {
                        $cartItemId = $item->getItemId();
                    }
                }
                $returnArray['cart_item_id'] = $cartItemId;
               // if (!$cartItemId) {
                    $productAdded = $this->_cartFactory->create()->setQuote($quote)->addProduct(
                        $product,
                        $params
                    )->save();
                // } else {
                //     $quoteItem = $quote->getItemById($cartItemId);
                //     $returnArray['item_id'] = $cartItemId;
                //     if (!$quoteItem) {
                //         $returnArray['error'] = 1;
                //         $returnArray['message'] = __('Unable to add product to cart.');

                //         return $this->getJsonResponse($returnArray);
                //     }
                //     $qty = $quoteItem->getQty()+$qty;
                //     $product = $quoteItem->getProduct();
                //     if (!$product) {
                //         $returnArray['error'] = 1;
                //         $returnArray['message'] = __('Unable to add product to cart.');

                //         return $this->getJsonResponse($returnArray);
                //     }
                //     $stockItem = $this->_stockRegistry
                //         ->getStockItem($product->getId());
                    
                //     if (!$stockItem) {
                //         $returnArray['error'] = 1;
                //         $returnArray['message'] = __('quantity not available.');

                //         return $this->getJsonResponse($returnArray);
                //     }
                //     $quoteItem->setQty($qty)->save();
                //     $returnArray['item_state'] = "added";
                //     $productAdded = "updated";
                // }
                $quote->collectTotals()->save();

                if (!$productAdded) {
                    $returnArray['error'] = 1;
                    $returnArray['message'] = __('Unable to add product to cart.');

                    return $this->getJsonResponse($returnArray);
                } else {
                    $returnArray['cartCount'] = $quote->getItemsQty() * 1;
                }

                $returnArray['message'] =
                __(
                    '%1 was added to your shopping cart.',
                    $this->_helperCatalog
                        ->stripTags($product->getName())
                );

                //delete files uploaded for custom option
                foreach ($filesToDelete as $eachFile) {
                    unlink($eachFile);
                }

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __("invalid request");

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }
}
