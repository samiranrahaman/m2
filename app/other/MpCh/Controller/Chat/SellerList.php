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

namespace Custom\MpChharo\Controller\Chat;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Store\Model\App\Emulation;

/**
 * MpChharo API chat controller.
 */
class SellerList extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_customerFactory.
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * $_dir.
     *
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $_dir;

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
     * execute notify admin.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $wholeData = $this->getRequest()->getPost();
            $adminEmail = isset($wholeData['adminEmail']) ? $wholeData['adminEmail'] : '';
            $websiteId = isset($wholeData['websiteId']) ? $wholeData['websiteId'] : '';
            $sellerId = isset($wholeData['sellerId']) ? $wholeData['sellerId'] : '';
            $returnArray = [];
            try {
                $adminEmail = $this->_helper->getConfigData('mpchharo/admin/email');

                if ($adminEmail) {
                    $customer = $this->_customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($adminEmail);

                    $customerIdNotToBeIncluded = [];
                    $customerIdNotToBeIncluded[] = 0;
                    $customerIdNotToBeIncluded[] = $customer->getId();

                    $androidTokenCollection = $this->_objectManager->create("\Custom\Chharo\Model\AndroidTokenFactory")
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('customer_id', array('nin' => $customerIdNotToBeIncluded));

                    $sellerCollection = $this->_objectManager->create("\Custom\Marketplace\Model\SellerFactory")
                    ->create()
                    ->getCollection()
                    ->addFieldToSelect('is_seller')
                    ->addFieldToSelect('seller_id')
                    ->addFieldToFilter('is_seller', 1);
                    $sellerIdArray = [];
                    foreach ($sellerCollection as $value) {
                        $sellerIdArray[] = $value->getSellerId();
                    }
                    $this->createLog('MpChharo seller list: ', (array) $sellerIdArray);
                    $sellerList = [];
                    foreach ($androidTokenCollection as $token) {
                        if (!in_array($token->getCustomerId(), $sellerIdArray)) {
                            continue;
                        }
                        $eachSeller = [];
                        $isExist = 0;
                        foreach ($sellerList as $key => $value) {
                            if ($value['customerId'] == $token->getCustomerId()) {
                                $sellerList[$key]['token'] = $value['token'].','.$token->getToken();
                                $isExist = 1;
                                break;
                            }
                        }
                        if ($isExist == 0) {
                            $eachSeller['customerId'] = $token->getCustomerId();
                            $eachSeller['token'] = $token->getToken();
                            $collection = $this->_customerFactory->create()
                            ->getCollection()
                           ->addAttributeToSelect('firstname')
                           ->addAttributeToSelect('lastname')
                           ->addAttributeToSelect('entity_id')
                           ->addFieldToFilter('entity_id', $token->getCustomerId());
                            foreach ($collection as $item) {
                                $eachSeller['name'] = $item->getFirstname();
                            }
                            $eachSeller['profileImage'] = 'http://magento2.Custom.com/marketplace-chharo/pub/media/chharo/customerpicture/Icon-1482489240.png';
                            $sellerList[] = $eachSeller;
                        }
                    }
                    $returnArray['success'] = 1;
                    $returnArray['sellerList'] = $sellerList;
                    $returnArray['apiKey'] = $this->_helper->getConfigData('chharo/notification/apikey');

                    return $this->getJsonResponse($returnArray);
                } else {
                    $returnArray['success'] = 0;
                    $returnArray['message'] = ____('Unauthorised Access');

                    return $this->getJsonResponse($returnArray);
                }

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog('MpChharo Exception log for class: '.get_class($this).' : '.$e->getMessage(), (array) $e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __('Invalid Request.');

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }
}
