<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Ui\Component\Form;

class ProductAttributeOptions implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Amasty\Feed\Model\Product\Attribute\Collection
     */
    private $attribute;

    /**
     * @var bool
     */
    private $isUi = true;

    public function __construct(
        \Amasty\Feed\Model\Product\Attribute\CollectionFactory $attributeFactory
    ) {
        $this->attribute = $attributeFactory->create();
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $result = [];

        $attributes = $this->attribute->getAttributesArray();

        if ($this->isUi) {
            foreach ($attributes as $attribute) {
                $result[] = [
                    'label' => $attribute[\Magento\Eav\Api\Data\AttributeInterface::FRONTEND_LABEL],
                    'value' => $attribute[\Magento\Eav\Api\Data\AttributeInterface::ATTRIBUTE_CODE]
                ];
            }
        } else {
            foreach ($attributes as $attribute) {
                $result[$attribute[\Magento\Eav\Api\Data\AttributeInterface::ATTRIBUTE_CODE]] =
                    $attribute[\Magento\Eav\Api\Data\AttributeInterface::FRONTEND_LABEL];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getOptionsForBlock()
    {
        $this->isUi = false;

        return $this->toOptionArray();
    }
}
