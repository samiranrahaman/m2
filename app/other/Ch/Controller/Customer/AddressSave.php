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
class AddressSave extends \Custom\Chharo\Controller\ApiController
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
     * execute save customer address
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $customerId = $this->getRequest()->getPost("customerId");
            $storeId = $this->getRequest()->getPost("storeId");
            $addressData = $this->getRequest()->getPost("addressData");
            // $addressData = json_encode(
            //     [
            //         "default_billing" => 1,
            //         "default_shipping" => 1,
            //         "country_id" => "AT",
            //         "region_id" => 102,
            //         "street"=>[
            //             0 => "this is test",
            //             1 => "this is too"
            //         ],
            //         "telephone" => 899978787,
            //         "firstname" => "Veronica",
            //         "lastname" => "Costello",
            //         "city" => "jhfgjgh",
            //         "postcode"=> "32009"
            //     ]
            // );
            $addressDataObject = json_decode($addressData);
            $addressData = [];
            foreach ($addressDataObject as $key => $addressValue) {
                $addressData[$key] = $addressValue;
            }

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

                $customerSession = $this->_objectManager
                    ->get("\Magento\Customer\Model\Session")
                    ->setCustomer($customer);

                $address  = $this->_objectManager->create("\Magento\Customer\Model\Address");

                $addressId = $this->getRequest()->getPost("addressId");

                if ($addressId != "") {
                    $existsAddress = $customer->getAddressById($addressId);
                    if ($existsAddress->getId() && $existsAddress->getCustomerId() == $customer->getId()) {
                        $address->setId($existsAddress->getId());
                    }
                }
                $errors = [];
                $addressForm = $this->_objectManager
                    ->create("\Magento\Customer\Model\Form");
                $addressForm->setFormCode("customer_address_edit")->setEntity($address);
                $addressErrors  = $addressForm->validateData($addressData);
                if ($addressErrors !== true) {
                    $errors = $addressErrors;
                }
                $addressForm->compactData($addressData);

                $address->setCustomerId($customer->getId())
                    ->setIsDefaultBilling($addressData["default_billing"])
                    ->setIsDefaultShipping($addressData["default_shipping"]);
                $addressErrors = $address->validate();
                $address->save();
                $errorMessage = __("The address has been saved.");
               
                /**
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                
                return $this->getJsonResponse(
                    ["status" => 1, "message" => $errorMessage]
                );
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
