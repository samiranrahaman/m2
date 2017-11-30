<div class="header" style="padding:10px;background:#339eae;">
		<div class="header_left">
			<ul>
				<li><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span>Australia</li>
				<li><span class="glyphicon glyphicon-earphone" aria-hidden="true"></span>Contact Us</li>
			</ul>	
		</div>
		<div class="header_right">
			<!--<div class="w3_agile_login">
				<div class="cd-main-header">
					<a class="cd-search-trigger" href="#cd-search"> <span></span></a>
					
				</div>
				<div id="cd-search" class="cd-search">
				<form action="#" method="post">
						<input name="Search" type="search" placeholder="Search...">
					</form>
				</div>
			</div>-->

			<?php echo $this->getLayout()->createBlock('Magento\Framework\View\Element\Template')->setTemplate('Magento_Search::form.mini.phtml')->toHtml(); ?>
			<?php echo $this->getLayout()->createBlock('Magento\Checkout\Block\Cart\Sidebar\Interceptor')->setTemplate('Magento_Checkout::cart/minicart.phtml')->toHtml(); ?>
			
		</div>
			<div class="clearfix"></div>
		
	</div> 
	
	
	<?php 
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
	     $shop_url = $objectManager->create('Webkul\Marketplace\Helper\Data')->getProfileUrl(); 
	   
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->create('Magento\Customer\Model\Session'); 
            
	   
	   
	   if(!$shop_url)
		{    
?>
	
<div class="header-bottom">
	<nav class="navbar navbar-default">
		<div class="navbar-header navbar-left">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo $this->getBaseUrl(); ?>"><h1>App<span>'n'</span>Sell</h1></a>
		</div>	
		<div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
			<nav class="link-effect-2" id="link-effect-2">
				<ul class="nav navbar-nav">
					<li class="active"><a href="<?php echo $this->getBaseUrl(); ?>"><span data-hover="Home">Home</span></a></li>
					<li><a href="<?php echo $this->getBaseUrl(); ?>"><span data-hover="Builf App">Build App</span></a></li>
					<li><a href="<?php echo $this->getBaseUrl(); ?>Addsubscriptionplans/"><span data-hover="Pricing">Pricing</span></a></li>
					<li><a href="<?php echo $this->getBaseUrl(); ?>"><span data-hover="Training">Training</span></a></li>
					<li><a href="<?php echo $this->getBaseUrl(); ?>contact"><span data-hover="Support">Support</span></a></li>
					<li><a href="<?php echo $this->getBaseUrl(); ?>contact"><span data-hover="about-us">About Us</span></a></li>
					<!--<li><a href="<?php echo $this->getBaseUrl(); ?>customer/account/login/"><h4><b>Sign In</b></h4></a></li>-->
					<?php  if($customerSession->isLoggedIn()) : ?>
    <li>
        <a href="<?php echo $this->getBaseUrl() .'customer/account/'; ?>">
            <?php echo __('My Account') ?>
        </a>
    </li>
    <?php endif; ?>
					<?php echo $this->getLayout()->createBlock('Magento\Customer\Block\Account\AuthorizationLink')->setTemplate('Magento_Customer::account/link/authorization.phtml')->toHtml(); ?>
			

					</ul>
			</nav>
		</div>
		<div class="agileinfo-social-grids">
			<ul>
				<li><a href="#"><i class="fa fa-facebook"></i></a></li>
				<li><a href="#"><i class="fa fa-twitter"></i></a></li>
				<li><a href="#"><i class="fa fa-rss"></i></a></li>   
			</ul>
		</div>
	</nav>
</div>

<?php } else { ?>

<?php 

							$x=explode("/",$_SERVER['REQUEST_URI']);
							//print_r($x);
							//echo $x[1];
							//$x[1]=$shop_url;
							$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
							
							$data=$objectManager->create('Webkul\Marketplace\Model\Seller')
									->getCollection()
									->addFieldToFilter('shop_url',array('eq'=>$x[1]));
									
							if(!empty($data))
							{ 
						 
?>

									
										<div class="header-bottom">
											<nav class="navbar navbar-default">
												<div class="navbar-header navbar-left">
													<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
														<span class="sr-only">Toggle navigation</span>
														<span class="icon-bar"></span>
														<span class="icon-bar"></span>
														<span class="icon-bar"></span>
													</button>
													
													<?php
													 foreach($data as $seller){ 
						
														$logo=$seller->getLogoPic()!=''?$seller->getLogoPic():"noimage.png";
														$MediaUrl=$objectManager->create('Webkul\Marketplace\Helper\Data')->getMediaUrl(); 
														 $logo1=$MediaUrl.'avatar/'.$logo;
														?>
														<a class="navbar-brand" href="<?php echo $block->getUrl(''); ?><?php echo $x[1];?>" title="Magento Commerce">
															<img style="width: 48%;" src="<?php echo $logo1;?>" alt="Magento Commerce">
														</a>
														<?php  } ?>
													
												</div>	
												<div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
													<nav class="link-effect-2" id="link-effect-2">
														<ul class="nav navbar-nav">
															 <li class="level0 home level-top ">
																				  <a href="<?php echo $this->getBaseUrl(); ?>" class="level-top"><span>Home</span></a>
																				</li> 
																				
													
																				<?php
																				$parentCategory = '';
																				$arr_have_child_cat = [];
																				
																				$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
																				$blockdata=$objectManager->create('Webkul\Marketplace\Block\Sellercategory');	
																				 $categorylist=$blockdata->getCategoryListcustom();
																				// print_r($categorylist); 
																					if(!empty($categorylist)):
																				foreach($blockdata->getCategoryListcustom() as $key => $value)
																				{ ?>
																						
																						<li class="level0 home level-top ">
																				  <a href="<?php echo $this->getBaseUrl(); ?><?php echo $x[1];?>/collection/?c=<?php echo $value['category_id'];?>" class="level-top"><span><?php echo $value['catname'];?></span></a>
																				</li> 
																				<?php 
																				} 
																				endif;
																				?>
																				
																				
																				
																				
																				
																				
																				
																				
																				
																				
																				
																				
																				
																			   <li class="level0 reviews level-top ">
																			   <a class="store-name" href="<?php echo $block->getUrl(''); ?><?php echo $x[1];?>/review<?php //echo $objectManager->create('Webkul\Marketplace\Helper\Data')->getRewriteUrl('marketplace/seller/feedback/shop/'.$partner['shop_url'])?>">
																					<?php echo __('Reviews') ?>
																				</a>
																				</li>
																				<!--<li class="level0 contact_us level-top ">
																				<a href="javascript:void(0)" class=" store-nameaskque"><?php echo __('Contact Us') ?></a>
																				</li>-->
																				
																				<?php
																				//if($helper->getSellerPolicyApproval()){
																					
																					?>
																					<!--<li class="level0 return-policy level-top ">
																						<a class="store-name" href="<?php echo $block->getUrl(''); ?><?php echo $x[1].'#return-policy';?> <?php //echo $helper->getRewriteUrl('marketplace/seller/profile/shop/'.$partner['shop_url']).'#return-policy';?>">
																							<?php echo __('Return Policy') ?>
																						</a>
																					</li>
																					<li class="level0 shipping-policy level-top ">
																						<a class="store-name" href="<?php echo $block->getUrl(''); ?><?php echo $x[1].'#shipping-policy';?><?php //echo $helper->getRewriteUrl('marketplace/seller/profile/shop/'.$partner['shop_url']).'#shipping-policy';?>">
																							<?php echo __('Shipping Policy') ?>
																						</a>
																					</li>-->
																				<?php
																				//}
																				
																				?>
																				<?php  if($customerSession->isLoggedIn()) : ?>
																				<li>
																					 <a href="<?php echo $this->getBaseUrl() .'customer/account/'; ?>">
																						<?php echo __('My Account') ?>
																					</a>
																				</li>
																				<?php endif; ?>
																				<?php echo $this->getLayout()->createBlock('Magento\Customer\Block\Account\AuthorizationLink')->setTemplate('Magento_Customer::account/link/authorization.phtml')->toHtml(); ?>
			

															</ul>
													</nav>
												</div>
												<div class="agileinfo-social-grids">
													<ul>
														<li><a href="#"><i class="fa fa-facebook"></i></a></li>
														<li><a href="#"><i class="fa fa-twitter"></i></a></li>
														<li><a href="#"><i class="fa fa-rss"></i></a></li>   
													</ul>
												</div>
											</nav>
										</div>
								
								
								
							<?php } else { ?>
							
							<div class="header-bottom">
								<nav class="navbar navbar-default">
									<div class="navbar-header navbar-left">
										<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
											<span class="sr-only">Toggle navigation</span>
											<span class="icon-bar"></span>
											<span class="icon-bar"></span>
											<span class="icon-bar"></span>
										</button>
										<a class="navbar-brand" href="<?php echo $this->getBaseUrl(); ?>"><h1>App<span>'n'</span>Sell</h1></a>
									</div>	
									<div class="collapse navbar-collapse navbar-right" id="bs-example-navbar-collapse-1">
										<nav class="link-effect-2" id="link-effect-2">
											<ul class="nav navbar-nav">
												<li class="active"><a href="<?php echo $this->getBaseUrl(); ?>"><span data-hover="Home">Home</span></a></li>
												<li><a href="<?php echo $this->getBaseUrl(); ?>"><span data-hover="Builf App">Build App</span></a></li>
												<li><a href="<?php echo $this->getBaseUrl(); ?>Addsubscriptionplans/"><span data-hover="Pricing">Pricing</span></a></li>
												<li><a href="<?php echo $this->getBaseUrl(); ?>"><span data-hover="Training">Training</span></a></li>
												<li><a href="<?php echo $this->getBaseUrl(); ?>contact"><span data-hover="Support">Support</span></a></li>
												<li><a href="<?php echo $this->getBaseUrl(); ?>contact"><span data-hover="about-us">About Us</span></a></li>
												
												<?php  if($customerSession->isLoggedIn()) : ?>
    <li>
         <a href="<?php echo $this->getBaseUrl() .'customer/account/'; ?>">
            <?php echo __('My Account') ?>
        </a>
    </li>
    <?php endif; ?>
												<?php echo $this->getLayout()->createBlock('Magento\Customer\Block\Account\AuthorizationLink')->setTemplate('Magento_Customer::account/link/authorization.phtml')->toHtml(); ?>
			

												</ul>
										</nav>
									</div>
									<div class="agileinfo-social-grids">
										<ul>
											<li><a href="#"><i class="fa fa-facebook"></i></a></li>
											<li><a href="#"><i class="fa fa-twitter"></i></a></li>
											<li><a href="#"><i class="fa fa-rss"></i></a></li>   
										</ul>
									</div>
								</nav>
							</div>
							
							
							
							<?php } ?>



<?php } ?>