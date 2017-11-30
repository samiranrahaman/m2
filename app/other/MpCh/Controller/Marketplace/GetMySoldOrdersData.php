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
class GetMySoldOrdersData extends AbstractMarketplace
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
                $orderStatus = $this->getRequest()->getPost('orderStatus');
                $toDate = $this->getRequest()->getPost('toDate');
                $fromDate = $this->getRequest()->getPost('fromDate');
                $orderId = $this->getRequest()->getPost('orderId');
                $pageNumber = $this->getRequest()->getPost('pageNumber');
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $helper = $this->_marketplaceHelper;
                $orderIds = [];
                $ids = [];
                $from = null;
                $to = null;
                $returnArray = [];
                $orderCollection = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()
                ->getCollection()
                ->addFieldToFilter('seller_id', $customerId)
                ->addFieldToSelect('order_id')
                ->distinct(true);
                foreach ($orderCollection as $eachOrder) {
                    array_push($orderIds, $eachOrder->getorder_id());
                }
                $filterOrders = $this->_orderFactory->create()
                ->getCollection();
                if ($orderStatus != '') {
                    $filterOrders->addFieldToFilter('status', $orderStatus);
                }
                $filterOrders->addFieldToFilter('entity_id', ['in' => $orderIds]);
                foreach ($filterOrders as $filterOrder) {
                    $collectionIds = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()
                    ->getCollection()
                    ->addFieldToFilter('order_id', $filterOrder->getId())
                    ->setOrder('entity_id', 'DESC')
                    ->setPageSize(1);

                    foreach ($collectionIds as $collectionId) {
                        $entity_id = $collectionId->getentity_id();
                    }
                    array_push($ids, $entity_id);
                }
                $collection = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")->create()
                ->getCollection();
                if ($toDate != '') {
                    $todate = date_create($toDate);
                    $to = date_format($todate, 'Y-m-d 23:59:59');
                }

                if ($fromDate != '') {
                    $fromdate = date_create($fromDate);
                    $from = date_format($fromdate, 'Y-m-d H:i:s');
                }

                $collection->addFieldToFilter('entity_id', ['in' => $ids])
                       ->addFieldToFilter('created_at', ['datetime' => true, 'from' => $from, 'to' => $to]);
                if ($orderId != '') {
                    $collection->addFieldToFilter('magerealorder_id', ['like' => '%'.$orderId.'%']);
                }
                $collection->setOrder('entity_id', 'DESC');
                if ($pageNumber != '') {
                    $returnArray['totalCount'] = $collection->getSize();
                    $collection->setPageSize(9)->setCurPage($pageNumber);
                }
                $allOrders = [];
                foreach ($collection as $res) {
                    $order = $this->_orderFactory->create()->load($res['order_id']);
                    $state = $order->getState();
                    $status = $order->getStatus();
                    $orderStatus = $this->_objectManager->create("Magento\Sales\Model\Order\StatusFactory")->create()
                    ->getResourceCollection()
                    ->addFieldToFilter('status', $status);

                    foreach ($orderStatus as $orderStatusData) {
                        $status = $orderStatusData->getLabel();
                    }

                    $name = $order->getCustomerName();

                    if ($res['order_id'] > 0) {
                        $eachOrder = [];
                        $eachOrder['label'] = __('Order').' #'.$res['magerealorder_id'];
                        $filteredCollection = $this->_objectManager
                        ->create("Custom\Marketplace\Model\SaleslistFactory")
                        ->create()
                        ->getCollection()
                        ->addFieldToFilter('order_id', $res['order_id'])
                        ->addFieldToFilter('seller_id', $customerId);

                        $orderList = [];
                        foreach ($filteredCollection as $filteredRes) {
                            $filteredRes = $filteredRes->getData();
                            $eachProduct = [];
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
                        $_collection = $this->_objectManager->create("Custom\Marketplace\Model\SaleslistFactory")
                        ->create()
                        ->getCollection();
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
                        $allOrders[] = $eachOrder;
                    }
                }
                $returnArray['orderList'] = $allOrders;

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
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
