<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<h3><?php echo JText::_('JOPENSIM_GROUPDETAILS'); ?></h3>

<?php if($this->settings['addons_groups'] == 1): ?>
<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
<tr>
	<th><?php echo JText::_('JOPENSIM_GROUPNAME'); ?></th>
	<th><?php echo JText::_('JOPENSIM_GROUPCHARTER'); ?></th>

	<?php if($this->grouplist['acceptnotices'] == 1 && $this->grouplist['power']['power_receivenotice'] == 1 && $this->grouplist['hasnotices'] > 0): ?>	
	<th><?php echo JText::_('JOPENSIM_GROUPNOTICE'); ?></th>
	<?php endif; ?>
	
	<?php if($this->grouplist['power']['power_rolemembersvisible'] == 1 || $this->grouplist['power']['isowner'] == 1): ?>
	<th><?php echo JText::_('JOPENSIM_GROUPMEMBERS'); ?></th>
	<?php endif; ?>
	
	<th><?php echo JText::_('JOPENSIM_GROUPLEAVE'); ?></th>
</tr>
<tr>
	<td><?php echo $this->grouplist['groupname']; ?></td>
	<td><?php echo $this->grouplist['charter']; ?></td>

	<?php if($this->grouplist['acceptnotices'] == 1 && $this->grouplist['power']['power_receivenotice'] == 1 && $this->grouplist['hasnotices'] > 0): ?>	
	<td>
	    <!-- <span class="icon-eye btn-large pull-right" alt='<?php // echo JText::_('VIEWNOTICES'); ?>' title='<?php // echo JText::_('VIEWNOTICES'); ?>' /></span> -->
		<a class="btn btn-default btn-primary pull-right" href='index.php?option=com_opensim&view=inworld&task=groupnotices&groupid=<?php echo $this->grouplist['groupid']; ?>&tmpl=component' alt='<?php echo JText::_('VIEWNOTICES'); ?>' title='<?php echo JText::_('VIEWNOTICES'); ?>'>Show</a>
	</td>
	<?php endif; ?>
	
	<?php if($this->grouplist['power']['power_rolemembersvisible'] == 1 || $this->grouplist['power']['isowner'] == 1): ?>
	<td>
	    <!-- <span class="icon-eye btn-large pull-right" alt='<?php // echo JText::_('VIEWMEMBERS'); ?>' title='<?php // echo JText::_('VIEWMEMBERS'); ?>' /></span> -->
	    <a class="btn btn-default btn-primary pull-right" href='index.php?option=com_opensim&view=inworld&task=groupmembers&groupid=<?php echo $this->grouplist['groupid']; ?>&tmpl=component' alt='<?php echo JText::_('VIEWMEMBERS'); ?>' title='<?php echo JText::_('VIEWMEMBERS'); ?>'>Show</a>		
	</td>
	<?php endif; ?>

	<td>
	    <!-- <span class="btn btn-default btn-primary pull-right" alt='<?php // echo JText::_('LEAVEGROUP'); ?>' title='<?php // echo JText::_('LEAVEGROUP'); ?>'></span> -->
	    <a class="btn btn-default btn-danger pull-right" href='index.php?option=com_opensim&view=inworld&task=leavegroup&groupid=<?php echo $this->grouplist['groupid']; ?>' target='_parent' onClick='return confirm("<?php echo addslashes(JText::_('LEAVEGROUPSURE')); ?>");' alt='<?php echo JText::_('LEAVEGROUP'); ?>' title='<?php echo JText::_('LEAVEGROUP'); ?>'><strong>X</strong></a>

	</td>
</tr>
<?php endif; ?>