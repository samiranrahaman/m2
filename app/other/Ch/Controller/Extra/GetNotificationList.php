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

namespace Custom\Chharo\Controller\Extra;

/**
 * Chharo API Extra controller.
 */
class GetNotificationList extends AbstractChharo
{
    /**
     * execute get notification list.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getPost()) {
            $storeId = $this->getRequest()->getPost('storeId');
            $returnArray = [];
            if ($storeId == '') {
                $storeId = $this->_helper->getCurrentStoreId();
            }
            try {
                $storeIds = [];
                array_push($storeIds, 0);
                array_push($storeIds, $storeId);
                /**
                 * $initialEnvironmentInfo store emulation start.
                 */
                $initialEnvironmentInfo = $this->_emulate->startEnvironmentEmulation($storeId);

                $notificationCollection = $this->_chharoNotification
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('status', 1)
                    ->addFieldToFilter(
                        'store_id',
                        [
                        'in' => $storeIds,
                        ]
                    )
                    ->setOrder('updated_at', 'DESC');
                foreach ($notificationCollection as $notification) {
                    $eachNotification = [];
                    $eachNotification['id'] = $notification->getId();
                    $eachNotification['content'] = $notification->getContent();
                    $eachNotification['notificationType'] = $notification->getType();
                    $eachNotification['title'] = $notification->getTitle();
                    if ($notification->getType() == 'category') {
                        //for category
                        $category = $this->_categoryFactory
                            ->create()
                            ->load($notification->getProCatId());
                        $eachNotification['categoryName'] = $category->getName();
                        $eachNotification['categoryId'] = $notification->getProCatId();
                    } elseif ($notification->getType() == 'product') {
                        //for product
                        $product = $this->_productFactory
                            ->create()
                            ->load($notification->getProCatId());
                        $eachNotification['productName'] = $product->getName();
                        $eachNotification['productType'] = $product->getTypeId();
                        $eachNotification['productId'] = $notification->getProCatId();
                    }
                    $returnArray[] = $eachNotification;
                }

                /*
                 * stop store emulation
                 */
                $this->_emulate->stopEnvironmentEmulation($initialEnvironmentInfo);

                return $this->getJsonResponse($returnArray);
            } catch (\Exception $e) {
                $this->createLog('Chharo Exception log for class: '.get_class($this).' : '.$e->getMessage(), (array) $e->getTrace());
                $returnArray['success'] = 0;
                $returnArray['message'] = __('invalid request');

                return $this->getJsonResponse($returnArray);
            }
        } else {
            $returnArray['success'] = 0;
            $returnArray['message'] = __('Invalid Request.');

            return $this->getJsonResponse($returnArray);
        }
    }
}
