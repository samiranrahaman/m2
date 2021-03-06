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
		\Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory

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
		if($sellerid != "")
		{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();		
		$banner_sql = "SELECT * FROM marketplace_userdata WHERE seller_id=$sellerid";
		$result2 = $connection->fetchAll($banner_sql);
		//print_r($result2);
		
		$imgname=$result2[0]['banner_pic'];
		if($imgname!='')
		{
			$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>$sellerid),'offersbanners'=>array(array('id'=>1,'img'=>'http://btranz.website/pub/media/avatar/'.$imgname.'')));
			
		}
		else{
			$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>$sellerid),'offersbanners'=>'null');
			
		}
		//$result[]=array('status'=>array("code"=>"1","message"=>"error"));
		
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
		
		$result4='';
		foreach($result3 as $data)
		{
			     //echo $data['mageproduct_id']; 
			       $productId =$data['mageproduct_id'];
				   $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
				   $currentproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
					//  print_r($currentproduct);
					$result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail());
					
			
		}
		//  print_r($result4);
		//$result['product']=$result4;
		
		$result_offerbanner[0]=array('id'=>1,'img'=>'https://d152j5tfobgaot.cloudfront.net/wp-content/uploads/2015/09/yourstory-e-commerce1.jpg');
		$result_offerbanner[1]=array('id'=>2,'img'=>'http://www.ecouponswala.in/wp-content/uploads/2014/10/Freecultr-Diwali-Offer-600x260.png');
		
		$result_brandlist[0]=array('id'=>1,'img'=>'http://btranz.website/fitness_logo4.jpg');
		$result_brandlist[1]=array('id'=>2,'img'=>'http://btranz.website/fitness_logo5.jpg');
		$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'offersbanners'=>$result_offerbanner,'product'=>$result4,'topsellingproduct'=>$result4,'brandlist'=>$result_brandlist);
		
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
			 $result4[]=array("id"=>$currentproduct->getId(),"name"=>$currentproduct->getName(),"description"=>$currentproduct->getDescription(),"price"=>$currentproduct->getPrice(),"finalPrice"=>$currentproduct->getFinalPrice(),"thumbnail"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getthumbnail(),"fullImage"=>'http://btranz.website/pub/media/catalog/product'.$currentproduct->getImage(),'sku'=>$currentproduct->getSku(),'mediagallery'=>$imgarray,"offferTag"=>array("shareurl"=>$producturl,"Tag"=>$optionText,'discount'=>$savetag,'rating'=>$ratingSummary),'delivery'=>array(array('method'=>'Standard Delivery','cost'=>'Free','subtitle'=>'Delivery in 5-6 days'),array('method'=>'Expresee Delivery','cost'=>'90','subtitle'=>'Delivery in 1-2 days')));
									
			
		
		
		
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
	
	//login using mail only
	/* $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();
	$customer = $this->_customer->setWebsiteId($websiteId); 
	$customer = $this->_customer->loadByEmail("samiran31@gmail.com"); */ 
	
	$customer = $this->customerAccountManagement->authenticate($username,$password);
	
	//echo $id=$this->_customer->getId();exit;
      // $this->_customerSession->setCustomerAsLoggedIn($customer);
    //print_r($customer->getId());exit;
	//$messages = $this->messageManager->getMessages(true); 
	//print_r($messages);exit; 
	if($customer->getId())
		 {
			 $result[]=array('status'=>array("code"=>"1","message"=>"success"),'customer'=>array('id'=>$customer->getId(),'username'=>$username,'password'=>$password,'name'=>$customer->getFirstname()));
		    //echo $customer->getId();
		 }
		 else
		 {
		$result[]=array('status'=>array("code"=>"1","message"=>"error"));
		
		 }
    
		return $result;   
              /*  if (!empty($username) && !empty($password)) {
            
                $customer = $this->customerAccountManagement->authenticate($username, $password);
                $this->session->setCustomerDataAsLoggedIn($customer);
                echo $this->session->regenerateId();
				$result[]=array('status'=>array("code"=>"1","message"=>"sucess","seller_id"=>"2"),'offersbanners'=>array(array('id'=>1,'img'=>'https://d152j5tfobgaot.cloudfront.net/wp-content/uploads/2015/09/yourstory-e-commerce1.jpg'),array('id'=>2,'img'=>'http://www.ecouponswala.in/wp-content/uploads/2014/10/Freecultr-Diwali-Offer-600x260.png')));
		return $result; 
			   } */				

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
        
      
         // 1.process registration programatically
	  // Get Website ID
         $websiteId  = $this->storeManager->getWebsite()->getWebsiteId();

        // Instantiate object (this is the most important part)
         $customer   = $this->customerFactory->create();
       // $customer->setWebsiteId($websiteId);

        // Preparing data for new customer
        $customer->setEmail($username); 
		// $customer->setTelephone('12365478');  
        $customer->setFirstname($name);
        $customer->setLastname($name);
        $customer->setPassword($password);

        // Save data
       $customer->save();
	   
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
	   
	   
	   
	   
	   
	   
        $customer->sendNewAccountEmail();
		// $id=$customer->getId(); 
		
		/* $redirectUrl='';
		$customer = $this->customerAccountManagement
                         ->createAccount($username,$password,$redirectUrl); */
		
		
		
		
		
		  if($customer->getId())
		 {
			 $result[]=array('status'=>array("code"=>"1","message"=>"success"),'customer'=>array('id'=>$customer->getId(),'username'=>$username,'password'=>$password,'name'=>$name));
		    //echo $customer->getId();
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
		$quote_sql = "Select * FROM quote where customer_id=$userid and is_active=1";
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
			 $result[]=array('status'=>array("code"=>"1","message"=>"success"));
		   
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
	 * @param string $price Right hand operand.  
	 * @param string $userid Right hand operand.
     * @return array()|associativeArray(). 
     */
    public function updatecartproduct($productid,$quantity,$userid) { 
      
		  
		  $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		  
		
		$quote_sql = "Select * FROM quote where customer_id=$userid and is_active=1";
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
		  
		
		$quote_sql = "Select * FROM quote where customer_id=$userid and is_active=1";
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
		  
		 
		$quote_sql = "Select * FROM quote where customer_id=$userid and is_active=1";
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
				$result[]=array('status'=>array("code"=>"1","message"=>"success"),'product'=>$result4);
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
		 //echo $sellerid;
		 //echo $productid;
		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
						$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
						$connection = $resource->getConnection();
						 $quote_sql = "Select DISTINCT(category_id) FROM marketplace_product a ,catalog_category_product b where a.mageproduct_id=b.product_id and a.seller_id=$sellerid and b.product_id=a.mageproduct_id limit 1";
						$result_result = $connection->fetchAll($quote_sql);
						//print_r($result_result);
			            if(!empty($result_result))
						{
							
							 foreach($result_result as $category)
							{
								$categoryid= $category['category_id'];
								 
								 
								 
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
								 
								 
								 
								 
							
							} 
							$result[]=array('status'=>array("code"=>"1","message"=>"success","seller_id"=>$sellerid),'product'=>$result4);
							
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
	 
	 
}