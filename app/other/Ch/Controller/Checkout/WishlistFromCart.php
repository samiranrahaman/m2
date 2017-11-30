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
class WishlistFromCart extends AbstractCheckout
{


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
            $itemId = $this->getRequest()->getPost("itemId");
            $returnArray = [];
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $quoteCollection = $this->_quoteFactory
                    ->create()->getCollection();
                $quoteCollection->addFieldToFilter("customer_id", $customerId);
                $quoteCollection->addOrder("updated_at", "desc");

                $quote = $quoteCollection->getFirstItem();
                $wishlist = $this->_objectManager
                    ->create("\Magento\Wishlist\Model\Wishlist")
                    ->loadByCustomerId($customerId, true);

                $item = $quote->getItemById($itemId);
                $productId  = $item->getProductId();
                $buyRequest = $item->getBuyRequest();
                $wishlist->addNewItem($productId, $buyRequest);
                $quote->removeItem($itemId);
                $quote->collectTotals()->save();
                $customer = $this->_customerFactory
                    ->create()
                    ->load($customerId);
                $collection = $wishlist->getItemCollection()->setInStockFilter(true);
                if ($this->_helper->getConfigData("wishlist/wishlist_link/use_qty")) {
                    $count = $collection->getItemsQty();
                } else {
                    $count = $collection->getSize();
                }
                $session = $this->_customerSession->setCustomer($customer);
                $session->setWishlistDisplayType($this->_helper->getConfigData("wishlist/wishlist_link/use_qty"));
                $session->setDisplayOutOfStockProducts(
                    $this->_helper->getConfigData("cataloginventory/options/show_out_of_stock")
                );
                $session->setWishlistItemCount($count);
                $wishlist->save();
               
                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                return $this->getJsonResponse(["success" => 1]);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray["success"] = 0;
                $returnArray["message"] = __("Invalid Request");
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["success"] = 0;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }
}
