<?php
/**
 * @category Mageants AdvanceReview
 * @package Mageants_AdvanceReview
 * @copyright Copyright (c) 2018 Mageants
 * @author Mageants Team <support@mageants.com>
 */
namespace Mageants\AdvanceReview\Controller\Product;

use Magento\Review\Controller\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Review\Model\Review;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Registry;
use Magento\Customer\Model\Session;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Review\Model\ReviewFactory;
use Magento\Review\Model\RatingFactory;
use Magento\Catalog\Model\Design;
use Magento\Framework\Session\Generic;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * Class Post
 * @package Mageants\AdvanceReview\Controller\Product
 */
class Post extends ProductController
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_mediaDirectory;
    
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;
  
    /**
     * Post constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Catalog\Model\Design $catalogDesign
     * @param \Magento\Framework\Session\Generic $reviewSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        Session $customerSession,
        CategoryRepositoryInterface $categoryRepository,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        ReviewFactory $reviewFactory,
        RatingFactory $ratingFactory,
        Design $catalogDesign,
        Generic $reviewSession,
        StoreManagerInterface $storeManager,
        Validator $formKeyValidator,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory
    ) {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        
        
        parent::__construct($context, $coreRegistry, $customerSession, $categoryRepository, $logger, $productRepository, $reviewFactory, $ratingFactory, $catalogDesign, $reviewSession, $storeManager, $formKeyValidator);
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }

        $data = $this->reviewSession->getFormData(true);
        if ($data) {
            $rating = [];
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data = $this->getRequest()->getPostValue();
            $files = $this->getRequest()->getFiles();
            $rating = $this->getRequest()->getParam('ratings', []);

        }
        if (!empty($files['image_video']['0']['name'])) {
            foreach ($files['image_video'] as $image_video) {
                if (!empty($image_video['tmp_name'])) {
                    $imgnm= $name=rand(10, 1000)."_".date("Ymd")."_".$image_video['name'];
                    try {
                        $target = $this->_mediaDirectory->getAbsolutePath('AdvanceReview/');
                       /* add code for resolve special characters issue */
                        $imagepaths = substr($imgnm, 0, strpos($imgnm, '.'));
                        $imagepathextension = substr(strrchr($imgnm, '.'), 1);
                        $fileName='';
                        if (preg_match('/[A-Za-z0-9]/', $imagepaths))
                        {
                            $imgnm=preg_replace('/[^A-Za-z0-9_.\-\/]+/i', '_', $imgnm);
                        }else{

                             $imgnm = 'file.'.$imagepathextension;
                        }
                        /* End here */
                        $uploader = $this->_fileUploaderFactory->create(['fileId' => $image_video]);
                        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png', 'mp4', '3gp', 'avi', 'webm']);
                        $uploader->setAllowRenameFiles(true);
                        $result = $uploader->save($target, $imgnm);
                        
                        if (!$result) {
                            $this->messageManager->addError(__('We can\'t uplload Image or Video now.'));
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

        if (($product = $this->initProduct()) && !empty($data)) {
            $review = $this->reviewFactory->create()->setData($data);
            $review->unsetData('review_id');

            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $review->setEntityId($review->getEntityIdByCode(Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setStatusId(Review::STATUS_PENDING)
                        ->setCustomerId($this->customerSession->getCustomerId())
                        ->setStoreId($this->storeManager->getStore()->getId())
                        ->setStores([$this->storeManager->getStore()->getId()])
                        ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        $this->ratingFactory->create()
                            ->setRatingId($ratingId)
                            ->setReviewId($review->getId())
                            ->setCustomerId($this->customerSession->getCustomerId())
                            ->addOptionVote($optionId, $product->getId());
                    }

                    $review->aggregate();
                    $this->messageManager->addSuccess(__('You submitted your review for moderation.'));
                } catch (\Exception $e) {
                    $this->reviewSession->setFormData($data);
                    $this->messageManager->addError(__('We can\'t post your review right now.'));
                }
            } else {
                $this->reviewSession->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                } else {
                    $this->messageManager->addError(__('We can\'t post your review right now.'));
                }
            }
        }
        $redirectUrl = $this->reviewSession->getRedirectUrl(true);
        $resultRedirect->setUrl($redirectUrl ?: $this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }
}
