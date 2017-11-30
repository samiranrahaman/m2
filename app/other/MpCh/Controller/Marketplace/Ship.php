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
class Ship extends AbstractMarketplace
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
                $incrementId = $this->getRequest()->getPost("id");
                
              
                $order = $this->_orderFactory->create()->loadByIncrementId($incrementId);
                $res = $this->doShipmentExecution($order, $customerId);
                $returnArray = $res;
                
                return $this->getJsonResponse($returnArray);
            }   catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $returnArray['message'] = __($e->getMessage());
                    $returnArray['success'] = 0;
            } 

            catch (\Exception $e) {
                $this->createLog("Exception log for ship: ".$e->getMessage(), $e->getTrace());
                $returnArray["success"] = 0;
                $returnArray["message"] = __('invalid request');
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["success"] = 0;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }

    protected function doShipmentExecution($order, $sellerId)
    {
        try {
            $returnArray = [];
            // $sellerId = $this->_customerSession->getCustomerId();
            $orderId = $order->getId();
            $marketplaceOrder = $this->_objectManager->create(
                'Custom\Marketplace\Helper\Orders'
            )
            ->getOrderinfo($orderId);
            $trackingid = '';
            $carrier = '';
            $trackingData = [];
            $paramData = $this->getRequest()->getParams();
            if (!empty($paramData['tracking_id'])) {
                $trackingid = $paramData['tracking_id'];
                $trackingData[1]['number'] = $trackingid;
                $trackingData[1]['carrier_code'] = 'custom';
            }
            if (!empty($paramData['carrier'])) {
                $carrier = $paramData['carrier'];
                $trackingData[1]['title'] = $carrier;
            }

            if (!empty($paramData['api_shipment'])) {
                $this->_eventManager->dispatch(
                    'generate_api_shipment',
                    [
                        'api_shipment' => $paramData['api_shipment'],
                        'order_id' => $orderId,
                    ]
                );
                $shipmentData = $this->_customerSession->getData('shipment_data');
                $apiName = $shipmentData['api_name'];
                $trackingid = $shipmentData['tracking_number'];
                $trackingData[1]['number'] = $trackingid;
                $trackingData[1]['carrier_code'] = 'custom';
                $this->_customerSession->unsetData('shipment_data');
            }

            if (empty($paramData['api_shipment']) || $trackingid != '') {
                if ($order->canUnhold()) {
                    $returnArray['success'] = 0;
                    $returnArray['message'] = __('Can not create shipment as order is in HOLD state');
                    return $returnArray;
                } else {
                    $items = [];
                    $shippingAmount = 0;

                    $trackingsdata = $this->_objectManager->create(
                        'Custom\Marketplace\Model\Orders'
                    )
                    ->getCollection()
                    ->addFieldToFilter(
                        'order_id',
                        $orderId
                    )
                    ->addFieldToFilter(
                        'seller_id',
                        $sellerId
                    );
                    foreach ($trackingsdata as $tracking) {
                        $shippingAmount = $tracking->getShippingCharges();
                    }

                    $collection = $this->_objectManager->create(
                        'Custom\Marketplace\Model\Saleslist'
                    )
                    ->getCollection()
                    ->addFieldToFilter(
                        'order_id',
                        $orderId
                    )
                    ->addFieldToFilter(
                        'seller_id',
                        $sellerId
                    );
                    foreach ($collection as $saleproduct) {
                        array_push($items, $saleproduct['order_item_id']);
                    }

                    $itemsarray = $this->_getShippingItemQtys($order, $items);

                    if (count($itemsarray) > 0) {
                        $shipment = false;
                        $shipmentId = 0;
                        if (!empty($paramData['shipment_id'])) {
                            $shipmentId = $paramData['shipment_id'];
                        }
                        if ($shipmentId) {
                            $shipment = $this->_objectManager->create(
                                'Magento\Sales\Model\Order\Shipment'
                            )->load($shipmentId);
                        } elseif ($orderId) {
                            if ($order->getForcedDoShipmentWithInvoice()) {
                                $this->messageManager
                                ->addError(
                                    __('Cannot do shipment for the order separately from invoice.')
                                );
                            }
                            if (!$order->canShip()) {
                                $this->messageManager->addError(
                                    __('Cannot do shipment for the order.')
                                );
                            }

                            $shipment = $this->_prepareShipment(
                                $order,
                                $itemsarray['data'],
                                $trackingData
                            );
                        }
                        if ($shipment) {
                            $comment = '';
                            $shipment->getOrder()->setCustomerNoteNotify(
                                !empty($data['send_email'])
                            );
                            $shippingLabel = '';
                            if (!empty($data['create_shipping_label'])) {
                                $shippingLabel = $data['create_shipping_label'];
                            }
                            $isNeedCreateLabel=!empty($shippingLabel) && $shippingLabel;
                            $shipment->getOrder()->setIsInProcess(true);

                            $transactionSave = $this->_objectManager->create(
                                'Magento\Framework\DB\Transaction'
                            )->addObject(
                                $shipment
                            )->addObject(
                                $shipment->getOrder()
                            );
                            $transactionSave->save();

                            $shipmentId = $shipment->getId();

                            $courrier = 'custom';
                            $sellerCollection = $this->_objectManager->create(
                                'Custom\Marketplace\Model\Orders'
                            )
                            ->getCollection()
                            ->addFieldToFilter(
                                'order_id',
                                ['eq' => $orderId]
                            )
                            ->addFieldToFilter(
                                'seller_id',
                                ['eq' => $sellerId]
                            );
                            foreach ($sellerCollection as $row) {
                                if ($shipment->getId() != '') {
                                    $row->setShipmentId($shipment->getId());
                                    $row->setTrackingNumber($trackingid);
                                    $row->setCarrierName($carrier);
                                    $row->save();
                                }
                            }

                            //$this->_objectManager->create("\Magento\Sales\Model\Order\Email\Sender\ShipmentSender")->send($shipment);

                            $shipmentCreatedMessage = __('The shipment has been created.');
                            $labelMessage = __('The shipping label has been created.');
                            $returnArray['success'] = 1;
                            $returnArray['shipmentId'] = $shipmentId;
                           // $returnArray['orderId'] = $incrementId;
                            $returnArray['message'] = $isNeedCreateLabel ? $shipmentCreatedMessage.' '.$labelMessage
                                : $shipmentCreatedMessage;
                            return $returnArray;

                            
                            
                        }
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $returnArray['success'] = 0;
            $returnArray['message'] = __($e->getMessage());
            return $returnArray;
        } catch (\Exception $e) {
            $this->createLog("Exception log for ship: ".$e->getMessage(), $e->getTrace());
            $returnArray['success'] = 0;
            $returnArray['message'] = __('We can\'t save the shipment right now.');
            return $returnArray;
        }
    }

    protected function _getShippingItemQtys($order, $items)
    {
        $data = [];
        $subtotal = 0;
        $baseSubtotal = 0;
        foreach ($order->getAllItems() as $item) {
            if (in_array($item->getItemId(), $items)) {
                $data[$item->getItemId()] = intval($item->getQtyOrdered() - $item->getQtyShipped());

                $_item = $item;

                // for bundle product
                $bundleitems = array_merge([$_item], $_item->getChildrenItems());

                if ($_item->getParentItem()) {
                    continue;
                }

                if ($_item->getProductType() == 'bundle') {
                    foreach ($bundleitems as $_bundleitem) {
                        if ($_bundleitem->getParentItem()) {
                            $data[$_bundleitem->getItemId()] = intval(
                                $_bundleitem->getQtyOrdered() - $item->getQtyShipped()
                            );
                        }
                    }
                }
                $subtotal += $_item->getRowTotal();
                $baseSubtotal += $_item->getBaseRowTotal();
            } else {
                if (!$item->getParentItemId()) {
                    $data[$item->getItemId()] = 0;
                }
            }
        }

        return ['data' => $data,'subtotal' => $subtotal,'baseSubtotal' => $baseSubtotal];
    }


    protected function _prepareShipment($order, $items, $trackingData)
    {
        $shipment = $this->_objectManager->create("Magento\Sales\Model\Order\ShipmentFactory")->create(
            $order,
            $items,
            $trackingData
        );

        if (!$shipment->getTotalQty()) {
            return false;
        }

        return $shipment->register();
    }
}
