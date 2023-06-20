<?php
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Controller\Product;

use Mageants\AdvanceReview\Model\ReviewLikeDislikeFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Mageants\AdvanceReview\Model\ResourceModel\ReviewLikeDislike\Collection;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class LikeDislike
 * @package Mageants\AdvanceReview\Controller\Product
 */
class LikeDislike extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Mageants\AdvanceReview\Model\ReviewLikeDislikeFactory
     */
    protected $_reviewLikeDislike;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultRedirect;

    /**
     * @var \Mageants\AdvanceReview\Model\ResourceModel\ReviewLikeDislike\Collection
     */
    protected $_reviewLikeDislikeCollection;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;
    

    /**
     * LikeDislike constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Mageants\AdvanceReview\Model\ReviewLikeDislikeFactory  $reviewLikeDislike
     * @param \Mageants\AdvanceReview\Model\ResourceModel\ReviewLikeDislike\Collection $reviewLikeDislikeCollection
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Controller\ResultFactory $result
     * @param array $data
     */
    public function __construct(
        //Context $context,
        ReviewLikeDislikeFactory  $reviewLikeDislike,
        Collection $reviewLikeDislikeCollection,
        JsonFactory $resultJsonFactory,
        ResultFactory $result,
        array $data = []
    ){
        parent::__construct($context);
        $this->_reviewLikeDislike = $reviewLikeDislike;
        $this->resultRedirect = $result;
        $this->_reviewLikeDislikeCollection = $reviewLikeDislikeCollection;
        $this->_resultJsonFactory = $resultJsonFactory;

    }

    /**
     * Return array count of like and dislike
     *
     * @return array
     */
    public function execute()
    {  
        $reviewId = $this->getRequest()->getParam('reviewid');
        $customerId = $this->getRequest()->getParam('customerId');
        $like = $this->getRequest()->getParam('like');
        
        $collection = $this->_reviewLikeDislikeCollection
            ->addFieldToFilter('review_id', $reviewId)
            ->addFieldToFilter('customer_id', $customerId);
        
        if(!empty($collection->getData())){
            foreach ($collection as $collections) {
                $model = $this->_reviewLikeDislike->create();
                $model->load($collections->getLikedislikeId());
                $model->setLikeDislike($like);
                $savedata=$model->save();
            }
        }
        else{
            $resultRedirect = $this->resultRedirect->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            $model = $this->_reviewLikeDislike->create();
            $model->addData([
                "review_id" => $reviewId,
                "customer_id" => $customerId,
                "like_dislike" => $like,
                ]);
            $saveData = $model->save();
        }

        $result = $this->_resultJsonFactory->create();
        
        $likeDislikeCountArray = array();
        
        $likeCollection=$this->_reviewLikeDislike->create()->getCollection()->addRevieIdFilter($reviewId)->addLikeFilter();
        
        $likeDislikeCountArray['like'] = count($likeCollection->getData());
        

        $dislikeCollection=$this->_reviewLikeDislike->create()->getCollection()->addRevieIdFilter($reviewId)->adddislikeFilter();
        
        $likeDislikeCountArray['dislike'] = count($dislikeCollection->getData());
        
        $result->setData($likeDislikeCountArray); 
        return $result;
    }
}