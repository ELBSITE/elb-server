<?php
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Review
 * @package Mageants\AdvanceReview\Model\ResourceModel
 */
class Review extends \Magento\Review\Model\ResourceModel\Review
{

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $connection = $this->getConnection();
        if($object->getImageVideo() != '' || $object->getVerifiedPurchase() !=null){
            $detail = [
                'title' => $object->getTitle(),
                'detail' => $object->getDetail(),
                'nickname' => $object->getNickname(),
                'image_video' => $object->getImageVideo(),
                'verified_purchase' => $object->getVerifiedPurchase(),
            ];
        }else{
            $detail = [
                'title' => $object->getTitle(),
                'detail' => $object->getDetail(),
                'nickname' => $object->getNickname(),
            ];
        }
        $select = $connection->select()->from($this->_reviewDetailTable, 'detail_id')->where('review_id = :review_id');
        $detailId = $connection->fetchOne($select, [':review_id' => $object->getId()]);

        if ($detailId) {
            $condition = ["detail_id = ?" => $detailId];
            $connection->update($this->_reviewDetailTable, $detail, $condition);
        } else {
            $detail['store_id'] = $object->getStoreId();
            $detail['customer_id'] = $object->getCustomerId();
            $detail['review_id'] = $object->getId();
            $connection->insert($this->_reviewDetailTable, $detail);
        }

        
        $stores = $object->getStores();
        if (!empty($stores)) {
            $condition = ['review_id = ?' => $object->getId()];
            $connection->delete($this->_reviewStoreTable, $condition);

            $insertedStoreIds = [];
            foreach ($stores as $storeId) {
                if (in_array($storeId, $insertedStoreIds)) {
                    continue;
                }

                $insertedStoreIds[] = $storeId;
                $storeInsert = ['store_id' => $storeId, 'review_id' => $object->getId()];
                $connection->insert($this->_reviewStoreTable, $storeInsert);
            }
        }

        $this->_aggregateRatings($this->_loadVotedRatingIds($object->getId()), $object->getEntityPkValue());

        return $this;
    }
}
