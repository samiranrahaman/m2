<?php
/**
 * Custom Software.
 *
 * @category  Custom
 * @package   Custom_Chharo
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */

namespace Custom\Chharo\Block\Adminhtml\Edit\Categoryimages;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Custom\Chharo\Block\Adminhtml\Edit\GenericButton;

/**
 * Class DeleteButton.
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $bannnerimageId = $this->getCategoryimagesId();
        $data = [];
        if ($bannnerimageId) {
            $data = [
                'label' => __('Delete Category Image'),
                'class' => 'delete',
                'id' => 'categoryimages-edit-delete-button',
                'data_attribute' => [
                    'url' => $this->getDeleteUrl(),
                ],
                'on_click' => '',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', ['id' => $this->getCategoryimagesId()]);
    }
}
