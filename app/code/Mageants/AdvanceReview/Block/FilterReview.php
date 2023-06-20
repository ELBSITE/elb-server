<?php
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Block;

use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http;
use Mageants\AdvanceReview\Model\ReviewLikeDislikeFactory;
use Mageants\AdvanceReview\Helper\Data;

/**
 * Class FilterReview
 * @package Mageants\AdvanceReview\Block
 */
class FilterReview extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mageants\AdvanceReview\Helper\Data
     */
    protected $_helper;

    /**
     * @var reviewsCollection
     */
    protected $_reviewsCollection;
    
    /**
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    protected $_reviewsColFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Mageants\AdvanceReview\Model\ReviewLikeDislikeFactory
     */
    protected $_reviewLikeDislike;

    /**
     * FilterReview constructor.
     * @param \Mageants\AdvanceReview\Helper\Data $helper
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeInterface
     * @param Http $request
     * @param ReviewLikeDislikeFactory $reviewLikeDislike
     */
    public function __construct(
        Data $helper,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeInterface,
        Http $request,
        ReviewLikeDislikeFactory $reviewLikeDislike
    ) {
        $this->_reviewsColFactory = $collectionFactory;
        $this->_helper = $helper;
        $this->_storeManager = $storeInterface;
        $this->_request = $request;
        $this->_reviewLikeDislike = $reviewLikeDislike;
    }

    /**
     * Return filetr data of review collection
     *
     * @return array
     */
    public function filterData()
    {
        $productId = $this->_request->getParam('pid');
        $verified = $this->_request->getParam('verified');
        $star = $this->_request->getParam('star');
        $img = $this->_request->getParam('img');
        $sortreview = $this->_request->getParam('sortreview');

        $page=($this->_request->getParam('p'))? $this->_request->getParam('p') : 1;
        $pageSize=($this->_request->getParam('limit'))? $this->_request->getParam('limit') : 5;


        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $productId
            );
            if ($verified) {
                $this->_reviewsCollection->addVerifiedPurchaseFilter($verified);
            }
            if ($img) {
                $this->_reviewsCollection->addImageVideoFilter();
            }

            $this->_reviewsCollection->getSelect()->columns('detail.detail_id')->joinInner(
                ['vote' => $this->_reviewsCollection->getTable('rating_option_vote')],
                'main_table.review_id = vote.review_id',
                array('review_value' => 'vote.value', 'review_percent' => 'vote.percent')
            );
            $this->_reviewsCollection->getSelect()->group("detail.detail_id");
            //$this->_reviewsCollection->getSelect()->having('COUNT(detail.detail_id)');

            if ($star) {
                $this->_reviewsCollection->addStarFilter($star);
            }

            if ($sortreview == 'mostrecent') {
                $this->_reviewsCollection->setOrder('created_at', 'desc');
            } else {
                $this->_reviewsCollection->setOrder('vote.value', 'desc');
            }
            $this->_reviewsCollection->setPageSize($pageSize);
            $this->_reviewsCollection->setCurPage($page);
            $this->_reviewsCollection->setDateOrder();
            $this->_reviewsCollection->addRateVotes();
        }
        
        return $this->_reviewsCollection;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->filterData()) {
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'my.custom.pager');
            $pager->setLimit(5)->setCollection($this->filterData());
            $this->setChild('pager', $pager);
            $this->filterData()->load();
        }
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }


    /**
     * Return url of media
     *
     * @return string
     */
    public function geMediaUrl($filename)
    {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl."AdvanceReview/".$filename;
    }

    /**
     * Return total like
     *
     * @return int|string
     */
    public function getLikeCount($reviewId)
    {
        return $this->_helper->getLikeCount($reviewId);
    }

    /**
     * Return total dislike
     *
     * @return int|string
     */
    public function getDislikeCount($reviewId)
    {
        return $this->_helper->getDislikeCount($reviewId);
    }
}
