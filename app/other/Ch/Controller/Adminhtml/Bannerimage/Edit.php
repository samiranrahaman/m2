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

namespace Custom\Chharo\Controller\Adminhtml\Bannerimage;

use Custom\Chharo\Controller\RegistryConstants;
use Custom\Chharo\Api\Data\BannerimageInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Custom\Chharo\Controller\Adminhtml\Bannerimage
{
    /**
     * Banner edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $bannerimageId = $this->initCurrentBanner();
        $isExistingBanner = (bool)$bannerimageId;
        if ($isExistingBanner) {
            try {
                $chharoDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo'
                );
                $bannerimageDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo/bannerimages'
                );
                if (!file_exists($chharoDirPath)) {
                    mkdir($chharoDirPath, 0777, true);
                }
                if (!file_exists($bannerimageDirPath)) {
                    mkdir($bannerimageDirPath, 0777, true);
                }
                $baseTmpPath = 'chharo/bannerimages/';
                $target = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).$baseTmpPath;
                $bannerimageData = [];
                $bannerimageData['chharo_bannerimage'] = [];
                $bannerimage = null;
                $bannerimage = $this->_bannerimageRepository->getById($bannerimageId);
                $result = $bannerimage->getData();
                if (count($result)) {
                    $bannerimageData['chharo_bannerimage'] = $result;
                    $bannerimageData['chharo_bannerimage']['filename'] = [];
                    $bannerimageData['chharo_bannerimage']['filename'][0] = [];
                    $bannerimageData['chharo_bannerimage']['filename'][0]['name'] = $result['filename'];
                    $bannerimageData['chharo_bannerimage']['filename'][0]['url'] =
                    $target.$result['filename'];
                    $filePath = $this->_mediaDirectory->getAbsolutePath(
                        $baseTmpPath
                    ).$result['filename'];
                    if (file_exists($filePath)) {
                        $bannerimageData['chharo_bannerimage']['filename'][0]['size'] =
                        filesize($filePath);
                    } else {
                        $bannerimageData['chharo_bannerimage']['filename'][0]['size'] = 0;
                    }
                    $bannerimageData['chharo_bannerimage'][BannerimageInterface::ID] = $bannerimageId;

                    $this->_getSession()->setBannerimageFormData($bannerimageData);
                } else {
                    $this->messageManager->addError(
                        __('Requested banner doesn\'t exist')
                    );
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('chharo/bannerimage/index');
                    return $resultRedirect;
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while editing the banner.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('chharo/bannerimage/index');
                return $resultRedirect;
            }
        }

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_Chharo::bannerimage');
        $this->prepareDefaultBannerTitle($resultPage);
        $resultPage->setActiveMenu('Custom_Chharo::bannerimage');
        if ($isExistingBanner) {
            $resultPage->getConfig()->getTitle()->prepend(__('Edit Item with id %1', $bannerimageId));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Banner'));
        }
        return $resultPage;
    }

    /**
     * Banner initialization
     *
     * @return string banner id
     */
    protected function initCurrentBanner()
    {
        $bannerimageId = (int)$this->getRequest()->getParam('id');

        if ($bannerimageId) {
            $this->_coreRegistry->register(RegistryConstants::CURRENT_BANNER_ID, $bannerimageId);
        }

        return $bannerimageId;
    }

    /**
     * Prepare banner default title
     *
     * @param  \Magento\Backend\Model\View\Result\Page $resultPage
     * @return void
     */
    protected function prepareDefaultBannerTitle(
        \Magento\Backend\Model\View\Result\Page $resultPage
    ) {
        $resultPage->getConfig()->getTitle()->prepend(__('Banner Image'));
    }
}
