<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\Framework\App\Action\Action;

// @codingStandardsIgnoreFile

?>
<?php
/**
 * Product list template
 *
 * @var $block \Magento\Catalog\Block\Product\ListProduct
 */
?>
<?php
$_productCollection = $block->getLoadedProductCollection();
$_helper = $this->helper('Magento\Catalog\Helper\Output');
?>
<?php if (!$_productCollection->count()): ?>
    <div class="message info empty"><div><?php /* @escapeNotVerified */ echo __('We can\'t find products matching the selection.') ?></div></div>
<?php else: ?>
    <?php echo $block->getToolbarHtml() ?>
    <?php echo $block->getAdditionalHtml() ?>
    <?php
    if ($block->getMode() == 'grid') {
        $viewMode = 'grid';
        $image = 'category_page_grid';
        $showDescription = false;
        $templateType = \Magento\Catalog\Block\Product\ReviewRendererInterface::SHORT_VIEW;
    } else {
        $viewMode = 'list';
        $image = 'category_page_list';
        $showDescription = true;
        $templateType = \Magento\Catalog\Block\Product\ReviewRendererInterface::FULL_VIEW;
    }
    /**
     * Position for actions regarding image size changing in vde if needed
     */
    $pos = $block->getPositioned();
    ?>
    <div class="products wrapper <?php /* @escapeNotVerified */ echo $viewMode; ?> products-<?php /* @escapeNotVerified */ echo $viewMode; ?>">
        <?php $iterator = 1; ?>
        <ol class="products list items product-items">
            <?php /** @var $_product \Magento\Catalog\Model\Product */ ?>
            <?php foreach ($_productCollection as $_product): ?>
                <?php /* @escapeNotVerified */ echo($iterator++ == 1) ? '<li class="item product product-item">' : '</li><li class="item product product-item">' ?>
                <div class="product-block">
                    <div class="product-item-info" data-container="product-grid">
                    <?php
                    $productImage = $block->getImage($_product, $image);
                    if ($pos != null) {
                        $position = ' style="left:' . $productImage->getWidth() . 'px;'
                            . 'top:' . $productImage->getHeight() . 'px;"';
                    }
                    ?>

                    <div class="product-image">
                        <?php // Product Image ?>
                        <a href="<?php /* @escapeNotVerified */ echo $_product->getProductUrl() ?>" class="product photo product-item-photo" tabindex="-1">
                            <?php echo $productImage->toHtml(); ?>
                        </a>

                        
                    </div>
                    <div class="product details product-item-details">
                        <?php /* @escapeNotVerified */ echo $block->getProductPrice($_product) ?>
                        
                        <?php
                            $_productNameStripped = $block->stripTags($_product->getName(), null, true);
                        ?>
                        <div class="product name product-item-name">
                            <a class="product-item-link"
                               href="<?php /* @escapeNotVerified */ echo $_product->getProductUrl() ?>">
                                <?php /* @escapeNotVerified */ echo $_helper->productAttribute($_product, $_product->getName(), 'name'); ?>
                            </a>
                        </div>

                        <?php echo $block->getReviewsSummaryHtml($_product, $templateType); ?>

                        

                        <?php echo $block->getProductDetailsHtml($_product); ?>

                        <div class="product-item-inner">
                            <div class="product actions product-item-actions"<?php echo strpos($pos, $viewMode . '-actions') ? $position : ''; ?>>
                                <div class="actions-primary"<?php echo strpos($pos, $viewMode . '-primary') ? $position : ''; ?>>
                                    <?php if ($_product->isSaleable()): ?>
                                        <?php $postParams = $block->getAddToCartPostParams($_product); ?>
                                        <form data-role="tocart-form" action="<?php /* @escapeNotVerified */ echo $postParams['action']; ?>" method="post">
                                            <input type="hidden" name="product" value="<?php /* @escapeNotVerified */ echo $postParams['data']['product']; ?>">
                                            <input type="hidden" name="<?php /* @escapeNotVerified */ echo Action::PARAM_NAME_URL_ENCODED; ?>" value="<?php /* @escapeNotVerified */ echo $postParams['data'][Action::PARAM_NAME_URL_ENCODED]; ?>">
                                            <?php echo $block->getBlockHtml('formkey')?>
                                            <button type="submit"
                                                    title="<?php echo $block->escapeHtml(__('Add to Cart')); ?>"
                                                    class="add-to-cart">
                                                <span class="hidden-lg hidden-md"><?php echo __('<i class="fa fa-shopping-cart"></i>') ?></span>
                                                <span class="hidden-sm hidden-xs"><?php /* @escapeNotVerified */ echo __('Add to Cart') ?></span>   
                                               
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <?php if ($_product->getIsSalable()): ?>
                                            <div class="stock available"><span><?php /* @escapeNotVerified */ echo __('In stock') ?></span></div>
                                        <?php else: ?>
                                            <div class="stock unavailable"><span><?php /* @escapeNotVerified */ echo __('Out of stock') ?></span></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div data-role="add-to-links" class="actions-secondary"<?php echo strpos($pos, $viewMode . '-secondary') ? $position : ''; ?>>
                                    <?php if ($this->helper('Magento\Wishlist\Helper\Data')->isAllow()): ?>
                                        <a href="#"
                                           class="add-to-wishlist"
                                           title="<?php echo $block->escapeHtml(__('Add to Wish List')); ?>"
                                           aria-label="<?php echo $block->escapeHtml(__('Add to Wish List')); ?>"
                                           data-post='<?php /* @escapeNotVerified */ echo $block->getAddToWishlistParams($_product); ?>'
                                           data-action="add-to-wishlist"
                                           role="button">
                                            <span><?php /* @escapeNotVerified */ echo __('<i class="fa fa-heart-o"></i>') ?></span>
                                        </a>
                                    <?php endif; ?>
                                    <?php
                                    $compareHelper = $this->helper('Magento\Catalog\Helper\Product\Compare');
                                    ?>
                                    <a href="#"
                                       class="add-to-compare"
                                       title="<?php echo $block->escapeHtml(__('Add to Compare')); ?>"
                                       aria-label="<?php echo $block->escapeHtml(__('Add to Compare')); ?>"
                                       data-post='<?php /* @escapeNotVerified */ echo $compareHelper->getPostDataParams($_product); ?>'
                                       role="button">
                                        <span><?php /* @escapeNotVerified */ echo __('<i class="fa fa-refresh"></i>') ?></span>
                                    </a>
                                </div>
                            </div>
                            <?php if ($showDescription):?>
                                <div class="product description product-item-description">
                                    <?php /* @escapeNotVerified */ echo $_helper->productAttribute($_product, $_product->getShortDescription(), 'short_description') ?>
                                    <a href="<?php /* @escapeNotVerified */ echo $_product->getProductUrl() ?>" title="<?php /* @escapeNotVerified */ echo $_productNameStripped ?>"
                                       class="action more"><?php /* @escapeNotVerified */ echo __('Learn More') ?></a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                </div>
                <?php echo($iterator == count($_productCollection)+1) ? '</li>' : '' ?>
            <?php endforeach; ?>
        </ol>
    </div>
    <?php echo $block->getToolbarHtml() ?>
    <?php if (!$block->isRedirectToCartEnabled()) : ?>
        <script type="text/x-magento-init">
        {
            "[data-role=tocart-form], .form.map.checkout": {
                "catalogAddToCart": {}
            }
        }
        </script>
    <?php endif; ?>
<?php endif; ?>
