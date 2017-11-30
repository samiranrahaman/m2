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
class NotifyAdmin extends \Custom\Chharo\Controller\ApiController
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
            $returnArray = [];
            $wholeData = $this->getRequest()->getPost();
            $sellerMessage = isset($wholeData['message']) ? $wholeData['message'] : '';
            $websiteId = isset($wholeData['websiteId']) ? $wholeData['websiteId'] : '';
            $sellerId = isset($wholeData['sellerId']) ? $wholeData['sellerId'] : '';
            $sellerName = isset($wholeData['sellerName']) ? $wholeData['sellerName'] : '';
            try {
                $adminEmail = $this->_helper->getConfigData('mpchharo/admin/email');

                $customer = $this->_customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($adminEmail);
                $androidTokenCollection = $this->_objectManager->create("\Custom\Chharo\Model\AndroidTokenFactory")
                ->create()
                ->getCollection()
                ->addFieldToFilter('customer_id', $customer->getId());

                foreach ($androidTokenCollection as $token) {
                    $message = [
                        'title' => 'New Message from '.$sellerName,
                        'message' => $sellerMessage,
                        'id' => $sellerId,
                        'name' => $sellerName,
                        'notificationType' => 'adminNotification',
                    ];
                    $url = 'https://fcm.googleapis.com/fcm/send';
                    $authKey = $this->_helper->getConfigData('chharo/notification/apikey');
                    $headers = [
                        'Authorization: key='.$authKey,
                        'Content-Type: application/json',
                    ];
                    $error = 0;
                    $errorMsg = [];
                    $fields = [
                        'to' => $token->getToken(),
                        'data' => $message,
                        'time_to_live' => 30,
                        'delay_while_idle' => true,
                    ];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);
                    curl_close($ch);
                }
                $returnArray['success'] = 1;

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
