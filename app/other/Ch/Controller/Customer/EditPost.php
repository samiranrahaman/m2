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
class EditPost extends \Custom\Chharo\Controller\ApiController
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
            $firstName = $this->getRequest()->getPost("firstName");
            $lastName  = $this->getRequest()->getPost("lastName");
            $emailAddress = $this->getRequest()->getPost("emailAddress");
            $doChangePassword = $this->getRequest()->getPost("doChangePassword");
            $customerId = $this->getRequest()->getPost("customerId");
            $storeId = $this->getRequest()->getPost("storeId");
            $error = 0;
            $message = "";
            if ($storeId == "") {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                
                /**
                 * $initialEnvironmentInfo store emulation start
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $customer = $this->_customerFactory
                    ->create()
                    ->load($customerId);
                $customerForm = $this->_objectManager->create("\Magento\Customer\Model\Form");
                $customerForm->setFormCode("customer_account_edit")->setEntity($customer);
                $customerData = [
                    "firstname" => $firstName,
                    "lastname" => $lastName,
                    "email" => $emailAddress,
                    "password_confirmation" => $this->getRequest()->getPost("confirmPassword"),
                    "password" => $this->getRequest()->getPost("newPassword"),
                    "current_password" => $this->getRequest()->getPost("currentPassword")
                ];
                $errors = [];
                $customerErrors = $customerForm->validateData($customerData);

                // if($customerId == 1){
                //     $error = 1;
                //     $message = __("Sorry you can't change demo account");
                //     $returnArray["success"] = 0;
                //     return $this->getJsonResponse($returnArray);
                //
                // }
                
                if ($customerErrors !== true) {
                    $errors = array_merge($customerErrors, $errors);
                    $error = 1;
                } else {
                    $customerForm->compactData($customerData);
                    $errors = [];
                    if ($doChangePassword) {
                        $currentPassword = $this->getRequest()->getPost("currentPassword");

                        $newPassword = $this->getRequest()->getPost("newPassword");

                        $confirmPassword = $this->getRequest()->getPost("confirmPassword");
                        if ($customer->validatePassword($currentPassword)) {
                            if (strlen($newPassword)) {
                                $customer->setPassword($newPassword);
                                $customer->setConfirmation($confirmPassword);
                            }
                        } else {
                            $error = 1;
                            $message = __("Invalid current password");
                        }
                    }
                    $customerErrors = $customer->validate();
                    if (is_array($customerErrors)) {
                        $errors = array_merge($errors, $customerErrors);
                    }
                }

                if ($errors && count($errors) > 0) {
                    $message = implode(", ", $errors);
                }

                if ($error == 0) {
                    $customer->setConfirmation(null);
                    $customer->save();
                    $message = __("The account information has been saved.");
                }

               
                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray["success"] = 1;
                return $this->getJsonResponse(["error" => $error, "message" => $message]);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray["error"] = 1;
                $returnArray["message"] = __($e->getMessage());
                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray["error"] = 1;
                $returnArray["message"] = __("Invalid Request.");
                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray["error"] = 1;
            $returnArray["message"] = __("Invalid Request.");
            return $this->getJsonResponse($returnArray);
        }
    }
}
