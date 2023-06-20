<?php
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Model\ResourceModel\Review;

/**
 * Class Collection
 * @package Mageants\AdvanceReview\Model\ResourceModel\Review
 */
class Collection extends \Magento\Review\Model\ResourceModel\Review\Collection
{
    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection::_initSelect();
        $this->getSelect()->join(
            ['detail' => $this->getReviewDetailTable()],
            'main_table.review_id = detail.review_id',
            ['detail_id', 'title', 'detail', 'nickname', 'image_video', 'verified_purchase', 'customer_id']
        );
        return $this;
    }

    /**
     * Add verified purchase filter
     *
     * @param int|string $verifiedPurchase
     * @return $this
     */
    public function addVerifiedPurchaseFilter($verifiedPurchase)
    {
        $this->getSelect()->where('verified_purchase = ?', 1);
        return $this;
    }

    /**
     * Add image video filter
     *
     * @return $this
     */
    public function addImageVideoFilter()
    {
        $this->getSelect()->where('image_video != ?', NULL)->where('image_video != ?', '');
        return $this;
    }

    /**
     * Add star filter
     *
     * @param int|string $star
     * @return $this
     */
    public function addStarFilter($star)
    {
        $this->getSelect()->where('value = ?', $star);
        return $this;
    }
}
