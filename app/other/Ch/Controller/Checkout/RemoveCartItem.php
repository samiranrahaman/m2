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
class RemoveCartItem extends AbstractCheckout
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
            $itemId = $this->getRequest()->getPost("itemId");
            $quoteId = $this->getRequest()->getPost("quoteId");
            $customerId = $this->getRequest()->getPost("customerId");
            $returnArray = [];
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                
                if ($customerId != "") {
                    $quoteCollection = $this->_quoteFactory
                        ->create()
                        ->getCollection();

                    $quoteCollection->addFieldToFilter(
                        "customer_id",
                        $customerId
                    );
                    $quoteCollection->addOrder("updated_at", "desc");
                    $quote = $quoteCollection->getFirstItem();
                }
                
                if ($quoteId) {
                    $quote = $this->_quoteFactory
                        ->create()
                        ->setStoreId($storeId)
                        ->load($quoteId);
                }
                $quote->removeItem($itemId);

                $quote->collectTotals()->save();
                $totals = $quote->getTotals();
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
                    $returnArray["discount"]["value"] = $this->_helperCatalog
                        ->stripTags(
                            $this->_priceHelper->currency(
                                $discount->getValue()
                            )
                        );
                }
                if (isset($totals["tax"])) {
                    $tax = $totals["tax"];
                    $returnArray["tax"]["title"] = $tax->getTitle();
                    $returnArray["tax"]["value"] = $this->_helperCatalog
                        ->stripTags(
                            $this->_priceHelper->currency(
                                $tax->getValue()
                            )
                        );
                }
                if (isset($totals["grand_total"])) {
                    $grandtotal = $totals["grand_total"];
                    $returnArray["grandtotal"]["title"] = $grandtotal->getTitle();
                    $returnArray["grandtotal"]["value"] = $this->_helperCatalog
                        ->stripTags(
                            $this->_priceHelper->currency(
                                $grandtotal->getValue()
                            )
                        );
                }
                if ($customerId != "" || $quoteId != "") {
                            $returnArray["itemCount"] = $quote->getItemsQty()*1;
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
