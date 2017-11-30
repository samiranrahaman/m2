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
 * Chharo API Upload  banner controller.
 */
class Pay extends \Custom\Chharo\Controller\ApiController
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
                $senderId = $this->getRequest()->getPost('senderId');
				$qrcode = $this->getRequest()->getPost('qrcode');
				$mobileno = $this->getRequest()->getPost('mobileno');
				
				if($qrcode!="") {
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
					$connection = $resource->getConnection();
					$tableName = $resource->getTableName('customer_address_entity');    
					$sql = "SELECT parent_id, firstname, telephone FROM customer_address_entity WHERE qrcode='".$qrcode."'";
					$result = $connection->fetchAll($sql);

					$returnArray['id'] = $result[0]['parent_id'];
					$returnArray['name'] = $result[0]['firstname'];
					$returnArray['mobileno'] = $result[0]['telephone'];
					$returnArray["success"] = 1;
					return $this->getJsonResponse($returnArray);
				} elseif($mobileno!="") {

									
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
					$connection = $resource->getConnection();
					$tableName = $resource->getTableName('customer_address_entity');    
					$sql = "SELECT parent_id, firstname FROM customer_address_entity WHERE telephone='".$mobileno."'";
					$result = $connection->fetchAll($sql);

					$returnArray['id'] = $result[0]['parent_id'];
					$returnArray['name'] = $result[0]['firstname'];
					$returnArray["success"] = 1;
					return $this->getJsonResponse($returnArray);
				}
            } catch (\Exception $e) {
                $this->createLog("Chharo Exception log for class: ".get_class($this)." : ".$e->getMessage(), (array)$e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __('Invalid request');

                return $this->getJsonResponse($returnArray);
            }
        }
}