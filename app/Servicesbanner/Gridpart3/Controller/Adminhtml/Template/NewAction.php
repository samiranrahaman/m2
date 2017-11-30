<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Servicesbanner\Gridpart3\Controller\Adminhtml\Template;

class NewAction extends \Servicesbanner\Gridpart3\Controller\Adminhtml\Template
{
    /**
     * Create new Newsletter Template
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
