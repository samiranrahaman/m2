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
class SendEmail extends AbstractMarketplace
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
                $incrementId = $this->getRequest()->getPost('incrementId');

                $order = $this->_orderFactory->create()
                ->loadByIncrementId($incrementId);
                $orderId = $order->getId();
                try {
                    $this->_objectManager
                    ->create(
                        "\Magento\Sales\Model\Order\Email\Sender\OrderSender"
                    )
                    ->send($order);
                    $historyItem = $this->_objectManager
                    ->create("Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory")
                    ->create()
                    ->getUnnotifiedForInstance($order);

                    if ($historyItem) {
                        $historyItem->setIsCustomerNotified(1);
                        $historyItem->save();
                    }
                    $returnArray['message'] = __('The order email has been sent.');
                    $returnArray['success'] = 1;
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $returnArray['message'] = __($e->getMessage());
                    $returnArray['success'] = 0;

                    return $this->getJsonResponse($returnArray);
                } catch (\Exception $e) {
                    $returnArray['message'] = __('Failed to send the order email.');
                    $returnArray['success'] = 0;

                    return $this->getJsonResponse($returnArray);
                }

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
