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
class EmptyCart extends AbstractCheckout
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
            $customerId = $this->getRequest()->getPost("customerId");
            $quoteId = $this->getRequest()->getPost("quoteId");
            $returnArray = [];
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                if ($customerId != "") {
                    $quoteCollection = $this->_quoteFactory
                        ->create()->getCollection();
                    $quoteCollection->addFieldToFilter("customer_id", $customerId);
                    $quoteCollection->addOrder("updated_at", "desc");
                    $quote = $quoteCollection->getFirstItem();
                }
                $quoteId = $this->getRequest()->getPost("quoteId");
                if ($quoteId != "") {
                    $quote = $this->_quoteFactory
                        ->create()
                        ->setStoreId($storeId)
                        ->load($quoteId);
                }
                $quote->removeAllItems()->collectTotals()->save();
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
