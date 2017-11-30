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
class UpdateCart extends AbstractCheckout
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
            $itemIds = $this->getRequest()->getPost("itemIds");
            // $itemIds = json_encode([
            //     71,
            //     72,
            //     73
            // ]);
            $itemIds = json_decode($itemIds);
            $itemQtys = $this->getRequest()->getPost("itemQtys");
            // $itemQtys = json_encode([
            //     1,
            //     2,
            //     1
            // ]);
            $itemQtys = json_decode($itemQtys);
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                $customerId = $this->getRequest()->getPost("customerId");
                if ($customerId != "") {
                    $quoteCollection = $this->_quoteFactory
                        ->create()->getCollection();
                    $quoteCollection->addFieldToFilter("customer_id", $customerId);
                    $quoteCollection->addOrder("updated_at", "desc");
                    $quote = $quoteCollection->getFirstItem();
                }
                $quoteId = $this->getRequest()->getPost("quoteId");
                if ($quoteId != "") {
                    $quoteId = $this->getRequest()->getPost("quoteId");
                    $quote = $this->_quoteFactory
                        ->create()
                        ->setStoreId($storeId)
                        ->load($quoteId);
                }
                $cartData = [];
                foreach ($itemIds as $key => $value) {
                    $cartData[$value] = ["qty" => $itemQtys[$key]];
                }
               
                $returnArray = [];
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $filter = new \Magento\Framework\Filter\LocalizedToNormalized(
                    [
                        "locale" => $this->_objectManager
                            ->get("\Magento\Framework\Locale\Resolver")
                            ->getLocale()
                    ]
                );

                foreach ($cartData as $index => $eachData) {
                    if (isset($eachData["qty"])) {
                        $cartData[$index]["qty"] = $filter->filter(trim($eachData["qty"]));
                    }
                }

                $tempData = [];
                foreach ($cartData as $itemId => $itemInfo) {
                    if (!isset($itemInfo["qty"])) {
                        continue;
                    }
                    $qty = (float) $itemInfo["qty"];
                    if ($qty <= 0) {
                        continue;
                    }
                    $quoteItem = $quote->getItemById($itemId);
                    if (!$quoteItem) {
                        continue;
                    }
                    $product = $quoteItem->getProduct();
                    if (!$product) {
                        continue;
                    }
                    $stockItem = $this->_stockRegistry
                        ->getStockItem($product->getId());
                    
                    if (!$stockItem) {
                        continue;
                    }
                    $quoteItem->setQty($qty)->save();
                }
                $quote->collectTotals()->save();

                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray["success"] = 1;
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
