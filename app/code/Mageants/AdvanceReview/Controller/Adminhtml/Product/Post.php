<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mageants\AdvanceReview\Controller\Adminhtml\Product;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Review\Controller\Adminhtml\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\LocalizedException;
use Magento\Review\Model\Review;

/**
 * Review admin controller for POST request.
 */
class Post extends ProductController implements HttpPostActionInterface
{
    /**
     * Create a product review.
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {   //echo die('hedhehd');
        $productId = $this->getRequest()->getParam('product_id', false);
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($data = $this->getRequest()->getPostValue()) {
            /** @var \Magento\Store\Model\StoreManagerInterface $storeManager */
            $storeManager = $this->_objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
            if ($storeManager->hasSingleStore()) {
                $data['stores'] = [
                    $storeManager->getStore(true)->getId(),
                ];
            } elseif (isset($data['select_stores'])) {
                $data['stores'] = $data['select_stores'];
            }
            
            $files = $this->getRequest()->getFiles();
            if (!empty($files['image_video']['0']['name'])) {
            foreach ($files['image_video'] as $image_video) {
                if (!empty($image_video['tmp_name'])) {
                    $imgnm= $name=rand(10, 1000)."_".date("Ymd")."_".$image_video['name'];
                    try {
                        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                        $fileUploaderFactory= $objectManager->get('Magento\MediaStorage\Model\File\UploaderFactory');
                        $file = $objectManager->create('Magento\Framework\Filesystem'); 
                        $this->_mediaDirectory = $file->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);    
                        $target = $this->_mediaDirectory->getAbsolutePath('AdvanceReview/');
                        var_dump($target);
                        $uploader = $fileUploaderFactory->create(['fileId' => $image_video]);
                        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'mp4', '3gp', 'avi', 'webm']);
                        $uploader->setAllowRenameFiles(true);
                        $result = $uploader->save($target, $imgnm);
                        
                        if (!$result) {
                            $this->messageManager->addError(__('webm can\'t uplload Image or Video now.'));
                            $filenm[]='';
                        } else {
                            $filenm[]=$imgnm;
                        }
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                        $filenm[]='';
                    }
                }
            }
            $data['image_video']= implode(", ", $filenm);
        } else {
            $data['image_video']='';
        }
        $review = $this->reviewFactory->create()->setData($data);
            try {
                $review->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                    ->setEntityPkValue($productId)
                    ->setStoreId(Store::DEFAULT_STORE_ID)
                    ->setStatusId($data['status_id'])
                    ->setCustomerId(null)//null is for administrator only
                    ->save();

                $arrRatingId = $this->getRequest()->getParam('ratings', []);
                foreach ($arrRatingId as $ratingId => $optionId) {
                    $this->ratingFactory->create()
                        ->setRatingId($ratingId)
                        ->setReviewId($review->getId())
                        ->addOptionVote($optionId, $productId);
                }

                $review->aggregate();

                $this->messageManager->addSuccessMessage(__('You saved the review.'));
                if ($this->getRequest()->getParam('ret') == 'pending') {
                    $resultRedirect->setPath('review/*/pending');
                } else {
                    $resultRedirect->setPath('review/*/');
                }
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __(
                    'Something went wrong while saving this review.'.$e->getMessage()));
            }
        }
        $resultRedirect->setPath('review/*/');
        return $resultRedirect;
    }
}
