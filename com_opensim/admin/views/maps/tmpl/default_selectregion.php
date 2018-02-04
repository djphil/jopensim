<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">

<form class="form-group" action="index.php" method="post" id="adminForm" name="adminForm2">
<fieldset>
    <legend>
	    <?php echo JText::_('REGION_CLICK_LOCATION'); ?>:	
	</legend>

    <input type='hidden' name='option' value='com_opensim' />
    <input type='hidden' name='view' value='maps' />
    <input type='hidden' name='region' value='<?php echo $this->region; ?>' />
    <input type='hidden' name='task' value='savedefault' />
    <input type='hidden' name='z' value='<?php echo (isset($this->locZ)) ? $this->locZ:"0"; ?>' />
	<input class="img-thumbnail regionLocationSelector" type='image' width='512' height='512' src='<?php echo $this->assetpath; ?>regionimage.php?uuid=<?php echo $this->regiondata['uuid']; ?>&mapserver=<?php echo $this->regiondata['serverIP']; ?>&mapport=<?php echo $this->regiondata['serverHttpPort'].$this->imgAddLink; ?>&scale=512' border='1' alt='<?php echo JText::_('REGION_CLICK_LOCATION'); ?>' title='<?php echo JText::_('REGION_CLICK_LOCATION'); ?>' class='regionLocationSelector' />
</fieldset>
</form>

<p>
	<?php echo JText::_('JOPENSIM_REGION_NAME').": <span class='text-info'>".$this->regiondata['regionName']; ?></span>
	<span class="label label-info"><?php echo $this->ueberschrift; ?></span>
</p>

<?php if($this->imgAddLink): ?>
    <img src='<?php echo $this->assetpath; ?>images/entrancecircle.png' width='26' height='26' align='absmiddle' alt='<?php echo JText::_('CURRENTENTRANCE'); ?>' title='<?php echo JText::_('CURRENTENTRANCE'); ?>' /> = <?php echo JText::_('CURRENTENTRANCE'); ?>
<?php endif; ?>

<h4><?php echo JText::_('MAP_OR'); ?></h4>

<form class="form-inline" action="index.php" method="post" name="adminForm">
<fieldset>
    <legend><?php echo JText::_('MANUAL_LOCATION'); ?>:</legend>
    <input class="form-control" type='hidden' name='option' value='com_opensim' />
    <input class="form-control" type='hidden' name='region' value='<?php echo $this->region; ?>' />
    <input class="form-control" type='hidden' name='view' value='maps' />
    <input class="form-control" type='hidden' name='task' value='savemanual' />

	<div class="form-group">
        <label for="x" class="btn btn-default btn-danger btn-mini"><strong>X</strong></label>
        <input class="form-control" id="x" type='text' size='1' name='loc_x' value='<?php echo (isset($this->locX)) ? $this->locX:""; ?>' /></p>
    </div>
	<div class="form-group">
        <label for="y" class="btn btn-default btn-success btn-mini"><strong>Y</strong></label>
        <input class="form-control" id="y" type='text' size='1' name='loc_y' value='<?php echo (isset($this->locY)) ? $this->locY:""; ?>' /></p>
    </div>
	<div class="form-group">
        <label for="z" class="btn btn-default btn-primary btn-mini"><strong>Z</strong></label>
        <input class="form-control" id="z" type='text' size='1' name='loc_z' value='<?php echo (isset($this->locZ)) ? $this->locZ:""; ?>' /></p>
    </div>

	<button type='submit' class='btn btn-default btn-success' />
        <span class='icon-checkmark'></span> <?php echo JText::_('JSAVE'); ?>
	</button>
</fieldset>
</form>
</div>
