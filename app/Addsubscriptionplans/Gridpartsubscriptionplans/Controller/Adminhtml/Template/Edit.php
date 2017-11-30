<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Addsubscriptionplans\Gridpartsubscriptionplans\Controller\Adminhtml\Template;

class Edit extends \Addsubscriptionplans\Gridpartsubscriptionplans\Controller\Adminhtml\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Edit Newsletter Template
     *
     * @return void
     */
    public function execute()
    {
        $model = $this->_objectManager->create('Addsubscriptionplans\Gridpartsubscriptionplans\Model\Template');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        $this->_coreRegistry->register('_gridpartscriptionplans_template', $model);

        $this->_view->loadLayout();
        $this->_setActiveMenu('Addsubscriptionplans_Gridpartsubscriptionplans::gridpartsubscriptionplans_template');

        if ($model->getId()) {
            $breadcrumbTitle = __('Edit Subscriptionplan');
            $breadcrumbLabel = $breadcrumbTitle;
        } else {
            $breadcrumbTitle = __('New Subscriptionplan');
            $breadcrumbLabel = __('Create  Subscriptionplan');
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__(' Subscriptionplan'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTemplateId() : __('New Subscriptionplan')
        );

        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        // restore data
        $values = $this->_getSession()->getData('gridpart2_template_form_data', true);
        if ($values) {
            $model->addData($values);
        }

        $this->_view->renderLayout();
    }
}
