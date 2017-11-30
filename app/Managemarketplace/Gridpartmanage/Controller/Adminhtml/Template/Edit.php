<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Managemarketplace\Gridpartmanage\Controller\Adminhtml\Template;

class Edit extends \Managemarketplace\Gridpartmanage\Controller\Adminhtml\Template
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
        $model = $this->_objectManager->create('Managemarketplace\Gridpartmanage\Model\Template');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        $this->_coreRegistry->register('_gridpartmanage_template', $model);

        $this->_view->loadLayout();
        $this->_setActiveMenu('Managemarketplace_Gridpartmanage::gridpartmanage_template');

        if ($model->getId()) {
            $breadcrumbTitle = __('Edit Setting');
            $breadcrumbLabel = $breadcrumbTitle;
        } else {
            $breadcrumbTitle = __('New Setting');
            $breadcrumbLabel = __('Create  Setting');
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__(' Setting'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTemplateId() : __('New Setting')
        );

        $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

        // restore data
        $values = $this->_getSession()->getData('gridpartmanage_template_form_data', true);
        if ($values) {
            $model->addData($values);
        }

        $this->_view->renderLayout();
    }
}
