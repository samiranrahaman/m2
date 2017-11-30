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
class AskQuestion extends AbstractMarketplace
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
                $subject = $this->getRequest()->getPost("subject");
                $query = $this->getRequest()->getPost("query");
                
                $helper = $this->_marketplaceHelper;

                $seller = $this->_customerFactory->create()->load($customerId);

                $sellerName = $seller->getName();
                $sellerEmail = $seller->getEmail();

                $adminStoremail = $helper->getAdminEmailId();
                $adminEmail = $adminStoremail ? $adminStoremail : $helper->getDefaultTransEmailId();
                $adminUsername = 'Admin';

                $emailTemplateVariables = [];
                $senderInfo = [];
                $receiverInfo = [];
                $emailTemplateVariables['myvar1'] = $adminUsername;
                $emailTemplateVariables['myvar2'] = $sellerName;
                $emailTemplateVariables['subject'] = $subject;
                $emailTemplateVariables['myvar3'] = $query;
                $senderInfo = [
                    'name' => $sellerName,
                    'email' => $sellerEmail,
                ];
                $receiverInfo = [
                    'name' => $adminUsername,
                    'email' => $adminEmail,
                ];

                $this->_objectManager
                ->create('Custom\Marketplace\Helper\Email')
                ->askQueryAdminEmail($emailTemplateVariables, $senderInfo, $receiverInfo);

                $returnArray["message"] = __("The message has been sent.");
                $returnArray["success"] = 1;
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
