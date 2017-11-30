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
class ChangeOrderStatus extends AbstractCheckout
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
            $incrementId = $this->getRequest()->getPost("incrementId");
            $confirm = $this->getRequest()->getPost("confirm");
            // $confirm =
            // json_encode(
            //     [
            //         "response"=>[
            //             "id"=>"kujghsdhakshkdhask",
            //             "state"=>"processing",
            //             "create_time"=>date("Y-m-d H:i:s")
            //         ],
            //         "client" => [
            //             "product_name" => "test client"
            //         ]
            //     ]
            // );
            $confirm = json_decode($confirm);

            $status = $this->getRequest()->getPost("status");
            $returnArray = [];

            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $order = $this->_orderFactory->create()
                    ->loadByIncrementId($incrementId);
                $payment = $order->getPayment();

                $payment->setTransactionId($confirm->response->id)
                    ->setPreparedMessage("status : ".$confirm->response->state)
                    ->setShouldCloseParentTransaction(true)
                    ->setIsTransactionClosed(0)
                    ->registerCaptureNotification($order->getGrandTotal());
                $order->save();
                $state = '';
                if ($status == 0) {
                    $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING)
                        ->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING)
                        ->save();
                        $state = \Magento\Sales\Model\Order::STATE_PROCESSING;
                } else {
                    $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED)
                        ->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED)
                        ->save();
                        $state = \Magento\Sales\Model\Order::STATE_CANCELED;
                }
                if ($order->canInvoice()) {

                    /**
                     * create invoice
                     */
                    $invoice = $this->_objectManager
                        ->create(
                            'Magento\Sales\Model\Service\InvoiceService'
                        )->prepareInvoice($order);

                    $invoice->setRequestedCaptureCase(
                        \Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE
                    );
                    $invoice->register();
                    $transactionSave = $this->_objectManager
                        ->create(
                            '\Magento\Framework\DB\Transaction'
                        )->addObject(
                            $invoice
                        )->addObject(
                            $invoice->getOrder()
                        );
                    $transactionSave->save();
                    $this->_invoiceSender->send($invoice);
                }
                $comment = "status :".$confirm->response->state."<br>";
                $comment .= "transaction id :".$confirm->response->id."<br>";
                $comment .= "date :".$confirm->response->create_time."<br>";
                $comment .= "from :".$confirm->client->product_name."<br>";
                $order->addStatusHistoryComment($comment)
                    ->setIsCustomerNotified(true);

                $order->save();
               
                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray["success"] = 1;
                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
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
