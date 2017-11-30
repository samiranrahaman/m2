<?php
/**
 * Custom Software.
 *
 * @category Custom
 *
 * @author    Custom
 * @copyright Copyright (c) 2010-2017 Custom Software Private Limited (https://Custom.com)
 * @license   https://store.Custom.com/license.html
 */
namespace Custom\Chharo\Model\Category;

class Tree extends \Magento\Catalog\Model\Category\Tree
{
    /**
     * @param \Magento\Framework\Data\Tree\Node $node
     * @param int                               $depth
     * @param int                               $currentLevel
     * @return \Magento\Catalog\Api\Data\CategoryTreeInterface[]|[]
     */
    protected function getChildren($node, $depth, $currentLevel)
    {
        if ($node->hasChildren()) {
            $children = [];
            foreach ($node->getChildren() as $child) {
                if ($depth !== null && $depth <= $currentLevel) {
                    break;
                }
                if($child->getIsActive()) {
                    $children[] = $this->getTree($child, $depth, $currentLevel + 1);
                }
            }
            return $children;
        }
        return [];
    }
}