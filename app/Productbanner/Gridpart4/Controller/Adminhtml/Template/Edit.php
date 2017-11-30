<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Productbanner\Gridpart4\Controller\Adminhtml\Template;

class Edit extends \Productbanner\Gridpart4\Controller\Adminhtml\Template
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
        $model = $this->_objectManager->create('Productbanner\Gridpart4\Model\Template');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
        }

        $this->_coreRegistry->register('_gridpart4_template', $model);

        $this->_view->loadLayout();
        $this->_setActiveMenu('Productbanner_Gridpart4::gridpart4_template');

        if ($model->getId()) {
            $breadcrumbTitle = __('Edit Banner');
            $breadcrumbLabel = $breadcrumbTitle;
        } else {
            $breadcrumbTitle = __('New Banner');
            $breadcrumbLabel = __('Create  Banner');
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__(' Banner'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTemplateId() : __('New Banner')
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
