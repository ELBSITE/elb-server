<?php 
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Model\Config\Source;

use Magento\Customer\Model\ResourceModel\Group\Collection;
 
/**
 * Class Multiselect
 * @package \Mageants\AdvanceReview\Model\Config\Source
 */
class Multiselect implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Mageants\AdvanceReview\Model\Config\Source
     */
    protected $_customerGroupColl;
 
    /**
     * Multiselect constructor
     * @param \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroupColl
     */
    public function __construct(
        Collection $customerGroupColl
    ) {
        $this->_customerGroupColl = $customerGroupColl;        
    }

    public function toOptionArray()
    {
        $groupOptions = $this->_customerGroupColl->toOptionArray();
        $optionArray= array();

        foreach ($groupOptions as $key => $data) {
            $optionArray[] = ['label' => $data['label'], 'value' => $data['value']];
        }
        return $optionArray;
    }
}