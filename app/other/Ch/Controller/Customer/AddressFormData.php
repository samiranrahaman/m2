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
class AddressFormData extends \Custom\Chharo\Controller\ApiController
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
     * execute address for data
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $returnArray = [];
            $addressId = $this->getRequest()->getPost("addressId");
            $customerId = $this->getRequest()->getPost("customerId");
            try {
                if ($addressId != "") {
                    if ($addressId > 0) {
                        $address = $this->_objectManager
                            ->create("\Magento\Customer\Model\Address")
                            ->load($addressId);
                        
                        if ($customerId != "") {
                            $customer = $this->_customerFactory
                                ->create()
                                ->load($customerId);

                            if ($customer->getDefaultBilling() == $addressId) {
                                $returnArray["addressData"]["isDefaultBilling"] = "true";
                            } else {
                                $returnArray["addressData"]["isDefaultBilling"] = "false";
                            }
                            if ($customer->getDefaultShipping() == $addressId) {
                                $returnArray["addressData"]["isDefaultShipping"] = "true";
                            } else {
                                $returnArray["addressData"]["isDefaultShipping"] = "false";
                            }
                        }
                        $addressData = $address->getData();
                        foreach ($addressData as $key => $addata) {
                            if ($addata != "") {
                                $returnArray["addressData"][$key] = $addata;
                            } else {
                                $returnArray["addressData"][$key] = "";
                            }
                        }
                        $returnArray["addressData"]["street"] = $address->getStreet();
                    }
                }
                $countryCollection = $this->_objectManager
                    ->create("\Magento\Directory\Model\ResourceModel\Country\Collection")
                    ->loadByStore()
                    ->toOptionArray(true);

                unset($countryCollection[0]);

                foreach ($countryCollection as $country) {
                    $eachCountry = [];
                    $eachCountry["country_id"] = $country["value"];
                    $eachCountry["name"] = $country["label"];

                    $regionCollection = $this->_objectManager
                        ->create("\Magento\Directory\Model\ResourceModel\Region\Collection")
                        ->addCountryFilter($eachCountry["country_id"])
                        ->toOptionArray();
                    
                    if (count($regionCollection) > 0) {
                        $eachCountry["states"] = $regionCollection;
                    }
                    $returnArray["countryData"][] = $eachCountry;
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
