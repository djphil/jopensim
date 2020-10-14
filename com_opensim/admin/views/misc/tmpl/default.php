<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
?>

<div class="jopensim-adminpanel">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<?php if($this->canDo->get('core.remoteadmin')): ?>
	<p class="text-info">
		<?php echo JText::_('JOPENSIM_MISC_DESC'); ?>
		<a class="hasTooltip" href='http://wiki.jopensim.com/index.php/Com_opensim#Remote_Admin' title="<?php echo JText::_('JOPENSIM_MOREINFO'); ?> ..." target='_blank'><?php echo JText::_('JOPENSIM_MOREINFO'); ?> ...</a>
	</p>
	<?php endif; ?>
    <form action="index.php" method="post" id="adminForm" name="adminForm">
	<input type="hidden" name="option" value="com_opensim" />
	<input type="hidden" name="view" value="misc" />
	<input type="hidden" name="task" value="" />
	</form>
	<?php if(is_array($this->misclinks)): ?>
	<div class='jopensim_misc_table'>
		<div class="btn-inline" id="jOpenSimQuickIcons">
<!--		<ul class="btn-group-vertical"> -->
			    <?php foreach($this->misclinks AS $misclink): ?>
<!--		    <li class="hasTooltip"><span class="icon-plus"></span><?php echo $misclink; ?></li> -->
			    <?php echo $misclink; ?>
			    <?php endforeach; ?>
<!--		</ul> -->
			<?php // echo $this->misclinks['sendmessage']; ?>
		</div>
	</div>
	<?php endif; ?>
</div>
</div>
