<?php
/**
 * Custom Software.
 *
 * @category  Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\MpChharo\Controller\Marketplace;

/**
 * MpChharo API .
 */
class GetSellerDashboardData extends AbstractMarketplace
{
    /**
     * execute.
     *
     * @return string JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            try {
                $returnArray = [];
                $storeId = $this->getRequest()->getPost('storeId');
                $customerId = $this->getRequest()->getPost('customerId');
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $helper = $this->_marketplaceHelper;
                $collection = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', $customerId)
                ->addFieldToFilter('order_id', ['neq' => 0]);
                $collection1 = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', $customerId)
                ->addFieldToFilter('order_id', ['neq' => 0]);

                $collection2 = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', $customerId)
                ->addFieldToFilter('order_id', ['neq' => 0]);

                $collection3 = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', $customerId)
                ->addFieldToFilter('order_id', ['neq' => 0]);

                $firstDayOfWeek = date('Y-m-d', strtotime('Last Monday', $this->_objectManager->create("Magento\Framework\Stdlib\DateTime\DateTime")
                    ->timestamp(time())));
                $lastDayOfWeek = date('Y-m-d', strtotime('Next Sunday', $this->_objectManager->create("Magento\Framework\Stdlib\DateTime\DateTime")
                    ->timestamp(time())));
                $month = $collection1->addFieldToFilter('created_at', ['datetime' => true, 'from' => date('Y-m').'-01 00:00:00', 'to' => date('Y-m').'-31 23:59:59']);
                $week = $collection2->addFieldToFilter('created_at', ['datetime' => true, 'from' => $firstDayOfWeek.' 00:00:00', 'to' => $lastDayOfWeek.' 23:59:59']);
                $day = $collection3->addFieldToFilter('created_at', ['datetime' => true, 'from' => date('Y-m-d').' 00:00:00', 'to' => date('Y-m-d').' 23:59:59']);
                $sale = 0;
                $getDateDetail['year'] = $sale;
                $sale1 = 0;
                foreach ($day as $record1) {
                    $sale1 = $sale1 + $record1->getActualSellerAmount();
                }
                $getDateDetail['day'] = $sale1;
                $sale2 = 0;
                foreach ($month as $record2) {
                    $sale2 = $sale2 + $record2->getActualSellerAmount();
                }
                $getDateDetail['month'] = $sale2;
                $sale3 = 0;
                foreach ($week as $record3) {
                    $sale3 = $sale3 + $record3->getActualSellerAmount();
                }
                $getDateDetail['week'] = $sale3;
                $temp = 0;
                foreach ($collection as $record) {
                    $temp = $temp + $record->getActualSellerAmount();
                }
                $returnArray['total']['label'] = __(' Total Sales ');
                $returnArray['total']['amount'] = $this->_priceFormat->currency($temp, true, false);
                $returnArray['today']['label'] = __(' Today ');
                $returnArray['today']['amount'] = $this->_priceFormat->currency($getDateDetail['day'], true, false);
                $returnArray['week']['label'] = __(' Week ');
                $returnArray['week']['amount'] = $this->_priceFormat->currency($getDateDetail['week'], true, false);
                $returnArray['month']['label'] = __(' Month ');
                $returnArray['month']['amount'] = $this->_priceFormat->currency($getDateDetail['month'], true, false);
                $totalSaleCollection = $this->_objectManager->create("Custom\Marketplace\Model\SaleperpartnerFactory")
                ->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', $customerId);
                $totalSale = 0;
		$totalRemainSale = 0;
                foreach ($totalSaleCollection as $value) {
                    $totalSale = $value->getAmountRecived();
                    $totalRemainSale = $value->getAmountRemain();
                }
                $returnArray['payout']['label'] = __(' Total Payout ');
                $returnArray['payout']['amount'] = $this->_priceFormat->currency($totalSale, true, false);
                $returnArray['remaining']['label'] = __(' Remaining Amount ');
                $returnArray['remaining']['amount'] = $this->_priceFormat->currency($totalRemainSale, true, false);

                // recent orders list
                $ids = [];
                $orderIds = [];
                $from = null;
                $to = null;
                $collectionOrders = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', $customerId)
                ->addFieldToSelect('order_id')
                ->distinct(true);

                foreach ($collectionOrders as $collectionOrder) {
                    array_push($orderIds, $collectionOrder->getOrderId());
                }
                $filterOrders = $this->_orderFactory->create()
                ->getCollection()
                ->addFieldToFilter('entity_id', ['in' => $orderIds]);

                foreach ($filterOrders as $filterOrder) {
                    $collectionIds = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('order_id', $filterOrder->getId())
                    ->setOrder('entity_id', 'DESC')
                    ->setPageSize(1);

                    foreach ($collectionIds as $collectionId) {
                        $entity_id = $collectionId->getEntityId();
                    }
                    array_push($ids, $entity_id);
                }
                $collection = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")
                ->create()
                ->getCollection();
                $collection->addFieldToFilter('entity_id', ['in' => $ids])
                ->addFieldToFilter('created_at', ['datetime' => true, 'from' => $from, 'to' => $to])
                ->setOrder('entity_id', 'DESC')
                ->setPageSize(5)
                ->setCurPage(1);
                $recentOrders = [];
                foreach ($collection as $res) {
                    $order = $this->_orderFactory->create()->load($res['order_id']);
                    $state = $order->getState();
                    $status = $order->getStatus();
                    $orderStatus = $this->_objectManager->create("\Magento\Sales\Model\Order\StatusFactory")->create()
                    ->getResourceCollection()
                    ->addFieldToFilter('status', $status);

                    foreach ($orderStatus as $orderStatusData) {
                        $status = $orderStatusData->getLabel();
                    }
                    $name = $order->getCustomerName();

                    if ($res['order_id'] > 0) {
                        $eachOrder = [];
                        $eachOrder['label'] = __('Order').' #'.$res['magerealorder_id'];
                        $filteredCollection = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()
                        ->getCollection()
                        ->addFieldToFilter('order_id', $res['order_id'])
                        ->addFieldToFilter('seller_id', $customerId);
                        $orderList = [];
                        foreach ($filteredCollection as $filteredRes) {
                            $eachProduct = [];
                            $filteredRes = $filteredRes->getData();
                            $eachProduct['name'] = $filteredRes['magepro_name'];
                            $eachProduct['qty'] = intval($filteredRes['magequantity']);
                            $eachProduct['id'] = $filteredRes['mageproduct_id'];
                            $eachProduct['type'] = $this->_productFactory
                            ->create()
                            ->load($filteredRes['mageproduct_id'])
                            ->getTypeId();
                            $orderList[] = $eachProduct;
                        }
                        $eachOrder['orderList'] = $orderList;
                        $eachOrder['status'] = strtoupper($status);
                        $eachOrder['summary'] = __('Customer: ').$name.'  '.__('Date: ').$this->_dateTime->formatDate($res['created_at'], 'medium', true);
                        $orderTotal = 0;
                        $_collection = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()->getCollection();
                        $_collection->getSelect()
                                    ->where('seller_id ='.$customerId)
                                    ->columns('SUM(actual_seller_amount) AS qty')
                                    ->group('order_id');
                        foreach ($_collection as $coll) {
                            if ($coll->getorder_id() == $res['order_id']) {
                                $orderTotal = $coll->getQty();
                            }
                        }
                        $eachOrder['orderTotal'] = __('Order Total: ').$this->_helperCatalog->stripTags($this->_priceFormat->currency($orderTotal));
                        $eachOrder['incrementId'] = $res['magerealorder_id'];
                        $recentOrders[] = $eachOrder;
                    }
                }
                $returnArray['orderList'] = $recentOrders;

                // recent reviews list
                $ratings = [];
                $products = [];
                $rate = [];
                $reviewCollection = $this->_objectManager->create("Custom\Marketplace\Model\FeedbackFactory")->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', $customerId)
                ->addFieldToFilter('status', 1)
                ->setOrder('created_at', 'DESC')
                ->setPageSize(5)
                ->setCurPage(1);

                $recentReviews = [];
                foreach ($reviewCollection as $oneReview) {
                    $oneReview = $oneReview->getData();
                    $eachReview = [];
                    $eachReview['rating'][] = ['label' => __('Price'), 'value' => round(($oneReview['feed_price'] / 20), 1, PHP_ROUND_HALF_UP)];
                    $eachReview['rating'][] = ['label' => __('Value'), 'value' => round(($oneReview['feed_value'] / 20), 1, PHP_ROUND_HALF_UP)];
                    $eachReview['rating'][] = ['label' => __('Quality'), 'value' => round(($oneReview['feed_quality'] / 20), 1, PHP_ROUND_HALF_UP)];
                    $eachReview['summary'] = $oneReview['feed_review'];
                    $reviewCustomer = $this->_customerFactory->create()->load($oneReview['buyer_id'])->getData();
                    $name = $reviewCustomer['firstname'].' '.$reviewCustomer['lastname'];
                    $reviewDatetime = strtotime($oneReview['created_at']);
                    $reviewDate = date('d-M-Y', $reviewDatetime);
                    $eachReview['title'] = __('By %1', $name, $oneReview['created_at']).' '.__('on').' '.__('%1', $reviewDate);
                    $recentReviews[] = $eachReview;
                }
                $returnArray['recentReviews'] = $recentReviews;
                $graphData = [];
                $curryear = date('Y');
                $store = $this->_objectManager
                ->create("Magento\Store\Model\StoreFactory")
                ->create()
                ->load($storeId);
                for ($i = 1; $i <= 12; ++$i) {
                    $date1 = $curryear.'-'.$i.'-01 00:00:00';
                    $date2 = $curryear.'-'.$i.'-31 23:59:59';
                    $collection = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()->getCollection()
                                    ->addFieldToFilter('seller_id', $customerId)
                                    ->addFieldToFilter('created_at', ['datetime' => true, 'from' => $date1, 'to' => $date2]);
                    $sum = [];
                    $temp = 0;
                    foreach ($collection as $record) {
                        $temp += $record->getActualSellerAmount();
                    }
                    $baseCurrencyCode = $store->getBaseCurrencyCode();
                    $currentCurrencyCode = $store->getCurrentCurrencyCode();
                    $price = $this->_objectManager->create("Magento\Directory\Helper\Data")->currencyConvert($temp, $baseCurrencyCode, $currentCurrencyCode);
                    $graphData[] = $price;
                }
                $returnArray['graphData'] = $graphData;

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog('MpChharo Exception Log for : '.$this->getRequest()->getActionName().' : '.$e->getMessage(), (array) $e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __($e->getMessage());

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }
}
