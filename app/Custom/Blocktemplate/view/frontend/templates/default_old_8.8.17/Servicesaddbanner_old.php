<?php
/**
 * Product category view template
 *
 * @var $block Custom\Blocktemplate\Block\Servicesaddbanner
 */
?>
<?php
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		if(isset($_SESSION['addbannerid2']))
		{
			 $previd2=$_SESSION['addbannerid2'];
			echo $sql = "Select * FROM Adbanner where status=1 and type='Services' and adbannerid!=$previd2  order by rand() limit 1";
		
		}
		else
		{
			echo $sql = "Select * FROM Adbanner where status=1 and type='Services'  order by rand()limit 1";
	
		}
		$result1 = $connection->fetchAll($sql);
		//print_r($result1);
		if(count($result1>0))
		{
			$_SESSION['addbannerid2'] = $result1[0]['adbannerid'];
		}
		else
			
		{
			$_SESSION['addbannerid2'] = 0;
		}
?>
<!--<h1><?php echo $block->getTitle(); ?></h1>-->
 
     <div class="row">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
 <div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="block block-static">
					<div class="static-content">
					<?php if($result1[0]['showname']=='yes') :?><div class="static-title"><?php echo $result1[0]['name'];?></div><?php endif ;?>
					<?php if($result1[0]['imagecaption']!='') :?><div class="static-desc"><?php echo $result1[0]['imagecaption'];?></div><?php endif ;?>
					<?php if($result1[0]['url']!='') :?><div class="static-link"><a target="_blank" href="<?php echo $result1[0]['url']?>"><span>view more</span></a></div><?php endif ;?>
					</div>
					<div class="img-content">
					<?php if($result1[0]['url']!='') :?>
					<a href="<?php echo $result1[0]['url']?>">
					<img src="http://159.203.151.92/pub/media/Gridpartbanner/background/image/<?php echo $result1[0]['background'];?>" />
					</a>
					<?php
					else:
					?>
					<img src="http://159.203.151.92/pub/media/Gridpartbanner/background/image/<?php echo $result1[0]['background'];?>" />
					
					<?php
					endif;
					?>
					</div>
					</div>
					</div>
</div>
   
 </div>
 </div>
    <!-- #endregion Jssor Slider End -->