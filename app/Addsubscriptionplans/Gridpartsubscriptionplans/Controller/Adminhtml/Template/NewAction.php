<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Addsubscriptionplans\Gridpartsubscriptionplans\Controller\Adminhtml\Template;

class NewAction extends \Addsubscriptionplans\Gridpartsubscriptionplans\Controller\Adminhtml\Template
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
