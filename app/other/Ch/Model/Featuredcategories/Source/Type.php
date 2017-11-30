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

namespace Custom\Chharo\Model\Featuredcategories\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type.
 */
class Type implements OptionSourceInterface
{
    /**
     * @var \Custom\Chharo\Model\Featuredcategories
     */
    protected $_chharoFeaturedcategories;

    /**
     * Constructor.
     *
     * @param \Custom\Chharo\Model\Featuredcategories $chharoFeaturedcategories
     */
    public function __construct(
        \Custom\Chharo\Model\Featuredcategories $chharoFeaturedcategories
    ) {
        $this->_chharoFeaturedcategories = $chharoFeaturedcategories;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->_chharoFeaturedcategories->getAvailableTypes();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $options;
    }
}
