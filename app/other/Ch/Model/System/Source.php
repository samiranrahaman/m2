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

namespace Custom\Chharo\Model\System;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Type.
 */
class Source implements OptionSourceInterface
{

    /**
     * Get options.
     *
     * @return array
     */
    public function toOptionArray()
    {
        
        $options = [];
        array_push($options, ['label'=>'Red-Green', 'value'=>1]);
        array_push($options, ['label'=>'Light Green', 'value'=>2]);
        array_push($options, ['label'=>'Deep Purple-Pink', 'value'=>3]);
        array_push($options, ['label'=>'Blue-Orange', 'value'=>4]);
        array_push($options, ['label'=>'Light Blue-Red', 'value'=>5]);


        return $options;
    }
}
