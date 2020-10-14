<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<h3><?php echo JText::sprintf('JOPENSIM_MONEY_USER_CUSTOMFEES2',$this->userdata['firstname']." ".$this->userdata['lastname']); ?>:</h3>
<form action="index.php" method="post" id="adminForm" name="adminForm" target='_parent'>
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='<?php echo $this->returnto; ?>' />
<input type='hidden' name='task' value='setCustomfee' />
<input type='hidden' name='jopensim_money_userid' value='<?php echo $this->userdata['uuid']; ?>' />
<input type='hidden' name='uuid' value='<?php echo $this->userdata['uuid']; ?>' />
<input type='hidden' name='returnto' value='<?php echo $this->returnto; ?>' />
<table>
<tr>
	<td><label for='jopensim_money_customfee_upload'><?php echo JText::_('JOPENSIM_MONEY_USER_CUSTOMFEES_UPLOAD'); ?>:</label></td>
	<td><input type='text' id='jopensim_money_customfee_upload' name='jopensim_money_customfee_upload' size='5' class='number_field' value='<?php echo $this->customfees['uploadfee']; ?>' /></td>
</tr>
<tr>
	<td><label for='jopensim_money_customfee_groupcreation'><?php echo JText::_('JOPENSIM_MONEY_USER_CUSTOMFEES_GROUPCREATION'); ?>:</label></td>
	<td><input type='text' id='jopensim_money_customfee_groupcreation' name='jopensim_money_customfee_groupcreation' size='5' class='number_field' value='<?php echo $this->customfees['groupfee']; ?>' /></td>
</tr>
<tr>
	<td colspan='2'>
	<input type='submit' value='<?php echo $this->buttonvalue; ?>' /><br /><br />
	<?php if($this->feeaction == "update"): ?>
	<a href='index.php?option=com_opensim&view=money&task=removeCustomFee&uuid=<?php echo $this->userdata['uuid']; ?>' target='_parent'><?php echo JText::_('JOPENSIM_MONEY_USER_CUSTOMFEES_DELETE'); ?></a>
	<?php endif; ?>
	</td>
</tr>
</table>
</form>
