<?php
namespace Adbanner\Gridpartbanner\Controller\Adminhtml;
abstract class Template extends \Magento\Backend\App\Action
{
    /**
     * Retrieve well-formed admin user data from the form input
     *
     * @param array $data
     * @return array
     */
  
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Adbanner_Gridpartbanner::gridpartbanner_template');
    }
     
}