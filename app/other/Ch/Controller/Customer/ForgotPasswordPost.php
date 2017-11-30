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

namespace Custom\Chharo\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API Customer controller.
 */
class ForgotPasswordPost extends \Custom\Chharo\Controller\ApiController
{

    /**
     * $_customerFactory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        HelperData $helper,
        HelperCatalog $helperCatalog,
        Emulation $emulate,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
    
        $this->_customerFactory = $customerFactory;
        parent::__construct($context, $helper, $helperCatalog, $emulate);
    }

    /**
     * execute category list
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $emailAddress = $this->getRequest()->getPost("emailAddress");
            $storeId = $this->getRequest()->getPost("storeId");
            $websiteId = $this->getRequest()->getPost("websiteId");
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                if ($emailAddress) {
                    $emailValidator = new \Zend\Validator\EmailAddress();

                    if (!$emailValidator->isValid($emailAddress)) {
                        $returnArray["success"] = 0;
                        $returnArray["message"] = __("Invalid email address.");
                        return $this->getJsonResponse($returnArray);
                    }
                    $customer = $this->_customerFactory
                        ->create()
                        ->setWebsiteId($websiteId)
                        ->loadByEmail($emailAddress);

                    if ($customer->getId()) {
                        try {
                            $newResetPasswordLinkToken = $this
                                ->_objectManager
                                ->create("\Magento\User\Helper\Data")
                                ->generateResetPasswordLinkToken();
                            $customer->changeResetPasswordLinkToken($newResetPasswordLinkToken);
                            $customer->sendPasswordResetConfirmationEmail();
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                            $returnArray["success"] = 0;
                            $returnArray["message"] = $e->getMessage();
                            return $this->getJsonResponse($returnArray);
                        }
                    }
                    $returnArray["success"] = 1;
                    $returnArray["message"] =
                    __(
                        "If there is an account associated with %1 you will receive an email with a link to reset your password.",
                        $emailAddress
                    );
                    return $this->getJsonResponse($returnArray);
                } else {
                    $returnArray["success"] = 0;
                    $returnArray["message"] = __("Please enter your email.");
                    return $this->getJsonResponse($returnArray);
                }

                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray["success"] = 0;
                $returnArray["message"] = __("Invalid request");
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["success"] = 0;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }
}
