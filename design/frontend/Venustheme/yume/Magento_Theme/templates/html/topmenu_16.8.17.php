<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

?>
<?php
/**
 * Top menu for store
 *
 * @see \Magento\Theme\Block\Html\Topmenu
 */
?>
<?php $columnsLimit = $block->getColumnsLimit() ?: 0; ?>
<?php $_menu = $block->getHtml('level-top', 'submenu', $columnsLimit) ?>

<nav class="navigation" role="navigation">
    <ul data-mage-init='{"menu":{"responsive":true, "expanded":true, "position":{"my":"left top","at":"left bottom"}}}'>
       
	   <li class="level0 home level-top ">
		  <a href="<?php echo $this->getBaseUrl(); ?>" class="level-top"><span>Home</span></a>
		</li> 
		<?php 
		 $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql_config = "Select * FROM managemarketplace ";
		$result_config = $connection->fetchAll($sql_config);
		if($result_config[0]['categorymenu']==1):
		?>
	   <?php /* @escapeNotVerified */ echo $_menu; ?>
	   <?php endif;?>
	   <li class="level0 HOWITWORKS level-top ">
		  <a href="<?php echo $this->getBaseUrl(); ?>how-it-works" class="level-top"><span>How It Works?</span></a>
		</li> 
		<li class="level0 HOWITWORKS level-top ">
		  <a href="<?php echo $this->getBaseUrl(); ?>contact" class="level-top"><span>Contact</span></a>
		</li> 
	   
    </ul>
</nav>
