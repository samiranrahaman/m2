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

namespace Custom\Chharo\Controller\Extra;

/**
 * Chharo API Extra controller.
 */
class SubscribeToNewsletter extends AbstractChharo
{


    /**
     * execute
     *
     * @return JSON
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $storeId = $this->getRequest()->getPost("storeId");
            $websiteId = $this->getRequest()->getPost("websiteId");
            $email = $this->getRequest()->getPost("email");
            $isLoggedIn = $this->getRequest()->getPost("isLoggedIn");
            $customerId = $this->getRequest()->getPost("customerId");

            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                $returnArray = [];
                $error = 0;
                $message = "";
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $ownerId = $this->_customerFactory
                    ->create()
                    ->setWebsiteId($websiteId)
                    ->loadByEmail($email)
                    ->getId();
                $emailValidator = new \Zend\Validator\EmailAddress();
                if (!$emailValidator->isValid($email)) {
                    $error = 1;
                    $message = __("Please enter a valid email address.");
                } elseif ($this->_helper->getConfigData(
                    \Magento\Newsletter\Model\Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG
                ) != 1 
                    && !$isLoggedIn
                ) {
                    $error = 1;
                    $message = __("Sorry, but administrator denied subscription for guests.");
                } elseif ($ownerId !== null && $ownerId != $customerId) {
                    $error = 1;
                    $message = __("This email address is already assigned to another user.");
                } else {
                    $status = $this->_objectManager
                        ->create("\Magento\Newsletter\Model\Subscriber")
                        ->subscribe($email);
                    if ($status == \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE
                    ) {
                        $message = __("Confirmation request has been sent.");
                    } else {
                        $message = __("Thank you for your subscription.");
                    }
                }
                $returnArray["errorCode"] = $error;
                $returnArray["message"] = $message;
                $returnArray["success"] = 1;
               
                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray["success"] = 0;
                $returnArray["message"] = __("Invalid Request.");
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["success"] = 0;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }
}
