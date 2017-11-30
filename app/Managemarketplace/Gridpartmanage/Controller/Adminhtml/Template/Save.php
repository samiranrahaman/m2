<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Managemarketplace\Gridpartmanage\Controller\Adminhtml\Template;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Managemarketplace\Gridpartmanage\Controller\Adminhtml\Template
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
   
         $template = $this->_objectManager->create('Managemarketplace\Gridpartmanage\Model\Template');
        $id = (int)$request->getParam('id');

        if ($id) {
            $template->load($id);
        }
       // $template->getGridpart2templateId();exit;
	   //$template->getId();exit;
        try {
            $data = $request->getParams();
           
            
            
            
             $template->setData('name',
                $request->getParam('name')
            )->setData('productbanner',
                $request->getParam('productbanner')
            )->setData('servicebanner',
                $request->getParam('servicebanner')
            )->setData('homeproductcategory',
                $request->getParam('homeproductcategory')
            )->setData('featuredproduct',
                $request->getParam('featuredproduct')
            )->setData('categorymenu',
                $request->getParam('categorymenu')
            )->setData('featuredservices',
                $request->getParam('featuredservices')
            )->setData('headeraddtocart',
                $request->getParam('headeraddtocart')
            ); 


            $template->save();

            $this->messageManager->addSuccess(__('The Setting has been saved1.'));
            $this->_getSession()->setFormData(false);

            
        } catch (LocalizedException $e) {
            
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('gridpartmanage_template_form_data', $this->getRequest()->getParams());
           // return $resultRedirect->setPath('*/*/edit', ['id' => $template->getGridpart2templateId(), '_current' => true]);
		   return $resultRedirect->setPath('*/*/edit', ['id' => $template->getId(), '_current' => true]);
        } catch (\Exception $e) {
            
            $this->messageManager->addException($e, __('Something went wrong while saving this template.'));
            $this->_getSession()->setData('gridpartmanage_template_form_data', $this->getRequest()->getParams());
           // return $resultRedirect->setPath('*/*/edit', ['id' => $template->getGridpart2templateId(), '_current' => true]);
		   return $resultRedirect->setPath('*/*/edit', ['id' => $template->getId(), '_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
