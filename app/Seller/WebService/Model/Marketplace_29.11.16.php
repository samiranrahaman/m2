<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Seller\WebService\Model;

use Seller\WebService\Api\MarketplaceInterface;
use Magento\Customer\Controller\Account;
use Magento\Customer\Api\AccountManagementInterface;
use \Magento\Framework\App\Helper\AbstractHelper;

/**
 * Defines the implementaiton class of the calculator service contract.
 */
class Marketplace implements MarketplaceInterface 
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
		 /* \Magento\Customer\Model\Customer $customer,
          \Magento\Customer\Model\Session $customerSession, */
		  AccountManagementInterface $customerAccountManagement,
		  //addto quote
		//   \Magento\Framework\App\Helper\Context $context,
       // \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Data\Form\FormKey $formkey,
        \Magento\Quote\Model\QuoteFactory $quote,
        \Magento\Quote\Model\QuoteManagement $quoteManagement,
       // \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Service\OrderService $orderService ,
       //add to cart	
        \Magento\Checkout\Model\Cart $cart,
		\Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
		\Magento\Framework\Event\Observer $observer
		//\Magento\Sales\Model\OrderFactory $orderFactory

	   ) {
        $this->storeManager     = $storeManager;
        $this->customerFactory  = $customerFactory;
		/* $this->_customer = $customer;
        $this->_customerSession = $customerSession; */
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
		//$this->orderFactory = $orderFactory;
       // parent::__construct($context);
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
		/* $result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"));
		//Select Data from table
		$sql = "Select * FROM marketplace_userdata where seller_id=$sellerid";
		$result1 = $connection->fetchAll($sql);
		$result[]=array('profile'=>$result1); */
		
		/* $result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"));
		//Select Data from table
		$sql = "Select * FROM marketplace_userdata where seller_id=$sellerid";
		$result1 = $connection->fetchAll($sql);
		$result[]=array('profile'=>$result1); */
		//array_push($result,$result2); 
		//$result['status']=array("code"=>"1","message"=>"sucess","seller_id"=>"2");
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
		//$tableName = $resource->getTableName('employee'); //gives table name with prefix
		 
		//Select Data from table
		$sql3 = "Select * FROM marketplace_product where seller_id=$sellerid";
		$result3 = $connection->fetchAll($sql3);
		//$result['status']=array("code"=>"1","message"=>"sucess","seller_id"=>"2");
		$result4='';
		
		//$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'offersbanners'=>array(array('id'=>1,'img'=>'"http://btranz.website/banner-image.png'),array('id'=>2,'img'=>'"http://btranz.website/141961_20160310_HPOasis_A1.jpg')));
		
		foreach($result3 as $data)
		{
			     //echo $data['mageproduct_id']; 
			       $productId =$data['mageproduct_id'];
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
					//  print_r($currentproduct);
					/* $result4[]['name']=$currentproduct->getName(); 
					$result4[]['description']=$currentproduct->getDescription(); 
					$result4[]['price']=$currentproduct->getPrice();
					$result4[]['finalPrice']=$currentproduct->getFinalPrice();
					$result4[]['thumbnail']='http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(); */
					
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
				   
				   
				   
				   
					$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
					//$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
					
			 
		} 
		  
		//$result['product']=$result4;
		
		//$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'product'=>$result4);
		//$result['status']=array("code"=>"1","message"=>"sucess","seller_id"=>"2");
		//$result['product']=$result4;
		
		
		//return $result;
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
						//$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>$sellerid),'offersbanners'=>array(array('id'=>1,'img'=>'http://btranz.website/pub/media/avatar/'.$imgname.'')));
						
						//$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
								
						$result[]=array('id'=>1,'img'=>'http://btranz.website/pub/media/avatar/'.$imgname.'');
						
						
					}
					else{
						
						//$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>$sellerid),'offersbanners'=>'null');
						$result[]=array('id'=>0,'img'=>'null');
						
					}
		}
		else
		{
			$result[]=array('id'=>0,'img'=>'null');
		}
		//$result[]=array('status'=>array("code"=>"1","message"=>"error"));
		
		//}
		/* else
		{
			$result[]=array('status'=>array("code"=>"1","message"=>"error"));
		} */
		
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
       
		$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'brandlist'=>array(array('id'=>1,'img'=>'http://btranz.website/fitness_logo4.jpg'),array('id'=>2,'img'=>'http://btranz.website/fitness_logo5.jpg')));
		
		
		return $result;     
		 
   // }
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
		//$tableName = $resource->getTableName('employee'); //gives table name with prefix
		 
		//Select Data from table
		$sql3 = "Select * FROM marketplace_product where seller_id=$sellerid";
		$result3 = $connection->fetchAll($sql3);
		//$result['status']=array("code"=>"1","message"=>"sucess","seller_id"=>"2");
		$result4='';
		
		//$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'offersbanners'=>array(array('id'=>1,'img'=>'"http://btranz.website/banner-image.png'),array('id'=>2,'img'=>'"http://btranz.website/141961_20160310_HPOasis_A1.jpg')));
		
		foreach($result3 as $data)
		{
			     //echo $data['mageproduct_id']; 
			       $productId =$data['mageproduct_id'];
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
					//  print_r($currentproduct);
					
					
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
					
				$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
				//$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail());
					
			
		}
		  
		//$result['product']=$result4;
		
		$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'product'=>$result4);
		
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
		/* $result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"));
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
		$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'getproductcategories'=>$result4);
		 
		
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
    public function getsellerhome($sellerid) {	
      if(empty($sellerid) || !isset($sellerid) || $sellerid == ""){
		 throw new InputException(__('Id required')); 	
		}
		else{	
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		//$tableName = $resource->getTableName('employee'); //gives table name with prefix
		 
		//Select Data from table
		$sql3 = "Select * FROM marketplace_product where seller_id=$sellerid";
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
					//$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail());
					
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
				   
					
					
					
					$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
					
			
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
						//$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>$sellerid),'offersbanners'=>array(array('id'=>1,'img'=>'http://btranz.website/pub/media/avatar/'.$imgname.'')));
						
						//$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
								
						//$result[]=array('id'=>1,'img'=>'http://btranz.website/pub/media/avatar/'.$imgname.'');
						$result_offerbanner[0]=array('id'=>1,'img'=>'http://btranz.website/pub/media/avatar/'.$imgname.'');
		
						
					}
					else{
						
						//$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>$sellerid),'offersbanners'=>'null');
						$result_offerbanner[]=array('id'=>0,'img'=>'null');
						
					}
		}
		else
		{
			$result_offerbanner[0]=array('id'=>1,'img'=>'https://d152j5tfobgaot.cloudfront.net/wp-content/uploads/2015/09/yourstory-e-commerce1.jpg');
		
		}
		
		
		
		
		
		
		/* $result_offerbanner[0]=array('id'=>1,'img'=>'https://d152j5tfobgaot.cloudfront.net/wp-content/uploads/2015/09/yourstory-e-commerce1.jpg');
		$result_offerbanner[1]=array('id'=>2,'img'=>'http://www.ecouponswala.in/wp-content/uploads/2014/10/Freecultr-Diwali-Offer-600x260.png');
		 */
		$result_brandlist[0]=array('id'=>1,'img'=>'http://btranz.website/fitness_logo4.jpg');
		$result_brandlist[1]=array('id'=>2,'img'=>'http://btranz.website/fitness_logo5.jpg');
		//$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'offersbanners'=>$result_offerbanner,'product'=>$result4,'topsellingproduct'=>$result4,'brandlist'=>$result_brandlist);
		$result[]=array('offersbanners'=>$result_offerbanner,'product'=>$result4,'topsellingproduct'=>$result4,'brandlist'=>$result_brandlist);
		else:
		$result[]=array('offersbanners'=>'null','product'=>'null','topsellingproduct'=>'null','brandlist'=>'null');
		
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
    public function getcategoryproductlist($categoryid,$sellerid) {	
      if(empty($categoryid) || !isset($categoryid) || $categoryid == ""){
		// throw new InputException(__('Id required')); 	
		$result[]=array('status'=>array("code"=>"0","message"=>"unsucess"));
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
					
					
					
				$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
					
					//$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail());
					
			
		}
		$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'product'=>$result4);
		 
		
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
    public function getproductdetails($productid) {	
      if(empty($productid) || !isset($productid) || $productid == ""){
		// throw new InputException(__('Id required')); 	
		$result[]=array('status'=>array("code"=>"0","message"=>"unsucess"));
		return $result; 
		}
		else{	
		
		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		
		 
		// if (!$this->getConfigFlag('active')) {
         //return false;
        //}
	//echo  $amount = $this->getConfigData('price');exit;
		
		
		
		
		/*
		$sql3 = "SELECT * FROM catalog_category_product a ,marketplace_product b where a.category_id=$categoryid and a.product_id=b.mageproduct_id and seller_id=$sellerid";
		$result3 = $connection->fetchAll($sql3);
		$result4='';
		foreach($result3 as $data)
		{ */
			   
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
					//print_r($currentproduct);
					$allimages=$currentproduct->getMediaGallery('images');
					//print_r($allimages);
					$imgarray=array();
					foreach($allimages as $imgurl)
						{
							$imgarray[]='http://btranz.website/pub/media/catalog/product'.$imgurl['file'];
						}
					//$result4[]=array("name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"fullImage"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getImage(),'sku'=>$currentproduct->getSku(),'mediagallery'=>$imgarray);
					
			
		//}
		 
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
				   
		
		
		  // $result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"fullImage"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getImage(),'sku'=>$currentproduct->getSku(),'mediagallery'=>$imgarray,"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary),'delivery'=>array(array('method'=>'Standard Delivery','cost'=>'Free'),array('method'=>'Expresee Delivery','cost'=>'$90')));
			 $result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"fullImage"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getImage(),'sku'=>$currentproduct->getSku(),'mediagallery'=>$imgarray,"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary),'delivery'=>array(array('method'=>'Standard Delivery','cost'=>'Free','subtitle'=>'Delivery in 5-6 days'),array('method'=>'Expresee Delivery','cost'=>'5','subtitle'=>'Delivery in 1-2 days')));
									
			
		
		
		
		$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'productdetails'=>$result4);
		 
		
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
	
	 $validate = 0;
	 try {
				$customer = $this->customerAccountManagement->authenticate($username,$password);
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
	 
    
		return $result;   
               			

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
			$result[]=array('status'=>array("code"=>"0","message"=>"Email id Exist!"));
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
						 $result[]=array('status'=>array("code"=>"1","message"=>"success"),'customer'=>array('id'=>$customer->getId(),'username'=>$username,'password'=>$password,'name'=>$name));
						//echo $customer->getId();
					 }
					 else
					 {
					$result[]=array('status'=>array("code"=>"0","message"=>"error"));
					
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
      
			 /* $orderData=[
				 'currency_id'  => 'USD',
				 'email'        => 'demo@gmail.com', //buyer email id
				 'shipping_address' =>[
						'firstname'    => 'jhon', //address Details
						'lastname'     => 'Deo',
								'street' => 'xxxxx',
								'city' => 'xxxxx',
						'country_id' => 'IN',
						'region' => 'xxx',
						'postcode' => '43244',
						'telephone' => '52332',
						'fax' => '32423',
						'save_in_address_book' => 1
							 ],
			 'items'=> [ //array of product which order you want to create
						  ['product_id'=>'5','qty'=>1,'price'=>'50'],
						  ['product_id'=>'6','qty'=>2,'price'=>'100']
						]  
						'items'=> [ //array of product which order you want to create
						  ['product_id'=>$productid,'qty'=>$quantity]
						] 
						
			];	 */  
			/*  $orderData=[ 
				 'currency_id'  => 'USD',
				 'items'=> [ //array of product which order you want to create
						  ['product_id'=>$productid,'qty'=>$quantity]
						]
			     ];  */
	     //print_r($orderData);
			
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
			
        /* $quote=$this->quote->create(); //Create object of quote
        $quote->setStore($store); //set store for which you create quote
        // if you have allready buyer id then you can load customer directly 
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer); //Assign quote to customer
		$quote->save(); */
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
		
		 
        //add items in quote
		// $params = array();
       /*  foreach($orderData['items'] as $item){
			//echo $item['price'];
			 $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
			 $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($item['product_id']);
				   
             //$product=$this->_product->load($item['product_id']);
          // $product->setPrice($item['price']);
		 //$productdetails->setPrice($item['price']);
            $quote->addProduct(
                //$product,
				$productdetails,
                intval($item['qty'])
            ); 
			
			$this->cart->addProduct($productdetails, array('qty' => $item['qty']));
			//$this->cart->addProduct($productdetails,$params);
			
            $this->cart->save(); 
        }  */
 /*
        //Set Address to quote
        $quote->getBillingAddress()->addData($orderData['shipping_address']);
        $quote->getShippingAddress()->addData($orderData['shipping_address']);
 
        // Collect Rates and Set Shipping & Payment Method
 
        $shippingAddress=$quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)
                        ->collectShippingRates()
                        ->setShippingMethod('freeshipping_freeshipping'); //shipping method
        $quote->setPaymentMethod('checkmo'); //payment method
        $quote->setInventoryProcessed(false); //not effetc inventory*/
       // $quote->save(); //Now Save quote and your quote is ready
 
        // Set Sales Order Payment
     // $quote->getPayment()->importData(['method' => 'checkmo']);
 
        // Collect Totals & Save Quote
        //$quote->collectTotals()->save();
 
        // Create Order From Quote
        //$order = $this->quoteManagement->submit($quote); 
        
      // $order->setEmailSent(0);
       /* $increment_id = $order->getRealOrderId();
         if($order->getEntityId()){
            $result['order_id']= $order->getRealOrderId();
        }else{
            $result=['error'=>1,'msg'=>'Your custom message'];
        } */
		
		if($quote->getId())
		 {
			 //$result[]=array('status'=>array("code"=>"1","message"=>"success"));
		   //$result['status']=array("code"=>"1","message"=>"success");
		   $result[]=array("message"=>"product Added");
		 }
		 else
		 {
		//$result[]=array('status'=>array("code"=>"1","message"=>"error"));
		 $result[]=array("message"=>"product not Added");
		 }
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
				$result[]=array('status'=>array("code"=>"1","message"=>"error"));
			}
			
			
			
		}
		else
		{
			$result[]=array('status'=>array("code"=>"1","message"=>"error"));
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
		    /* $quoteId=$result_result[0]['entity_id'];
			$quote = $this->quote->create()->load($quoteId);
			$quote_sql = "delete FROM quote_item where quote_id=$quoteId and product_id=$productid";
		    $connection->rawQuery($quote_sql); */
			
			/* $quote->addProduct(
                //$product,
				$productdetails,
                intval($item['qty'])
            ); 
			 $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
			 $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
			
			$this->cart->addProduct($productdetails, array('qty' =>$quantity));
			//$this->cart->addProduct($productdetails,$params);
			
            $this->cart->save();  */
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
				$result[]=array('status'=>array("code"=>"1","message"=>"error"));
			}
			
			
			
		}
		else
		{
			$result[]=array('status'=>array("code"=>"1","message"=>"error"));
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
							$imgarray[]='http://btranz.website/pub/media/catalog/product'.$imgurl['file'];
						}
					$result4[]=array("id"=>$product_id,"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"fullImage"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getImage(),'sku'=>$currentproduct->getSku(),'mediagallery'=>$imgarray,'qty'=>$data['qty']);
					
				/* $store_id=$data['store_id'];
				$name=$data['name'];
				$description=$data['description'];
				$description=$data['price'];
				$description=$data['qty'];
				$description=$data['base_price'];
				$description=$data['custom_price'];
				$description=$data['discount_percent'];
				$description=$data['discount_amount']; */
				
				
				
				/* $objectManager = \Magento\Framework\App\ObjectManager::getInstance();//instance of object manager
				$itemModel = $objectManager->create('Magento\Quote\Model\Quote\Item');//Quote item mode
				$quoteItem=$itemModel->load($quote_item_id);//load particular item which you want to delete by his item id */
				//echo $quoteItem->getName();
				//echo $quoteItem->price();
				/* $quoteItem->delete();//deletes the item
				$this->cart->removeItem($quote_item_id);
		       $this->cart->save(); */ 
			 
				}
				//print_r($result4);
				$result[]=array('status'=>array("code"=>"1","message"=>"success"),'product'=>$result4,'quoteid'=>$quoteId);
			}
			else
			{
				$result[]=array('status'=>array("code"=>"1","message"=>"error"));
			}
			
			
		}
		else
		{
			$result[]=array('status'=>array("code"=>"1","message"=>"error"));
		
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
		
		
		$helper->setCurrentStore(4);
		 $datvalue=str_replace("-","/",$dat);	
			
		$store=$this->storeManager->getStore();
		$websiteId = $this->storeManager->getStore()->getWebsiteId();
		//$websiteId =4;
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
		$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=4 and is_active=1";
		$result_result = $connection->fetchAll($quote_sql);
		//print_r($result_result); 
		if(!empty($result_result))
		{
			
		    $quoteId=$result_result[0]['entity_id'];
			$quote = $this->quote->create()->load($quoteId);
			//echo"1";
			
		  $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		  $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
         
		// $checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		 // $checkoutSession->setQuoteId($quoteId);
		  
	     /*  $quote_sql_samebook = "Select * FROM quote_item where booking_date='10/10/2016' and booking_day='Monday' and booking_slot_time='9:0-12:0'";
		  $result_result_samebook = $connection->fetchAll($quote_sql_samebook);
		  if(!empty($result_result_samebook))
		  {
			  $this->cart->addProduct($productdetails, array('qty' =>$quantity));
		      $this->cart->save();
		  }
		  else
		  {
			$quoteItem = $this->quoteItemFactory->create();
			$quoteItem->setProduct($productdetails);
			$quote->addItem($quoteItem);
			$quote->collectTotals()->save(); 
			$quote_id=$quote->getId() ;
		    $quoteItem_id= $quoteItem->getId();
		  
		    $quote_sql = "update quote_item  set booking_date='10/10/2016',booking_day='Monday',booking_slot_time='9:0-12:0',booking_slot_count='1' where item_id='$quoteItem_id' and quote_id='$quote_id'";
		    $connection->rawQuery($quote_sql);  
		  } */ 
		  
		  /* $this->cart->addProduct($productdetails, array('qty' =>$quantity));
		  $this->cart->save(); */
		  
		  /*create new cart item*/
		    $quoteItem = $this->quoteItemFactory->create();
			$quoteItem->setProduct($productdetails);
			$quote->addItem($quoteItem);
			$quote->collectTotals()->save(); 
			 $quote_id=$quote->getId() ;
		     $quoteItem_id= $quoteItem->getId();
		  
		   // $quote_sql = "update quote_item  set booking_date='10/10/2016',booking_day='Monday',booking_slot_time='9:0-12:0',booking_slot_count='1' where item_id='$quoteItem_id' and quote_id='$quote_id'";
		    $quote_sql = "update quote_item  set booking_date='$datvalue',booking_day='$day',booking_slot_time='$slot',booking_slot_count='$slotno' where item_id='$quoteItem_id' and quote_id='$quote_id'";
		    $connection->rawQuery($quote_sql);
		  
			 
		}
		else 
		{  
			
		$quote=$this->quote->create();
		
		$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
		 $productdetails = $objectManager->create('Magento\Catalog\Model\Product')->load($productid);
         
		$checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		$checkoutSession->setQuoteId($quote->getId());
		 $quote->getId();
		
		$this->cart->addProduct($productdetails, array('qty' =>$quantity));
		$this->cart->save();
		$quote = $this->cart->getQuote();
       
  	    $quote->setCustomerId ($userid);
		 
		//echo $store>getId(); 
		// Update changes
		 $quote->setStore($store); //set store for which you create quote
        // if you have allready buyer id then you can load customer directly 
        $customer= $this->customerRepository->getById($customer->getEntityId());
        $quote->setCurrency();
        $quote->assignCustomer($customer);
		
		
		$quote->save();
	       $quote_id=$quote->getId();
	       $quoteItems = $quote->getAllVisibleItems();
			foreach ($quoteItems as $item)
			{
			$quoteItem_id = $item->getId();
			$quote_sql = "update quote_item  set booking_date='10/10/2016',booking_day='Monday',booking_slot_time='9:0-12:0',booking_slot_count='1' where item_id='$quoteItem_id' and quote_id='$quote_id'";
		    $connection->rawQuery($quote_sql);
			}
	  
	  
	  
		}
		//exit;
		if($quote->getId())
		 {
			// $result[]=array('status'=>array("code"=>"1","message"=>"success"));
			$result='success';
		   
		 }
		 else
		 {
		//$result[]=array('status'=>array("code"=>"1","message"=>"error"));
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
	 * @return array()|associativeArray(). 
     */
	 public function productsearch($sellerid,$name)
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
									   
									$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag));
											
							
						}
						
						
								
				  }
				  $result[]=array('status'=>array("code"=>"1","message"=>"success","seller_id"=>$sellerid),'product'=>$result4);
		          return $result; 
                  else:
 				  
				  $result[]=array('status'=>array("code"=>"1","message"=>"success","seller_id"=>$sellerid),'product'=>'No result found!');
		          return $result; 
				  
				  endif;
           
				  }
				  else
				  {
					  
					  $result[]=array('status'=>array("code"=>"1","message"=>"error"));
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
	 
	public function simillarproducts($sellerid,$productid) 
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
											   
											   
											   $sql_wishlist = "Select * FROM wishlist a ,wishlist_item b  where a.customer_id=$sellerid and a.wishlist_id=b.wishlist_id and b.product_id=$productId ";
											   $result_wishlist = $connection->fetchAll($sql_wishlist);
											   
											   
											   
											   $reviewFactory = $objectManager->create('Magento\Review\Model\Review');
											   $storeId = $this->storeManager->getStore()->getId();
											   $reviewFactory->getEntitySummary($currentproduct, $storeId);
											   $ratingSummary = $currentproduct->getRatingSummary()->getRatingSummary();
											   if($ratingSummary=='')
											{
												$ratingSummary='null';
											} 
											  
											  
											  $result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary));
							                 // $result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail());
							

												
														
											}
									}
									$c++; 
							}
						 }
						
						
				$result[]=array('status'=>array("code"=>"1","message"=>"sucess"),'product'=>$result4);
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
						$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"categoryName"=>$categoryname,"categoryid"=>$categoryId);	
						}	
								
				  }
				  $result[]=array('status'=>array("code"=>"1","message"=>"success","seller_id"=>$sellerid),'product'=>$result4);
		          return $result; 
                  else:
 				  
				  $result[]=array('status'=>array("code"=>"1","message"=>"success","seller_id"=>$sellerid),'product'=>'No result found!');
		          return $result; 
				  
				  endif;
           
				  }
				  else
				  {
					  
					  $result[]=array('status'=>array("code"=>"1","message"=>"error"));
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
			 $imgurl= 'http://btranz.website/pub/media/gridpart2/background/image/'.$data['background']	;		
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
			 $imgurl= 'http://btranz.website/pub/media/gridpart2/background/image/'.$data['background']	;		
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
		
		$quote_sql_latest = "Select * FROM quote where customer_id=$userid and store_id=1 and is_active=1 order by entity_id ASC limit 1";
	     $result_result_cart = $connection->fetchAll($quote_sql_latest);
		  //  print_r($result_result_cart); 
		
		
		
		$quote_sql = "Select * FROM quote where customer_id=$userid and store_id=1 and entity_id='$quoteId' and is_active=1";
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
			 $result[]=array('status'=>array("code"=>"1","message"=>"error"));
		}
		/* if(!empty($result_result))
		{
		
			 $quoteId=$result_result[0]['entity_id'];
			 $quote = $this->quote->create()->load($quoteId);
			$objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
           $checkoutSession =$objectManager->get('Magento\Checkout\Model\Session');
		  $checkoutSession->setQuoteId($quoteId);
		   $this->cart = $cart;
		   $cartCount = $this->cart->getItemsCount();
		   $Quote= $this->cart->getQuote()->getData();
            print_r($Quote);
		} */
	  
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
						  $result[]=array('status'=>array("code"=>"1","message"=>"Error"));
					 
					 }
						
				}
				else
				{
					$result[]=array('status'=>array("code"=>"1","message"=>"Error"));
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
					//$applogo ='http://btranz.website/pub/media/marketplace/background/image/a/d/admin.jpg';
					//$theme ='Blue';
					 //$sellerId =$result3[0]['Seller_Id'];
					 $appname =$result3[0]['App_Name'];
					 //$applogo ='http://btranz.website/pub/media/marketplace/background/image'.$result3[0]['App_Logo'];
					 $applogo ='http://btranz.website/pub/media/gridpart2/background/image'.$result3[0]['App_Logo'];
					
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
            'created_at',
            'desc'
        );
		
	
        $orderIds = $orders->getAllIds();
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
                            $quote_sql2 = "Select * FROM sales_order_item where order_id=$order_id";
							$result_result2 = $connection->fetchAll($quote_sql2);
							//print_r($result_result2);
							$orderitemarray='';
							if(!empty($result_result2))
							{ 
						       	//$orderitemarray='';
								  foreach( $result_result2 AS $data)
								{
									$currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($data['product_id']);
									$image='';
									 if($currentproduct->getImage()!='')
									 {
										 $image='http://btranz.website/pub/media/catalog/product'.$currentproduct->getImage();
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
							'order_item_list'=>$orderitemarray);		
							
							
					
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
     * @param int $userid 
	 * @param int $productid
     * @return array()|associativeArray(). 
     */
    public function addwishlist($userid,$productid) 
	{
		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$wishlistRepository = $objectManager->get('\Magento\Wishlist\Model\WishlistFactory');
		$productRepository = $objectManager->get('\Magento\Catalog\Api\ProductRepositoryInterface');
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
				  $wishlist = "Select * FROM wishlist a,wishlist_item b where a.customer_id=$userid and a.wishlist_id=b.wishlist_id";
				  $result_wishlist = $connection->fetchAll($wishlist);
				  $wishlistproduct='';
				  if(!empty($result_wishlist))
				  {
					  foreach($result_wishlist as $wsh)
					  {
						  $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($wsh['product_id']);
						  //$wishlistproduct[]=$wsh['product_id'];
						   $wishlistproduct[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"fullImage"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getImage(),'sku'=>$currentproduct->getSku());
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
	 * @return array()|associativeArray(). 
     */
    public function getaccountinfo($userid) 
	{
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
			$result[]=array('status'=>array("code"=>"1","message"=>"Success"),'accountinfo'=>$customer->getData());
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
			$billaddress = "Select * FROM customer_address_entity  where entity_id=$billing";
		    $result_billing_address = $connection->fetchAll($billaddress);
			$shipaddress = "Select * FROM customer_address_entity  where entity_id=$shipping";
		    $result_shipaddress = $connection->fetchAll($shipaddress);
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
			echo $updateaddress = "update customer_address_entity set `city`='$city',`country_id`='$country_id',`firstname`='$fname',`lastname`='$lname',`postcode`='$postcode',`street`='$street',`telephone`='$telephone' where `entity_id`=$billing";
		    $connection->rawQuery($updateaddress);
			}
			 if($shipping!='')
			{
			echo $updateaddress = "update customer_address_entity set `city`='$city',`country_id`='$country_id',`firstname`='$fname',`lastname`='$lname',`postcode`='$postcode',`street`='$street',`telephone`='$telephone' where `entity_id`=$shipping";
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
    public function editaccountinfo($userid,$fname,$lname,$email) 
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
			$quote_check_email_exist= "Select * FROM customer_entity where `email`='$email' and `entity_id`!=$entity_id";
			$result_email = $connection->fetchAll($quote_check_email_exist);
			   if(!empty($result_email))
			   {
				     $result[]=array('status'=>array("code"=>"0","message"=>"Error"),'accountinfo'=>'Email id Exist!');
			   }
			   else
			   { 
				    $updateaddress = "update customer_entity set `email`='$email',`firstname`='$fname',`lastname`='$lname' where `entity_id`=$entity_id";
					$connection->rawQuery($updateaddress);
					$result[]=array('status'=>array("code"=>"1","message"=>"Success"));
				  
			   }
			
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
	$validate = 0;
	$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
	$customerAccountManagement = $objectManager->get('\Magento\Customer\Model\AccountManagement');
	//$customerAccountManagement->changePasswordById($userid,$oldpassword,$newpassword);
	 try {
				$customerAccountManagement->changePasswordById($userid,$oldpassword,$newpassword);
				$validate = 1;
			}
			catch(InvalidEmailOrPasswordException $ex) {
				$result[]=array('status'=>array("code"=>"0","message"=>"Error",'message'=>$ex->getMessage));
				$validate = 0; 
			} 
      if($validate == 1) 
			 {
				 $result=array('status'=>array("message"=>"Password change Successful"));
			 }
			 else
			 {
				 $result[]=array('status'=>array("code"=>"0","message"=>"Error",'message'=>$ex->getMessage));
			 }
	  return $result;         
	   
	}
	/**
     * @param string $email 
	 * @return array()|associativeArray(). 
     */
    public function forgotpassword($email) 
	{ 
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
			$customerAccountManagement = $objectManager->get('\Magento\Customer\Model\AccountManagement');
			$_customers = $objectManager->create('Magento\Customer\Api\AccountManagementInterface');
			$customer=$this->customerFactory->create();
        $customer->loadByEmail($email);
	    $customer->getId();
		if(!empty($customer->getData()))
		{
              
			//$result[]=array('status'=>array("code"=>"0","message"=>"Success",'id'=>$customer->getId()));
					try {
						$_customers->initiatePasswordReset($email, \Magento\Customer\Model\AccountManagement::EMAIL_RESET);
						$result=array('status'=>array("message"=>"Password reset email sent."));
					} catch (NoSuchEntityException $e) {
						// Do nothing, we don't want anyone to use this action to determine which email accounts are registered.
					} catch (\Exception $exception) {
						echo __('We\'re unable to send the password reset email.');
					}
		}
		else
		{
			//$result[]=array('status'=>array("message"=>"Error"));
			$result=array('status'=>array("message"=>"Error."));
		}
			
	     
	
	          
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
			$result[]=array('status'=>array("code"=>"1","message"=>"Error"));
		}
	    
		return $result; 
	}
	/**
	 * @param int $email
     * @return array()|associativeArray(). 
     */
    public function acivateaccount($userid) 
	{ 
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$customer=$this->customerFactory->create();
        $customer->load($userid);
	    //$customer->getId();
		if(!empty($customer->getData()))  
		{
			 $updateaddress = "update customer_entity set `is_active`='1' where `entity_id`=$userid";
			 $connection->rawQuery($updateaddress);
			$result[]=array('status'=>array("code"=>"1","message"=>"Success"));
		}
		else
		{
			$result[]=array('status'=>array("code"=>"1","message"=>"Error"));
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
}