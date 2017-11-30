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

namespace Custom\Chharo\Controller\Adminhtml\Categoryimages;

use Custom\Chharo\Controller\RegistryConstants;
use Custom\Chharo\Api\Data\CategoryimagesInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Custom\Chharo\Controller\Adminhtml\Categoryimages
{
    /**
     * Categoryimages edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $categoryimagesId = $this->initCurrentCategoryimages();
        $isExistingCategoryimages = (bool)$categoryimagesId;
        if ($isExistingCategoryimages) {
            try {
                $chharoDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo'
                );
                $categoryimagesDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo/categoryimages'
                );
                $categoryIconImageDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo/categoryimages/icon'
                );
                $categoryBannerImageDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo/categoryimages/banner'
                );
                if (!file_exists($chharoDirPath)) {
                    mkdir($chharoDirPath, 0777, true);
                }
                if (!file_exists($categoryimagesDirPath)) {
                    mkdir($categoryimagesDirPath, 0777, true);
                }
                if (!file_exists($categoryIconImageDirPath)) {
                    mkdir($categoryIconImageDirPath, 0777, true);
                }
                if (!file_exists($categoryBannerImageDirPath)) {
                    mkdir($categoryBannerImageDirPath, 0777, true);
                }

                $iconBaseTmpPath = 'chharo/categoryimages/icon/';
                $iconTarget = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).$iconBaseTmpPath;

                $bannerBaseTmpPath = 'chharo/categoryimages/banner/';
                $bannerTarget = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).$bannerBaseTmpPath;

                $categoryimagesData = [];
                $categoryimagesData['chharo_categoryimages'] = [];
                $categoryimages = null;
                $categoryimages = $this->_categoryimagesRepository->getById(
                    $categoryimagesId
                );
                $result = $categoryimages->getData();
                if (count($result)) {
                    $categoryimagesData['chharo_categoryimages'] = $result;

                    /* *
                     * for icon image
                     */
                    $categoryimagesData['chharo_categoryimages']['icon'] = [];
                    $categoryimagesData['chharo_categoryimages']['icon'][0] = [];
                    $categoryimagesData['chharo_categoryimages']['icon'][0]['name'] =
                    $result['icon'];
                    $categoryimagesData['chharo_categoryimages']['icon'][0]['url'] =
                    $iconTarget.$result['icon'];
                    $iconFilePath = $this->_mediaDirectory->getAbsolutePath(
                        $iconBaseTmpPath
                    ).$result['icon'];

                    if (file_exists($iconFilePath)) {
                        $categoryimagesData['chharo_categoryimages']['icon'][0]['size'] =
                        filesize($iconFilePath);
                    } else {
                        $categoryimagesData['chharo_categoryimages']['icon'][0]['size'] = 0;
                    }

                    /* *
                     * for banner image
                     */
                    $categoryimagesData['chharo_categoryimages']['banner'] = [];
                    $categoryimagesData['chharo_categoryimages']['banner'][0] = [];
                    $categoryimagesData['chharo_categoryimages']['banner'][0]['name'] =
                    $result['banner'];
                    $categoryimagesData['chharo_categoryimages']['banner'][0]['url'] =
                    $bannerTarget.$result['banner'];
                    $bannerFilePath = $this->_mediaDirectory->getAbsolutePath(
                        $bannerBaseTmpPath
                    ).$result['banner'];
                    if (file_exists($bannerFilePath)) {
                        $categoryimagesData['chharo_categoryimages']['banner'][0]['size'] =
                        filesize($bannerFilePath);
                    } else {
                        $categoryimagesData['chharo_categoryimages']['banner'][0]['size'] = 0;
                    }

                    $categoryimagesData['chharo_categoryimages'][CategoryimagesInterface::ID]=
                    $categoryimagesId;

                    $this->_getSession()->setCategoryimagesFormData($categoryimagesData);
                } else {
                    $this->messageManager->addError(
                        __('Requested categoryimages doesn\'t exist')
                    );
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('chharo/categoryimages/index');
                    return $resultRedirect;
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while editing the category image.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('chharo/categoryimages/index');
                return $resultRedirect;
            }
        }

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_Chharo::categoryimages');
        $this->prepareDefaultCategoryimagesTitle($resultPage);
        $resultPage->setActiveMenu('Custom_Chharo::categoryimages');
        if ($isExistingCategoryimages) {
            $resultPage->getConfig()->getTitle()->prepend(
                __('Edit Item with id %1', $categoryimagesId)
            );
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Category Image'));
        }
        return $resultPage;
    }

    /**
     * Categoryimages initialization
     *
     * @return string categoryimages id
     */
    protected function initCurrentCategoryimages()
    {
        $categoryimagesId = (int)$this->getRequest()->getParam('id');

        if ($categoryimagesId) {
            $this->_coreRegistry->register(
                RegistryConstants::CURRENT_CATEGORYIMAGES_ID,
                $categoryimagesId
            );
        }

        return $categoryimagesId;
    }

    /**
     * Prepare categoryimages default title
     *
     * @param  \Magento\Backend\Model\View\Result\Page $resultPage
     * @return void
     */
    protected function prepareDefaultCategoryimagesTitle(
        \Magento\Backend\Model\View\Result\Page $resultPage
    ) {
        $resultPage->getConfig()->getTitle()->prepend(__('Category Image'));
    }
}
