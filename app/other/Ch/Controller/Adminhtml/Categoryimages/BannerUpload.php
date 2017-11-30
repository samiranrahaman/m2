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

use Magento\Framework\Controller\ResultFactory;

class BannerUpload extends \Custom\Chharo\Controller\Adminhtml\Categoryimages
{
    public function execute()
    {
        $result = [];
        if ($this->getRequest()->isPost()) {
            try {
                $fields = $this->getRequest()->getParams();
                $chharoDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo'
                );
                $categoryimagesDirPath = $this->_mediaDirectory->getAbsolutePath(
                    'chharo/categoryimages'
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
                if (!file_exists($categoryBannerImageDirPath)) {
                    mkdir($categoryBannerImageDirPath, 0777, true);
                }
                $baseTmpPath = 'chharo/categoryimages/banner/';
                $target = $this->_mediaDirectory->getAbsolutePath($baseTmpPath);
                try {
                    /**
 * @var $uploader \Magento\MediaStorage\Model\File\Uploader 
*/
                    $uploader = $this->_fileUploaderFactory->create(
                        ['fileId' => 'chharo_categoryimages[banner]']
                    );
                    $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                    $uploader->setAllowRenameFiles(true);
                    $result = $uploader->save($target);
                    if (!$result) {
                        $result = [
                            'error' => __('File can not be saved to the destination folder.'),
                            'errorcode' => ''
                        ];
                    }

                    if (isset($result['file'])) {
                        try {
                            $result['tmp_name'] = str_replace('\\', '/', $result['tmp_name']);
                            $result['path'] = str_replace('\\', '/', $result['path']);
                            $result['url'] = $this->storeManager
                                ->getStore()
                                ->getBaseUrl(
                                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                                ) . $this->getFilePath($baseTmpPath, $result['file']);
                            $result['name'] = $result['file'];
                        } catch (\Exception $e) {
                            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
                        }
                    }

                    $result['cookie'] = [
                        'name' => $this->_getSession()->getName(),
                        'value' => $this->_getSession()->getSessionId(),
                        'lifetime' => $this->_getSession()->getCookieLifetime(),
                        'path' => $this->_getSession()->getCookiePath(),
                        'domain' => $this->_getSession()->getCookieDomain(),
                    ];
                } catch (\Exception $e) {
                    $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
                }
            } catch (\Exception $e) {
                $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    /**
     * Retrieve path
     *
     * @param string $path
     * @param string $imageName
     *
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }
}
