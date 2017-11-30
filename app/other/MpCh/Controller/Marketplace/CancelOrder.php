<?php
/**
 * Custom Software.
 *
 * @category  Custom
 * @package   Custom_MpChharo
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\MpChharo\Controller\Marketplace;

/**
 * MpChharo API .
 */
class CancelOrder extends AbstractMarketplace
{

    /**
     * execute
     * @return string JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $returnArray = [];
                $customerId = $this->getRequest()->getPost("customerId");
                $incrementId = $this->getRequest()->getPost("incrementId");
                $order = $this->_orderFactory->create()
                ->loadByIncrementId($incrementId);
                $orderId = $order->getId();
                try {
                    $orderHelper = $this->_objectManager->get("Custom\Marketplace\Helper\Orders");
                    $flag = $orderHelper->cancelorder($order, $customerId);
                    if ($flag) {
                        $collection = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()
                        ->getCollection()
                        ->addFieldToFilter("seller_id", $customerId)
                        ->addFieldToFilter("order_id", $orderId);

                        foreach ($collection as $saleproduct) {
                            $saleproduct->setCpprostatus(2);
                            $saleproduct->setPaidStatus(2);
                            $saleproduct->save();
                            $trackingcoll = $this->_objectManager->create("Custom\Marketplace\Model\OrdersFactory")
                            ->create()
                            ->getCollection()
                            ->addFieldToFilter("order_id", $orderId)
                            ->addFieldToFilter("seller_id", $customerId);
                            foreach ($trackingcoll as $tracking) {
                                $tracking->setTrackingNumber("canceled");
                                $tracking->setCarrierName("canceled");
                                $tracking->setIsCanceled(1);
                                $tracking->save();
                            }
                        }
                        $returnArray["message"] = __("The order has been cancelled.");
                        $returnArray["success"] = 1;
                    } else {
                        $returnArray["message"] = __("You are not permitted to cancel this order.");
                        $returnArray["success"] = 0;
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $returnArray["message"] = __($e->getMessage());
                    $returnArray["success"] = 0;
                    return $this->getJsonResponse($returnArray);
                } catch (\Exception $e) {
                    $returnArray["message"] = __("The order has not been cancelled.");
                    $returnArray["success"] = 0;
                    return $this->getJsonResponse($returnArray);
                }
               
                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
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
