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
class ApplyCoupon extends AbstractCheckout
{
 

    /**
     * execute apply coupon
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $storeId = $this->getRequest()->getPost("storeId");
            $couponCode = $this->getRequest()->getPost("couponCode");
            $removeCoupon = $this->getRequest()->getPost("removeCoupon");
            $returnArray = [];
            $error = 0;
            $message = "";
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
                    $quote =
                    $this->_quoteFactory->create()
                        ->setStoreId($storeId)
                        ->load($quoteId);
                }
                if ($removeCoupon == 1) {
                    $couponCode = "";
                }
                $oldCouponCode = $quote->getCouponCode();
                if (!strlen($couponCode) && !strlen($oldCouponCode)) {
                    $error = 1;
                }
                $codeLength = strlen($couponCode);

                $isCodeLengthValid =
                $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;
                $quote->getShippingAddress()->setCollectShippingRates(true);
                $quote->setCouponCode($isCodeLengthValid ? $couponCode : "")->collectTotals()->save();
                if ($codeLength) {
                    if ($isCodeLengthValid && $couponCode == $quote->getCouponCode()) {
                        $error = 0;
                        $message = __(
                            "Coupon code '%1' was applied.",
                            $this->_helperCatalog->stripTags($couponCode)
                        );
                    } else {
                        $error = 1;
                        $message = __(
                            "Coupon code '%1' is not valid.",
                            $this->_helperCatalog->stripTags($couponCode)
                        );
                    }
                } else {
                    $error = 0;
                    $message = __("Coupon code was canceled.");
                }
                $returnArray["error"] = $error;
                $returnArray["message"] = $message;
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
