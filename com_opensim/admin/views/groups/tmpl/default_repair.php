<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<h1><?php echo JText::_('JOPENSIM_GROUPREPAIR'); ?>:</h1>
<form action="index.php" method="post" id="adminForm" name="adminForm" target='_parent'>
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='groups' />
<input type='hidden' name='task' value='assignOwner' />
<input type='hidden' name='groupID' value='<?php echo $this->groupDetails['GroupID']; ?>' />
<input type='hidden' name='OwnerRoleID' value='<?php echo $this->groupDetails['OwnerRoleID']; ?>' />
<select name='memberID' id='memberID'>
	<option value='0'><?php echo JText::_('JOPENSIM_SELECT_NEW_GROUPOWNER'); ?></option>
<?php if(!is_array($this->groupMembers) || count($this->groupMembers) == 0): ?>
	<option value='0'><?php echo JText::_('JOPENSIM_GROUPNOMEMBERS'); ?></option>
<?php else: ?>
<?php foreach($this->groupMembers AS $member): ?>
	<option value='<?php echo $member['AgentID']; ?>'><?php echo $member['MemberName']; ?></option>
<?php endforeach; ?>
<?php endif; ?>
</select>
<input type='submit' value='<?php echo JText::_('JOPENSIM_GROUPNEWOWNER'); ?>' />
</form>
<?php if(!is_array($this->groupMembers) || count($this->groupMembers) == 0): ?>
<a href='index.php?option=com_opensim&view=groups&task=deleteGroups&checkGroup[0]=<?php echo $this->groupDetails['GroupID']; ?>' target='_parent' onClick='return confirm("<?php echo addslashes(JText::_('JOPENSIM_DELETEGROUPSURE')); ?>");'><?php echo JText::_('JOPENSIM_DELETEGROUP'); ?></a>
<?php endif; ?>
