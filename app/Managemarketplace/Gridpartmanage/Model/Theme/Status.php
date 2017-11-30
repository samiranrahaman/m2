<?php
namespace Managemarketplace\Gridpartmanage\Model\Theme;



class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Ashsmith\Blog\Model\Post
     */
    protected $_template;

    /**
     * Constructor
     *
     * @param \Managemarketplace\Gridpartmanage\Model\Template $template
     */
    public function __construct(\Managemarketplace\Gridpartmanage\Model\Template $template)
    {
        $this->_template =  $template;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->_template->getAvailableStatuses();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
