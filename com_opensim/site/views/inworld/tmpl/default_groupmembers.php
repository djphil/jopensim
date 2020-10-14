<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php if($this->settings['addons_groups'] == 1): ?>
<h3><a href='index.php?option=com_opensim&view=inworld&task=groupdetail&groupid=<?php echo $this->grouplist['groupid']; ?>&tmpl=component'><?php echo $this->grouplist['groupname']; ?></a> <?php echo JText::_('GROUPMEMBERS'); ?></h3>

<?php if($this->grouplist['power']['power_rolemembersvisible'] == 1 || $this->grouplist['power']['isowner'] == 1): ?>
<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
<tr class='<?php echo $this->pageclass_sfx; ?>'>
	<th class='<?php echo $this->pageclass_sfx; ?>'><?php echo JText::_('MEMBERNAME'); ?></th>
	<th class='<?php echo $this->pageclass_sfx; ?>'><?php echo JText::_('MEMBERROLES'); ?></th>
	<th class='<?php echo $this->pageclass_sfx; ?>'>&nbsp;</th>
</tr>
<?php if (is_array($this->memberlist)): ?>
<?php foreach($this->memberlist AS $member): ?>
<tr class='<?php echo $this->pageclass_sfx; ?>'>
	<td class='<?php echo $this->pageclass_sfx; ?>'><?php echo $member['name']; ?></td>
	<td class='<?php echo $this->pageclass_sfx; ?>'><?php echo $member['roles']; ?></td>
	<td class='<?php echo $this->pageclass_sfx; ?>'>
	    <?php if($this->power['power_eject'] && !$member['isowner']): ?>
		<!-- <span class="icon-exit btn-large pull-right" alt='<?php // echo JText::_('EJECTFROMGROUP'); ?>' title='<?php // echo JText::_('EJECTFROMGROUP'); ?>'></span> -->
        <a class="btn btn-danger pull-right" href='index.php?option=com_opensim&view=inworld&task=ejectgroup&groupid=<?php echo $this->grouplist['groupid']; ?>&ejectid=<?php echo $member['AgentID']; ?>' target='_parent' onClick='return confirm("<?php echo addslashes(JText::_('EJECTGROUPSURE')); ?>");' alt='<?php echo JText::_('EJECTFROMGROUP'); ?>' title='<?php echo JText::_('EJECTFROMGROUP'); ?>'><strong>X</strong></a>
		<?php else: ?>
		    <span class="pull-right" alt='' title=''></span>
		<?php endif; ?>
		</td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr class='<?php echo $this->pageclass_sfx; ?>'>
	<td class='<?php echo $this->pageclass_sfx; ?>' colspan='3'><?php echo JText::_('MEMBERLISTEMPTY'); ?></td>
</tr>
<?php endif; ?>
</table>
<?php endif; ?>
<?php endif; ?>