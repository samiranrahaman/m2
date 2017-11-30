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

use Magento\Framework\Controller\ResultFactory;
use Custom\Chharo\Controller\RegistryConstants;
use Custom\Chharo\Api\Data\NotificationInterface;

class Delete extends \Custom\Chharo\Controller\Adminhtml\Notification
{
    /**
     * Delete notification action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $formKeyIsValid = $this->_formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__('Notification could not be deleted.'));
            return $resultRedirect->setPath('chharo/notification/index');
        }

        $notificationId = $this->initCurrentNotification();
        if (!empty($notificationId)) {
            try {
                $this->_notificationRepository->deleteById($notificationId);
                $this->messageManager->addSuccess(__('Notification has been deleted.'));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }

        /**
 * @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect 
*/
        return $resultRedirect->setPath('chharo/notification/index');
    }

    /**
     * Notification initialization
     *
     * @return string notification id
     */
    protected function initCurrentNotification()
    {
        $notificationId = (int)$this->getRequest()->getParam('id');

        if ($notificationId) {
            $this->_coreRegistry->register(RegistryConstants::CURRENT_NOTIFICATION_ID, $notificationId);
        }

        return $notificationId;
    }
}
