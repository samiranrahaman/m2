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
class GetAddrBookData extends \Custom\Chharo\Controller\ApiController
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
            $customerEmail = $this->getRequest()->getPost("customerEmail");
            $websiteId = $this->getRequest()->getPost("websiteId");
            $returnArray = [];
           
            try {
                $customer = $this->_customerFactory->create()
                    ->setWebsiteId($websiteId)
                    ->loadByEmail($customerEmail);
                $address = $customer->getPrimaryBillingAddress();
                if ($address instanceof \Magento\Framework\DataObject) {
                    $returnArray["billingAddress"]["value"] = $address->getFirstname()." ".$address->getLastname()."\n";
                    foreach ($address->getStreet() as $street) {
                        $returnArray["billingAddress"]["value"] .= $street."\n";
                    }
                    $returnArray["billingAddress"]["value"] .= $address->getCity().", ".$address->getRegion().", ".$address->getPostcode()."\n".$this->_objectManager->create("\Magento\Directory\Model\Country")->load($address->getCountryId())->getName()."\n"."T:".$address->getTelephone();
                    $returnArray["billingAddress"]["id"] = $address->getId();
                } else {
                    $returnArray["billingAddress"]["value"] = __("You have not set a default billing address.");
                    $returnArray["billingAddress"]["id"] = "";
                }
                $address = $customer->getPrimaryShippingAddress();
                if ($address instanceof \Magento\Framework\DataObject) {
                    $returnArray["shippingAddress"]["value"] = $address->getFirstname()." ".$address->getLastname()."\n";
                    foreach ($address->getStreet() as $street) {
                        $returnArray["shippingAddress"]["value"] .= $street."\n";
                    }
                    $returnArray["shippingAddress"]["value"] .= $address->getCity().", ".$address->getRegion().", ".$address->getPostcode()."\n".$this->_objectManager->create("\Magento\Directory\Model\Country")->load($address->getCountryId())->getName()."\n"."T:".$address->getTelephone();
                    $returnArray["shippingAddress"]["id"] = $address->getId();
                } else {
                    $returnArray["shippingAddress"]["value"] = __("You have not set a default shipping address.");
                    $returnArray["shippingAddress"]["id"] = "";
                }
                $additionalAddress = $customer->getAdditionalAddresses();
                foreach ($additionalAddress as $key => $eachAdditionalAddress) {
                    if ($eachAdditionalAddress instanceof \Magento\Framework\DataObject) {
                        $eachAdditionalAddressArray = [];
                        $eachAdditionalAddressArray["value"] = $eachAdditionalAddress->getFirstname()." ".$eachAdditionalAddress->getLastname()."\n";
                        foreach ($eachAdditionalAddress->getStreet() as $street) {
                            $eachAdditionalAddressArray["value"] .= $street."\n";
                        }
                        $eachAdditionalAddressArray["value"] .= $eachAdditionalAddress->getCity().", ".$eachAdditionalAddress->getRegion().", ".$eachAdditionalAddress->getPostcode()."\n".$this->_objectManager->create("\Magento\Directory\Model\Country")->load($eachAdditionalAddress->getCountryId())->getName()."\n"."T:".$eachAdditionalAddress->getTelephone();
                        $eachAdditionalAddressArray["id"] = $eachAdditionalAddress->getId();
                    } else {
                        $eachAdditionalAddressArray["value"] = __("You have no additional address.");
                        $eachAdditionalAddressArray["id"] = "";
                    }
                    $returnArray["additionalAddress"][] = $eachAdditionalAddressArray;
                }
                $returnArray["success"] = 1;
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
