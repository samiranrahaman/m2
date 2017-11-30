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

use Custom\Chharo\Controller\RegistryConstants;
use Custom\Chharo\Api\Data\NotificationInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Custom\Chharo\Controller\Adminhtml\Notification
{
    /**
     * Notification edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $notificationId = $this->initCurrentNotification();
        $isExistingNotification = (bool)$notificationId;
        if ($isExistingNotification) {
            try {
                $chharoDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo'
                );
                $notificationDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo/notification'
                );
                if (!file_exists($chharoDirPath)) {
                    mkdir($chharoDirPath, 0777, true);
                }
                if (!file_exists($notificationDirPath)) {
                    mkdir($notificationDirPath, 0777, true);
                }
                $baseTmpPath = 'chharo/notification/';
                $target = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).$baseTmpPath;
                $notificationData = [];
                $notificationData['chharo_notification'] = [];
                $notification = null;
                $notification = $this->_notificationRepository->getById($notificationId);
                $result = $notification->getData();
                if (count($result)) {
                    $notificationData['chharo_notification'] = $result;
                    $notificationData['chharo_notification']['filename'] = [];
                    $notificationData['chharo_notification']['filename'][0] = [];
                    $notificationData['chharo_notification']['filename'][0]['name'] = $result['filename'];
                    $notificationData['chharo_notification']['filename'][0]['url'] =
                    $target.$result['filename'];
                    $filePath = $this->_mediaDirectory->getAbsolutePath(
                        $baseTmpPath
                    ).$result['filename'];
                    if (file_exists($filePath)) {
                        $notificationData['chharo_notification']['filename'][0]['size'] =
                        filesize($filePath);
                    } else {
                        $notificationData['chharo_notification']['filename'][0]['size'] = 0;
                    }
                    $notificationData['chharo_notification'][NotificationInterface::ID] = $notificationId;

                    $this->_getSession()->setNotificationFormData($notificationData);
                } else {
                    $this->messageManager->addError(
                        __('Requested notification doesn\'t exist')
                    );
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('chharo/notification/index');
                    return $resultRedirect;
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while editing the notification.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('chharo/notification/index');
                return $resultRedirect;
            }
        }

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_Chharo::notification');
        $this->prepareDefaultNotificationTitle($resultPage);
        $resultPage->setActiveMenu('Custom_Chharo::notification');
        if ($isExistingNotification) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Item with id %1', $notificationId));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Notification'));
        }
        return $resultPage;
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

    /**
     * Prepare notification default title
     *
     * @param  \Magento\Backend\Model\View\Result\Page $resultPage
     * @return void
     */
    protected function prepareDefaultNotificationTitle(
        \Magento\Backend\Model\View\Result\Page $resultPage
    ) {
        $resultPage->getConfig()->getTitle()->prepend(__('Notification'));
    }
}
