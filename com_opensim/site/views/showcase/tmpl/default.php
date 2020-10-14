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
<<?php echo $this->titleformat; ?>><?php echo $this->pagetitle; ?></<?php echo $this->titleformat; ?>>
<?php endif; ?>
<?php if($this->showregions): ?>
<?php if($this->regionintro): ?>
<p><?php echo $this->regionintro; ?></p>
<?php endif; ?>
<?php if(is_array($this->regions) && count($this->regions) > 0): ?>
<?php foreach($this->regions AS $region): ?>
<div class="text-center btn btn-default jopensim_showcase">
	<?php if($region['regionlink']): ?>
	<a href="<?php echo $region['regionlink']; ?>">
	<?php endif; ?>
	<?php echo $region['mapimage']; ?><br /><?php echo $region['regionName']; ?><br />
	<?php if($region['regionlink']): ?>
	</a>
	<?php endif; ?>
	<?php if($this->link_local && $this->mainlink != "local"): ?>
	<div class="tplocal">
		<a class="tpbtn btn-local" href="<?php echo $region['linklocal']; ?>">
		<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_LOCAL'); ?>
		</a>
	</div>
	<?php endif; ?>
	<?php if($this->link_hg && $this->mainlink != "hg"): ?>
	<div class="tphg">
		<a class="tpbtn btn-hg" href="<?php echo $region['linkhg']; ?>">
		<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_HG'); ?>
		</a>
	</div>
	<?php endif; ?>
	<?php if($this->link_hgv3 && $this->mainlink != "hgv3"): ?>
	<div class="tphgv3">
		<a class="tpbtn btn-hgv3" href="<?php echo $region['linkhgv3']; ?>">
		<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_HGV3'); ?>
		</a>
	</div>
	<?php endif; ?>
	<?php if($this->link_hop && $this->mainlink != "hop"): ?>
	<div class="tphop">
		<a class="tpbtn btn-tphop" href="<?php echo $region['linkhop']; ?>">
		<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_HOP'); ?>
		</a>
	</div>
	<?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>
<?php if($this->showregions): ?>
<div class="clearfix"></div>
<?php if(is_array($this->classifieds) && count($this->classifieds) > 0): ?>
<?php if($this->classifiedintro): ?>
<p><?php echo $this->classifiedintro; ?></p>
<?php endif; ?>
<div class="clearfix"></div>
<?php foreach($this->classifieds AS $classified): ?>
<?php $buttonscount = 0; ?>
<div class="text-center btn btn-default jopensim_showcase">
	<?php if($classified['mainlink']): ?>
	<a href="<?php echo $classified['mainlink']; ?>">
	<?php endif; ?>
	<div class='jopensim_showcase_image' style='min-height:<?php echo $this->imagesize; ?>px;'><?php echo $classified['image']; ?></div><?php echo $classified['name']; ?><br />
	<?php if($classified['mainlink']): ?>
	</a>
	<?php endif; ?>
	<?php if($this->link_local && $this->mainlink != "local" && $classified['linklocal']): ?>
	<?php $buttonscount++; ?>
	<div class="tplocal">
		<a class="tpbtn btn-local" href="<?php echo $classified['linklocal']; ?>">
		<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_LOCAL'); ?>
		</a>
	</div>
	<?php endif; ?>
	<?php if($this->link_hg && $this->mainlink != "hg" && $classified['linkhg']): ?>
	<?php $buttonscount++; ?>
	<div class="tphg">
		<a class="tpbtn btn-hg" href="<?php echo $classified['linkhg']; ?>">
		<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_HG'); ?>
		</a>
	</div>
	<?php endif; ?>
	<?php if($this->link_hgv3 && $this->mainlink != "hgv3" && $classified['linkhgv3']): ?>
	<?php $buttonscount++; ?>
	<div class="tphgv3">
		<a class="tpbtn btn-hgv3" href="<?php echo $classified['linkhgv3']; ?>">
		<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_HGV3'); ?>
		</a>
	</div>
	<?php endif; ?>
	<?php if($this->link_hop && $this->mainlink != "hop" && $classified['linkhop']): ?>
	<?php $buttonscount++; ?>
	<div class="tphop">
		<a class="tpbtn btn-tphop" href="<?php echo $classified['linkhop']; ?>">
		<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_HOP'); ?>
		</a>
	</div>
	<?php endif; ?>
	<?php if($buttonscount == 0): ?>
	<div class="tpdummy"><div class="tpbtn btn-tpdummy">
	<?php if($this->link_local || $this->link_hg || $this->link_hgv3 || $this->link_hop): ?>
	<?php echo JText::_('JOPENSIM_SHOWCASE_LOCATION_UNAVAILABLE'); ?>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</div></div>
	<?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>



<?php endif; ?>
