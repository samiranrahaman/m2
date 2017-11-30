<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Addsubscriptionplans\Gridpartsubscriptionplans\Controller\Adminhtml\Template;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;

class Save extends \Addsubscriptionplans\Gridpartsubscriptionplans\Controller\Adminhtml\Template
{
    /**
     * Save Newsletter Template
     *
     * @return //
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
   
        $template = $this->_objectManager->create('Addsubscriptionplans\Gridpartsubscriptionplans\Model\Template');
        $id = (int)$request->getParam('id');

        if ($id) {
            $template->load($id);
        }
       // $template->getGridpart2templateId();exit;
	   //$template->getId();exit;
        try {
            $data = $request->getParams();
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
            )->setData('price',
                $request->getParam('price')
            )->setData('ongoingfee',
                $request->getParam('ongoingfee')
            )->setData('status',
                $request->getParam('status')
			)->setData('perannually',
                $request->getParam('perannually')
			)->setData('permonth',
			    $request->getParam('permonth')
			)->setData('ba',
			    $request->getParam('ba')
			)->setData('onba',
			    $request->getParam('onba')
			)->setData('ed',
			    $request->getParam('ed')
			)->setData('paa',
			    $request->getParam('paa')
			)->setData('ct',
			    $request->getParam('ct')
			)->setData('alogo',
			    $request->getParam('alogo')
			)->setData('aypsp',
			    $request->getParam('aypsp')
			)->setData('elc',
			    $request->getParam('elc')
			)->setData('dmt',
			    $request->getParam('dmt')
			)->setData('cam',
			    $request->getParam('cam')
			)->setData('cvvc',
			    $request->getParam('cvvc')
			)->setData('crc',
			    $request->getParam('crc')
			)->setData('cd',
			    $request->getParam('cd')
            );	
            $template->save();
			
			

            $this->messageManager->addSuccess(__('The App Data has been saved.'));
            $this->_getSession()->setFormData(false);

            
        } catch (LocalizedException $e) {
            
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('gridpartsubscriptionplans_template_form_data', $this->getRequest()->getParams());
           // return $resultRedirect->setPath('*/*/edit', ['id' => $template->getGridpart2templateId(), '_current' => true]);
		  return $resultRedirect->setPath('*/*/edit', ['id' => $template->getSubscriptionplanid(), '_current' => true]);
        } catch (Exception $e) {
            
            $this->messageManager->addException($e, __('Something went wrong while saving this template.'));
            $this->_getSession()->setData('gridpartsubscriptionplans_template_form_data', $this->getRequest()->getParams());
           // return $resultRedirect->setPath('*/*/edit', ['id' => $template->getGridpart2templateId(), '_current' => true]);
		   return $resultRedirect->setPath('*/*/edit', ['id' => $template->getSubscriptionplanid(), '_current' => true]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
