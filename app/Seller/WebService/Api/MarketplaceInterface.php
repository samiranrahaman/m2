<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Seller\WebService\Api;

use Seller\WebService\Api\Data\PointInterface;

interface MarketplaceInterface
{
    /**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid
     * @param int $num2
     * @return
     */
    public function getsellerprofile($sellerid);
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid
     * @return
     */
    public function getproductlistofseller($sellerid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid
     * @return
     */
    public function getservicelistofseller($sellerid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
	 * @param int $sellerid
     * @return
     */	
    public function getofferbanner($sellerid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
	 * @param int $sellerid
     * @return
     */	
    public function getserviceofferbanner($sellerid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid
     * @return
     */
    public function gettopproductselling($sellerid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid
     * @return
     */
    public function gettopserviceselling($sellerid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @return
     */
    public function getbrandlist();	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid
     * @return
     */
    public function getproductcategories($sellerid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid
     * @return
     */
    public function getproductmaincategories($sellerid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid
     * @param int $userid
     * @return
     */
    public function getsellerhome($sellerid,$userid);
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $categoryid
	 * @param int $sellerid
	 * @param int $userid
     * @return
     */
    public function getcategoryproductlist($categoryid,$sellerid,$userid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $productid
	 * @param int $userid
     * @return
     */
    public function getproductdetails($productid,$userid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $username 
	 * @param string $password 
     * @return
     */
    public function userlogin($username,$password);
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $username 
	 * @param string $password 
	 * @param string $name  
     * @return
     */
    public function registration($username,$password,$name);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $productid 
	 * @param string $quantity 
	 * @param string $userid 
     * @return
     */
    public function addtocart($productid,$quantity,$userid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $productid 
	 * @param string $quantity 
	 * @param string $userid 
     * @return
     */
    public function updatecartproduct($productid,$quantity,$userid);
	
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $productid 
	 * @param string $userid 
     * @return
     */
    public function deletecartproduct($productid,$userid);
	
	/**
     * Return the sum of the two numbers.
     * @param string $userid 
     * @return
     */
    public function getcartproduct($userid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param string $productid 
	 * @param string $quantity 
	 * @param string $userid 
	 * @param string $dat 
	 * @param string $day 
	 * @param string $slot 
	 * @param string $slotno 
     * @return
     */
    public function bookingaddtocart($productid,$quantity,$userid,$dat,$day,$slot,$slotno);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
	 * @param string $sellerid
     * @param string $name 
	 * @param int $userid 
	 * @return
     */
	public function productsearch($sellerid,$name,$userid);
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $productid
	 * @param int $sellerid
	 * @param int $userid
     * @return
     */
    public function simillarproducts($sellerid,$productid,$userid); 
	
	/**
     * Return the sum of the two numbers.
     *
     * @api
     * @param int $sellerid
	 * @param string $name 
     * @return
     */
    public function autocompletesearch($sellerid,$name); 
	
	/**
     * Return the Home Page Banner Images of the Website.
     *
     * @api
     * @return
     */
    public function homepagebanner();
	
	/**
     * Return the Home Page Banner Images of the Website.
     *
     * @api
     * @return
     */
    public function appmarecentproductapp();
	/**
     * Return the Home Page Banner Images of the Website.
     *
     * @api
     * @return
     */
    public function appmatoptproductapp();
	
	
	/**
     * Return the checkout data.
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
     * @return
     */
    public function customcheckout($userid,$email,$name,$street,$city,$country_id,$postcode,$telephone,$shipping,$payment,$quoteId);
	/**
     * Return the checkout data.
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
     * @return
     */
    public function bookingcustomcheckout($userid,$email,$name,$street,$city,$country_id,$postcode,$telephone,$shipping,$payment,$quoteId);
	
	
	/**
     * Return the checkout data.
     *
     * @param int $userid
	 * @param int $productid 
     * @return
     */
    public function buynow($userid,$productid);
	
	/**
     * Return the checkout data.
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
     * @return
     */
    public function customcheckoutdirect($userid,$productid,$price,$email,$name,$street,$city,$country_id,$postcode,$telephone,$shipping,$payment,$quoteid);
	
	/**
     * Return the checkout data.
     * @param int $quoteid
     * @return
     */
    public function customcheckoutdirectconfirm($quoteid);
	
	/**
     * Return the checkout data.
     *
     * @param int $userid
	 * @param int $quoteId
     * @return
     */
    public function customcheckoutcarttotal($userid,$quoteId);
	/**
     * Return the checkout data.
     *
     * @param int $userid
	 * @param int $quoteId
	 * @param string $coupon
     * @return
     */
    public function applycoupon($userid,$quoteId,$coupon);
	/**
     * Return the checkout data.
     *
     * @param int $userid
	 * @param int $quoteId
     * @return
     */
    public function removecoupon($userid,$quoteId);
	/**
     * Return the checkout data.
     *
     * @param int $userid
	 * @param int $quoteId
	 * @param string $shipping
     * @return
     */
    public function applydeleverytype($userid,$quoteId,$shipping);
	/**
     * Return the checkout data.
     * @param int $userid
     * @return
     */
    public function androidappbuild($userid);
	/**
     * Return the checkout data.
     *
     * @param int $userid
	 * @return
     */
    public function orderhistory($userid);
	/**
     * Return the checkout data.
     * @param int $userid
	 * @param int $productid
	 * @return
     */
    public function addwishlist($userid,$productid);
	
	/**
     * Return the checkout data.
     * @param int $userid
	 * @return
     */
    public function getwishlist($userid);
	/**
     * Return the checkout data.
     * @param int $userid
	 * @param int $productid
	 * @return
     */
    public function itemremovewishlist($userid,$productid);
	/**
     * Return the checkout data.
     * @param int $userid
	 * @param int $productid
	 * @return
     */
    public function wishlisttag($userid,$productid);
	/**
     * Return the checkout data.
     * @param int $userid
	 * @return
     */
    public function getaccountinfo($userid);
	/**
     * Return the checkout data.
     * @param int $userid
	 * @return
     */
    public function getaddress($userid);
	/**
     * Return the checkout data.
     * @param int $userid
	 * @param string $fname
	 * @param string $lname
	 * @param string $street
	 * @param string $city
	 * @param string $country_id
	 * @param string $postcode
	 * @param string $telephone
	 * @return
     */
    public function editbillingaddress($userid,$fname,$lname,$street,$city,$country_id,$postcode,$telephone);
	/**
     * Return the checkout data.
     * @param int $userid
	 * @param string $fname
	 * @param string $lname
	 * @param string $dob
	* @return
     */
    public function editaccountinfo($userid,$fname,$lname,$dob);
	/**
     * Return the checkout data.
     * @param int $userid
	 * @param string $oldpassword
	 * @param string $newpassword
	* @return
     */
    public function changepassword($userid,$oldpassword,$newpassword);
	/**
      * @param string $email
	  * @return
     */
    public function forgotpassword($email);
	/**
      * @return
     */
    public function countylist();
	/**
      * @return
	  * @param int $userid
     */
    public function deacivateaccount($userid); 
	/**
      * @return
	  * @param string $email
     */
    public function acivateaccount($email); 
		/**
      * @return
	  * @param string $username
	  * @param string $password
     */
    public function adminlogin($username,$password); 
		/**
      * @return
	   * @param int $product_id
	  */
    public function bookingcalender($product_id); 
	
		/**
      * @return
	   * @param string $day
	    * @param string $month
		* @param string $list_day
		* @param string $year
	   * @param int $product_id
	  */
    public function availableappointment($product_id,$day,$month,$list_day,$year); 
	
		/**
      * @return
	  * @param int $customer_id 
	 * @param int $product_id 
	 * @param string $date 
	 * @param string $slot 
	 * @param string $stime 
	 * @param string $endtime 
	  */
    public function bookingform($customer_id,$product_id,$dat,$slot,$stime,$endtime);
		/**
      * @return
	  * @param int $orderid 
	  */
    public function orderitemlist($orderid);
		/**
      * @return
	  * @param int $orderid 
	  * @param int $userid 
	  */
    public function cancelorderbuyer($orderid,$userid);
	
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
    public function postreview($product_id,$userid,$nicname,$summary,$review,$quality,$value,$price);
		/**
      * @return
	  * @param int $product_id 
	  */
    public function getreview($product_id);
}