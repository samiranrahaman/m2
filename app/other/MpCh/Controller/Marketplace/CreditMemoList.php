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
class CreditMemoList extends AbstractMarketplace
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
                $customerId = $this->getRequest()->getPost('customerId');
                $incrementId = $this->getRequest()->getPost('incrementId');

                $helper = $this->_marketplaceHelper;
                $returnArray = [];
                $order = $this->_orderFactory->create()->loadByIncrementId($incrementId);
                $orderId = $order->getId();
                $returnArray['mainHeading'] = __('Credit Memos List');
                $returnArray['labels']['incrementId'] = __('Credit Memos #');
                $returnArray['labels']['billToName'] = __('Bill To Name');
                $returnArray['labels']['date'] = __('Created At');
                $returnArray['labels']['status'] = __('Status');
                $returnArray['labels']['amount'] = __('Amount');
                $marketplaceOrders = $this->_objectManager->create("Custom\Marketplace\Model\OrdersFactory")->create()
                ->getCollection()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('seller_id', $customerId);
                $tracking = new \Magento\Framework\DataObject();
                foreach ($marketplaceOrders as $tracking) {
                    $tracking = $tracking;
                }
                $creditmemoIds = [];
                $creditmemoIds = explode(',', $tracking->getCreditmemoId());
                $creditmemoCollection = $this->_objectManager->create("Magento\Sales\Model\Order\Creditmemo")
                ->getCollection()
                ->addFieldToFilter('entity_id', ['in' => $creditmemoIds]);
                $allCreditMemos = [];
                foreach ($creditmemoCollection as $creditmemo) {
                    $eachCreditMemos = [];
                    $eachCreditMemos['incrementId'] = $creditmemo['increment_id'];
                    $eachCreditMemos['billToName'] = $order->getCustomerName();
                    $eachCreditMemos['date'] = $this->_helperCatalog
                    ->formatDate($creditmemo->getCreatedAt(), 'medium');
                    $eachCreditMemos['status'] = __('Refunded');
                    $eachCreditMemos['amount'] = strip_tags($order->formatPrice($creditmemo->getGrandTotal()));
                    $eachCreditMemos['id'] = $creditmemo['entity_id'];
                    $allCreditMemos[] = $eachCreditMemos;
                }
                $returnArray['allCreditMemos'] = $allCreditMemos;

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
