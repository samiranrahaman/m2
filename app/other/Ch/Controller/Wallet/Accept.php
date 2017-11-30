<?php
/**
 * Custom Software.
 *
 * @category Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\Chharo\Controller\Wallet;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API - accept payments
 */
class Accept extends \Custom\Chharo\Controller\ApiController
{
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
     * execute category list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
            try {
                $customerId = $this->getRequest()->getPost('customerId');
                $customer = $this->_customerFactory->create()
                    ->setWebsiteId(1)
                    ->load($customerId);
				$customerName = $customer->getFirstname();
				$mobileno = $customer->getPrimaryBillingAddress()->getTelephone();
				
				$returnArray['qrcode'] = md5($customerId.$customerName);
				$returnArray['mobileno'] = $mobileno;
				$returnArray["success"] = 1;
                return $this->getJsonResponse($returnArray);
				
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __('Invalid request');

                return $this->getJsonResponse($returnArray);
            }
        }
}
