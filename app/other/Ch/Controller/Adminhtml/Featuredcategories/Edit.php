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

namespace Custom\Chharo\Controller\Adminhtml\Featuredcategories;

use Custom\Chharo\Controller\RegistryConstants;
use Custom\Chharo\Api\Data\FeaturedcategoriesInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Custom\Chharo\Controller\Adminhtml\Featuredcategories
{
    /**
     * Featuredcategories edit action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $featuredcategoriesId = $this->initCurrentFeaturedcategories();
        $isExistingFeaturedcategories = (bool)$featuredcategoriesId;
        if ($isExistingFeaturedcategories) {
            try {
                $chharoDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo'
                );
                $featuredcategoriesDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo/featuredcategories'
                );
                if (!file_exists($chharoDirPath)) {
                    mkdir($chharoDirPath, 0777, true);
                }
                if (!file_exists($featuredcategoriesDirPath)) {
                    mkdir($featuredcategoriesDirPath, 0777, true);
                }
                $baseTmpPath = 'chharo/featuredcategories/';
                $target = $this->storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ).$baseTmpPath;
                $featuredcategoriesData = [];
                $featuredcategoriesData['chharo_featuredcategories'] = [];
                $featuredcategories = null;
                $featuredcategories = $this->_featuredcategoriesRepository->getById(
                    $featuredcategoriesId
                );
                $result = $featuredcategories->getData();
                if (count($result)) {
                    $featuredcategoriesData['chharo_featuredcategories'] = $result;
                    $featuredcategoriesData['chharo_featuredcategories']['filename'] = [];
                    $featuredcategoriesData['chharo_featuredcategories']['filename'][0] = [];
                    $featuredcategoriesData['chharo_featuredcategories']['filename'][0]['name'] =
                    $result['filename'];
                    $featuredcategoriesData['chharo_featuredcategories']['filename'][0]['url'] =
                    $target.$result['filename'];
                    $filePath = $this->_mediaDirectory->getAbsolutePath(
                        $baseTmpPath
                    ).$result['filename'];
                    if (file_exists($filePath)) {
                        $featuredcategoriesData['chharo_featuredcategories']['filename'][0]['size'] =
                        filesize($filePath);
                    } else {
                        $featuredcategoriesData['chharo_featuredcategories']['filename'][0]['size'] = 0;
                    }
                    $featuredcategoriesData['chharo_featuredcategories'][FeaturedcategoriesInterface::ID]=
                    $featuredcategoriesId;

                    $this->_getSession()->setFeaturedcategoriesFormData($featuredcategoriesData);
                } else {
                    $this->messageManager->addError(
                        __('Requested featuredcategories doesn\'t exist')
                    );
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('chharo/featuredcategories/index');
                    return $resultRedirect;
                }
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while editing the featuredcategories.')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('chharo/featuredcategories/index');
                return $resultRedirect;
            }
        }

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Custom_Chharo::featuredcategories');
        $this->prepareDefaultFeaturedcategoriesTitle($resultPage);
        $resultPage->setActiveMenu('Custom_Chharo::featuredcategories');
        if ($isExistingFeaturedcategories) {
            $resultPage->getConfig()->getTitle()->prepend(
                __('Edit Item with id %1', $featuredcategoriesId)
            );
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('New Featuredcategories'));
        }
        return $resultPage;
    }

    /**
     * Featuredcategories initialization
     *
     * @return string featuredcategories id
     */
    protected function initCurrentFeaturedcategories()
    {
        $featuredcategoriesId = (int)$this->getRequest()->getParam('id');

        if ($featuredcategoriesId) {
            $this->_coreRegistry->register(
                RegistryConstants::CURRENT_FEATUREDCATEGORIES_ID,
                $featuredcategoriesId
            );
        }

        return $featuredcategoriesId;
    }

    /**
     * Prepare featuredcategories default title
     *
     * @param  \Magento\Backend\Model\View\Result\Page $resultPage
     * @return void
     */
    protected function prepareDefaultFeaturedcategoriesTitle(
        \Magento\Backend\Model\View\Result\Page $resultPage
    ) {
        $resultPage->getConfig()->getTitle()->prepend(__('Featuredcategories'));
    }
}
