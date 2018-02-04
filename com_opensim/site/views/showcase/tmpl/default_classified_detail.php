<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php if(is_array($this->classified)): ?>
<h1><?php echo $this->classified['name']; ?></h1>
<p><?php echo JText::sprintf('JOPENSIM_SHOWCASE_CLASSIFIED_PRESENTEDBY',$this->classified['creator']); ?></p>
<?php if($this->classifiedimages): ?>
<?php echo $this->classified['image']; ?>
<?php endif; ?>
<?php if($this->classified['description']): ?>
<p><?php echo $this->classified['description']; ?></p>
<?php endif; ?>
<?php if(($this->link_local || $this->link_hg || $this->link_hgv3 || $this->link_hop) && ($this->classified['linklocal'] || $this->classified['linkhg'] || $this->classified['linkhgv3'] || $this->classified['linkhop'])): ?>
<div class="text-center btn btn-default jopensim_showcase">
<?php if($this->link_local && $this->classified['linklocal']): ?>
<div class="tplocal">
	<a class="tpbtn btn-local" href="<?php echo $this->classified['linklocal']; ?>">
	<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_LOCAL'); ?>
	</a>
</div>
<?php endif; ?>
<?php if($this->link_hg && $this->classified['linkhg']): ?>
<div class="tphg">
	<a class="tpbtn btn-hg" href="<?php echo $this->classified['linkhg']; ?>">
	<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_HG'); ?>
	</a>
</div>
<?php endif; ?>
<?php if($this->link_hgv3 && $this->classified['linkhgv3']): ?>
<div class="tphgv3">
	<a class="tpbtn btn-hgv3" href="<?php echo $this->classified['linkhgv3']; ?>">
	<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_HGV3'); ?>
	</a>
</div>
<?php endif; ?>
<?php if($this->link_hop && $this->classified['linkhop']): ?>
<div class="tphop">
	<a class="tpbtn btn-tphop" href="<?php echo $this->classified['linkhop']; ?>">
	<?php echo JText::_('JOPENSIM_SHOWCASE_LINK_HOP'); ?>
	</a>
</div>
<?php endif; ?>
</div>
<p />
<?php endif; ?>
<?php endif; ?>
