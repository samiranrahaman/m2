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
class SendCreditMemoMail extends AbstractMarketplace
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
                $sellerId = $this->getRequest()->getPost('customerId');
                $incrementId = $this->getRequest()->getPost('incrementId');
                $creditmemoId = $this->getRequest()->getPost('creditmemoId');

                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $order = $this->_orderFactory->create()->loadByIncrementId($incrementId);

                if ($creditmemo = $this->_initCreditmemo($creditmemoId, $order->getId(), $sellerId)) {
                    try {
                        $this->_objectManager->create(
                        'Magento\Sales\Api\CreditmemoManagementInterface'
                    )->notify($creditmemo->getEntityId());
                        $returnArray['success'] = 1;
                        $returnArray['message'] = __('Message has been sent');
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $returnArray['success'] = 0;
                        $returnArray['message'] = __($e->getMessage());
                    } catch (\Exception $e) {
                        $returnArray['success'] = 0;
                        $returnArray['message'] = __('Failed to send the creditmemo email.');
                    }
                } else {
                    $returnArray['success'] = 0;
                    $returnArray['message'] = __('You are not authorized to send message');
                }

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);

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
