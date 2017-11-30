<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Adbanner\Gridpartbanner\Controller\Adminhtml\Template;

class Grid extends \Adbanner\Gridpartbanner\Controller\Adminhtml\Template
{
    /**
     * Managing newsletter grid
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
