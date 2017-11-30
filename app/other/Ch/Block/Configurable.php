<?php

namespace Custom\Chharo\Block;

class Configurable extends \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable
{

    /**
     * Composes configuration for js
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $store = $this->getCurrentStore();
        $currentPro = $this->getProduct();

        $regularProductPrice = $currentPro->getPriceInfo()->getPrice('regular_price');
        $finalProductPrice = $currentPro->getPriceInfo()->getPrice('final_price');

        $productOptions = $this->helper->getOptions($currentPro, $this->getAllowProducts());
        $attributesData = $this->configurableAttributeData->getAttributesData($currentPro, $productOptions);
        $costomizedAttr = [];
        $customizedIndex = [];
        $customizeOptionPrizes = [];
        $optionPrizes = $this->getOptionPrices();
        if (count($attributesData['attributes']) > 0) {
            foreach ($attributesData['attributes'] as $value) {
                $costomizedAttr[] =  $value;
            }
        }

        if (isset($productOptions['index'])) {
            foreach ($productOptions['index'] as $index => $indexValue) {
                $indexValue['product'] = $index;
                $customizedIndex[] =  $indexValue;
            }
        }

        if (isset($optionPrizes)) {
            foreach ($optionPrizes as $index => $optionPrice) {
                $optionPrice['product'] = $index;
                $customizeOptionPrizes[] =  $optionPrice;
            }
        }

        $optionPrizes = $customizeOptionPrizes;

        $productOptions['index'] = $customizedIndex;

        $attributesData['attributes'] = $costomizedAttr;

        $config = [
            'attributes' => $attributesData['attributes'],
            'template' => str_replace('%s', '<%- data.price %>', $store->getCurrentCurrency()->getOutputFormat()),
            'optionPrices' => $optionPrizes,
            'prices' => [
                'oldPrice' => [
                    'amount' => $this->_registerJsPrice($regularProductPrice->getAmount()->getValue()),
                ],
                'basePrice' => [
                    'amount' => $this->_registerJsPrice(
                        $finalProductPrice->getAmount()->getBaseAmount()
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->_registerJsPrice($finalProductPrice->getAmount()->getValue()),
                ],
            ],
            'productId' => $currentPro->getId(),
            'chooseText' => __('Choose an Option...'),
            'images' => isset($productOptions['images']) ? $productOptions['images'] : [],
            'index' => isset($productOptions['index']) ? $productOptions['index'] : [],
        ];

        if ($currentPro->hasPreconfiguredValues() && !empty($attributesData['defaultValues'])) {
            $config['defaultValues'] = $attributesData['defaultValues'];
        }

        $config = array_merge($config, $this->_getAdditionalConfig());

        return $config;
    }
}
