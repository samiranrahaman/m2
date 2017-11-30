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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends \Custom\Chharo\Controller\Adminhtml\Bannerimage
{
    /**
     * Save banner action.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $returnToEdit = false;
        $originalRequestData = $this->getRequest()->getPostValue();
        $bannerimageId = isset($originalRequestData['chharo_bannerimage']['id'])
            ? $originalRequestData['chharo_bannerimage']['id']
            : null;
        if ($originalRequestData) {
            try {
                $bannerimageData = $originalRequestData['chharo_bannerimage'];
                $bannerimageData['filename'] = $this->getBannerImageName($bannerimageData);
                $bannerimageData['store_id'] = $this->getBannerStoreId($bannerimageData);
                $request = $this->getRequest();
                $isExistingBanner = (bool) $bannerimageId;
                $bannerimage = $this->bannerimageDataFactory->create();
                if ($isExistingBanner) {
                    $currentBanner = $this->_bannerimageRepository->getById($bannerimageId);
                    $bannerimageData['id'] = $bannerimageId;
                }
                $bannerimageData['updated_at'] = $this->_date->gmtDate();
                if (!$isExistingBanner) {
                    $bannerimageData['created_at'] = $this->_date->gmtDate();
                }
                $bannerimage->setData($bannerimageData);

                // Save banner
                if ($isExistingBanner) {
                    $this->_bannerimageRepository->save($bannerimage);
                } else {
                    $bannerimage = $this->_bannerimageRepository->save($bannerimage);
                    $bannerimageId = $bannerimage->getId();
                }
                $this->_getSession()->unsBannerimageFormData();
                // Done Saving bannerimage, finish save action
                $this->_coreRegistry->register(RegistryConstants::CURRENT_BANNER_ID, $bannerimageId);
                $this->messageManager->addSuccess(__('You saved the banner.'));
                $returnToEdit = (bool) $this->getRequest()->getParam('back', false);
            } catch (\Magento\Framework\Validator\Exception $exception) {
                $messages = $exception->getMessages();
                if (empty($messages)) {
                    $messages = $exception->getMessage();
                }
                $this->_addSessionErrorMessages($messages);
                $this->_getSession()->setBannerimageFormData($originalRequestData);
                $returnToEdit = true;
            } catch (\Exception $exception) {
                $this->messageManager->addException(
                    $exception,
                    __('Something went wrong while saving the banner. %1', $exception->getMessage())
                );
                $this->_getSession()->setBannerimageFormData($originalRequestData);
                $returnToEdit = true;
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($returnToEdit) {
            if ($bannerimageId) {
                $resultRedirect->setPath(
                    'chharo/bannerimage/edit',
                    ['id' => $bannerimageId, '_current' => true]
                );
            } else {
                $resultRedirect->setPath(
                    'chharo/bannerimage/new',
                    ['_current' => true]
                );
            }
        } else {
            $resultRedirect->setPath('chharo/bannerimage/index');
        }

        return $resultRedirect;
    }

    private function getBannerImageName($bannerimageData)
    {
        if (isset($bannerimageData['filename'][0]['name'])) {
            if (isset($bannerimageData['filename'][0]['name'])) {
                return $bannerimageData['filename'] = $bannerimageData['filename'][0]['name'];
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please upload banner image.')
                );
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please upload banner image.')
            );
        }
    }

    private function getBannerStoreId($bannerimageData)
    {
        if (isset($bannerimageData['store_id'])) {
            return $bannerimageData['store_id'] = implode(',', $bannerimageData['store_id']);
        } else {
            return $bannerimageData['store_id'] = 0;
        }
    }
}
