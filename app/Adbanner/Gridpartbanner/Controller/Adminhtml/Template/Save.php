<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adbanner\Gridpartbanner\Controller\Adminhtml\Template;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Adbanner\Gridpartbanner\Controller\Adminhtml\Template
{
    /**
     * Save Newsletter Template
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
		
        $resultRedirect = $this->resultRedirectFactory->create();
        $request = $this->getRequest();
		
		 $id =(int)$request->getParam('id');
		//print_r($_POST);exit;
		
		
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/template'));
        }
   
         $template = $this->_objectManager->create('Adbanner\Gridpartbanner\Model\Template');
        $id = (int)$request->getParam('id');

        if ($id) {
            $template->load($id);
        }
       // $template->getGridpart2templateId();exit;
	   //$template->getId();exit;
        try {
            $data = $request->getParams();
           
             if (isset($_FILES['background']) && $_FILES['background']['name'] != '') {
                try {
                    
                    $uploader = $this->_objectManager->get('Adbanner\Gridpartbanner\Model\Theme\Upload');
                    $backgroundModel = $this->_objectManager->get('Adbanner\Gridpartbanner\Model\Theme\Background');
                    $data['background'] = $uploader->uploadFileAndGetName('background', $backgroundModel->getBaseDir(), $data);
                    
                
                } catch (Exception $e) {
                    
                    $data['background'] = $_FILES['background']['name'];
                    
                }
            } else {
                $data['background'] = $data['background']['value'];
            }
                
            
            
            /* $template->setData('name',
                $request->getParam('name')
            )->setData('background',
                $data['background']
            )->setData('androidurl',
                $request->getParam('androidurl')
            )->setData('isoroidurl',
                $request->getParam('isoroidurl')
            )->setData('status',
                $request->getParam('status')
            ); */

             $template->setData('name',
                $request->getParam('name')
            )->setData('background',
                $data['background']
            )->setData('imagecaption',
                $request->getParam('imagecaption')
		    )->setData('showname',
                $request->getParam('showname')
		    )->setData('url',
                $request->getParam('url')
			)->setData('type',
                $request->getParam('type')
            )->setData('status',
                $request->getParam('status')
            );

            $template->save();

            $this->messageManager->addSuccess(__('The App Data has been saved.'));
            $this->_getSession()->setFormData(false);

            
        } catch (LocalizedException $e) {
            
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('gridpart2_template_form_data', $this->getRequest()->getParams());
           // return $resultRedirect->setPath('*/*/edit', ['id' => $template->getGridpart2templateId(), '_current' => true]);
		   return $resultRedirect->setPath('*/*/edit', ['id' => $template->getId(), '_current' => true]);
        } catch (\Exception $e) {
            
            $this->messageManager->addException($e, __('Something went wrong while saving this template.'));
            $this->_getSession()->setData('gridpart2_template_form_data', $this->getRequest()->getParams());
           // return $resultRedirect->setPath('*/*/edit', ['id' => $template->getGridpart2templateId(), '_current' => true]);
		   return $resultRedirect->setPath('*/*/edit', ['id' => $template->getId(), '_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
