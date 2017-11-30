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

namespace Custom\Chharo\Controller\Adminhtml\Notification;

class Validate extends \Custom\Chharo\Controller\Adminhtml\Notification
{
    /**
     * Customer validation
     *
     * @param  \Magento\Framework\DataObject $response
     * @return NotificationInterface|null
     */
    protected function _validateNotification($response)
    {
        $notification = null;
        $errors = [];

        try {
            /**
 * @var NotificationInterface $notification 
*/
            $notification = $this->notificationDataFactory->create();

            $data = $this->getRequest()->getParams();
            $dataResult = $data['chharo_notification'];
            $errors = [];
            if (!isset($dataResult['filename'][0]['name'])) {
                $errors[] =  __('Please upload notification image.');
            }
            if (isset($dataResult['sort_order'])) {
                if (!is_numeric($dataResult['sort_order'])) {
                    $errors[] =  __('Sort order should be a number.');
                }
            } else {
                $errors[] =  __('Sort order field can not be blank.');
            }
            if (isset($dataResult['type']) && isset($dataResult['pro_cat_id'])) {
                if ($dataResult['type'] == 'product') {
                    try {
                        $this->productRepositoryInterface->getById($dataResult['pro_cat_id']);
                    } catch (\Exception $exception) {
                        $errors[] =  __('Requested product doesn\'t exist');
                    }
                }
                if ($dataResult['type'] == 'category') {
                    try {
                        $this->categoryRepositoryInterface->get($dataResult['pro_cat_id']);
                    } catch (\Exception $exception) {
                        $errors[] =  __('Requested category doesn\'t exist');
                    }
                }
            } else {
                $errors[] =  __('Notification type or id should be set.');
            }
        } catch (\Magento\Framework\Validator\Exception $exception) {
            $exceptionMsg = $exception->getMessages(
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
            /**
             * @var $error Error
             */
            foreach ($exceptionMsg as $error) {
                $errors[] = $error->getText();
            }
        }

        if ($errors) {
            $messages = $response->hasMessages() ? $response->getMessages() : [];
            foreach ($errors as $error) {
                $messages[] = $error;
            }
            $response->setMessages($messages);
            $response->setError(1);
        }

        return $notification;
    }

    /**
     * AJAX customer validation action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(0);

        $notification = $this->_validateNotification($response);

        $resultJson = $this->resultJsonFactory->create();
        if ($response->getError()) {
            $response->setError(true);
            $response->setMessages($response->getMessages());
        }

        $resultJson->setData($response);
        return $resultJson;
    }
}
