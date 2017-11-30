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

namespace Custom\Chharo\Controller\Customer;

use Magento\Framework\App\Action\Context;
use Custom\Chharo\Helper\Data as HelperData;
use Custom\Chharo\Helper\Catalog as HelperCatalog;
use Magento\Store\Model\App\Emulation;

/**
 * Chharo API Customer controller.
 */
class GetAllOrders extends \Custom\Chharo\Controller\ApiController
{
    /**
     * $_customerFactory.
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
     * execute get all customer orders.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $customerEmail = $this->getRequest()->getPost('customerEmail');
            $websiteId = $this->getRequest()->getPost('websiteId');
            $storeId = $this->getRequest()->getPost('storeId');

            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $customer = $this->_customerFactory
                    ->create()
                    ->setWebsiteId($websiteId)
                    ->loadByEmail($customerEmail);

                $returnArray = [];
                $orders = $this->_objectManager
                    ->create(
                        "\Magento\Sales\Model\ResourceModel\Order\Collection"
                    )
                    ->addFieldToSelect('*')
                    ->addFieldToFilter('customer_id', $customer->getId())
                    ->addFieldToFilter(
                        'status',
                        [
                            'in' => $this->_objectManager
                                ->create("\Magento\Sales\Model\Order\Config")
                                ->getVisibleOnFrontStatuses(),
                        ]
                    )
                    ->setOrder('created_at', 'DESC');
                $pageNumber = $this->getRequest()->getPost('pageNumber');
                if ($pageNumber != '') {
                    $pageNumber = $this->getRequest()->getPost('pageNumber');
                    $returnArray['totalCount'] = $orders->getSize();
                    $orders->setPageSize(16)->setCurPage($pageNumber);
                }
                $allOrders = [];
                foreach ($orders as $key => $_order) {
                    $eachOrder = [];
                    $eachOrder['id'] = $key;
                    $eachOrder['order_id'] = $_order->getRealOrderId();
                    $eachOrder['date'] = $this->_helperCatalog
                        ->formatDate($_order->getCreatedAt());
                    $eachOrder['ship_to'] = $_order->getShippingAddress() ? $this->_helperCatalog->stripTags(
                        $_order->getShippingAddress()->getName()
                    ) : ' ';
                    $eachOrder['order_total'] = $this->_helperCatalog
                        ->stripTags(
                            $_order->formatPrice(
                                $_order->getGrandTotal()
                            )
                        );
                    $eachOrder['status'] = $_order->getStatusLabel();
                    if ($this->canReorder($_order) == 1) {
                        $eachOrder['canReorder'] = $this->canReorder($_order);
                    } else {
                        $eachOrder['canReorder'] = 0;
                    }
                    $allOrders[] = $eachOrder;
                }
                $returnArray['allOrders'] = $allOrders;

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);
                $returnArray['success'] = 1;

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
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
