<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Sellerbackend\WebService\Model;

use Sellerbackend\WebService\Api\MarketplacesellerInterface;
use Magento\Customer\Controller\Account;
use Magento\Customer\Api\AccountManagementInterface;
use \Magento\Framework\App\Helper\AbstractHelper;

/**
 * Defines the implementaiton class of the calculator service contract.
 */
class Marketplaceseller implements MarketplacesellerInterface 
  {
	  //login using mail only
	  /* protected $_customer;
      protected $_customerSession; */
	 /** @var AccountManagementInterface */
    protected $customerAccountManagement;
	  /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Framework\App\Action\Context      $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\CustomerFactory    $customerFactory
	 * @param AccountManagementInterface $customerAccountManagement
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
		AccountManagementInterface $customerAccountManagement,
		
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
       
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService ,
       //add to cart	
        \Magento\Checkout\Model\Cart $cart,
		\Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
		\Magento\Framework\Event\Observer $observer
		

	   ) {
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
		
		$this->customerAccountManagement = $customerAccountManagement;
		//cart
		$this->cart = $cart;
		
		//addtocart
       //  $this->_storeManager = $storeManager;
        $this->_product = $product;
        $this->_formkey = $formkey;
        $this->quote = $quote;
        $this->quoteManagement = $quoteManagement;
       // $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->orderService = $orderService;
		$this->quoteItemFactory = $quoteItemFactory;
		$this->observer = $observer;
		
    }
  /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
     * @param int $num2 Right hand operand.
     * @return array()|associativeArray().
     */
    public function getsellerprofile($sellerid) {
      if(empty($sellerid) || !isset($sellerid) || $sellerid == ""){
		 throw new InputException(__('Id required'));
		}
		else{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName = $resource->getTableName('employee'); //gives table name with prefix
		
		$result['code']=1;
		return $result;     
		 
    }
	 }
	 
	 
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
    
     * @return array()|associativeArray().
     */
    public function getproductlistofseller($sellerid) {	
      if(empty($sellerid) || !isset($sellerid) || $sellerid == ""){
		 throw new InputException(__('Id required')); 	
		}
		else{	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		 
		//Select Data from table
		//$sql3 = "Select * FROM marketplace_product where seller_id=$sellerid";
		$sql3 = "Select * FROM marketplace_product a, catalog_product_entity b 
		where 
		a.mageproduct_id=b.entity_id and 
		a.seller_id=$sellerid and 
		b.type_id!='customProductType' ";
		$result3 = $connection->fetchAll($sql3);
		$result4='';
		
		foreach($result3 as $data)
		{
			     
			       $productId =$data['mageproduct_id'];
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
					
					
					$offfer_tag = $currentproduct->getData('offfer_tag');
					$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
					
					   $optionText =$attr->getSource()->getOptionText($offfer_tag);
			
				if($optionText=='')
				{
					$optionText='null';
				}
				   
				     $_price=$currentproduct->getPrice();
				     $_finalPrice=$currentproduct->getFinalPrice();
					 $savetag='null';
						if($_finalPrice < $_price):
						$_savePercent = 100 - round(($_finalPrice / $_price)*100);
						//echo $_savePercent;
						$savetag=$_savePercent;
                   endif;

				   $producturl=$currentproduct->getProductUrl();
				   
				   
				   $sql_wishlist = "Select * FROM wishlist a ,wishlist_item b  where a.customer_id=$sellerid and a.wishlist_id=b.wishlist_id and b.product_id=$productId ";
		           $result_wishlist = $connection->fetchAll($sql_wishlist);
				  /*  $wish='';
				   if(!empty($result_wishlist))
				   {
					   $wish=array("productId"=>$productId,"sellerid"=>$sellerid,"status"=>1); 
					   
				   }
				   else
				   {
					   $wish=array("productId"=>$productId,"sellerid"=>$sellerid,"status"=>0);
				   } */
				   
				   
				   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
				   $storeId = $this->storeManager->getStore()->getId();
                   $reviewFactory->getEntitySummary($currentproduct, $storeId);
				   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
				   if($ratingSummary=='')
				{
					$ratingSummary='null';
				}
				   //print_r($ratingSummary);
				   
				   
				   
				   
					$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
					
					
			 
		} 
		
		return $result4;
		
    }
	 } 
	 
	 
		/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
    
     * @return array()|associativeArray().
     */
    public function getservicelistofseller($sellerid) {	
      if(empty($sellerid) || !isset($sellerid) || $sellerid == ""){
		 throw new InputException(__('Id required')); 	
		}
		else{	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		 
		//Select Data from table
		//$sql3 = "Select * FROM marketplace_product where seller_id=$sellerid";
		$sql3 = "Select * FROM marketplace_product a, catalog_product_entity b 
		where 
		a.mageproduct_id=b.entity_id and 
		a.seller_id=$sellerid and 
		b.type_id='customProductType' ";
		$result3 = $connection->fetchAll($sql3);
		$result4='';
		
		foreach($result3 as $data)
		{
			     
			       $productId =$data['mageproduct_id'];
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
					
					
					$offfer_tag = $currentproduct->getData('offfer_tag');
					$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
					
					   $optionText =$attr->getSource()->getOptionText($offfer_tag);
			
				if($optionText=='')
				{
					$optionText='null';
				}
				   
				     $_price=$currentproduct->getPrice();
				     $_finalPrice=$currentproduct->getFinalPrice();
					 $savetag='null';
						if($_finalPrice < $_price):
						$_savePercent = 100 - round(($_finalPrice / $_price)*100);
						//echo $_savePercent;
						$savetag=$_savePercent;
                   endif;

				   $producturl=$currentproduct->getProductUrl();
				   
				   
				   $sql_wishlist = "Select * FROM wishlist a ,wishlist_item b  where a.customer_id=$sellerid and a.wishlist_id=b.wishlist_id and b.product_id=$productId ";
		           $result_wishlist = $connection->fetchAll($sql_wishlist);
				  /*  $wish='';
				   if(!empty($result_wishlist))
				   {
					   $wish=array("productId"=>$productId,"sellerid"=>$sellerid,"status"=>1); 
					   
				   }
				   else
				   {
					   $wish=array("productId"=>$productId,"sellerid"=>$sellerid,"status"=>0);
				   } */
				   
				   
				   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
				   $storeId = $this->storeManager->getStore()->getId();
                   $reviewFactory->getEntitySummary($currentproduct, $storeId);
				   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
				   if($ratingSummary=='')
				{
					$ratingSummary='null';
				}
				   //print_r($ratingSummary);
				   
				   
				   
				   
					$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
					
					
			 
		} 
		
		return $result4;
		
    }
	 }  
	 
	  /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
     * @param int $num2 Right hand operand.
     * @return array()|associativeArray().
     */
     public function getofferbanner($sellerid)
	{
		/* if($sellerid != "")
		{ */
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();		
		$banner_sql = "SELECT * FROM marketplace_userdata WHERE seller_id=$sellerid";
		$result2 = $connection->fetchAll($banner_sql);
		//print_r($result2);exit;
		if(!empty($result2))
		{
			
					$imgname=$result2[0]['banner_pic'];
					if($imgname!='')
					{
						$result[]=array('id'=>1,'img'=>'http://159.203.151.92/pub/media/avatar/'.$imgname.'');
						
						
					}
					else{
						
						$result[]=array('id'=>0,'img'=>'null');
						
					}
		}
		else
		{
			$result[]=array('id'=>0,'img'=>'null');
		}
		 return $result;     
	 
	 }
	 
	  /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
     * @param int $num2 Right hand operand.
     * @return array()|associativeArray().
     */
     public function getserviceofferbanner($sellerid)
	{
		/* if($sellerid != "")
		{ */
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();		
		$banner_sql = "SELECT * FROM marketplace_userdata WHERE seller_id=$sellerid";
		$result2 = $connection->fetchAll($banner_sql);
		//print_r($result2);exit;
		if(!empty($result2))
		{
			
					$imgname=$result2[0]['banner_pic'];
					if($imgname!='')
					{
						$result[]=array('id'=>1,'img'=>'http://159.203.151.92/pub/media/avatar/banner.png');
						
						
					}
					else{
						
						$result[]=array('id'=>0,'img'=>'null');
						
					}
		}
		else
		{
			$result[]=array('id'=>0,'img'=>'null');
		}
		 return $result;     
	 
	 }
	 
	 
	  /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
     * @param int $num2 Right hand operand.
     * @return array()|associativeArray().
     */
    public function getbrandlist() {			
       
		$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'brandlist'=>array(array('id'=>1,'img'=>'http://159.203.151.92/fitness_logo4.jpg'),array('id'=>2,'img'=>'http://159.203.151.92/fitness_logo5.jpg')));
		
		
		return $result;     
		 
   
	 }
	 
	 
	 
	 /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
    
     * @return array()|associativeArray().
     */
    public function gettopproductselling($sellerid) {	
       if(empty($sellerid) || !isset($sellerid) || $sellerid == ""){
		 throw new InputException(__('Id required')); 	
		}
		else{	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		 
		//Select Data from table
		//$sql3 = "Select * FROM marketplace_product where seller_id=$sellerid";
		$sql3 = "Select * FROM marketplace_product a, catalog_product_entity b 
		where 
		a.mageproduct_id=b.entity_id and 
		a.seller_id=$sellerid and 
		b.type_id!='customProductType' ";
		$result3 = $connection->fetchAll($sql3);
		
		$result4='';
		
		foreach($result3 as $data)
		{
			     
			       $productId =$data['mageproduct_id'];
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
					$offfer_tag = $currentproduct->getData('offfer_tag');
					$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
					//$optionText='null';
				 //if ($attr->usesSource()) {
					   $optionText =$attr->getSource()->getOptionText($offfer_tag);
				// }
				if($optionText=='')
				{
					$optionText='null';
				}
				   
				     $_price=$currentproduct->getPrice();
				     $_finalPrice=$currentproduct->getFinalPrice();
					 $savetag='null';
						if($_finalPrice < $_price):
						$_savePercent = 100 - round(($_finalPrice / $_price)*100);
						//echo $_savePercent;
						$savetag=$_savePercent;
                   endif;

				   $producturl=$currentproduct->getProductUrl();
				   
				   
				   $sql_wishlist = "Select * FROM wishlist a ,wishlist_item b  where a.customer_id=$sellerid and a.wishlist_id=b.wishlist_id and b.product_id=$productId ";
		           $result_wishlist = $connection->fetchAll($sql_wishlist);
				   /* $wish='';
				   if(!empty($result_wishlist))
				   {
					   $wish=array("productId"=>$productId,"sellerid"=>$sellerid,"status"=>1); 
					   
				   }
				   else
				   {
					   $wish=array("productId"=>$productId,"sellerid"=>$sellerid,"status"=>0);
				   } */
				   
				   
				   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
				   $storeId = $this->storeManager->getStore()->getId();
                   $reviewFactory->getEntitySummary($currentproduct, $storeId);
				   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
				   if($ratingSummary=='')
				{
					$ratingSummary='null';
				}
					
					//$delivery=array('method'=>'Standard Delivery','price'=>'Free');
					
				$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
					
			
		}
		  
		//$result['product']=$result4;
		
		$result[]=array('status'=>array("code"=>"1","message"=>"success"),'product'=>$result4);
		
		return $result;   
		
    }
	 } 
	 
	
	 /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
    
     * @return array()|associativeArray().
     */
    public function gettopserviceselling($sellerid) {	
       if(empty($sellerid) || !isset($sellerid) || $sellerid == ""){
		 throw new InputException(__('Id required')); 	
		}
		else{	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		 
		//Select Data from table
		//$sql3 = "Select * FROM marketplace_product where seller_id=$sellerid";
		$sql3 = "Select * FROM marketplace_product a, catalog_product_entity b 
		where 
		a.mageproduct_id=b.entity_id and 
		a.seller_id=$sellerid and 
		b.type_id='customProductType' ";
		$result3 = $connection->fetchAll($sql3);
		
		$result4='';
		
		foreach($result3 as $data)
		{
			     
			       $productId =$data['mageproduct_id'];
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
					$offfer_tag = $currentproduct->getData('offfer_tag');
					$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
					//$optionText='null';
				 //if ($attr->usesSource()) {
					   $optionText =$attr->getSource()->getOptionText($offfer_tag);
				// }
				if($optionText=='')
				{
					$optionText='null';
				}
				   
				     $_price=$currentproduct->getPrice();
				     $_finalPrice=$currentproduct->getFinalPrice();
					 $savetag='null';
						if($_finalPrice < $_price):
						$_savePercent = 100 - round(($_finalPrice / $_price)*100);
						//echo $_savePercent;
						$savetag=$_savePercent;
                   endif;

				   $producturl=$currentproduct->getProductUrl();
				   
				   
				   $sql_wishlist = "Select * FROM wishlist a ,wishlist_item b  where a.customer_id=$sellerid and a.wishlist_id=b.wishlist_id and b.product_id=$productId ";
		           $result_wishlist = $connection->fetchAll($sql_wishlist);
				   /* $wish='';
				   if(!empty($result_wishlist))
				   {
					   $wish=array("productId"=>$productId,"sellerid"=>$sellerid,"status"=>1); 
					   
				   }
				   else
				   {
					   $wish=array("productId"=>$productId,"sellerid"=>$sellerid,"status"=>0);
				   } */
				   
				   
				   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
				   $storeId = $this->storeManager->getStore()->getId();
                   $reviewFactory->getEntitySummary($currentproduct, $storeId);
				   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
				   if($ratingSummary=='')
				{
					$ratingSummary='null';
				}
					
					//$delivery=array('method'=>'Standard Delivery','price'=>'Free');
					
				$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
					
			
		}
		  
		//$result['product']=$result4;
		
		$result[]=array('status'=>array("code"=>"1","message"=>"success"),'product'=>$result4);
		
		return $result;   
		
    }
	 }
	 
	 /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
   
	 * @return array()|associativeArray().
     */
    public function getproductcategories($sellerid) {	
      if(empty($sellerid) || !isset($sellerid) || $sellerid == ""){
		 throw new InputException(__('Id required')); 	
		}
		else{	
		/* $result[]=array('status'=>array("code"=>"1","message"=>"success","seller_id"=>"2"));
		$result4[]='';
		 $objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
                    $categoryFactory = $objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');

                     $categories = $categoryFactory->create()                              
                                ->addAttributeToSelect('*');  
		//print_r($categories->getData()); 
		 foreach($categories  as $data)
		{ 
		
		$result4[]['name']=$data->getName(); 
				 $result4[]['id']=$data->getId();
			 
		}		
		  
		$result[]=array('getproductcategories'=>$result4); */ 
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql3 = "Select * FROM (Select * FROM marketplace_product a ,catalog_category_product b where a.seller_id=2 and b.product_id=a.mageproduct_id  group by b.category_id) s ,catalog_category_entity c where c.entity_id=s.category_id";
		$result3 = $connection->fetchAll($sql3);
		$result4='';
		foreach($result3 as $data)
		{
			if($data['parent_id']==2)
			{
				$categoryId = $data['entity_id'];
			$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$object_manager = $_objectManager->create('Magento\Catalog\Model\Category')
			->load($categoryId);
			
			$subcats  =$object_manager->getChildren();
			$subcategories = array();
			//print_r($subcats);
			 foreach(explode(',',$subcats) as $subCatid){
				// $objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
                 //   $categoryFactory = $objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
                $_subCategory = $_objectManager->create('Magento\Catalog\Model\Category')->load($subCatid);
						   if($_subCategory->getIsActive()) {
					$subcategories[] = array('id'=>$_subCategory->getId(),'name'=>$_subCategory->getName(),'image_url'=>$_subCategory->getImageUrl()); 
				}
			}
			//print_r($subcategories);
			
				$result4[]=array('category_id'=>$data['entity_id'],'category_name'=>$object_manager->getName(),'sub_category'=>$subcategories);  
			}
			
		}
		$result[]=array('status'=>array("code"=>"1","message"=>"success"),'getproductcategories'=>$result4);
		 
		
		return $result;   
		
    }
	 } 
	 
	 
	  /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
   
	 * @return array()|associativeArray().
     */
    public function getproductmaincategories($sellerid) {	
      	
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql3 = "Select * FROM (Select * FROM marketplace_product a ,catalog_category_product b where a.seller_id=2 and b.product_id=a.mageproduct_id  group by b.category_id) s ,catalog_category_entity c where c.entity_id=s.category_id";
		$result3 = $connection->fetchAll($sql3);
		$result4='';
		foreach($result3 as $data)
		{
			if($data['parent_id']==2)
			{
				$categoryId = $data['entity_id'];
				if($categoryId!=24):
			$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$object_manager = $_objectManager->create('Magento\Catalog\Model\Category')
			->load($categoryId);
			
			/* $subcats  =$object_manager->getChildren();
			$subcategories = array();
			
			 foreach(explode(',',$subcats) as $subCatid){
				
                $_subCategory = $_objectManager->create('Magento\Catalog\Model\Category')->load($subCatid);
						   if($_subCategory->getIsActive()) {
					$subcategories[] = array('id'=>$_subCategory->getId(),'name'=>$_subCategory->getName(),'image_url'=>$_subCategory->getImageUrl()); 
				}
			} */
			
			
				$result4[]=array('category_id'=>$data['entity_id'],'category_name'=>$object_manager->getName(),'image_url'=>$object_manager->getImageUrl());  
			endif;
			}
			
		}
		
		$sql_banner= "Select * FROM custom_productbanner where status=1 order by productbanner_id desc";
		$result_banner = $connection->fetchAll($sql_banner);
		//print_r($result_banner);
		
		 if(!empty($result_banner))
		{
			 foreach ($result_banner as $data_banner) { 
			
			          //print_r($data_banner);
					$imgname=$data_banner['background'];
					 if($imgname!='')
					{
						$result_bannerarraay[]=array('id'=>1,'img'=>'http://159.203.151.92/pub/media/Gridpart4/background/image'.$imgname.'');
						
						
					} 
					
			} 
		}
		else
		{
			$result_bannerarraay[]=array('id'=>0,'img'=>'null');
		} 
		//print_r($result_bannerarraay);
		//exit;
		
		
		$result[]=array('status'=>array("code"=>"1","message"=>"success"),'banner'=>$result_bannerarraay,'getproductcategories'=>$result4);
		 
		
		return $result;   
		
    
	 } 
	 
	 
		/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid Left hand operand.
    
     * @return array()|associativeArray().
     */
    public function getsellerhome($sellerid,$userid) {	
      if(empty($sellerid) || !isset($sellerid) || $sellerid == "")
	  {
		 throw new InputException(__('Id required')); 	
		}
		else{	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		//$tableName = $resource->getTableName('employee'); //gives table name with prefix
		 
		//for product
		//$sql3 = "Select * FROM marketplace_product where seller_id=$sellerid";
		$sql3 = "Select * FROM marketplace_product a, catalog_product_entity b 
		where 
		a.mageproduct_id=b.entity_id and 
		a.seller_id=$sellerid and 
		b.type_id!='customProductType' ";
		$result3 = $connection->fetchAll($sql3);
		if(!empty($result3)):
		$result4='';
		foreach($result3 as $data)
		{
			     //echo $data['mageproduct_id']; 
			       $productId =$data['mageproduct_id'];
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
					//  print_r($currentproduct);
					//$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail());
					
					$offfer_tag = $currentproduct->getData('offfer_tag');
					$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
					//$optionText='null';
				 //if ($attr->usesSource()) {
					   $optionText =$attr->getSource()->getOptionText($offfer_tag);
				// }
				if($optionText=='')
				{
					$optionText='null';
				}
				   
				     $_price=$currentproduct->getPrice();
				     $_finalPrice=$currentproduct->getFinalPrice();
					 $savetag='null';
						if($_finalPrice < $_price):
						$_savePercent = 100 - round(($_finalPrice / $_price)*100);
						//echo $_savePercent;
						$savetag=$_savePercent;
                   endif;

				   $producturl=$currentproduct->getProductUrl();
				   
				   $wishlisttag='';
				    if($userid > 0)
				   {
                      $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id and b.product_id=$productId";
				      $result_wishlist = $connection->fetchAll($wishlist);
					if(!empty($result_wishlist))
					{
						$wishlisttag=1;
						 
					}
					else
					{
						$wishlisttag=0;
					}
				   }
				   else
				   {
					$wishlisttag=0;
				   } 
				   
				 
				   
				   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
				   $storeId = $this->storeManager->getStore()->getId();
                   $reviewFactory->getEntitySummary($currentproduct, $storeId);
				   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
				   if($ratingSummary=='')
				{
					$ratingSummary='null';
				}
				   //print_r($ratingSummary);
				   
					
					
					
					$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary),'wishlisttag'=>$wishlisttag);
					
			
		}
		//  print_r($result4);
		//$result['product']=$result4;
		
		
		$banner_sql = "SELECT * FROM marketplace_userdata WHERE seller_id=$sellerid";
		$banner_result2 = $connection->fetchAll($banner_sql);
		//print_r($banner_result2);
		if(!empty($banner_result2))
		{
			
					$imgname=$banner_result2[0]['banner_pic'];
					if($imgname!='')
					{
						$result_offerbanner[0]=array('id'=>1,'img'=>'http://159.203.151.92/pub/media/avatar/'.$imgname.'');
		
						
					}
					else{
						
						$result_offerbanner[]=array('id'=>0,'img'=>'null');
						
					}
		}
		else
		{
			$result_offerbanner[0]=array('id'=>1,'img'=>'https://d152j5tfobgaot.cloudfront.net/wp-content/uploads/2015/09/yourstory-e-commerce1.jpg');
		
		}
		
		
		$result_brandlist[0]=array('id'=>1,'img'=>'http://159.203.151.92/fitness_logo4.jpg');
		$result_brandlist[1]=array('id'=>2,'img'=>'http://159.203.151.92/fitness_logo5.jpg');
		$result[]=array('offersbanners'=>$result_offerbanner,'product'=>$result4,'topsellingproduct'=>$result4,
		'brandlist'=>$result_brandlist); 
		
		
		else:
		$result[]=array('offersbanners'=>'null','product'=>'null','topsellingproduct'=>'null','brandlist'=>'null');
		
		endif;
		
		
		
		
		
		
		//end for product
		
		//for service//
		
		
		      //$sql3 = "Select * FROM marketplace_product where seller_id=$sellerid";
			  $sql32 = "Select * FROM marketplace_product a, catalog_product_entity b 
		where 
		a.mageproduct_id=b.entity_id and 
		a.seller_id=$sellerid and 
		b.type_id='customProductType' ";
		
		$result32 = $connection->fetchAll($sql32);
		if(!empty($result32)):
		$result_service_product='';
		foreach($result32 as $data)
		{
			     //echo $data['mageproduct_id']; 
			       $productId =$data['mageproduct_id'];
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
					//  print_r($currentproduct);
					//$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail());
					
					$offfer_tag = $currentproduct->getData('offfer_tag');
					$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
					//$optionText='null';
				 //if ($attr->usesSource()) {
					   $optionText =$attr->getSource()->getOptionText($offfer_tag);
				// }
				if($optionText=='')
				{
					$optionText='null';
				}
				   
				     $_price=$currentproduct->getPrice();
				     $_finalPrice=$currentproduct->getFinalPrice();
					 $savetag='null';
						if($_finalPrice < $_price):
						$_savePercent = 100 - round(($_finalPrice / $_price)*100);
						//echo $_savePercent;
						$savetag=$_savePercent;
                   endif;

				   $producturl=$currentproduct->getProductUrl();
				   
				   $wishlisttag='';
				    if($userid > 0)
				   {
                      $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id and b.product_id=$productId";
				      $result_wishlist = $connection->fetchAll($wishlist);
					if(!empty($result_wishlist))
					{
						$wishlisttag=1;
						 
					}
					else
					{
						$wishlisttag=0;
					}
				   }
				   else
				   {
					$wishlisttag=0;
				   } 
				   
				 
				   
				   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
				   $storeId = $this->storeManager->getStore()->getId();
                   $reviewFactory->getEntitySummary($currentproduct, $storeId);
				   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
				   if($ratingSummary=='')
				{
					$ratingSummary='null';
				}
				   //print_r($ratingSummary);
				   
					
					
					
					$result_service_product[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary),'wishlisttag'=>$wishlisttag);
					
			
		}
		//  print_r($result4);
		//$result['product']=$result4;
		
		
		$banner_sql = "SELECT * FROM marketplace_userdata WHERE seller_id=$sellerid";
		$banner_result2 = $connection->fetchAll($banner_sql);
		//print_r($banner_result2);
		if(!empty($banner_result2))
		{
			
					$imgname=$banner_result2[0]['banner_pic'];
					if($imgname!='')
					{
						$result_offerbanner[0]=array('id'=>1,'img'=>'http://159.203.151.92/pub/media/avatar/banner.png');
		
						
					}
					else{
						$result_offerbanner[]=array('id'=>0,'img'=>'null');
						
					}
		}
		else
		{
			$result_offerbanner[0]=array('id'=>1,'img'=>'https://d152j5tfobgaot.cloudfront.net/wp-content/uploads/2015/09/yourstory-e-commerce1.jpg');
		
		}
		
		
		//end for service
		
		$result_brandlist[0]=array('id'=>1,'img'=>'http://159.203.151.92/fitness_logo4.jpg');
		$result_brandlist[1]=array('id'=>2,'img'=>'http://159.203.151.92/fitness_logo5.jpg');
		//$result[]=array('status'=>array("code"=>"1","message"=>"success","seller_id"=>"2"),'offersbanners'=>$result_offerbanner,'product'=>$result4,'topsellingproduct'=>$result4,'brandlist'=>$result_brandlist);
		$result[]=array('service_offersbanners'=>$result_offerbanner,'services'=>$result_service_product,
		'topsellingservices'=>$result_service_product,'brandlist'=>$result_brandlist); 
		
		
		else:
		$result[]=array('service_offersbanners'=>'null','services'=>'null',
		'topsellingservices'=>'null','brandlist'=>'null');
		
		endif;
		
		return $result;   
		
    }
	  
	  }
	 
	  /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $categoryid Left hand operand.
     * @param int $sellerid Left hand operand.
	 * @return array()|associativeArray().
     */
    public function getcategoryproductlist($categoryid,$sellerid,$userid) {	
      if(empty($categoryid) || !isset($categoryid) || $categoryid == ""){
		// throw new InputException(__('Id required')); 	
		$result[]=array('status'=>array("code"=>"0","message"=>"unsuccess"));
		return $result; 
		}
		else{	
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql3 = "SELECT * FROM catalog_category_product a ,marketplace_product b where a.category_id=$categoryid and a.product_id=b.mageproduct_id and seller_id=$sellerid";
		$result3 = $connection->fetchAll($sql3);
		$result4='';
		foreach($result3 as $data)
		{
			   $productId =$data['product_id'];
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
					
					
					 $offfer_tag = $currentproduct->getData('offfer_tag');
					$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
					//$optionText='null';
				 //if ($attr->usesSource()) {
					   $optionText =$attr->getSource()->getOptionText($offfer_tag);
				// }
				if($optionText=='')
				{
					$optionText='null';
				}
				   
				     $_price=$currentproduct->getPrice();
				     $_finalPrice=$currentproduct->getFinalPrice();
					 $savetag='null';
						if($_finalPrice < $_price):
						$_savePercent = 100 - round(($_finalPrice / $_price)*100);
						//echo $_savePercent;
						$savetag=$_savePercent;
                   endif;

				   $producturl=$currentproduct->getProductUrl();
				   
				   
				    $wishlisttag='';
				    if($userid > 0)
				   {
                      $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id and b.product_id=$productId";
				      $result_wishlist = $connection->fetchAll($wishlist);
					if(!empty($result_wishlist))
					{
						$wishlisttag=1;
						 
					}
					else
					{
						$wishlisttag=0;
					}
				   }
				   else
				   {
					$wishlisttag=0;
				   } 
				   
				   
				   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
				   $storeId = $this->storeManager->getStore()->getId();
                   $reviewFactory->getEntitySummary($currentproduct, $storeId);
				   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
				   if($ratingSummary=='')
				{
					$ratingSummary='null';
				}
					
					
					
				$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary),'wishlisttag'=>$wishlisttag);
					
					//$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail());
					
			
		}
		
		
		
		$categoryId = $categoryid;
			$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$object_manager = $_objectManager->create('Magento\Catalog\Model\Category')
			->load($categoryId);
			
			$subcats  =$object_manager->getChildren();
			$subcategories = array();
			//print_r($subcats);
			 foreach(explode(',',$subcats) as $subCatid){
				// $objectManagerr = \Magento\Framework\App\ObjectManager::getInstance();
                 //   $categoryFactory = $objectManagerr->create('Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
                $_subCategory = $_objectManager->create('Magento\Catalog\Model\Category')->load($subCatid);
						   if($_subCategory->getIsActive()) {
					$subcategories[] = array('id'=>$_subCategory->getId(),'name'=>$_subCategory->getName(),'image_url'=>$_subCategory->getImageUrl()); 
				}
			}
		
		
		
		
		
		
		
		
		
		
		
		
		$result[]=array('status'=>array("code"=>"1","message"=>"success"),'sub_category'=>$subcategories,'product'=>$result4);
		 
		
		return $result;   
		
    }
	 }
	 
	 
	   /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $productid Left hand operand.
	 * @return array()|associativeArray().
     */
    public function getproductdetails($productid,$userid) {	
      if(empty($productid) || !isset($productid) || $productid == ""){
		// throw new InputException(__('Id required')); 	
		$result[]=array('status'=>array("code"=>"0","message"=>"unsuccess"));
		return $result; 
		}
		else{	
		
	  $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		
		 $quote_sql = "Select * FROM catalog_product_entity where entity_id=$productid ";
		 $result_result = $connection->fetchAll($quote_sql);	
		 //exit;
		 if(!empty($result_result))
		 {
			       $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
					//print_r($currentproduct);
					$allimages=$currentproduct->getMediaGallery('images');
					//print_r($allimages);
					$imgarray=array();
					foreach($allimages as $imgurl)
						{
							$imgarray[]='http://159.203.151.92/pub/media/catalog/product'.$imgurl['file'];
						}
				
		            $offfer_tag = $currentproduct->getData('offfer_tag');
					$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
					$optionText =$attr->getSource()->getOptionText($offfer_tag);
				
				if($optionText=='')
				{
					$optionText='null';
				} 
				   
				      $_price=$currentproduct->getPrice();
				     $_finalPrice=$currentproduct->getFinalPrice();
					 $savetag='null';
						if($_finalPrice < $_price):
						$_savePercent = 100 - round(($_finalPrice / $_price)*100);
						//echo $_savePercent;
						$savetag=$_savePercent;
                   endif;

				   $producturl=$currentproduct->getProductUrl();
				    
				   $wishlisttag='';
				    if($userid > 0)
				   {
                      $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id and b.product_id=$productid";
				      $result_wishlist = $connection->fetchAll($wishlist);
					if(!empty($result_wishlist))
					{
						$wishlisttag=1;
						 
					}
					else
					{
						$wishlisttag=0;
					}
				   }
				   else
				   {
					$wishlisttag=0;
				   } 
				   
				   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
				   $storeId = $this->storeManager->getStore()->getId();
                   $reviewFactory->getEntitySummary($currentproduct, $storeId);
				   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
				   if($ratingSummary=='')
				{
					$ratingSummary='null';
				} 
				   //print_r($ratingSummary);
				   $totalreview='';
				   $quote_sql_rev = "Select count(*) 'totalreview' FROM review where entity_pk_value=$productid and status_id=1";
		           $result_result_rev = $connection->fetchAll($quote_sql_rev);
		           $totalreview =$result_result_rev[0]['totalreview'];
		
		  // $result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"fullImage"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getImage(),'sku'=>$currentproduct->getSku(),'mediagallery'=>$imgarray,"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary),'delivery'=>array(array('method'=>'Standard Delivery','cost'=>'Free'),array('method'=>'Expresee Delivery','cost'=>'$90')));
			 $result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"fullImage"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getImage(),'sku'=>$currentproduct->getSku(),'mediagallery'=>$imgarray,"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary,'totalreview'=>$totalreview),'delivery'=>array(array('method'=>'Standard Delivery','cost'=>'Free','subtitle'=>'Delivery in 5-6 days'),array('method'=>'Expresee Delivery','cost'=>'5','subtitle'=>'Delivery in 1-2 days')),'wishlisttag'=>$wishlisttag);
		     $result[]=array('status'=>array("code"=>"1","message"=>"success"),'productdetails'=>$result4);
		 }
		 else
		 {
			  $result[]=array('status'=>array("code"=>"0","message"=>"error"));
		 }
	 
		
		
				  
				   
		 
		
		return $result;   
		
    }
	 }
	 
	   /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $username Left hand operand.
     * @param string $password Right hand operand.
     * @return array()|associativeArray().
     */
    public function userlogin($username,$password) { 
	
	            //$result='';
				//$customer = $this->customerAccountManagement->authenticate($username,$password);
				
	            $result1 = $this->customerAccountManagement->customauthenticate($username,$password);
				//print_r($result1);
				if(!empty($result1))
				{
					//return $result1;
					return $result2=array('status'=>array("code"=>"1","message"=>"Success"),'user_detail'=>$result1);
				}
				else
				{
					$result2=array('status'=>array("code"=>"0","message"=>"Invalid login or password."));
					return $result2;
				}
	         // return $result;
	 /* $validate = 0;
	 try {
				//$customer = $this->customerAccountManagement->authenticate($username,$password);
				$result = $this->customerAccountManagement->customauthenticate($username,$password);
				print_r($result);
				$validate = 1;
			}
			catch(InvalidEmailOrPasswordException $ex) {
				$result[]=array('status'=>array("code"=>"0","message"=>"Error",'message'=>$ex->getMessage));
				$validate = 0; 
			} 
	
	
	  if($validate == 1) 
			 {
				     if($customer->getId())
						 {
							// $result[]=array('status'=>array("code"=>"1","message"=>"success"),'customer'=>array('id'=>$customer->getId(),'username'=>$username,'password'=>$password,'name'=>$customer->getFirstname()));
							//echo $customer->getId();
							
							$result[]=array('status'=>array("message"=>"Login Successful"),'customer'=>array('id'=>$customer->getId(),'username'=>$username,'password'=>$password,'name'=>$customer->getFirstname()));
						//$result=array('status'=>array("message"=>"Login Successful."));
						 }
						 else
						 {
						$result[]=array('status'=>array("code"=>"1","message"=>"error"));
						
						 }
			 }
			 else
			 {
				 $result[]=array('status'=>array("code"=>"0","message"=>"Error",'message'=>$ex->getMessage));
			 }
	 
    
		return $result;  */  
               			

	 }
	 
	    /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $username Left hand operand.
	 * @param string $password Right hand operand.
	 * @param string $name Right hand operand.
	 * @param string $redirectUrl Right hand operand.
     * @return array()|associativeArray().
     */
    public function registration($username,$password,$name) { 
        
      
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$email_exist= "Select * FROM customer_entity where email='$username'";
		$result_email = $connection->fetchAll($email_exist);
		if(!empty($result_email))
		{
			$result=array('status'=>array("code"=>"0","message"=>"Email id Exist!"));
		}
		else
		{
			  $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
         $customer   = $this->customerFactory->create();
        $customer->setEmail($username); 
		if(strpos($name, ' ') > 0)
		{
					$parts = explode(" ", $name);
				 $lastname = array_pop($parts);
				 $firstname = implode(" ", $parts);
				
				}
				else
				{
					 $lastname = $name;
					  $firstname = $name;
				}
				
				
		
		
      
					$customer->setFirstname($firstname);
					$customer->setLastname($lastname); 
					$customer->setPassword($password);

					// Save data
				   $customer->save();
				 
				    if($customer->getId())
					 {
						   $customer->sendNewAccountEmail();
						  // $customer->sendNewAccountEmail('registered', '',1);
						 $result=array('status'=>array("code"=>"1","message"=>"success",'user_detail'=>array('id'=>$customer->getId(),'username'=>$username,'password'=>$password,'name'=>$name)));
						//echo $customer->getId();
					 }
					 else
					 {
					$result=array('status'=>array("code"=>"0","message"=>"Registration Error"));
					
					 }
				   
				   
				   
		}
         
	   
	    /* $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
	   $addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');
	   $address = $addresss->create();
                    $address->setCustomerId($customer->getId())
                    ->setTelephone('0038511223344')
                    ->setIsDefaultBilling('1')
                    ->setIsDefaultShipping('1')
                    ->setSaveInAddressBook('1'); 
                     $address = $addresss->create();
                    $address->setCustomerId($customer->getId())
                    ->setFirstname('Mav')
                    ->setLastname('rick')
                    ->setCountryId('HR')
                    //->setRegionId('1') //state/province, only needed if the country is USA
                    ->setPostcode('31000')
                    ->setCity('Osijek')
                    ->setTelephone('0038511223344')
                    ->setFax('0038511223355')
                    ->setCompany('GMI')
                    ->setStreet('NO:12 Lake View')
                    ->setIsDefaultBilling('1')
                    ->setIsDefaultShipping('1')
                    ->setSaveInAddressBook('1'); 
	   
	    $address->save(); */
	   
	   
	   
	   
	   
	   
        
		// $id=$customer->getId(); 
		
		/* $redirectUrl='';
		$customer = $this->customerAccountManagement
                         ->createAccount($username,$password,$redirectUrl); */
		
		
		
		
		
		 
		 return $result; 
	 }
	
	
	   /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $productid Left hand operand.
	 * @param string $quantity Right hand operand.
	 * @param string $price Right hand operand.
	 * @param string $userid Right hand operand.
     * @return array()|associativeArray(). 
     */
    public function addtocart($productid,$quantity,$userid) { 
      
			 
	     
			
		$store=$this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        //$customer->loadByEmail($orderData['email']);// load customet by email address
		$customer->load($userid);
		 $customer->getId();
		 $customer->getEntityId();
       /*  if(!$customer->getEntityId()){
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email']) 
                    ->setPassword($orderData['email']);
            $customer->save();
        } */
		
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		
		
		$quote_sql_customer = "Select * FROM customer_entity where entity_id=$userid ";
		 $result_result_customer = $connection->fetchAll($quote_sql_customer);	
		 //exit;
		 if(!empty($result_result_customer)):
		 
		
		$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and is_active=1";
		$result_result = $connection->fetchAll($quote_sql);
		//print_r($result_result); 
		if(!empty($result_result))
		{
		    $quoteId=$result_result[0]['entity_id'];
			$quote = $this->quote->create()->load($quoteId);
			//echo"1";
			
		  $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		  $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
          $checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		  $checkoutSession->setQuoteId($quoteId);
		  
		  $this->cart->addProduct($productdetails, array('qty' =>$quantity));
		  $this->cart->save();
			 
		}
		else 
		{
			
        
		$quote=$this->quote->create();
		
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		 $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
         /* $quote->addProduct(
                //$product,
				$productdetails,
                intval($quantity)
            ); */  
		/** @var $checkoutSession \Magento\Checkout\Model\Session */
        //$checkoutSession = $this->getModel('Magento\Checkout\Model\Session');
		//$checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
        //$checkoutSession->setQuoteId($quote->getId());
		
		$checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		$checkoutSession->setQuoteId($quote->getId());
		
		
		$this->cart->addProduct($productdetails, array('qty' =>$quantity));
		$this->cart->save();
		$quote = $this->cart->getQuote();
        //$quote->setCustomerId ($this->_currentCustomer->getCustomerId ());
  	    $quote->setCustomerId ($userid);
		// Configure quote
		//$quote->setInventoryProcessed (false);
		//$quote->collectTotals ();

		// Update changes
		 $quote->setStore($store); //set store for which you create quote
        // if you have allready buyer id then you can load customer directly 
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer);
		
		
		$quote->save();
		//echo $quote->getId();
		}
		
		
		if($quote->getId())
		 {
			
		   //$result[]=array("message"=>"product Added");
		   $result[]=array('status'=>array("code"=>"1","message"=>"product Added"));
		 }
		 else
		 {
		 
		// $result[]=array("message"=>"product not Added");
		$result[]=array('status'=>array("code"=>"0","message"=>"product not Added"));
		 }
		 
		 
		else:
		  $result[]=array('status'=>array("code"=>"0","message"=>"error"));
		endif;
		
		
        return $result;   
    
	 }
	 
	 
	    /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $productid Left hand operand.
	 * @param string $quantity Right hand operand.
	 * @param string $price Right hand operand.  
	 * @param string $userid Right hand operand.
     * @return array()|associativeArray(). 
     */
    public function updatecartproduct($productid,$quantity,$userid) { 
      
		  
		  $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		  
		
		$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and is_active=1";
		$result_result = $connection->fetchAll($quote_sql);
		//print_r($result_result); 
		if(!empty($result_result))
		{
		    
		    $quoteId=$result_result[0]['entity_id'];
			
			
		    $quote_sql2 = "Select * FROM quote_item where quote_id=$quoteId and product_id=$productid";
		   $result_result2 = $connection->fetchAll($quote_sql2);
			
			if(!empty($result_result2))
			
			{
				$checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		       $checkoutSession->setQuoteId($quoteId);
			
			
				 $quote_item_id=$result_result2[0]['item_id'];
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
				$itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item mode
				$quoteItem=$itemModel->load($quote_item_id);//load particular item which you want to delete by his item id
				$quoteItem->delete();//deletes the item
				$this->cart->removeItem($quote_item_id);
		        //$this->cart->save();
				 
				  $quote = $this->quote->create()->load($quoteId);
				  $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				  $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
				  $this->cart->addProduct($productdetails, array('qty' =>$quantity));
				  $this->cart->save();
				
				$result[]=array('status'=>array("code"=>"1","message"=>"success"));
			}
			else
			{
				$result[]=array('status'=>array("code"=>"0","message"=>"error"));
			}
			
			
			
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"error"));
		}
       
		 return $result;  
	 }
	 
	 
	    /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $productid Left hand operand.    
	 * @param string $userid Right hand operand.
     * @return array()|associativeArray(). 
     */
    public function deletecartproduct($productid,$userid) { 
      
		  $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		  
		
		$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and is_active=1";
		$result_result = $connection->fetchAll($quote_sql);
		//print_r($result_result); 
		if(!empty($result_result))
		{
		   
			// $quote->save();
			$quoteId=$result_result[0]['entity_id'];
			
			  
			
		   $quote_sql2 = "Select * FROM quote_item where quote_id=$quoteId and product_id=$productid";
		    $result_result2 = $connection->fetchAll($quote_sql2);
			
			if(!empty($result_result2))
			
			{
				$checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		    $checkoutSession->setQuoteId($quoteId);
			 
				$quote_item_id=$result_result2[0]['item_id'];
				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
				$itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item mode
				$quoteItem=$itemModel->load($quote_item_id);//load particular item which you want to delete by his item id
				$quoteItem->delete();//deletes the item
				$this->cart->removeItem($quote_item_id);
		       $this->cart->save();
				$result[]=array('status'=>array("code"=>"1","message"=>"success"));
			}
			else
			{
				$result[]=array('status'=>array("code"=>"0","message"=>"error")); 
			}
			
			
			
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"error"));
		}
       
		 return $result;  
	 }
	 
	     /**
     * Return the sum of the two numbers.
     * @param string $userid Right hand operand.
     * @return array()|associativeArray(). 
     */
    public function getcartproduct($userid) { 
      
		   $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		   
		 $quoteId='';
		$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and is_active=1";
		$result_result = $connection->fetchAll($quote_sql);
		if(!empty($result_result))
		{
			$quoteId=$result_result[0]['entity_id'];
			
			 $quote_sql2 = "Select * FROM quote_item where quote_id=$quoteId";
		    $result_result2 = $connection->fetchAll($quote_sql2);
			
			if(!empty($result_result2))
			
			{
				foreach( $result_result2 AS $data)
				{
			$product_id=$data['product_id'];
				$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($product_id);
					$allimages=$currentproduct->getMediaGallery('images');
					$imgarray=array();
					foreach($allimages as $imgurl)
						{
							$imgarray[]='http://159.203.151.92/pub/media/catalog/product'.$imgurl['file'];
						}
						
				  $offfer_tag = $currentproduct->getData('offfer_tag');
					$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
					$optionText =$attr->getSource()->getOptionText($offfer_tag);
				
					if($optionText=='')
					{
						$optionText='null';
					} 
				   
				      $_price=$currentproduct->getPrice();
				     $_finalPrice=$currentproduct->getFinalPrice();
					 $savetag='null';
						if($_finalPrice < $_price):
						$_savePercent = 100 - round(($_finalPrice / $_price)*100);
						//echo $_savePercent;
						$savetag=$_savePercent;
                   endif;

				   $producturl=$currentproduct->getProductUrl();
				    
				   //$wish='';
				   /* $sql_wishlist = "Select * FROM wishlist a ,wishlist_item b  where a.customer_id=$sellerid and a.wishlist_id=b.wishlist_id and b.product_id=$productId ";
		           $result_wishlist = $connection->fetchAll($sql_wishlist);
				   $wish='';
				   if(!empty($result_wishlist))
				   {
					   $wish=array("productId"=>$productId,"sellerid"=>$sellerid,"status"=>1); 
					   
				   }
				   else
				   {
					   $wish=array("productId"=>$productId,"sellerid"=>$sellerid,"status"=>0);
				   } */
				   
				   
				   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
				   $storeId = $this->storeManager->getStore()->getId();
                   $reviewFactory->getEntitySummary($currentproduct, $storeId);
				   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
				   if($ratingSummary=='')
				{
					$ratingSummary='null';
				} 
				   //print_r($ratingSummary);
				   
					$result4[]=array("id"=>$product_id,"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"fullImage"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getImage(),'sku'=>$currentproduct->getSku(),'mediagallery'=>$imgarray,'qty'=>$data['qty'],"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
				}
				//print_r($result4);
				$result[]=array('status'=>array("code"=>"1","message"=>"success"),'product'=>$result4,'quoteid'=>$quoteId);
			}
			else
			{
				$result[]=array('status'=>array("code"=>"0","message"=>"error"));
			}
			
			
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"error"));
		
		}
		 return $result;  
		 
	 }
	 
	 
	    /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $productid Left hand operand.
	 * @param string $quantity Right hand operand.
	 * @param string $userid Right hand operand.
	 * @param string $dat Right hand operand.
	 * @param string $day Right hand operand.
	 * @param string $slot Right hand operand.
	 * @param string $slotno Right hand operand.
	 * @return array()|associativeArray(). 
     */
    public function bookingaddtocart($productid,$quantity,$userid,$dat,$day,$slot,$slotno) { 
      
	   
		
		
		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
	     $helper = $objectManager->get('Webkul\Marketplace\Helper\Data');
		 //$helper->setCurrentStore(4);
		 $helper->setCurrentStore(1);
	     $datvalue=str_replace("-","/",$dat);	
		 
		  $store=$this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        //$customer->setWebsiteId($websiteId);
        //$customer->loadByEmail($orderData['email']);// load customet by email address
		$customer->load($userid);
		 $customer->getId();
		 $customer->getEntityId();
		 $customer= $this->customerRepository->getById($customer->getEntityId());
		 
		 
		    $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		    $connection = $resource->getConnection();  
		    //$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=4 and is_active=1 order by entity_id ASC limit 1";
	        
			$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and is_active=1 order by entity_id ASC limit 1";
	        
			$result_result = $connection->fetchAll($quote_sql);
		    //print_r($result_result); 
				if(!empty($result_result))
				{
					                   $quoteId=$result_result[0]['entity_id'];
					                   //$quote_sql = "delete from quote where customer_id=$userid and store_id=4 and entity_id=$quoteId ";
									   $quote_sql = "delete from quote where customer_id=$userid and store_id=1 and entity_id=$quoteId ";
										$connection->rawQuery($quote_sql);
					
					
					                    $quote=$this->quote->create(); 
										$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
										 $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
										$checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
										$checkoutSession->setQuoteId($quote->getId());
										
										
										$this->cart->addProduct($productdetails, array('qty' =>1));
										$this->cart->save();
										$quote = $this->cart->getQuote();
										//$quote->setCustomerId ($this->_currentCustomer->getCustomerId ());
										$quote->setCustomerId ($userid);
										// Configure quote 
										//$quote->setInventoryProcessed (false);
										//$quote->collectTotals ();

										// Update changes
										 $quote->setStore($store); //set store for which you create quote
										// if you have allready buyer id then you can load customer directly 
										//$customer= $this->customerRepository->getById($customer->getEntityId());
										$quote->setCurrency();
										$quote->assignCustomer($customer); 
										$quote->collectTotals()->save();
										$quote_id=$quote->getId();
										$quoteItems = $quote->getAllVisibleItems(); 
										foreach ($quoteItems as $item)
										{
										$quoteItem_id = $item->getId();
										//$quote_sql = "update quote_item  set booking_date='10/10/2016',booking_day='Monday',booking_slot_time='9:0-12:0',booking_slot_count='1' where item_id='$quoteItem_id' and quote_id='$quote_id'";
										$quote_sql = "update quote_item  set booking_date='$datvalue',booking_day='$day',booking_slot_time='$slot',booking_slot_count='$slotno' where item_id='$quoteItem_id' and quote_id='$quote_id'";
										$connection->rawQuery($quote_sql);
										}
					
					
					
					
				}
				else
				{
					                   
					                    $quote=$this->quote->create(); 
										
										$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
										 $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
										$checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
										$checkoutSession->setQuoteId($quote->getId());
										
										
										$this->cart->addProduct($productdetails, array('qty' =>1));
										$this->cart->save();
										$quote = $this->cart->getQuote();
										//$quote->setCustomerId ($this->_currentCustomer->getCustomerId ());
										$quote->setCustomerId ($userid);
										// Configure quote 
										//$quote->setInventoryProcessed (false);
										//$quote->collectTotals ();

										// Update changes
										 $quote->setStore($store); //set store for which you create quote
										// if you have allready buyer id then you can load customer directly 
										//$customer= $this->customerRepository->getById($customer->getEntityId());
										$quote->setCurrency();
										$quote->assignCustomer($customer); 
										$quote->collectTotals()->save();
										$quote_id=$quote->getId();
										$quoteItems = $quote->getAllVisibleItems(); 
										foreach ($quoteItems as $item)
										{
										$quoteItem_id = $item->getId();
										//$quote_sql = "update quote_item  set booking_date='10/10/2016',booking_day='Monday',booking_slot_time='9:0-12:0',booking_slot_count='1' where item_id='$quoteItem_id' and quote_id='$quote_id'";
										$quote_sql = "update quote_item  set booking_date='$datvalue',booking_day='$day',booking_slot_time='$slot',booking_slot_count='$slotno' where item_id='$quoteItem_id' and quote_id='$quote_id'";
										$connection->rawQuery($quote_sql);
										}  
										
				} 
 
		
        
		if($quote->getId())
		 {
		
			$result='success';
		   
		 }
		 else
		 {
		
		$result='error';
		 }
        return $result;
		
		
		
		
    
	 }
	 
	 
	   /**
     * Return the sum of the two numbers.
     *
     * @api
	 * @param string $sellerid Left hand operand.
     * @param string $name Left hand operand.
	 * @param int $userid 
	 * @return array()|associativeArray(). 
     */
	 public function productsearch($sellerid,$name,$userid)
	 {
		 
		          $result4='';
				  if($name!='')
				  {
					  $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				    $products = $objectManager->create('Magento\Catalog\Model\ProductFactory')
				                     ->create()
									 ->getCollection()
									// ->addFieldToFilter('store_id', 1); 
                                   ->addFieldToFilter('name', array("like"=>'%'.$name.'%') );									
				  
				 //var_dump($products->getData());
				  if(!empty($products->getData())):
				    foreach($products as $product)
				  {
                        $productId =$product->getId();	

						
						$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
						$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
						$connection = $resource->getConnection();
						$quote_sql = "Select * FROM marketplace_product where mageproduct_id=$productId and seller_id=$sellerid ";
						$result_result = $connection->fetchAll($quote_sql);
						
						
						
						
						if(!empty($result_result))
						{
							
										$currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
										$offfer_tag = $currentproduct->getData('offfer_tag');
										$attr = $currentproduct->getResource()->getAttribute('offfer_tag'); 				
										$optionText =$attr->getSource()->getOptionText($offfer_tag);
								
										if($optionText=='')
											{
											  $optionText='null';
											 }
								   
									 $_price=$currentproduct->getPrice();
									 $_finalPrice=$currentproduct->getFinalPrice();
									 $savetag='null';
										if($_finalPrice < $_price):
										$_savePercent = 100 - round(($_finalPrice / $_price)*100);
										//echo $_savePercent;
										$savetag=$_savePercent;
									   endif;

								   $producturl=$currentproduct->getProductUrl();
								   
								   
								    $wishlisttag='';
											if($userid > 0)
										   {
											  $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id and b.product_id=$productId";
											  $result_wishlist = $connection->fetchAll($wishlist);
											if(!empty($result_wishlist))
											{
												$wishlisttag=1;
												 
											}
											else
											{
												$wishlisttag=0;
											}
										   }
										   else
										   {
											$wishlisttag=0;
										   } 
										   
										 
										   
										   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
										   $storeId = $this->storeManager->getStore()->getId();
										   $reviewFactory->getEntitySummary($currentproduct, $storeId);
										   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
										   if($ratingSummary=='')
										{
											$ratingSummary='null';
										}
								   
								   
								   
								   
								   
								   
								   
									   
									$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary),'wishlisttag'=>$wishlisttag);
											
							
						}
						
						
								
				  }
				  $result[]=array('status'=>array("code"=>"1","message"=>"success"),'product'=>$result4);
		          return $result; 
                  else:
 				  
				  $result[]=array('status'=>array("code"=>"1","message"=>"success"),'product'=>'No result found!');
		          return $result; 
				  
				  endif;
           
				  }
				  else
				  {
					  
					  $result[]=array('status'=>array("code"=>"0","message"=>"error"));
					  return $result; 
				  }
				    
	 } 
	 
 

 /**
     * Return the sum of the two numbers.
     *
     * @api
	 * @param string $sellerid Left hand operand.
     * @param string $productid Left hand operand.
	 * @return array()|associativeArray(). 
     */
	 
	public function simillarproducts($sellerid,$productid,$userid) 
	{
		               $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
						$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
						$connection = $resource->getConnection();
						//$quote_sql = "Select * FROM marketplace_product where mageproduct_id=$productid and seller_id=$sellerid";
						//$result_result = $connection->fetchAll($quote_sql);	
						//print_r($result_result);
						
						 // if(!empty($result_result))					
						
					//	{
							
						$currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);

						$cats = $currentproduct->getCategoryIds();
						
						
						$c=0;
						
						$categoryname='';
						$categoryId='';
						$result4='';
						  foreach ($cats as $categoryIds) 
						 {
							// echo $categoryIds;
							 if($categoryIds!=2)
							{
									 if($c<1)
									{
									//echo $categoryId =$categoryIds;  
									$sql3 = "SELECT * FROM catalog_category_product a ,marketplace_product b where a.category_id=$categoryIds and a.product_id=b.mageproduct_id and seller_id=$sellerid";
									$result3 = $connection->fetchAll($sql3);
									//print_r($result3);
									   
										 foreach($result3 as $data)
											{
												
                                               $productId =$data['product_id'];
											  $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
						                      $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
							                  
											  $offfer_tag = $currentproduct->getData('offfer_tag');
												$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
												//$optionText='null';
											 //if ($attr->usesSource()) {
												   $optionText =$attr->getSource()->getOptionText($offfer_tag);
											// }
											if($optionText=='')
											{
												$optionText='null';
											}
											   
												 $_price=$currentproduct->getPrice();
												 $_finalPrice=$currentproduct->getFinalPrice();
												 $savetag='null';
													if($_finalPrice < $_price):
													$_savePercent = 100 - round(($_finalPrice / $_price)*100);
													//echo $_savePercent;
													$savetag=$_savePercent;
											   endif;

											   $producturl=$currentproduct->getProductUrl();
											   
											   
											      $wishlisttag='';
														if($userid > 0)
													   {
														  $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id and b.product_id=$productId";
														  $result_wishlist = $connection->fetchAll($wishlist);
														if(!empty($result_wishlist))
														{
															$wishlisttag=1;
															 
														}
														else
														{
															$wishlisttag=0;
														}
													   }
													   else
													   {
														$wishlisttag=0;
													   } 
											   
											   
											   
											   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
											   $storeId = $this->storeManager->getStore()->getId();
											   $reviewFactory->getEntitySummary($currentproduct, $storeId);
											   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
											   if($ratingSummary=='')
											{
												$ratingSummary='null';
											} 
											  
											  
											  $result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary),'wishlisttag'=>$wishlisttag);
							                 // $result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail());
							

												
														
											}
									}
									$c++; 
							}
						 }
						
						
				$result[]=array('status'=>array("code"=>"1","message"=>"success"),'product'=>$result4);
		         return $result; 
	 } 
	 
	 
