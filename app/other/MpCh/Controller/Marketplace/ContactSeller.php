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
class ContactSeller extends AbstractMarketplace
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
                $sellerId = $this->getRequest()->getPost('sellerId');
                $subject = $this->getRequest()->getPost('subject');
                $query = $this->getRequest()->getPost('query');
                $productId = $this->getRequest()->getPost('productId');
                $customerEmail = $this->getRequest()->getPost('customerEmail');
                $customerName = $this->getRequest()->getPost('customerName');
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $seller = $this->_customerFactory->create()->load($sellerId);
                $sellerEmail = $seller->getEmail();
                $sellerName = $seller->getFirstname().' '.$seller->getLastname();
                if (!isset($productId) || $productId == null) {
                    $productId = 0;
                }

                $buyerEmail = $customerEmail;
                $buyerName = $customerName;

                if (strlen($buyerName) < 2) {
                    $buyerName = 'Guest';
                }
                $emailTemplateVariables = [];
                $emailTemplateVariables['myvar1'] = $sellerName;
                $emailTemplateVariables['myvar3'] = $this->_productFactory
                ->create()->load($productId)->getName();
                $emailTemplateVariables['myvar4'] = $query;
                $emailTemplateVariables['myvar5'] = $buyerEmail;
                $emailTemplateVariables['myvar6'] = $subject;
                $senderInfo = [
                'name' => $buyerName,
                'email' => $buyerEmail,
                ];
                $receiverInfo = [
                    'name' => $seller->getName(),
                    'email' => $sellerEmail,
                ];
                $data['email'] = $customerEmail;
                $data['name'] = $customerName;
                $data['product-id'] = $productId;
                $data['ask'] = $query;
                $data['subject'] = $subject;
                $data['seller-id'] = $sellerId;

                $this->_objectManager->create(
                    'Custom\Marketplace\Helper\Email'
                )->sendQuerypartnerEmail(
                    $data,
                    $emailTemplateVariables,
                    $senderInfo,
                    $receiverInfo
                );

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;
                $returnArray['message'] = __('Mail sent successfully !!');

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog('MpChharo Exception log: '.$e->getMessage(), $e->getTrace());
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
