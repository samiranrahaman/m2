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
class Invoice extends AbstractMarketplace
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
                
                try {
                    $order = $this->_orderFactory->create()->loadByIncrementId($incrementId);

                    $sellerId = $customerId;

                    $orderId = $order->getId();
                    if ($order->canUnhold()) {
                        $returnArray['message'] = __('Can not create invoice as order is in HOLD state');
                        $returnArray['success'] = 0;
                    } else {
                        $data = [];
                        $data['send_email'] = 1;
                        $marketplaceOrder = $this->_objectManager->create(
                            'Custom\Marketplace\Model\OrdersFactory'
                        )->create();
                        $model = $marketplaceOrder
                            ->getCollection()
                            ->addFieldToFilter(
                                'seller_id',
                                $sellerId
                            )
                            ->addFieldToFilter(
                                'order_id',
                                $orderId
                            );
                        foreach ($model as $tracking) {
                            $marketplaceOrder = $tracking;
                        }
    
                        $invoiceId = $marketplaceOrder->getInvoiceId();
                        
                        if (!$invoiceId) {
                            $items = [];
                            $itemsarray = [];
                            $shippingAmount = 0;
                            $codcharges = 0;
                            $paymentCode = '';
                            $paymentMethod = '';
                            if ($order->getPayment()) {
                                $paymentCode = $order->getPayment()->getMethod();
                            }
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
                                if ($paymentCode == 'mpcashondelivery') {
                                    $codcharges = $tracking->getCodCharges();
                                }
                            }
                            $codCharges = 0;
                            $tax = 0;
                            $collection = $this->_objectManager->create(
                                'Custom\Marketplace\Model\Saleslist'
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
                            foreach ($collection as $saleproduct) {
                                if ($paymentCode == 'mpcashondelivery') {
                                    $codCharges = $codCharges + $saleproduct->getCodCharges();
                                }
                                $tax = $tax + $saleproduct->getTotalTax();
                                array_push($items, $saleproduct['order_item_id']);
                            }

                            $itemsarray = $this->_getItemQtys($order, $items);
                            
                            if (count($itemsarray) > 0 && $order->canInvoice()) {
                                $invoice = $this->_objectManager->create(
                                    'Magento\Sales\Model\Service\InvoiceService'
                                )->prepareInvoice($order, $itemsarray['data']);
                                if (!$invoice) {
                                    throw new \Magento\Framework\Exception\LocalizedException(
                                        __('We can\'t save the invoice right now.')
                                    );
                                }
                                if (!$invoice->getTotalQty()) {
                                    throw new \Magento\Framework\Exception\LocalizedException(
                                        __('You can\'t create an invoice without products.')
                                    );
                                }
                                $this->_coreRegistry->register(
                                    'current_invoice',
                                    $invoice
                                );

                                if (!empty($data['capture_case'])) {
                                    $invoice->setRequestedCaptureCase(
                                        $data['capture_case']
                                    );
                                }

                                if (!empty($data['comment_text'])) {
                                    $invoice->addComment(
                                        $data['comment_text'],
                                        isset($data['comment_customer_notify']),
                                        isset($data['is_visible_on_front'])
                                    );

                                    $invoice->setCustomerNote($data['comment_text']);
                                    $invoice->setCustomerNoteNotify(
                                        isset($data['comment_customer_notify'])
                                    );
                                }

                                $invoice->setShippingAmount($shippingAmount);
                                $invoice->setBaseShippingInclTax($shippingAmount);
                                $invoice->setBaseShippingAmount($shippingAmount);
                                $invoice->setSubtotal($itemsarray['subtotal']);
                                $invoice->setBaseSubtotal($itemsarray['baseSubtotal']);
                                if ($paymentCode == 'mpcashondelivery') {
                                    $invoice->setMpcashondelivery($codCharges);
                                }
                                $invoice->setGrandTotal(
                                    $itemsarray['subtotal'] +
                                    $shippingAmount +
                                    $codcharges +
                                    $tax
                                );
                                $invoice->setBaseGrandTotal(
                                    $itemsarray['subtotal'] + $shippingAmount + $codcharges + $tax
                                );

                                $invoice->register();

                                $invoice->getOrder()->setCustomerNoteNotify(
                                    !empty($data['send_email'])
                                );
                                $invoice->getOrder()->setIsInProcess(true);

                                $transactionSave = $this->_objectManager->create(
                                    'Magento\Framework\DB\Transaction'
                                )->addObject(
                                    $invoice
                                )->addObject(
                                    $invoice->getOrder()
                                );
                                $transactionSave->save();

                                $invoiceId = $invoice->getId();

                                $this->_invoiceSender->send($invoice);

                                $returnArray['message'] = __('Invoice has been created for this order.');
                                $returnArray['success'] = 1;
                            } else {
                                $returnArray['message'] = __('You cannot create invoice for this order.');
                                $returnArray['success'] = 0;
                            }
                            /*update mpcod table records*/
                            if ($invoiceId != '') {
                                if ($paymentCode == 'mpcashondelivery') {
                                    $saleslistColl = $this->_objectManager->create(
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
                                    foreach ($saleslistColl as $saleslist) {
                                        $saleslist->setCollectCodStatus(1);
                                        $saleslist->save();
                                    }
                                }

                                $trackingcol1 = $this->_objectManager->create(
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
                                foreach ($trackingcol1 as $row) {
                                    $row->setInvoiceId($invoiceId);
                                    $row->save();
                                }
                            }
                        } else {
                            $returnArray['message'] = __('Cannot create Invoice for this order.');
                            $returnArray['success'] = 0;
                        }
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $returnArray['message'] = __($e->getMessage());
                    $returnArray['success'] = 0;
                } catch (\Exception $e) {
                    $returnArray['message'] = __('We can\'t save the invoice right now.');
                    $returnArray['success'] = 0;
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
