<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php if($this->tmpl != "component"): ?>
<h1><?php echo JText::_('JOPENSIM_REGION_DESTINATIONGUIDE'); ?></h1>
<?php endif; ?>
<?php if(is_array($this->regions) && count($this->regions) > 0): ?>
<?php foreach($this->regions AS $region): ?>
<div class="text-center btn btn-default">
	<a href="secondlife://<?php echo $region['regionName']; ?>">
	<div class='jopensim_showcase_image'><?php echo $region['mapimage']; ?></div><?php echo $region['regionName']; ?>
	</a>
</div>
<?php endforeach; ?>
<?php if($this->showclassified): ?>
<div class="clearfix"></div>
<?php if(is_array($this->classifieds) && count($this->classifieds) > 0): ?>
<?php foreach($this->classifieds AS $classified): ?>
<div class="text-center btn btn-default">
	<a href="<?php echo $classified['linklocal']; ?>">
	<div class='jopensim_showcase_image' style='min-height:150px;'><?php echo $classified['image']; ?></div><?php echo $classified['name']; ?>
	</a>
</div>
<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>