/**
     * Return the sum of the two numbers.
     *
     * @api
	 * @param string $sellerid Left hand operand.
     * @param string $name Left hand operand.
	 * @return array()|associativeArray(). 
     */
	 public function autocompletesearch($sellerid,$name)
	 {
		 
		          $result4='';
				  if($name!='')
				  {
					  $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				      $products = $objectManager->create('Magento\Catalog\Model\ProductFactory')
				                     ->create()
									 ->getCollection()
									//->addFieldToFilter('store_id', 1)
                                   ->addFieldToFilter('name', array("like"=>'%'.$name.'%') );									
				  
				 //echo "<pr>";var_dump($products->getData()); 
				  if(!empty($products->getData())):
				    foreach($products as $product)
				  {
                         $productId =$product->getId();	 
						
						$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
						$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
						$connection = $resource->getConnection();
						 $quote_sql = "Select * FROM marketplace_product where mageproduct_id=$productId and seller_id=$sellerid ";
						$result_result = $connection->fetchAll($quote_sql);	
						
						if(!empty($result_result))
						{
							
						$currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                        //echo"<pre>";print_r($currentproduct->getData());
						$cats = $currentproduct->getCategoryIds();
						$c=0;
						
						$categoryname='';
						$categoryId='';
						 foreach ($cats as $categoryIds) {
							if($categoryIds!=2)
							{
								if($c<1)
							{
						//$currentcategory = $objectManager->create('Magento\Catalog\Model\category')->load($categoryIds);
						     //$sami=$categoryIds;
							 //$currentcategory = $objectManager->create('Magento\Catalog\Model\category')->load($sami);
							// $categoryname=$currentcategory->getName();
							//$categoryId = 3;
							$categoryId =$categoryIds;
							$_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
							$object_manager = $_objectManager->create('Magento\Catalog\Model\Category')
							->load($categoryId);
							//print_r($object_manager->getData());
							$categoryname=$object_manager->getName();
							}
							$c++;
							}
							
						}  

						$producturl=$currentproduct->getProductUrl();
									   
						//$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"categoryName"=>$currentcategory->getName());											
						$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"categoryName"=>$categoryname,"categoryid"=>$categoryId,"urltag"=>$name);	
						}	
								
				  }
				  $result[]=array('status'=>array("code"=>"1","message"=>"success"),'product'=>$result4);
		          return $result; 
                  else:
 				  
				  $result[]=array('status'=>array("code"=>"1","message"=>"success"),'product'=>'No result found!');
		          return $result; 
				  
				  endif;
           
				  }
				  else
				  {
					  
					  $result[]=array('status'=>array("code"=>"0","message"=>"error"));
					  return $result; 
				  }
				    
	 }


    /**
     * Return the Home Page Banner Images of the Website.
     *
     * @api
     * @return array()|associativeArray().
     */
     public function homepagebanner()
	 {
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql = "Select * FROM custom_productbanner where status=1 order by productbanner_id asc";
		$result1 = $connection->fetchAll($sql);	

        if(!empty($result1))
		{			
			
		foreach ($result1 as $data) {
		$banner_id=$data['productbanner_id'];
        $image_name=$data['name'];
		$image_background=$data['background'];
		$image_caption=$data['imagecaption'];
		$result[]=array('homepagebanner'=>array(array('Id'=>$banner_id,'Image Name'=>$image_name,'img'=>'http://localhost/markateplace/pub/media/Gridpart4/background/image'.$image_background.'','Image Caption'=>$image_caption)));	
			
		}			
		}	
		else
		{
			$result[]=array('status'=>array("code"=>"1","message"=>"error"));
			
		}

				
	     return $result;  
	
	 }	 
	 
	 /**
     * Return the Home Page Banner Images of the Website.
     *
     * @api
     * @return array()|associativeArray().
     */
     public function appmarecentproductapp()
	 {
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql = "Select * FROM gridpart2_template where status=1 and type='Product' order by gridpart2template_id asc";
		$result1 = $connection->fetchAll($sql);	
		$result='';
        if(!empty($result1))
		{
		foreach ($result1 as $data) {
         if($data['background']!=''){
			 $imgurl= 'http://159.203.151.92/pub/media/gridpart2/background/image/'.$data['background']	;		
		 }
		 else
		 {
			 $imgurl='';
		 }
		$result[]=array('text'=>$data['name'],'image'=>$imgurl,'android'=>$data['stylecolor'],'ios'=>$data['textcolor']);
		}
		}			
	     return $result;  
	
	 }	
	  /**
     * Return the Home Page Banner Images of the Website.
     *
     * @api
     * @return array()|associativeArray().
     */
     public function appmatoptproductapp()
	 {
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql = "Select * FROM gridpart2_template where status=1 and type='Product' and top=1 order by gridpart2template_id asc";
		$result1 = $connection->fetchAll($sql);	
		$result='';
        if(!empty($result1))
		{
		foreach ($result1 as $data) {
         if($data['background']!=''){
			 $imgurl= 'http://159.203.151.92/pub/media/gridpart2/background/image/'.$data['background']	;		
		 }
		 else
		 {
			 $imgurl='';
		 }
		$result[]=array('text'=>$data['name'],'image'=>$imgurl,'android'=>$data['stylecolor'],'ios'=>$data['textcolor']);
		}
		}			
	     return $result;  
	
	 }
	 
	  /**
     * Return the Home Page Banner Images of the Website.
     *
     * @param int $userid
	 * @param string $email 
	 * @param string $name
	 * @param string $street
	 * @param string $city
	 * @param string $country_id
	 * @param string $postcode
	 * @param string $telephone
	 * @param string $shipping
	 * @param string $payment
	 * @param int $quoteId
     * @return array()|associativeArray().
     */
     public function customcheckout($userid,$email,$name,$street,$city,$country_id,$postcode,$telephone,$shipping,$payment,$quoteId)
	 {
		/* $parts = explode(" ", $name);
		 $lastname = array_pop($parts);
		if($lastname=='')
		{
			$lastname='@';
		}
         $firstname = implode(" ", $parts);
		if($firstname=='')
		{
			$firstname='@';
		} */
		
		if(strpos($name, ' ') > 0)
		{
			$parts = explode(" ", $name);
		 $lastname = array_pop($parts);
		 $firstname = implode(" ", $parts);
		
		}
    else
    {
		 $lastname = $name;
		  $firstname = $name;
	}
		
		//exit;
		
		
		
		
		
		 $orderData=[
				 'currency_id'  => 'USD',
				 'email'        => $email, //buyer email id
				 'shipping_address' =>[
						'firstname'    => $firstname, //address Details
						'lastname'     => $lastname,
								'street' => $street,
								'city' => $city,
						//'country_id' => 'IN',
						'country_id' => $country_id,
						'region' => '',
						'postcode' => $postcode,
						'telephone' => $telephone,
						'fax' => '',
						'save_in_address_book' => 1
							 ],
			 'items'=> [ //array of product which order you want to create
						  ['product_id'=>'5','qty'=>1,'price'=>'50'],
						  ['product_id'=>'6','qty'=>2,'price'=>'100']
						]  
						
			];

         /*for sote 4 */			
		  /* $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
	     $helper = $objectManager->get('Webkul\Marketplace\Helper\Data');
		 $helper->setCurrentStore(4);  */
		 /* end for sote 4 */
		
		
		$store=$this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        //$customer->loadByEmail($orderData['email']);// load customet by email address
		$customer->load($userid);
		 $customer->getId();
		 $customer->getEntityId();
       /*  if(!$customer->getEntityId()){
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email']) 
                    ->setPassword($orderData['email']);
            $customer->save();
        } */
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		
		//$quote_sql_latest = "Select * FROM quote where customer_id=$userid and store_id=4 and is_active=1 order by entity_id ASC limit 1";
		$quote_sql_latest = "Select * FROM quote where customer_id=$userid and store_id=1 and is_active=1 order by entity_id ASC limit 1";
	    

		$result_result_cart = $connection->fetchAll($quote_sql_latest);
		  //  print_r($result_result_cart); 
		
		
		$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and entity_id='$quoteId' and is_active=1";
		//$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=4 and entity_id='$quoteId' and is_active=1";
		
		
		$result_result = $connection->fetchAll($quote_sql);	
		if(!empty($result_result))
		{
		
			  $quoteId2=$result_result[0]['entity_id'];
			 $quote = $this->quote->create()->load($quoteId2);
			$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
           $checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		  $checkoutSession->setQuoteId($quoteId2);
		   
		  
		          //Set Address to quote
         $quote->getBillingAddress()->addData($orderData['shipping_address']);
        $quote->getShippingAddress()->addData($orderData['shipping_address']);
  
        // Collect Rates and Set Shipping & Payment Method
 
         $shippingAddress=$quote->getShippingAddress(); 
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        //->setShippingMethod('freeshipping_freeshipping'); //shipping method
						->setShippingMethod($shipping); //shipping method
						
        //$quote->setPaymentMethod('cashondelivery'); //payment method
		$quote->setPaymentMethod($payment);
        $quote->setInventoryProcessed(false); //not effetc inventory
        $quote->save(); //Now Save quote and your quote is ready
  
        // Set Sales Order Payment
        //$quote->getPayment()->importData(['method' => 'cashondelivery']);
          $quote->getPayment()->importData(['method' => $payment]);
        // Collect Totals & Save Quote
        $quote->collectTotals()->save();
        $quote->getId();
        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);
        
       $order->setEmailSent(0);
	  //$order->setEmailSent(1);
        $increment_id = $order->getRealOrderId();
	    $lastOrderId = $increment_id;
		
		$helper = $objectManager->get(
            'Webkul\Marketplace\Helper\Data'
        );
		
		$getProductSalesCalculation = $objectManager->get(
            'Webkul\Marketplace\Observer\SalesOrderPlaceAfterObserver'
        );
        $getProductSalesCalculation->getProductSalesCalculation($order);

        /*send placed order mail notification to seller*/

        $salesOrder = $objectManager->create(
            'Webkul\Marketplace\Model\ResourceModel\Seller\Collection'
        )->getTable('sales_order');
        $salesOrderItem = $objectManager->create(
            'Webkul\Marketplace\Model\ResourceModel\Seller\Collection'
        )->getTable('sales_order_item');
		
		
		
		 $paymentCode = '';
        if ($order->getPayment()) {
            $paymentCode = $order->getPayment()->getMethod();
        }

        $shippingInfo = '';
        $shippingDes = '';

        $billingId = $order->getBillingAddress()->getId();

        $billaddress = $objectManager->create(
            'Magento\Sales\Model\Order\Address'
        )->load($billingId);
        $billinginfo = $billaddress['firstname'].'<br/>'.
        $billaddress['street'].'<br/>'.
        $billaddress['city'].' '.
        $billaddress['region'].' '.
        $billaddress['postcode'].'<br/>'.
        $objectManager->create(
            'Magento\Directory\Model\Country'
        )->load($billaddress['country_id'])->getName().'<br/>T:'.
        $billaddress['telephone'];

        $payment = $order->getPayment()->getMethodInstance()->getTitle();

		
		
		 if ($order->getShippingAddress()) {
            $shippingId = $order->getShippingAddress()->getId();
            $address = $objectManager->create(
                'Magento\Sales\Model\Order\Address'
            )->load($shippingId);
            $shippingInfo = $address['firstname'].'<br/>'.
            $address['street'].'<br/>'.
            $address['city'].' '.
            $address['region'].' '.
            $address['postcode'].'<br/>'.
            $objectManager->create(
                'Magento\Directory\Model\Country'
            )->load($address['country_id'])->getName().'<br/>T:'.
            $address['telephone'];
            $shippingDes = $order->getShippingDescription();
        }
		
		
		$adminStoremail = $helper->getAdminEmailId();
        $adminEmail = $adminStoremail ? $adminStoremail : $helper->getDefaultTransEmailId();
        $adminUsername = 'Admin';

        $customerModel = $objectManager->create(
            'Magento\Customer\Model\Customer'
        );

        $sellerOrder = $objectManager->create(
            'Webkul\Marketplace\Model\Orders'
        )
        ->getCollection()
        ->addFieldToFilter('order_id', $lastOrderId)
        ->addFieldToFilter('seller_id', ['neq' => 0]);
		
	   
	   foreach ($sellerOrder as $info) {
            $userdata = $customerModel->load($info['seller_id']);
            $username = $userdata['firstname'];
            $useremail = $userdata['email'];

            $senderInfo = [];
            $receiverInfo = [];

            $receiverInfo = [
                'name' => $username,
                'email' => $useremail,
            ];
            $senderInfo = [
                'name' => $adminUsername,
                'email' => $adminEmail,
            ];
            $totalprice = '';
            $totalTaxAmount = 0;
            $codCharges = 0;
            $shippingCharges = 0;
            $orderinfo = '';

            $saleslistIds = [];
            $collection1 = $objectManager->create(
                'Webkul\Marketplace\Model\Saleslist'
            )->getCollection()
            ->addFieldToFilter('order_id', $lastOrderId)
            ->addFieldToFilter('seller_id', $info['seller_id'])
            ->addFieldToFilter('parent_item_id', ['null' => 'true'])
            ->addFieldToFilter('magerealorder_id', ['neq' => 0])
            ->addFieldToSelect('entity_id');

            $saleslistIds = $collection1->getData();

            $fetchsale = $objectManager->create(
                'Webkul\Marketplace\Model\Saleslist'
            )
            ->getCollection()
            ->addFieldToFilter(
                'entity_id', 
                ['in' => $saleslistIds]
            );
            $fetchsale->getSelect()->join(
                $salesOrder.' as so', 
                'main_table.order_id = so.entity_id', 
                ['status' => 'status']
            );

            $fetchsale->getSelect()->join(
                $salesOrderItem.' as soi', 
                'main_table.order_item_id = soi.item_id AND main_table.order_id = soi.order_id', 
                [
                    'item_id' => 'item_id', 
                    'qty_canceled' => 'qty_canceled', 
                    'qty_invoiced' => 'qty_invoiced', 
                    'qty_ordered' => 'qty_ordered', 
                    'qty_refunded' => 'qty_refunded', 
                    'qty_shipped' => 'qty_shipped', 
                    'product_options' => 'product_options', 
                    'mage_parent_item_id' => 'parent_item_id'
                ]
            );
            foreach ($fetchsale as $res) {
                $product = $objectManager->create(
                    'Magento\Catalog\Model\Product'
                )->load($res['mageproduct_id']);

                /* product name */
                $productName = $res->getMageproName();
                $result = [];
                if ($options = unserialize($res->getProductOptions())) {
                    if (isset($options['options'])) {
                        $result = array_merge($result, $options['options']);
                    }
                    if (isset($options['additional_options'])) {
                        $result = array_merge($result, $options['additional_options']);
                    }
                    if (isset($options['attributes_info'])) {
                        $result = array_merge($result, $options['attributes_info']);
                    }
                }
                if ($_options = $result) {
                    $proOptionData = '<dl class="item-options">';
                    foreach ($_options as $_option) {
                        $proOptionData .= '<dt>'.$_option['label'].'</dt>';

                        $proOptionData .= '<dd>'.$_option['value'];
                        $proOptionData .= '</dd>';
                    }
                    $proOptionData .= '</dl>';
                    $productName = $productName.'<br/>'.$proOptionData;
                } else {
                    $productName = $productName.'<br/>';
                }
                /* end */

                $sku = $product->getSku();
                $orderinfo = $orderinfo."<tbody><tr>
                                <td class='item-info'>".$productName."</td>
                                <td class='item-info'>".$sku."</td>
                                <td class='item-qty'>".($res['magequantity'] * 1)."</td>
                                <td class='item-price'>".
                                    $order->formatPrice(
                                        $res['magepro_price'] * $res['magequantity']
                                    ).
                                '</td>
                             </tr></tbody>';
                $totalTaxAmount = $totalTaxAmount + $res['total_tax'];
                $totalprice = $totalprice + ($res['magepro_price'] * $res['magequantity']);

                /*
                * Low Stock Notification mail to seller
                */
                if ($helper->getlowStockNotification()) {
                    $stockItemQty = $product['quantity_and_stock_status']['qty'];
                    if ($stockItemQty <= $helper->getlowStockQty()) {
                        $orderProductInfo = "<tbody><tr>
                                <td class='item-info'>".$productName."</td>
                                <td class='item-info'>".$sku."</td>
                                <td class='item-qty'>".($res['magequantity'] * 1).'</td>
                             </tr></tbody>';

                        $emailTemplateVariables = [];
                        $emailTempVariables['myvar1'] = $orderProductInfo;
                        $emailTempVariables['myvar2'] = $username;

                        $this->_objectManager->get(
                            'Webkul\Marketplace\Helper\Email'
                        )->sendLowStockNotificationMail(
                            $emailTemplateVariables,
                            $senderInfo,
                            $receiverInfo
                        );
                    }
                }
            }
            $shippingCharges = $info->getShippingCharges();
            $totalCod = 0;

            if ($paymentCode == 'mpcashondelivery') {
                $totalCod = $info->getCodCharges();
                $codRow = "<tr class='subtotal'>
                            <th colspan='3'>".__('Cash On Delivery Charges')."</th>
                            <td colspan='3'><span>".
                                $order->formatPrice($totalCod).
                            '</span></td>
                            </tr>';
            } else {
                $codRow = '';
            }

            $orderinfo = $orderinfo."<tfoot class='order-totals'>
                                <tr class='subtotal'>
                                    <th colspan='3'>".__('Shipping & Handling Charges')."</th>
                                    <td colspan='3'><span>".
                                    $order->formatPrice($shippingCharges)."</span></td>
                                </tr>
                                <tr class='subtotal'>
                                    <th colspan='3'>".__('Tax Amount')."</th>
                                    <td colspan='3'><span>".
                                    $order->formatPrice($totalTaxAmount).'</span></td>
                                </tr>'.$codRow."
                                <tr class='subtotal'>
                                    <th colspan='3'>".__('Grandtotal')."</th>
                                    <td colspan='3'><span>".
                                    $order->formatPrice(
                                        $totalprice + 
                                        $totalTaxAmount + 
                                        $shippingCharges + 
                                        $totalCod
                                    ).'</span></td>
                                </tr></tfoot>';

            $emailTemplateVariables = [];
            if ($shippingInfo != '') {
                $isNotVirtual = 1;
            } else {
                $isNotVirtual = 0;
            }
            $emailTempVariables['myvar1'] = $order->getRealOrderId();
            $emailTempVariables['myvar2'] = $order['created_at'];
            $emailTempVariables['myvar4'] = $billinginfo;
            $emailTempVariables['myvar5'] = $payment;
            $emailTempVariables['myvar6'] = $shippingInfo;
            $emailTempVariables['isNotVirtual'] = $isNotVirtual;
            $emailTempVariables['myvar9'] = $shippingDes;
            $emailTempVariables['myvar8'] = $orderinfo;
            $emailTempVariables['myvar3'] = $username;

            $objectManager->get(
                'Webkul\Marketplace\Helper\Email'
            )->sendPlacedOrderEmail(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo
            );
        }
	   
	   
         if($order->getEntityId())
		 {
           // $result['order_id']= $order->getRealOrderId();
		   
		   
/* 		    $quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and is_active=1 order by entity_id ASC limit 1";
	        $result_result = $connection->fetchAll($quote_sql);
		    print_r($result_result);  */
				if(!empty($result_result_cart))
				{
					       $quoteId3=$result_result_cart[0]['entity_id'];
						   
						   if($quoteId3==$quoteId)
						   {
								   $quote_sql3 = "Select * FROM quote where customer_id=$userid and store_id=1 and is_active=1 and entity_id !=$quoteId3";
								   $result_result3 = $connection->fetchAll($quote_sql3);
								   if(!empty($result_result3))
								   {
									   foreach ($result_result3 as $qt)
									   {
											$qtid=$qt['entity_id'];
										   $quote_sql = "delete FROM quote where entity_id=$qtid ";
										  $connection->rawQuery($quote_sql); 
									   }
								   }
								   $result[]=array('status'=>array("code"=>"1","message"=>"success"),'order_id'=> $order->getRealOrderId());
								   
						   }
						   else
						   {
									$quote_sql2 = "Select * FROM quote_item where quote_id=$quoteId limit 1";
								   $result_result2 = $connection->fetchAll($quote_sql2);
									//print_r($result_result2);
									if(!empty($result_result2))
									
									{
										$checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
									$checkoutSession->setQuoteId($quoteId3);
									 
										$quote_item_id=$result_result2[0]['item_id'];
										$quote_item_productid=$result_result2[0]['product_id'];
										
										
										$quote_sql24 = "Select * FROM quote_item where quote_id=$quoteId3 and product_id=$quote_item_productid";
								        $result_result24 = $connection->fetchAll($quote_sql24);
										if(!empty($result_result24))
										{
											$quote_item_id=$result_result24[0]['item_id'];
											$objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
											$itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item mode
											$quoteItem=$itemModel->load($quote_item_id);//load particular item which you want to delete by his item id
											$quoteItem->delete();//deletes the item
											$this->cart->removeItem($quote_item_id);
										   $this->cart->save();
											
										}
										$result[]=array('status'=>array("code"=>"1","message"=>"success"),'order_id'=> $order->getRealOrderId());
										
									}
									else
									{
										$result[]=array('status'=>array("code"=>"1","message"=>"error"));
									}
						   }
						   
						   
						   
						   
						   
						   
						   
						   
						   
						   
						   
			               
				
				
				}
		   
		   
		   
		   
			
			
			
        }else{
            $result[]=array('status'=>array("code"=>"0","message"=>"error"));
        }  
		  
		  
		  
		  
		  
		  
		 // $result[]=array('satus'=>'error');
		  
		  
		  
		}			
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"error"));
		}			
		
 			
	     return $result;  
	
	 }

	  /**
     * Return the Home Page Banner Images of the Website.
     *
     * @param int $userid
	 * @param int $productid
	  
	* @return array()|associativeArray().
     */
     public function buynow($userid,$productid) 
	 {
		 
		  $store=$this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        //$customer->loadByEmail($orderData['email']);// load customet by email address
		$customer->load($userid);
		 $customer->getId();
		 $customer->getEntityId();
		 $customer= $this->customerRepository->getById($customer->getEntityId());
		 
		 
		    $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		    $connection = $resource->getConnection();  
		    $quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and is_active=1 order by entity_id ASC limit 1";
	        $result_result = $connection->fetchAll($quote_sql);
		    //print_r($result_result); 
				if(!empty($result_result))
				{
											$quoteId=$result_result[0]['entity_id'];
											$quote = $this->quote->create()->load($quoteId);
											//echo"1";
											
										 // $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
										  $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
										  $checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
										  $checkoutSession->setQuoteId($quoteId);
										  
										  $this->cart->addProduct($productdetails, array('qty' =>1));
										  $this->cart->save();
				}
				else
				{
					                   
					                    $quote=$this->quote->create();
										
										$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
										 $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
										$checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
										$checkoutSession->setQuoteId($quote->getId());
										
										
										$this->cart->addProduct($productdetails, array('qty' =>1));
										$this->cart->save();
										$quote = $this->cart->getQuote();
										//$quote->setCustomerId ($this->_currentCustomer->getCustomerId ());
										$quote->setCustomerId ($userid);
										// Configure quote
										//$quote->setInventoryProcessed (false);
										//$quote->collectTotals ();

										// Update changes
										 $quote->setStore($store); //set store for which you create quote
										// if you have allready buyer id then you can load customer directly 
										//$customer= $this->customerRepository->getById($customer->getEntityId());
										$quote->setCurrency();
										$quote->assignCustomer($customer);
										
										
										$quote->save();
										//echo $quote->getId();
				} 

		
		 $orderData=[
				 'currency_id'  => 'USD',
				 'email'        => '', //buyer email id
				 'shipping_address' =>[
						'firstname'    => '', //address Details
						'lastname'     => '',
								'street' => '',
								'city' => '',
						//'country_id' => 'IN',
						'country_id' => '',
						'region' => '',
						'postcode' => '',
						'telephone' => '',
						'fax' => '',
						'save_in_address_book' => 1
							 ],
			 'items'=> [ //array of product which order you want to create
						  ['product_id'=>$productid,'qty'=>1,'price'=>'0']
						]  
						
			];	
			
			
		
		 
		 	
        $quote=$this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote
        // if you have allready buyer id then you can load customer directly 
        //$customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer
 
        //add items in quote
        foreach($orderData['items'] as $item){
            $product=$this->_product->load($item['product_id']);
            //$product->setPrice($item['price']);
            $quote->addProduct(
                $product,
                intval($item['qty'])
            );
        }
 
         $quote->save(); //Now Save quote and your quote is ready
 
        // Collect Totals & Save Quote
        $quote->collectTotals()->save();
         //$quoteid=$quote->getId();
		 // $result['quoteid']= $quoteid;
		  $quoteid=$quote->getId();
		  if($quoteid)
		  {
			
			   //$result['quoteid']= $quoteid;
				$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'quoteid'=>$quoteid);
		  }
		  else
		  {
			 $result[]=array('status'=>array("code"=>"0","message"=>"error"));
		  }
        
		
        return $result;
			 
			
	 }
	  /**
     * Return the Home Page Banner Images of the Website.
     *
     * @param int $userid
	 * @param int $productid
	 * @param string $price 
	 * @param string $email 
	 * @param string $name
	 * @param string $street
	 * @param string $city
	 * @param string $country_id
	 * @param string $postcode
	 * @param string $telephone
	 * @param string $shipping
	 * @param string $payment
	 * @param string $quoteid
	 
     * @return array()|associativeArray().
     */
     public function customcheckoutdirect($userid,$productid,$price,$email,$name,$street,$city,$country_id,$postcode,$telephone,$shipping,$payment,$quoteid)
	 {
		if(strpos($name, ' ') > 0)
		{
			$parts = explode(" ", $name);
		 $lastname = array_pop($parts);
		 $firstname = implode(" ", $parts);
		
		}
    else
    {
		 $lastname = $name;
		  $firstname = $name;
	}
		 $orderData=[
				 'currency_id'  => 'USD',
				 'email'        => $email, //buyer email id
				 'shipping_address' =>[
						'firstname'    => $firstname, //address Details
						'lastname'     => $lastname,
								'street' => $street,
								'city' => $city,
						//'country_id' => 'IN',
						'country_id' => $country_id,
						'region' => '',
						'postcode' => $postcode,
						'telephone' => $telephone,
						'fax' => '',
						'save_in_address_book' => 1
							 ],
			 'items'=> [ //array of product which order you want to create
						  ['product_id'=>$productid,'qty'=>1,'price'=>$price]
						]  
						
			];	
			
			
		/* 	 $store=$this->_storeManager->getStore();
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        $customer->loadByEmail($orderData['email']);// load customet by email address
        if(!$customer->getEntityId()){
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email']) 
                    ->setPassword($orderData['email']);
            $customer->save();
        } */
		$store=$this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        //$customer->loadByEmail($orderData['email']);// load customet by email address
		$customer->load($userid);
		 $customer->getId();
		 $customer->getEntityId();
       /*  if(!$customer->getEntityId()){
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email']) 
                    ->setPassword($orderData['email']);
            $customer->save();
        } */
		
	    $quote = $this->quote->create()->load($quoteid);
        $quote=$this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote
        // if you have allready buyer id then you can load customer directly 
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer
 
        //add items in quote
        foreach($orderData['items'] as $item){
            $product=$this->_product->load($item['product_id']);
            $product->setPrice($item['price']);
            $quote->addProduct(
                $product,
                intval($item['qty'])
            );
        }
 
        //Set Address to quote
        $quote->getBillingAddress()->addData($orderData['shipping_address']);
        $quote->getShippingAddress()->addData($orderData['shipping_address']);
 
        // Collect Rates and Set Shipping & Payment Method
 
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod('freeshipping_freeshipping'); //shipping method
        $quote->setPaymentMethod('cashondelivery'); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory
        $quote->save(); //Now Save quote and your quote is ready
 
        // Set Sales Order Payment
        $quote->getPayment()->importData(['method' => 'cashondelivery']);
 
        // Collect Totals & Save Quote
        $quote->collectTotals()->save();
         //$quoteid=$quote->getId();
		 // $result['quoteid']= $quoteid;
		  $quoteid=$quote->getId();
		 /*  if($quoteid)
		  {
			    $result['quoteid']= $quoteid;
		  }
		  else
		  {
			  $result=['error'=>1,'msg'=>'Your custom message'];
		  } */
        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);
        
        $order->setEmailSent(0);
		
		
		
		$increment_id = $order->getRealOrderId();
       
		if($order->getEntityId()){
           // $result['order_id']= $order->getRealOrderId();
			$result[]=array('status'=>array("code"=>"1","message"=>"success"),'order_id'=> $order->getRealOrderId());
        }else{
            $result[]=array('status'=>array("code"=>"0","message"=>"error"));
        } 
        return $result;
			 
			
	
	 }
     /**
     * Return the sum of the two numbers.
     * @param int $quoteid Right hand operand.
     * @return array()|associativeArray(). 
     */
    public function customcheckoutdirectconfirm($quoteid) { 
      
		$quote = $this->quote->create()->load($quoteid);
		//echo $quote->getId();
       $order = $this->quoteManagement->submit($quote);
        
        //$order->setEmailSent(0);
		
		
		
		 $increment_id = $order->getRealOrderId();
        if($order->getEntityId()){
            $result['order_id']= $order->getRealOrderId();
        }else{
            $result=['error'=>1,'msg'=>'Your custom message'];
        }  
		  //$result=['error'=>1,'msg'=>'Your custom message'];
        return $result;
		  
		 
	 }
	 
	 
	 /**
     * Return the sum of the two numbers.
     * @param string $userid Right hand operand.
	 * @param int $quoteId
     * @return array()|associativeArray(). 
     */
    public function customcheckoutcarttotal($userid,$quoteId) { 
      
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and entity_id=$quoteId and is_active=1";
		$result_result = $connection->fetchAll($quote_sql);	
		//echo "<pre>";print_r($result_result);    
		if(!empty($result_result)) 
		{
			$base_subtotal_with_discount=$result_result[0]['base_subtotal_with_discount'];
			$subtotal=$result_result[0]['subtotal'];
			$discount=$subtotal-$base_subtotal_with_discount;
			
			$grand_total=$result_result[0]['grand_total'];
			
			$shipping_charge=$grand_total-$base_subtotal_with_discount;
			
			$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'cart'=>array("subtotal"=>$subtotal,"subtotal_with_discount"=>$discount,'shipping_charge'=>$shipping_charge,'grandtotal'=>$grand_total));
		}
		else
		{
			 $result[]=array('status'=>array("code"=>"0","message"=>"error"));
		}
	
	  
		return $result;   
		 
	 }
	 /**
     * Return the sum of the two numbers.
     * @param string $userid Right hand operand.
	 * @param int $quoteId
	 * @param string $coupon
     * @return array()|associativeArray(). 
     */
    public function applycoupon($userid,$quoteId,$coupon) { 
      
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$quote_sql = "Select * FROM quote where customer_id=$userid and entity_id=$quoteId and store_id=1 and is_active=1";
		$result_result = $connection->fetchAll($quote_sql);
		//print_r($result_result);
		if(!empty($result_result))  
		{
		 //$couponCode = $orderData['coupon_code'];
		 $couponCode = $coupon;
			$quoteId=$result_result[0]['entity_id'];
			 $quote = $this->quote->create()->load($quoteId);
			$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
           $checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		  $checkoutSession->setQuoteId($quoteId);
				if($couponCode!='')
				{
					 
					 $quote_sql1 = "Select * FROM salesrule_coupon where code='$couponCode' and expiration_date >NOW()";
		            $result_result1 = $connection->fetchAll($quote_sql1);
					 if(!empty($result_result1))
					 {
						  $quote->setCouponCode($couponCode)->collectTotals();
						  $quote->save(); 
						  $result[]=array('status'=>array("code"=>"1","message"=>"Success"));
					 
					 }
					 else
					 {
						  $result[]=array('status'=>array("code"=>"0","message"=>"Error"));
					 
					 }
						
				}
				else
				{
					$result[]=array('status'=>array("code"=>"0","message"=>"Error"));
				}

                
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"Error"));
		}
	   
	   
		return $result;   
		 
	 }
	
	 
	  /**
     * Return the sum of the two numbers.
     * @param string $userid Right hand operand.
	  * @param int $quoteId
     * @return array()|associativeArray(). 
     */
    public function removecoupon($userid,$quoteId) { 
      
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$quote_sql = "Select * FROM quote where customer_id=$userid and entity_id=$quoteId and store_id=1 and is_active=1";
		$result_result = $connection->fetchAll($quote_sql);
		//print_r($result_result);
		if(!empty($result_result))  
		{
		 //$couponCode = $orderData['coupon_code'];
		 $couponCode = '';
			 $quoteId=$result_result[0]['entity_id'];
			 $quote = $this->quote->create()->load($quoteId);
			$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
           $checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		  $checkoutSession->setQuoteId($quoteId);
				if(empty($couponCode)){
					$quote->setCouponCode($couponCode)->collectTotals();
				}

                $quote->save(); 

		//$quote->setCouponCode('coupon');
		 //$quote->collectTotals()->save();
	     $result[]=array('status'=>array("code"=>"1","message"=>"Success"));
		

		}
		else
		{
			$result[]=array('status'=>array("code"=>"1","message"=>"Error"));
		}
	   
	   return $result;   
		 
	 }
	 
	  /**
     * Return the sum of the two numbers.
     * @param string $userid Right hand operand.
	 * @param int $quoteId
	 * @param string $shipping
     * @return array()|associativeArray(). 
     */
    public function applydeleverytype($userid,$quoteId,$shipping) { 
      
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and entity_id='$quoteId' and is_active=1";
		$result_result = $connection->fetchAll($quote_sql);	
		//print_r($result_result);
		if(!empty($result_result))
		{
			 $quoteId2=$result_result[0]['entity_id'];
			 $quote = $this->quote->create()->load($quoteId2);
			 $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
             $checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		     $checkoutSession->setQuoteId($quoteId2);
			 //$orderData['shipping_address']='';
			 //$orderData['shipping_address']='';
			// $quote->getShippingAddress()->addData('');
			 $shippingAddress=$quote->getShippingAddress(); 
             $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates() 
                        //->setShippingMethod('freeshipping_freeshipping'); //shipping method
						->setShippingMethod($shipping); //shipping method
						 $quote->save();
					$quoteid3=$quote->getId();	
					
			$quote->collectTotals()->save();		   
					
                    if($quoteid3)
		  {
			$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'quoteid'=>$quoteid3);
		  }
		  else
		  {
			 $result[]=array('status'=>array("code"=>"0","message"=>"error"));
		  }
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"Error"));
		}
	   
	   
		return $result;   
		 
	 }
	 
	 /**
      @param int $userid 
     * @return array()|associativeArray(). 
     */
    public function androidappbuild($userid) { 
	
	
	     
		
		try {
			$sellerId =$userid;
            //$data = $request->getParams();
			$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
           //$template = $objectManager->create('Webkul\Marketplace\Model\Instantapp');
		        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		        $connection = $resource->getConnection();
				$sql3 = "Select * FROM basic_color where Seller_Id=$sellerId order by id desc limit 1";
		         $result3 = $connection->fetchAll($sql3);
		   //$template->getCopy('/home/btranzwev/appbuilder.btranz.website/app','/home/btranzwev/appbuilder.btranz.website/seller/5');
		  //full_copy('/home/btranzwev/appbuilder.btranz.website/app','/home/btranzwev/appbuilder.btranz.website/seller/5');
		   
           //print_r($data);
		            //$src = "/home/btranzwev/appbuilder.btranz.website/app/EcamarceApp";  // source folder or file
					$src = "/home/btranzwev/appbuilder.btranz.website/app";
					$dest = "/home/btranzwev/appbuilder.btranz.website/globicle/".$sellerId;   // destination folder or file        
                    
					if (!is_dir($dest)) {
						//mkdir($dest);  
                        shell_exec("cp -r $src $dest");		
                        //echo "<H2>Copy files completed!</H2>";						
					} 
					 else
					{
						//echo "Exist";
						sleep(1);    // this does the trick
                        rename ($dest,$dest.rand()); //no error 
						//rmdir($dest);
						shell_exec("cp -r $src $dest");	
					} 
					
                  //exit;
					
					//$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		            $connection = $resource->getConnection();
					 $sql3 = "Select * FROM basic_color where Seller_Id=$sellerId order by id desc limit 1";
		             $result3 = $connection->fetchAll($sql3);
					//print_r($result3);
					// $sellerId =2;
					//$appname ='demo';
					//$applogo ='http://159.203.151.92/pub/media/marketplace/background/image/a/d/admin.jpg';
					//$theme ='Blue';
					 //$sellerId =$result3[0]['Seller_Id'];
					 $appname =$result3[0]['App_Name'];
					 //$applogo ='http://159.203.151.92/pub/media/marketplace/background/image'.$result3[0]['App_Logo'];
					 $applogo ='http://159.203.151.92/pub/media/gridpart2/background/image'.$result3[0]['App_Logo'];
					
					 $src_logo = $applogo;
					$dest_logo = "/home/btranzwev/appbuilder.btranz.website/globicle/".$sellerId."/EcamarceApp/app/src/main/res/drawable/logo_icon.png";   // destination folder or file        
                    //shell_exec("cp -r $src_logo $dest_logo");
					 /* if (!is_dir($dest_logo)) {
						//mkdir($dest);  
                        shell_exec("cp -r $src_logo $dest_logo");		
                        echo "<H2>Copy files completed!</H2>";						
					} 
					 else
					{
						echo "Exist";
						sleep(1);    // this does the trick
                        rename ($dest_logo,$dest_logo.rand()); //no error 
						//rmdir($dest);
						shell_exec("cp -r $src_logo $dest_logo");	
					} */ 
					//$file = 'example.txt';
					//$newfile = 'example.txt.bak';

					if (!copy($src_logo, $dest_logo)) {
						echo "failed to copy $src_logo...\n";
					}
					//exit;
					
					$theme =$result3[0]['selectedTheme'];
					 
					$packagename = "com.btranz.ecamarceapp.".$appname;
    
    // $colorPrimary = $_POST["colorPrimary"];
    // $colorPrimaryDark = $_POST["colorPrimaryDark"];
    // $colorAccent = $_POST["colorAccent"];
    
    //echo $uname; 
    $command1 = 'gradle assembleprodDebug'; 
        
    // echo '<script type="text/javascript">alert("' .  $packagename . '");</script>';

   // $chGradlePath = "seller/".$sellerId."/EcamarceApp/app";    
   $chGradlePath = "/home/btranzwev/appbuilder.btranz.website/globicle/".$sellerId."/EcamarceApp/app";      
    
            //read the entire string
            $str=file_get_contents($chGradlePath.'/build.gradle');

                //replace something in the file string - this is a VERY simple example
                    $str=str_replace("AppName", "$appname",$str);
                    $str=str_replace("seller_id1", "$sellerId",$str);
                    $str=str_replace("theme1", "$theme",$str); 
                   // $str=str_replace("com.btranz.ecamarceapp.prod", "$packagename",$str);
            
                // $str=str_replace("colorPrimary1", "$colorPrimary",$str);
                // $str=str_replace("colorPrimaryDark1", "$colorPrimaryDark",$str);
                // $str=str_replace("colorAccent1", "$colorAccent",$str);
                //write the entire string
					file_put_contents($chGradlePath.'/build.gradle', $str);

								//echo '<script type="text/javascript">alert("' .  getcwd() . '");</script>';
						

						//$command ="seller/".$sellerId."/EcamarceApp";
						$command ="/home/btranzwev/appbuilder.btranz.website/globicle/".$sellerId."/EcamarceApp";
						chdir($command);
					   // echo getcwd();
					   // echo '<script type="text/javascript">alert("' .  getcwd() . '");</script>';
					  //$output =  shell_exec('gradle assembleprodDebug');
					  //print_r($output);
						// $command1 
					 // exec('./gradlew assembleprodDebug 2>&1',$out,$err);
						exec('sh gradlew assembleprodDebug 2>&1',$out,$err);
					  //var_dump($out); 
					   //  var_dump($err); 
							//echo "<pre>" ;print_r($out);	
					  // echo "<pre>" ;print_r($err);	
					//exit;   
						if(empty($err))
						{
							$App_Logopath=$result3[0]['App_Logo'];
								$androidlink='http://appbuilder.btranz.website/globicle/'.$sellerId.'/EcamarceApp/app/build/outputs/apk/app-prod-debug.apk';	
							$sql31 = "Select * FROM gridpart2_template where seller_id=$sellerId";
							$result31 = $connection->fetchAll($sql31);
						  if(!empty($result31))
						  {
							  $quote_sql = "update  gridpart2_template  set name='$appname',background='$App_Logopath',stylecolor='$androidlink',textcolor='',status='1',type='Product' where seller_id='$sellerId'";
							 $connection->rawQuery($quote_sql);	
							 $result[]=array('status'=>array("code"=>"1","message"=>"Success"));
						  }
						  else
						  {
							  $quote_sql = "insert  into gridpart2_template  set seller_id='$sellerId',name='$appname',background='$App_Logopath',stylecolor='$androidlink',textcolor='',status='1',type='Product'";
							 $connection->rawQuery($quote_sql);	
							 $result[]=array('status'=>array("code"=>"1","message"=>"Success"));
						  }
							
								
						}		
						 else
						 {
							 $result[]=array('status'=>array("code"=>"0","message"=>"Error"));
						 }
					  
        } 
		catch (\Exception $e) {
              $result[]=array('status'=>array("code"=>"0","message"=>"error"));
        }
      
		return $result;   
		 
	 }
	  
    /**
      @param int $userid 
     * @return array()|associativeArray(). 
     */
    public function orderhistory($userid) { 
	
 
	
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$_orderCollectionFactory = $objectManager->get('\Magento\Sales\Model\ResourceModel\Order\CollectionFactory');
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		
		
	    $orders = $_orderCollectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'customer_id',
            $userid
        )->addFieldToFilter(
            'store_id',
            1
		  )->setOrder(
            'entity_id',
            'asc'
        );
       /*  )->setOrder(
            'created_at',
            'desc'
        ); */
		
	
        $orderIds = $orders->getAllIds();
		rsort($orderIds);
		//echo "<pre>";print_r($orderIds);
	 // echo  $orders = json_encode($orderIds); 
	 
	//$orderId=85;
	//$idarray=explode(',',$orderIds);
	$orderarray='';

	if(!empty($orderIds))
	{
			  foreach($orderIds as $k=>$order_id)
			{
				  $orderId=$order_id;
				// echo "<br>";
				 $quote_sql_order = "Select * FROM sales_order where entity_id=$orderId";
				 $result_result_order = $connection->fetchAll($quote_sql_order);
				  if(!empty($result_result_order))
				 {
					
							 $order_id=$result_result_order[0]['entity_id'];
							 //$orderitemarray=$order_id;
                           $quote_sql2 = "Select * FROM sales_order_item where order_id=$order_id ORDER BY item_id asc limit 1";
							$result_result2 = $connection->fetchAll($quote_sql2);
							
							$orderitemarray='';
							if(!empty($result_result2))
							{ 
						       	  foreach( $result_result2 AS $data)
								{
									$currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($data['product_id']);
									$image='';
									 if($currentproduct->getImage()!='')
									 {
										 $image='http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getImage();
									 }
									$orderitemarray[]=array("product_id"=>$data['product_id'],"name"=>$data['name'],
									"description"=>$data['description'],
									"price"=>$data['price'],'sku'=>$data['sku'],'qty'=>$data['qty_ordered'],
									'qty'=>$data['qty_ordered'],'qty'=>$data['qty_ordered'],'qty'=>$data['qty_ordered'],
									"thumbnail"=> $image);  
									
									
								} 
								
							
							}  
							
							//echo $increment_id=$result_result_order[0]['increment_id'];
							//echo "<br>";
							//$order = $objectManager->create('Magento\Sales\Model\Order')->load($order_id);
							//$order = $objectManager->create('Magento\Sales\Model\Order')->loadByIncrementId('000000041');
							//echo "<pre>"; print_r($order->getShippingAddress()->getData());exit;
							
							$billing_address_array='';
							$billing_address_id=$result_result_order[0]['billing_address_id'];
							if($billing_address_id!='')
							{
							$order_billing_address = "Select * FROM sales_order_address where entity_id=$billing_address_id";
							 $result_result_order_billing_address = $connection->fetchAll($order_billing_address);
							  if(!empty($result_result_order_billing_address))
							  {
								// echo "<pre>"; print_r( $result_result_order_billing_address);  
								// $billing_address_array=array(''=>$result_result_order_billing_address[''][]); 
								/* foreach($result_result_order_billing_address as $k=>$add)
								{
									$billing_address_array[$k]=$add;
								}
								  echo "<pre>";print_r($billing_address_array); */
								  $billing_address_array=$result_result_order_billing_address;
							  }
							} 
							$shipping_address_array='';
							$shipping_address_id=$result_result_order[0]['shipping_address_id'];
							if($shipping_address_id!='')
							{
								$order_shipping_address = "Select * FROM sales_order_address where entity_id=$shipping_address_id";
							 $result_result_order_shipping_address = $connection->fetchAll($order_shipping_address);
							  if(!empty($result_result_order_shipping_address))
							  {
								
								 // echo "<pre>";print_r($result_result_order_shipping_address); 
								  $shipping_address_array=$result_result_order_shipping_address;
							  } 
							
							}
							$order_payment_array='';
							$order_payment_id=$result_result_order[0]['entity_id'];
							if($order_payment_id!='')
							{
								$order_payment = "Select * FROM sales_order_payment where parent_id=$order_payment_id";
							 $result_order_payment = $connection->fetchAll($order_payment);
							  if(!empty($result_order_payment))
							  {
								
								 // echo "<pre>";print_r($result_result_order_shipping_address); 
								  $order_payment_array=$result_order_payment[0]['method'];
							  } 
							
							}
							
							
							
							
							
							
							$orderarray[]=array('entity_id'=>$result_result_order[0]['entity_id'],'status'=>$result_result_order[0]['status'],
							'shipping_description'=>$result_result_order[0]['shipping_description'],'customer_id'=>$result_result_order[0]['customer_id'],
							'base_grand_total'=>$result_result_order[0]['base_grand_total'],
							'base_subtotal'=>$result_result_order[0]['base_subtotal'],
							'grand_total'=>$result_result_order[0]['grand_total'],
							'shipping_amount'=>$result_result_order[0]['shipping_amount'],
							'total_qty_ordered'=>$result_result_order[0]['total_qty_ordered'],
							//'billing_address_id'=>$result_result_order[0]['billing_address_id'],
							'billing_address'=>$billing_address_array,
							'shipping_address'=>$shipping_address_array, 
							'payment_method'=>$order_payment_array,
							'increment_id'=>$result_result_order[0]['increment_id'],
							'base_currency_code'=>$result_result_order[0]['base_currency_code'],
							'customer_email'=>$result_result_order[0]['customer_email'],
							'customer_firstname'=>$result_result_order[0]['customer_firstname'],
							'customer_lastname'=>$result_result_order[0]['customer_lastname'],
							'created_at'=>$result_result_order[0]['created_at'],
							'updated_at'=>$result_result_order[0]['updated_at'],
							'quote_id'=>$result_result_order[0]['quote_id'],
							'order_single_itemdetails'=>$orderitemarray,
							'order_item_list'=>$order_id);		
							
							
					
				 } 
				 
				 
				 
				 
				
			} 
			$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'order'=>$orderarray);
			//$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'order'=>'');
			
	}
	else
	{
		 $result[]=array('status'=>array("code"=>"0","message"=>"error"));
	}
	
	 
	  return $result; 
	}

   /**
      @param int $orderid 
     * @return array()|associativeArray(). 
     */
    public function orderitemlist($orderid) 
	{
	   $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		//$_orderCollectionFactory = $objectManager->get('\Magento\Sales\Model\ResourceModel\Order\CollectionFactory');
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$orderitemarray='';
		                    $quote_sql2 = "Select * FROM sales_order_item where order_id=$orderid";
							$result_result2 = $connection->fetchAll($quote_sql2);
							//print_r($result_result2);
							$orderitemarray='';
							if(!empty($result_result2))
							{ 
						       	foreach( $result_result2 AS $data)
								{
									$currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($data['product_id']);
									$image='';
									 if($currentproduct->getImage()!='')
									 {
										 $image='http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getImage();
									 }
									$orderitemarray[]=array("product_id"=>$data['product_id'],"name"=>$data['name'],
									"description"=>$data['description'],
									"price"=>$data['price'],'sku'=>$data['sku'],'qty'=>$data['qty_ordered'],
									'qty'=>$data['qty_ordered'],'qty'=>$data['qty_ordered'],'qty'=>$data['qty_ordered'],
									"thumbnail"=> $image);  
									
									
								} 
								
							  $result[]=array('status'=>array("code"=>"1","message"=>"Success"),'order_item_list'=>$orderitemarray);
							}
                            else
                            {
								$result[]=array('status'=>array("code"=>"0","message"=>"error"));
         
							}								
		 
		 return $result; 
	}
   
   /**
     * @param int $orderid 
	 * @param int $userid
     * @return array()|associativeArray(). 
     */
    public function cancelorderbuyer($orderid,$userid) 
	{
	          $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
	     	$OrderFactory = $objectManager->get('\Magento\Sales\Model\OrderFactory');
			//$orderManagement = $objectManager->get('\Magento\Sales\Api\OrderManagementInterface');
	        
	        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$quote_sql2 = "Select * FROM sales_order where entity_id=$orderid and customer_id=$userid";
			$result_result2 = $connection->fetchAll($quote_sql2);
			if(!empty($result_result2))	
			{
				
			$order = $OrderFactory->create()->load($orderid);
			// echo   $order->getId();
			$order->cancel()->save();
			//$orderManagement->cancel($orderid);
		    $result[]=array('status'=>array("code"=>"1","message"=>"Success"));
			}	
            else
			{
		    $result[]=array('status'=>array("code"=>"0","message"=>"error"));
			}				
			
            return $result; 
	}
   
   /**
     * @param int $userid 
	 * @param int $productid
     * @return array()|associativeArray(). 
     */
    public function addwishlist($userid,$productid) 
	{
		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$wishlistRepository = $objectManager->get('\Magento\Wishlist\Model\WishlistFactory');
		$productRepository = $objectManager->get('\Magento\Catalog\Api\ProductRepositoryInterface');
		
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$quote_sql = "Select * FROM catalog_product_entity where entity_id=$productid ";
		 $result_result = $connection->fetchAll($quote_sql);
		if(!empty($result_result)):
		 
		
							try {
									$product = $productRepository->getById($productid);
								} catch (NoSuchEntityException $e) {
									$product = null;
								}

							$wishlist = $wishlistRepository->create()->loadByCustomerId($userid, true);

							$wishlist->addNewItem($product);
							$wishlist->save();
							$id=$wishlist->getId();
							if($id!='')
							{
								$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'wishlist_id'=>$id);
							}
							else
							{
								$result[]=array('status'=>array("code"=>"0","message"=>"Error"));
							}
			else:
		 
							$result[]=array('status'=>array("code"=>"0","message"=>"error")); 
		   endif;			
							return $result; 
	}
	/**
     * @param int $userid 
	 * @return array()|associativeArray(). 
     */
    public function getwishlist($userid) 
	{
		          $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		          $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		          $connection = $resource->getConnection();
				  $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id order by b.wishlist_item_id desc ";
				  $result_wishlist = $connection->fetchAll($wishlist);
				  $wishlistproduct='';
				  if(!empty($result_wishlist))
				  {
					  foreach($result_wishlist as $wsh)
					  {
						  $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($wsh['product_id']);
						  
						                    $offfer_tag = $currentproduct->getData('offfer_tag');
											$attr = $currentproduct->getResource()->getAttribute('offfer_tag');
											$optionText =$attr->getSource()->getOptionText($offfer_tag);
											
											if($optionText=='')
											{
												$optionText='null';
											}
											   
												 $_price=$currentproduct->getPrice();
												 $_finalPrice=$currentproduct->getFinalPrice();
												 $savetag='null';
													if($_finalPrice < $_price):
													$_savePercent = 100 - round(($_finalPrice / $_price)*100);
													//echo $_savePercent;
													$savetag=$_savePercent;
											   endif;

											   $producturl=$currentproduct->getProductUrl();
											   
											   
											      /* $wishlisttag='';
														if($userid > 0)
													   {
														  $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id and b.product_id=$productId";
														  $result_wishlist = $connection->fetchAll($wishlist);
														if(!empty($result_wishlist))
														{
															$wishlisttag=1;
															 
														}
														else
														{
															$wishlisttag=0;
														}
													   }
													   else
													   {
														$wishlisttag=0;
													   } */ 
											   
											   
											   
											   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
											   $storeId = $this->storeManager->getStore()->getId();
											   $reviewFactory->getEntitySummary($currentproduct, $storeId);
											   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
											   if($ratingSummary=='')
											{
												$ratingSummary='null';
											} 
						  
						  
						  
						  
						  
						  
						  
						  
						  
						  //$wishlistproduct[]=$wsh['product_id'];
						   $wishlistproduct[]=array("id"=>$currentproduct->getId(),"wishlist_item_id"=>$wsh['wishlist_item_id'],"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getthumbnail(),"fullImage"=>'http://159.203.151.92/pub/media/catalog/product'.$currentproduct->getImage(),'sku'=>$currentproduct->getSku(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
					  }
					  $result[]=array('status'=>array("code"=>"1","message"=>"Success"),'product'=>$wishlistproduct);
				  }
				  else
				  {
					  $result[]=array('status'=>array("code"=>"0","message"=>"Error"),'product'=>'Item not found');
				  }
					
					return $result;
	}
	 /**
     * @param int $userid 
	 * @param int $productid
	 * @return array()|associativeArray(). 
     */
    public function itemremovewishlist($userid,$productid) 
	{
		           $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$wishlistProviderInterface = $objectManager->get('\Magento\Wishlist\Controller\WishlistProviderInterface');
					$itemFactory = $objectManager->get('\Magento\Wishlist\Model\ItemFactory');
					 $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
					$connection = $resource->getConnection();
				  $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id and b.product_id=$productid";
				  $result_wishlist = $connection->fetchAll($wishlist);
					if(!empty($result_wishlist))
					{
						$wishlist_item_id=$result_wishlist[0]['wishlist_item_id'];
						$item=$itemFactory->create()->load($wishlist_item_id);
						
						/* if (!$item->getId()) {
						$result[]=array('status'=>array("code"=>"0","message"=>"Error"),'product'=>'Item not found');
						}
						$wishlist = $wishlistProviderInterface->getWishlist($item->getWishlistId());
						if (!$wishlist) {
							$result[]=array('status'=>array("code"=>"0","message"=>"Error"),'product'=>'Wishlist not found');
						} */
						
						 $item->delete();
						 //$wishlist = $wishlistProviderInterface->getWishlist($item->getWishlistId());
						 //$wishlist->save();
						 $result[]=array('status'=>array("code"=>"1","message"=>"Success")); 
					}
					else
					{
						$result[]=array('status'=>array("code"=>"0","message"=>"Error"),'product'=>'Item not found');
					}
					return $result;
	}
	
	
	 /**
     * @param int $userid 
	 * @param int $productid
	 * @return array()|associativeArray(). 
     */
    public function wishlisttag($userid,$productid) 
	{
		           $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
					$wishlistProviderInterface = $objectManager->get('\Magento\Wishlist\Controller\WishlistProviderInterface');
					$itemFactory = $objectManager->get('\Magento\Wishlist\Model\ItemFactory');
					 $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
					$connection = $resource->getConnection();
				  $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id and b.product_id=$productid";
				  $result_wishlist = $connection->fetchAll($wishlist);
					if(!empty($result_wishlist))
					{
						
						 $result[]=array('status'=>array("code"=>"1","message"=>"Success"),'wishlisttag'=>'1'); 
					}
					else
					{
						$result[]=array('status'=>array("code"=>"0","message"=>"Error"),'wishlisttag'=>'0');
					}
					return $result;
	}
	
	/**
     * @param int $userid 
	 * @return array()|associativeArray(). 
     */
    public function getaccountinfo($userid) 
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
				$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
				$connection = $resource->getConnection();
		$customer=$this->customerFactory->create();
        //$customer->setWebsiteId($websiteId);
        //$customer->loadByEmail($orderData['email']);// load customet by email address
		$customer->load($userid);
	  $customer->getId();
		 //$customer->getEntityId();
		// print_r($customer->getData());
		if(!empty($customer->getData()))
		{
			//$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'accountinfo'=>array('firstname'=>$customer->getFirstname(),'lastname'=>$customer->getLastname(),'email'=>$customer->getEmail()));
			 $billing=$customer->getDefaultBilling();
			$telephone='';
			if($billing!='')
			{
			$billaddress = "Select * FROM customer_address_entity  where entity_id=$billing";
		    $result_billing_address = $connection->fetchAll($billaddress);  
			$telephone=$result_billing_address[0]['telephone'];
			}
			
		  
		  
		  
			
		//echo"<pre>";print_r($customer->getData());
		$customerarray=$customer->getData();
		$customerarray['telephone']=$telephone;
		//echo"<pre>";print_r($customerarray);
			$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'accountinfo'=>$customerarray);
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"Error"));
		}
		
		return $result;
	}
	
	/**
     * @param int $userid 
	 * @return array()|associativeArray(). 
     */
    public function getaddress($userid) 
	{   
	
	            $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
				$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
				
				 $countryHelper = $objectManager->get('Magento\Directory\Model\Config\Source\Country'); 
		$countryFactory = $objectManager->get('Magento\Directory\Model\CountryFactory');
        $countries = $countryHelper->toOptionArray();
			//echo"<pre>";print_r( $countries);   
				
		$connection = $resource->getConnection();
		$customer=$this->customerFactory->create();
        //$customer->setWebsiteId($websiteId);
        //$customer->loadByEmail($orderData['email']);// load customet by email address
		$customer->load($userid);
	    $customer->getId();
		if(!empty($customer->getData()))
		{
			$billing=$customer->getDefaultBilling();
			$shipping=$customer->getDefaultShipping();
			$result_billing_address='';
			$result_shipaddress='';
			
			if($billing!='')
			{
				$billaddress = "Select * FROM customer_address_entity  where entity_id=$billing";
		        $result_billing_address = $connection->fetchAll($billaddress);
			}
			
			if($shipping!='')
			{
						//echo $result_billing_address[0]['country_id'];
					$shipaddress = "Select * FROM customer_address_entity  where entity_id=$shipping";
					$result_shipaddress = $connection->fetchAll($shipaddress);
			}
			
			
			if(!empty($result_billing_address)): 
				
					foreach($countries as $country)
					{
					  //if()
						  if($country['value']==$result_billing_address[0]['country_id'])
						  {
							  $result_billing_address[0]['country_label']=$country['label'];
						  }		
						  if($country['value']==$result_shipaddress[0]['country_id'])
						  {
							  $result_shipaddress[0]['country_label']=$country['label'];
						  }					  
					}
			/* else:
			 $result_billing_address[0]['country_label']='';
			 $result_shipaddress[0]['country_label']=''; */ 
			endif;
			
			//echo "<pre>"; print_r($result_billing_address);
			
			
			//$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'accountinfo'=>array('firstname'=>$customer->getFirstname(),'lastname'=>$customer->getLastname(),'email'=>$customer->getEmail()));
			$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'billingaddress'=>$result_billing_address,'shippingaddress'=>$result_shipaddress);
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"Error"));
		}
		
		return $result;
	}
	
	/**
     * @param int $userid 
	 * @return array()|associativeArray(). 
     */
    public function editbillingaddress($userid,$fname,$lname,$street,$city,$country_id,$postcode,$telephone) 
	{   
	           $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
				$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
				$connection = $resource->getConnection();
	   $customer=$this->customerFactory->create();
	   $customer->load($userid);
	   if(!empty($customer->getData()))
		{
			$billing=$customer->getDefaultBilling();
			$shipping=$customer->getDefaultShipping();
				if($billing!='')
				{
				  $updateaddress = "update customer_address_entity set `city`='$city',`country_id`='$country_id',`firstname`='$fname',`lastname`='$lname',`postcode`='$postcode',`street`='$street',`telephone`='$telephone' where `entity_id`=$billing";
				$connection->rawQuery($updateaddress);
				}
				else
				{
				   $updateaddress = "insert into customer_address_entity set `city`='$city',`country_id`='$country_id',`firstname`='$fname',`lastname`='$lname',`postcode`='$postcode',`street`='$street',`telephone`='$telephone' ";
				   $connection->rawQuery($updateaddress);
				   $lastInsertId = $connection->lastInsertId();
				   $updateaddress = "update customer_entity set `default_billing`='$lastInsertId' where entity_id=$userid ";
				   $connection->rawQuery($updateaddress);
				}
				//exit;
				
				
			 if($shipping!='')
			{
			     $updateaddress = "update customer_address_entity set `city`='$city',`country_id`='$country_id',`firstname`='$fname',`lastname`='$lname',`postcode`='$postcode',`street`='$street',`telephone`='$telephone' where `entity_id`=$shipping";
		         $connection->rawQuery($updateaddress);
			}
			else 
			{
				   $updateaddress = "insert into customer_address_entity set `city`='$city',`country_id`='$country_id',`firstname`='$fname',`lastname`='$lname',`postcode`='$postcode',`street`='$street',`telephone`='$telephone'"; 
				   $connection->rawQuery($updateaddress);
				   $lastInsertId = $connection->lastInsertId();
				   $updateaddress = "update customer_entity set `default_shipping`='$lastInsertId' where entity_id=$userid ";
				   $connection->rawQuery($updateaddress);
			}
			
			$result[]=array('status'=>array("code"=>"1","message"=>"Success")); 
		
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"Error"));
		}
	    
		
		return $result;
	}
	
	/**
     * @param int $userid 
	 * @return array()|associativeArray(). 
     */
    public function editaccountinfo($userid,$fname,$lname,$dob) 
	{   
	     
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$customer=$this->customerFactory->create();
        $customer->load($userid);
	    $customer->getId();
		if(!empty($customer->getData()))
		{
			$entity_id=$customer->getEntityId();
		
			    $updateaddress = "update customer_entity set dob ='$dob',`firstname`='$fname',`lastname`='$lname' where `entity_id`=$entity_id";
				$connection->rawQuery($updateaddress);
				$result[]=array('status'=>array("code"=>"1","message"=>"Success")); 
			
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"Error"));
		}
	   
		return $result;
	}
	
	/**
     * @param int $userid 
	 * @param string $oldpassword 
	 * @param string $newpassword 
	 * @return array()|associativeArray(). 
     */
    public function changepassword($userid,$oldpassword,$newpassword) 
	{ 
	
	           $result1 = $this->customerAccountManagement->customauthenticatechangepass($userid,$oldpassword,$newpassword);
				//print_r($result1);
				if(!empty($result1))
				{
					//return $result1;
					return $result2=array('status'=>array("code"=>"1","message"=>"Success"));
				}
				else
				{
					$result2=array('status'=>array("code"=>"0","message"=>"Invalid Password."));
					return $result2;
				}
	       
	   
	}
	/**
     * @param string $email 
	 * @return array()|associativeArray(). 
     */
    public function forgotpassword($email) 
	{ 
	
	            //echo $email;exit;
			 $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			 $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		     $connection = $resource->getConnection();
			     $quote_sql2 = "Select * FROM customer_entity where email='$email'"; 
				 $result_result2 = $connection->fetchAll($quote_sql2);
				 if(!empty($result_result2)):
			
							/* $customerAccountManagement = $objectManager->get('\Magento\Customer\Model\AccountManagement');
					    	$_customers = $objectManager->create('Magento\Customer\Api\AccountManagementInterface');
							$customer=$this->customerFactory->create();
						   $customer->loadByEmail($email);
						   $customer->getId();
							if(!empty($customer->getData()))
							{
							  
							//$result[]=array('status'=>array("code"=>"0","message"=>"Success",'id'=>$customer->getId()));
									try {
										$_customers->initiatePasswordReset($email, \Magento\Customer\Model\AccountManagement::EMAIL_RESET);
										$result=array('status'=>array("code"=>"1","message"=>"Password reset email sent."));
									} catch (NoSuchEntityException $e) {
										// Do nothing, we don't want anyone to use this action to determine which email accounts are registered.
									} catch (\Exception $exception) {
										echo __('We\'re unable to send the password reset email.');
									}
							 }
							else
							{
								
								//$result=array('status'=>array("message"=>"Error."));
								$result=array('status'=>array("code"=>"0","message"=>"error"));  
							} */
							$customerAccountManagement = $objectManager->get('\Magento\Customer\Model\AccountManagement');
					    	$_customers = $objectManager->create('Magento\Customer\Api\AccountManagementInterface');
							$_customers->initiatePasswordReset($email, \Magento\Customer\Model\AccountManagement::EMAIL_RESET);
							$result=array('status'=>array("code"=>"1","message"=>"Password reset email sent."));
			
			else:
			
			//$result=array('status'=>array("message"=>"Error."));
			$result=array('status'=>array("code"=>"0","message"=>"error"));  
			endif;
	       
	
	        return $result;         
	   
	}
	
	/**
     * @return array()|associativeArray(). 
     */
    public function countylist() 
	{ 
	     $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
	    $countryHelper = $objectManager->get('Magento\Directory\Model\Config\Source\Country'); 
		$countryFactory = $objectManager->get('Magento\Directory\Model\CountryFactory');

		$countries = $countryHelper->toOptionArray(); //Load an array of countries

			foreach ( $countries as $countryKey => $country ) {

				if ( $country['value'] != '' ) { //Ignore the first (empty) value

					$stateArray = $countryFactory->create()->setId(
						$country['value']
					)->getLoadedRegionCollection()->toOptionArray(); //Get all regions for the given ISO country code

					if ( count($stateArray) > 0 ) { //Again ignore empty values
						$countries[$countryKey]['states'] = $stateArray;
					}

				}
			}

		//var_dump($countries);
		//echo "<pre>";print_r($countries);
	    $result[]=array('status'=>array("code"=>"1","message"=>"Success"),'country'=>$countries);
		return $result; 
	}
	
	/**
	 * @param int $email
     * @return array()|associativeArray(). 
     */
    public function deacivateaccount($userid) 
	{ 
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$customer=$this->customerFactory->create();
        $customer->load($userid);
	    //$customer->getId();
		if(!empty($customer->getData()))
		{
			 $updateaddress = "update customer_entity set `is_active`='0' where `entity_id`=$userid";
			 $connection->rawQuery($updateaddress);
			$result[]=array('status'=>array("code"=>"1","message"=>"Success"));
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"Error"));
		}
	    
		return $result; 
	}
	/**
	 * @param string $email
     * @return array()|associativeArray(). 
     */
    public function acivateaccount($email) 
	{ 
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		//$customer=$this->customerFactory->create();
        //$customer->load($userid);
	    //$customer->getId();
		
		 $sql3 = "Select * FROM customer_entity where email='$email'";
		$result3 = $connection->fetchAll($sql3);
		if(!empty($result3))  
		{
			  $entity_id=$result3[0]['entity_id'];
			
			 $updateaddress = "update customer_entity set `is_active`='1' where `entity_id`=$entity_id";
			 $connection->rawQuery($updateaddress); 
			$result[]=array('status'=>array("code"=>"1","message"=>"Success"));
		}
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"Error"));
		}
	    
		return $result; 
	}
	
	/**
	 * @param string $username
	 * @param string $password
     * @return array()|associativeArray(). 
     */
    public function adminlogin($username,$password) 
	{ 
	        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		    $connection = $resource->getConnection();
			//echo SHA2('btranz_marketplace01', 256);
			//$pp=CONCAT(SHA2('btranz_marketplace01', 256), ':btranz_marketplace01:1'); 
			 $adminpass= "Select * FROM admin_user where username='btranz_marketplace' and password=CONCAT(SHA2('9cd81cffd345c41ac0f35d5fe4a4c5dabtranz_marketplace01', 256), ':9cd81cffd345c41ac0f35d5fe4a4c5da:1')";
			$adminpass_result = $connection->fetchAll($adminpass);
	       // $password='btranz_marketplace01';
			//echo "<pre>";print_r($adminpass_result); 
			
			$result[]=array('status'=>array("code"=>"1","message"=>"Error",'pass'=>'60cf4f7fec45d45cdc5613942f9524136f02c51379881b55961f88831514c86f:37UeZAbksE7uNglkX5Ji1H6PQzhARZX1:1','pp'=>$pp));
		    return $result; 
	}
	/**
	* @param int $product_id
	 * @return array()|associativeArray(). 
     */
    public function bookingcalender($product_id) 
	{ 
                          $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$quote_sql = "Select * FROM  sr_booking_timing where product_id=$product_id and type!=0 limit 1";
		$result_schedule = $connection->fetchAll($quote_sql);
		$calendar='';
		if(!empty($result_schedule))
		{
              $startdate=$result_schedule[0]['startdate'];
			  $enddate=$result_schedule[0]['enddate'];
			
			$startdate=$result_schedule[0]['startdate'];
			 $enddate=$result_schedule[0]['enddate'];
			//echo $diff12 = date_diff($startdate,$enddate);
			$dates = array();
			$month_year=array();
			$startdate = strtotime($startdate);
			$enddate = strtotime($enddate);
           $step = '+1 day';
		   $output_format = 'm/d/Y';
			while( $startdate <= $enddate ) {

				$dates[] = date($output_format, $startdate);
				//$month_year[]=
				//echo"<br>";
				$m=date('m',$startdate);
				//echo"<br>";
				$y=date('Y',$startdate);
				//echo"<br>";
				$sami=$m.','.$y;
				if(!in_array($sami,$month_year))
				{
					$month_year[]=$sami;
				}
				
				$startdate = strtotime($step, $startdate);
			}
			//echo "<pre>";print_r($dates);
			//echo "<pre>";print_r($month_year);
			$calendar.='<div class="divs" id="calender_main_div">';
			$cc=1;
			foreach($month_year as $data)
			{
				                 // echo $data;
								 // print_r (explode(",",$data));
								 
								 $md=explode(",",$data);
								$month=$md[0];
								$year=$md[1];
				                 // $month='12';
								//$year='2016';
								//if(($year==date('Y') && $month<date('m'))||($year>=date('Y')))
								/* if($year>=date('Y'))
								{
												if($year==date('Y') && $month>date('m'))
												{ */
										//echo date('m'); 
										//echo date('Y');	
											$stl='';
											if($cc!=1) { $stl='style="display: none;"';}
											$calendar.= ' <div class="cls'.$cc.'" '.$stl.'  id="calender_main_divinner'.$cc.'">';
											//$calendar.='<h2>'.$month.' '.$year.'</h2>';
											$monthchar='';
											if($month=='01'){ $monthchar='January';}
											if($month=='02'){ $monthchar='February';}
											if($month=='03'){ $monthchar='March';}
											if($month=='04'){ $monthchar='April';}
											if($month=='05'){ $monthchar='May';}
											if($month=='06'){ $monthchar='June';}
											if($month=='07'){ $monthchar='July';}
											if($month=='08'){ $monthchar='August';}
											if($month=='09'){ $monthchar='September';}
											if($month=='10'){ $monthchar='October';}
											if($month=='11'){ $monthchar='November';}
											if($month=='12'){ $monthchar='December';}
											//$calendar.= '<a id="prev" onclick="prevfunction();">prev</a>';
									   //$calendar.= '<a id="next" onclick="nextfunction();">next</a>';
											$calendar.='<table cellpadding="0" cellspacing="0" class="calendar" id="addrow">'; 
											$calendar.='<tr  style="background-color: black;"> 
														 <th style="text-align: center;cursor: pointer;"> <a id="prev" onclick="prevfunction();" data-goto="2017-01-01" class="page-left"><i style="font: normal normal normal 32px/1 FontAwesome;color: aliceblue;" class="fa fa fa-arrow-left" aria-hidden="true"></i></a>				</th>
														  <th style="text-align: center" colspan="5">
														  <span class="calendarSavingState" style="display: none;">
														  <i class="fa fa-refresh fa-spin" aria-hidden="true"></i>
														   </span>
														   <span class="monthName" style="font-size: 31px;color: aliceblue;">'.$monthchar.' '.$year.'</span>
														 </th>
														  <th style="text-align: center;cursor: pointer;"> <a id="next" onclick="nextfunction();" data-goto="2017-01-01" class="page-right"><i style="font: normal normal normal 32px/1 FontAwesome;color: aliceblue;" class="fa fa-arrow-right" aria-hidden="true"></i></a>				</th>
														  </tr>';
											/* table headings */
											$headings = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
											$calendar.= '<tr class="calendar-row calendar-day-head-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

											/* days and weeks vars now ... */
											$running_day = date('w',mktime(0,0,0,$month,1,$year));
											$days_in_month = date('t',mktime(0,0,0,$month,1,$year)); 
											$days_in_this_week = 1;
											$day_counter = 0;
											$dates_array = array();

											/* row for week one */
											$calendar.= '<tr class="calendar-row">';

											/* print "blank" days until the first of the current week */
											for($x = 0; $x < $running_day; $x++):
												$calendar.= '<td class="calendar-day-np"> </td>';
												$days_in_this_week++;
											endfor;

											/* keep going with days.... */
											for($list_day = 1; $list_day <= $days_in_month; $list_day++):
											$datafomat='';
											 if($list_day<10)
											 {
												  $datafomat=$month.'/0'.$list_day.'/'.$year;
											 }
											 else
											 {
												  $datafomat=$month.'/'.$list_day.'/'.$year;
												  
											 }
												$timestamp = strtotime($datafomat);
												$day = date('l', $timestamp);
											   $quote_sql2 = "Select * FROM  sr_booking_timing where product_id=$product_id and weekday='$day' limit 1";
											   $result_schedule2 = $connection->fetchAll($quote_sql2);
											   if(!empty($result_schedule2))
											   {
												   //echo time();
												  // echo date();
												  // if (strtotime($datafomat) >time()) 
												 if (strtotime($datafomat) >(time()-(60*60*24))) 
												                    {
																		//echo $datafomat;
												$calendar.= '<td class="calendar-day booking_date_exist" onclick="showbookingslot(this,'.$product_id.',\''.$day.'\',\''.$month.'\',\''.$list_day.'\',\''.$year.'\');">';
																	} else {
												 $calendar.= '<td class="calendar-day booking_date_expired" >';
																	}
												   //$calendar.= '<td class="calendar-day booking_date_exist" onclick="showbookingslot(this,'.$product_id.',\''.$day.'\',\''.$month.'\',\''.$list_day.'\',\''.$year.'\');">';
											   }
											   else
											   {
												   $calendar.= '<td class="calendar-day">';
											   }
													/* if (in_array($datafomat,$dates)) 
												   
												   {
														$calendar.= '<td class="calendar-day booking_date_exist" onclick="showbookingslot(this);">';
													}
													else
													{
														$calendar.= '<td class="calendar-day">';
													} */
												
												
												//$calendar.= '<td class="calendar-day" onclick="showbookingslot(this);">';
													/* add in the day number */
													$calendar.= '<span class="day-number">'.$list_day.'</span>';

													/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
													$calendar.= str_repeat('<p> </p>',2);
													
												$calendar.= '</td>';
												if($running_day == 6):
													$calendar.= '</tr>';
													if(($day_counter+1) != $days_in_month):
														$calendar.= '<tr class="calendar-row">';
													endif;
													$running_day = -1;
													$days_in_this_week = 0;
												endif;
												$days_in_this_week++; $running_day++; $day_counter++;
											endfor;

											/* finish the rest of the days in the week */
											if($days_in_this_week < 8):
												for($x = 1; $x <= (8 - $days_in_this_week); $x++):
													$calendar.= '<td class="calendar-day-np"> </td>';
												endfor;
											endif;

											/* final row */
											$calendar.= '</tr>';

											/* end the table */
											$calendar.= '</table></div>';
											
											/* all done, return result */
											//return $calendar;
									/* 		}
											else
											{
												$calendar.= 'No Record Found!';
											}
								}
								
								else
								{
									$calendar.= 'No Record Found!';
								} */
				$cc++;
				
			}
			$calendar.= '</div>';
			//$calendar.= '<a id="prev" onclick="prevfunction();">prev</a>';
			//$calendar.= '<a id="next" onclick="nextfunction();">next</a>';
			
			//$calendar.= '<a id="next" >next</a>';
			//$calendar.= '<a id="prev" >prev</a>';
			
 
			//echo $calendar;
			//echo "<pre>"; print_r($month_year);
			//echo "<pre>"; print_r($dates);
			
		}
		else
		{
			 $calendar='<div class="divs" id="calender_main_div" style="background-color: #ff5501;padding: 38px 67px;">Booking Disable!</div>';
		}
          
      //exit;
	/* draw table */
		return $calendar;
	}
	/**
	* @param string $day
	* @param string $month
	* @param string $list_day
	* @param string $year
	* @param int $product_id
	 * @return array()|associativeArray(). 
     */
    public function availableappointment($product_id,$day,$month,$list_day,$year) 
	{ 
	   
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
	       
		    $quote_sql2 = "Select * FROM  sr_booking_timing where product_id=$product_id and weekday='$day'";
		    $result_schedule2 = $connection->fetchAll($quote_sql2);
			 $selecteddate='';
			                   if($list_day<10)
								 {
									    $datafomat=$month.'/0'.$list_day.'/'.$year;
								 }
								 else
								 {
									   $datafomat=$month.'/'.$list_day.'/'.$year;
									  
								 }
/* 			$timestamp = strtotime($selecteddate);
			echo $monthname = date('F', $timestamp); 
			echo $today = date("F j, Y, g:i a", $timestamp); */
			//exit; 
			$monthchar='';
								if($month=='01'){ $monthchar='January';}
								if($month=='02'){ $monthchar='February';}
								if($month=='03'){ $monthchar='March';}
								if($month=='04'){ $monthchar='April';}
								if($month=='05'){ $monthchar='May';}
								if($month=='06'){ $monthchar='June';}
								if($month=='07'){ $monthchar='July';}
								if($month=='08'){ $monthchar='August';}
								if($month=='09'){ $monthchar='September';}
								if($month=='10'){ $monthchar='October';}
								if($month=='11'){ $monthchar='November';}
								if($month=='12'){ $monthchar='December';}
			
			
			
			
			
			$availableappointment='<td colspan="7">  
	<div class="booked-appt-list shown" style="display: block;">
	<h2><span>Available Appointments on </span><strong>'.$monthchar.' '.$list_day.', '.$year.'</strong><span></span></h2>';
			
			$value2=str_replace("/","-",$datafomat);
			if(!empty($result_schedule2)):
			foreach($result_schedule2 as $val_sh)
			{
				//$slottime=$val_sh['starthoure'];
			$slottime=$val_sh['starthoure'].':'.$val_sh['startminute'].'-'.$val_sh['endhoure'].':'.$val_sh['endminute'];
			//	$bookingschedule.='<tr><td>'.$value1.'</td><td>'.$day.'</td><td>'.$val_sh['starthoure'].':'.$val_sh['startminute'].'-'.$val_sh['endhoure'].':'.$val_sh['endminute'].'</td><td><input type="text" name="" value="1" width="50px" /></td><td><button type="button" onclick="booking_add_to_cart();">Book</button></td></tr>';
			//$bookingschedule.='<tr><td>'.$value1.'</td><td>'.$day.'</td><td>'.$val_sh['starthoure'].':'.$val_sh['startminute'].'-'.$val_sh['endhoure'].':'.$val_sh['endminute'].'</td><td><button type="button" onclick="booking_add_to_cart(\''.$value2.'\',\''.$day.'\',\''.$slottime.'\',1,'.$customer_id.','.$product_id.');">Book</button></td></tr>';
			 
			/*  $date = $selecteddate.' '.$val_sh['starthoure'].':'.$val_sh['startminute'];
             $stime=date('h:i a', strtotime($date));
			
			 $date2 = $selecteddate.' '.$val_sh['endhoure'].':'.$val_sh['endminute'];
			 $endtime=date('h:i a', strtotime($date2)); */
			$date =$val_sh['starthoure'].':'.$val_sh['startminute'];
            $stime=date('h:i a', strtotime($date));
			
			 $date2 =$val_sh['endhoure'].':'.$val_sh['endminute'];
			 $endtime=date('h:i a', strtotime($date2)); 
			 
			 $availablespace=$val_sh['availableslot'];
			
			$quote_sql2_completeorder = "Select SUM(  `items_qty` )  'totalorder' FROM  quote a, quote_item b where b.store_id=4 and b.quote_id=a.entity_id and a.reserved_order_id!='' and b.booking_date='$datafomat' and b.booking_day='$day' and b.booking_slot_time='$slottime'";
		    $result_complete_order = $connection->fetchAll($quote_sql2_completeorder);
			//$availableappointment.=$quote_sql2_completeorder;
			if(!empty($result_complete_order))
			{
				 $availablespace= $availablespace-$result_complete_order[0]['totalorder']; 
			}
			
			//if ((time()-(60*60*24)) < strtotime($date2.$datafomat)) 
				/*  if (strtotime($datafomat. .$date2) >(time()-(60*60*24))) 
			        {
					    $availableappointment.='yes';
					}
					else
					{
						$availableappointment.='no';
					} */
			
			
			 $availableappointment.='<div class="timeslot bookedClearFix" style="padding: 21px 3px 82px 8px;">
									<div style="
									float: left;
									width: 80%;
									">
									 <h3>
													<i class="fa fa-clock-o" aria-hidden="true"></i>
													 '.$stime.' – '.$endtime.' 
													<h3>
													<h5>'.$availablespace.' spaces available</h5>
									</div>
									<div style="float: right;">';
					if( $availablespace>0)
					{
						$availableappointment.='<div class="primary">
									<a href="javascript:void(0)" onclick="confirmbooking_popup(\''.$value2.'\',\''.$day.'\',\''.$slottime.'\',1,'.$product_id.',\''.$stime.'\',\''.$endtime.'\')" class="action create primary"><span>Book Appointment</span></a>
								</div>';
					}	
					else
					{
						$availableappointment.='<div class="primary">
									<a href="javascript:void(0)" style="background: #adcae1;border: 1px solid #adcae1;" class="action create primary"><span>Book Appointment</span></a>
								</div>';
					}

					
		     
					
					$availableappointment.='</div>
									</div>';
			
			
			
			
			}
			endif;
	
	$availableappointment.='</td>';
	
	return $availableappointment; 
	} 
	
	
	
	  /**
     * Return the sum of the two numbers.
     *
     * @param int $customer_id Right hand operand.
	 * @param int $product_id Right hand operand.
	 * @param string $date Right hand operand.
	 * @param string $slot Right hand operand.
	 * @param string $stime Right hand operand.
	 * @param string $endtime Right hand operand.
	 * @return array()|associativeArray(). 
     */
    public function bookingform($customer_id,$product_id,$date,$slot,$stime,$endtime) { 
						    
							$datafomat=str_replace("-","/",$date);
						   $timestamp = strtotime($datafomat);
						   $day = date('l', $timestamp);
						 //  echo $date;
						   $datasfilds=explode("-",$date);
						   $month=$datasfilds[0];
						   $list_day=$datasfilds[1];
						   $year=$datasfilds[2];
						   $monthchar='';
								if($month=='01'){ $monthchar='January';}
								if($month=='02'){ $monthchar='February';}
								if($month=='03'){ $monthchar='March';}
								if($month=='04'){ $monthchar='April';}
								if($month=='05'){ $monthchar='May';}
								if($month=='06'){ $monthchar='June';}
								if($month=='07'){ $monthchar='July';}
								if($month=='08'){ $monthchar='August';}
								if($month=='09'){ $monthchar='September';}
								if($month=='10'){ $monthchar='October';}
								if($month=='11'){ $monthchar='November';}
								if($month=='12'){ $monthchar='December';}
	                       
									 
		 $result='<div>
	<h5>You are about to request an appointment for administrator. Please review and confirm that you would like to request the following appointment: </h5>
	<div class="timeslot bookedClearFix" style="padding: 21px 3px 82px 8px;background-color: beige;">
	                                                <h3>
													<i class="fa fa-calendar-o" aria-hidden="true"></i>  
													 '.$monthchar.' '.$list_day.', '.$year.' at '.$stime.' – '.$endtime.' 
													</h3>
	</div>
	<div style="padding: 20px;">
	<span><button style="background-color: #ff5501;color: white;" type="button" onclick="booking_add_to_cart(\''.$date.'\',\''.$day.'\',\''.$slot.'\',1,'.$customer_id.','.$product_id.');">Request Booking</button></span><span></span></div>
	</div>';  
	
/* echo $customer_id;
echo $date;
$datvalue=str_replace("-","/",$date);
	   return $result; */  
	   return $result;
	}
	
	
	
		  /**
     * Return the Home Page Banner Images of the Website.
     *
     * @param int $userid
	 * @param string $email 
	 * @param string $name
	 * @param string $street
	 * @param string $city
	 * @param string $country_id
	 * @param string $postcode
	 * @param string $telephone
	 * @param string $shipping
	 * @param string $payment
	 * @param int $quoteId
     * @return array()|associativeArray().
     */
     public function bookingcustomcheckout($userid,$email,$name,$street,$city,$country_id,$postcode,$telephone,$shipping,$payment,$quoteId)
	 {
		/* $parts = explode(" ", $name);
		 $lastname = array_pop($parts);
		if($lastname=='')
		{
			$lastname='@';
		}
         $firstname = implode(" ", $parts);
		if($firstname=='')
		{
			$firstname='@';
		} */
		
		if(strpos($name, ' ') > 0)
		{
			$parts = explode(" ", $name);
		 $lastname = array_pop($parts);
		 $firstname = implode(" ", $parts);
		
		}
    else
    {
		 $lastname = $name;
		  $firstname = $name;
	}
		
		//exit;
		
		
		
		
		
		 $orderData=[
				 'currency_id'  => 'USD',
				 'email'        => $email, //buyer email id
				 'shipping_address' =>[
						'firstname'    => $firstname, //address Details
						'lastname'     => $lastname,
								'street' => $street,
								'city' => $city,
						//'country_id' => 'IN',
						'country_id' => $country_id,
						'region' => '',
						'postcode' => $postcode,
						'telephone' => $telephone,
						'fax' => '',
						'save_in_address_book' => 1
							 ],
			 'items'=> [ //array of product which order you want to create
						  ['product_id'=>'5','qty'=>1,'price'=>'50'],
						  ['product_id'=>'6','qty'=>2,'price'=>'100']
						]  
						
			];

         /*for sote 4 */			
		  $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
	     $helper = $objectManager->get('Webkul\Marketplace\Helper\Data');
		 $helper->setCurrentStore(4);  
		 /* end for sote 4 */
		
		
		$store=$this->storeManager->getStore();
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $customer=$this->customerFactory->create();
        $customer->setWebsiteId($websiteId);
        //$customer->loadByEmail($orderData['email']);// load customet by email address
		$customer->load($userid);
		 $customer->getId();
		 $customer->getEntityId();
       /*  if(!$customer->getEntityId()){
            //If not avilable then create this customer 
            $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email']) 
                    ->setPassword($orderData['email']);
            $customer->save();
        } */
			
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		
		/* $quote_sql_latest = "Select * FROM quote where customer_id=$userid and store_id=4 and is_active=1 order by entity_id ASC limit 1";
		$result_result_cart = $connection->fetchAll($quote_sql_latest); */
		  //  print_r($result_result_cart); 
		
		
		$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=4 and entity_id='$quoteId' and is_active=1";
		
		
		$result_result = $connection->fetchAll($quote_sql);	
		if(!empty($result_result))
		{
		
			  $quoteId2=$result_result[0]['entity_id'];
			 $quote = $this->quote->create()->load($quoteId2);
			$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
           $checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		  $checkoutSession->setQuoteId($quoteId2);
		   
		  
		          //Set Address to quote
         $quote->getBillingAddress()->addData($orderData['shipping_address']);
        $quote->getShippingAddress()->addData($orderData['shipping_address']);
  
        // Collect Rates and Set Shipping & Payment Method
 
         $shippingAddress=$quote->getShippingAddress(); 
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        //->setShippingMethod('freeshipping_freeshipping'); //shipping method
						->setShippingMethod($shipping); //shipping method
						
        //$quote->setPaymentMethod('cashondelivery'); //payment method
		$quote->setPaymentMethod($payment);
        $quote->setInventoryProcessed(false); //not effetc inventory
        $quote->save(); //Now Save quote and your quote is ready
  
        // Set Sales Order Payment
        //$quote->getPayment()->importData(['method' => 'cashondelivery']);
          $quote->getPayment()->importData(['method' => $payment]);
        // Collect Totals & Save Quote
        $quote->collectTotals()->save();
        $quote->getId();
        // Create Order From Quote
        $order = $this->quoteManagement->submit($quote);
        
       $order->setEmailSent(0);
	  //$order->setEmailSent(1);
        echo $increment_id = $order->getRealOrderId();
	    $lastOrderId = $increment_id;
		
		$helper = $objectManager->get(
            'Webkul\Marketplace\Helper\Data'
        );
		
		$getProductSalesCalculation = $objectManager->get(
            'Webkul\Marketplace\Observer\SalesOrderPlaceAfterObserver'
        );
       $getProductSalesCalculation->getProductSalesCalculation($order); 
	  
	  
	  
	  
	   
			 if($order->getEntityId())
			 {
			   $result[]=array('status'=>array("code"=>"1","message"=>$lastOrderId));
				
			}
			else{
				$result[]=array('status'=>array("code"=>"0","message"=>"error"));
			}  
		  
		  
		  
		  
		  
		}			
		else
		{
			$result[]=array('status'=>array("code"=>"0","message"=>"error"));
		}			
		
 			
	     return $result;  
	
	 }
	 /**
      * @return
	  * @param int $product_id 
	  * @param int $userid 
	  * @param string $nicname 
	  * @param string $summary 
	  * @param string $review 
	  * @param string $quality 
	  * @param string $value  
	  * @param string $price 
	   
	  */
    public function postreview($product_id,$userid,$nicname,$summary,$review,$quality,$value,$price)
	{
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		//$reviewFactory = $objectManager->get('Magento\Review\Model\Review');
		
		$reviewFactory = $objectManager->get('Magento\Review\Model\ReviewFactory');
		$ratingFactory = $objectManager->get('Magento\Review\Model\RatingFactory');
		
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		 $quote_sql = "Select * FROM catalog_product_entity where entity_id=$product_id and type_id!='customProductType' ";
		 $result_result = $connection->fetchAll($quote_sql);	
		 //exit;
		 if(!empty($result_result))
		 {
			 $quote_sql2 = "Select * FROM customer_entity where entity_id=$userid";
		    $result_result2 = $connection->fetchAll($quote_sql2);
			   if(!empty($result_result2))
			   {
				   $revarrau=array('form_key'=> 'L2CuF0ML38ZLrkhz',
												'ratings'=> array(3=> 12,2=> 8,1=> 3),
												'validate_rating' =>'', 
												'nickname'=> $nicname,
												'title' => $summary,
												'detail' =>$review
											); 
						
		   
					
							 $helper = $objectManager->get('Webkul\Marketplace\Helper\Data');
							 $helper->setCurrentStore(1);  
							 $store=$this->storeManager->getStore();


                              $review = $reviewFactory->create()->setData($revarrau);;
							  $review->unsetData('review_id'); 
							  $review->setEntityPkValue($product_id)
									->setStatusId(2)
									->setEntityId(1)
									->setStoreId($this->storeManager->getStore()->getId())
                                    ->setStores([$this->storeManager->getStore()->getId()]) 
									->setCustomerId($userid)
									->save(); 



						/* $rating = array(
						 1 => array(1,2,3,4,5), 
						 2 => array(6,7,8,9,10),
						 3 => array(11,12,13,14,15)
						); */
						$rating = array(3=>$price,2=>$value,1=>$quality);
						//$rating = array($quality,$value,$price);
						//print_r($rating); 
						//$rating=array('1'=>3,'2'=>8,'3'=>12);
					 foreach ($rating as $ratingId => $optionId) {
											$ratingFactory->create()
												->setRatingId($ratingId)
												->setReviewId($review->getId())
												->setCustomerId($userid)
												->addOptionVote($optionId,$product_id);  
                                   }  

                    $review->aggregate();		
					if($review->getId())
					{
						$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'review_id'=>$review->getId());
					}
					else
					{
						$result[]=array('status'=>array("code"=>"0","message"=>"error"));
					}
			   }
			   else
			   {
				   $result[]=array('status'=>array("code"=>"0","message"=>"error"));
			   }
			  
			 
		 }
		 else
		 {
			 $result[]=array('status'=>array("code"=>"0","message"=>"error"));
		 }
							 
		
	    return $result;  
	}
	
	 /**
      * @return
	  * @param int $product_id 
	 */
    public function getreview($product_id)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		
		
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$quote_sql = "Select * FROM review where entity_pk_value=$product_id and status_id=1 order by review_id desc";
		//$quote_sql = "Select * FROM review where entity_pk_value=$product_id  order by review_id desc ";
		 $result_result = $connection->fetchAll($quote_sql);
		//echo "<pre>"; print_r($result_result);
		 $reviewdetails='';
		 $result_review=''; 
		  $quality='';
					 $Value='';
					 $Price='';
		 if(!empty($result_result))
		 {
			 foreach($result_result as $revid)
			 {
				// print_r($revid);
				$review_id=$revid['review_id'];
				$created_at=$revid['created_at'];
				$quote_sql2 = "Select * FROM review_detail where review_id=$review_id ";
				 $result_result2 = $connection->fetchAll($quote_sql2);
				 if(!empty($result_result2))
					 {
						  //print_r($result_result2); 
						 //$reviewdetails=$result_result2;
						 $reviewdetails['review_id']=$result_result2[0]['review_id'];
						 $reviewdetails['title']=$result_result2[0]['title'];
						 $reviewdetails['detail']=$result_result2[0]['detail'];
						 $reviewdetails['nickname']=$result_result2[0]['nickname'];
						 $reviewdetails['customer_id']=$result_result2[0]['customer_id'];
					 } 
					
				 $quote_sql23 = "Select * FROM rating_option_vote where review_id=$review_id and rating_id=1";
				 $result_result_quality = $connection->fetchAll($quote_sql23);
				 if(!empty($result_result_quality))
					 {
						//print_r($result_result23); 
						 //$reviewdetails=$result_result2; 
						$quality=$result_result_quality[0]['option_id'];
					 } 
					 
				  $quote_sqlValue = "Select * FROM rating_option_vote where review_id=$review_id and rating_id=2";
				 $result_result_Value = $connection->fetchAll($quote_sqlValue);
				 if(!empty($result_result_Value))
					 {
						//print_r($result_result23); 
						 //$reviewdetails=$result_result2; 
						$Value=$result_result_Value[0]['option_id'];
					 } 
					 
					  $quote_sqPrice = "Select * FROM rating_option_vote where review_id=$review_id and rating_id=3";
				 $result_result_Price = $connection->fetchAll($quote_sqPrice);
				 if(!empty($result_result_Price))
					 {
						//print_r($result_result23); 
						 //$reviewdetails=$result_result2; 
						$Price=$result_result_Price[0]['option_id'];
					 } 
					 
					 
					// $rating=array('quality'=>$quality,'value'=>$Value,'price'=>$Price) ;
					 $reviewdetails['rating_quality']=$quality;
					 $reviewdetails['rating_value']=$Value;
					 $reviewdetails['rating_price']=$Price;
					 $reviewdetails['created_at']=$created_at;
					 
					//$result_review[]=array('reviewdetails'=>$reviewdetails,'rating'=>$rating); 
					//$result_review[]=array('reviewdetails'=>$reviewdetails);
					$result_review[]=$reviewdetails;
					 
			 }
			
		     $result[]=array('status'=>array("code"=>"1","message"=>"Success"),'review'=>$result_review); 
		 
			  
		 }
		 else
		 {
			  //$result[]=array('status'=>array("code"=>"0","message"=>"error"));  
			   $result[]=array('status'=>array("code"=>"0","message"=>"error"),'review'=>"No result found!"); 
		 }
		 
		 
		
		 return $result;  
	}
}