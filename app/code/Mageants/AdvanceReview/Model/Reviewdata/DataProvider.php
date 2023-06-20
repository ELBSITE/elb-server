<?php
/**
* @category Mageants EventManager
* @package Mageants_EventManager
* @copyright Copyright (c) 2019 Mageants
* @author Mageants Team <support@mageants.com>
*/

namespace Mageants\AdvanceReview\Model\Reviewdata;

use Magento\Store\Model\StoreManagerInterface;
use Mageants\AdvanceReview\Model\ResourceModel\Review\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collection;
    protected $_loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $CollectionFactory,
        StoreManagerInterface $storeManager,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $CollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->_storeManager=$storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

     /**
     * @return Mageants\EventManager\Model\ResourceModel\Eventdata\CollectionFactory
     */ 
     public function getData()
     {  
        //echo die("test"); 
        $baseurl =  $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        //echo die($baseurl);

        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }

        $items = $this->collection->getItems();
        //var_dump($items);exit();

        foreach ($items as $contact) {
            $temp = $this->_loadedData[$contact->getId()] = $contact->getData();
            var_dump($temp);exit();
            
            if ($temp['image_video']) {
                
                $multi_img = [];
                $multi_img = explode(",", $temp['image_video']);
                $img = [];
                foreach ($multi_img as $key => $item) {
                
                $img[$key]['name'] = $item;
                $img[$key]['url'] = $baseurl.'/AdvanceReview'.$item;
                $temp['image_video'] = $img;
               
             }           
            
            $data = $this->dataPersistor->get('review_detail');

            
            if (!empty($data)) {

                $page = $this->collection->getNewEmptyItem();
               
                $this->loadedData[$page->getId()] = $page->getData();   

                $this->dataPersistor->clear('review_detail');
            }else {
                
                if ( $contact->getData('image_video') != null) {
                    $parseData[$contact->getId()] = $temp;
                    var_dump($temp);

                   
                     return $parseData;  
                } else {
                    return $this->_loadedData;
                }
            }
                   
                }
                
            
        }
        return $this->_loadedData;
     }
    
}
