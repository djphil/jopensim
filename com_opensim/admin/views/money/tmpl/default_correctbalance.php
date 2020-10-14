<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" onSubmit='return confirm("<?php echo JText::sprintf('JOPENSIM_MONEY_BALANCECORRECTION',$this->moneysettings['name'],($this->balanceAll*-1)); ?>");' target='_parent'>
<input type="hidden" name="option" value="com_opensim" />
<input type="hidden" name="view" value="money" />
<input type="hidden" name="task" value="balancecorrection" />
<input type="hidden" name="correctionvalue" value="<?php echo $this->balanceAll; ?>" />

<table width='100%'>
<tr>
	<td><label for=''><?php echo JText::_('JOPENSIM_MONEY_BANKERACCOUNT'); ?>:</label></td>
	<td>
	<select name='bankerUID'>
<?php
if(is_array($this->bankerlist) && count($this->bankerlist) > 0) {
	foreach($this->bankerlist AS $banker) {
?>
		<option value='<?php echo $banker['userid']; ?>'<?php echo ($banker['userid'] == $this->moneysettings['bankerUID']) ? " selected='selected'":""; ?>><?php echo $banker['firstname']." ".$banker['lastname']; ?></option>
<?php
	}
} else {
?>
		<option value=''><?php echo JText::_('JOPENSIM_MONEY_ERROR_NOBANKER'); ?></option>
<?php
	if($this->moneysettings['bankerUID']) {
?>
		<option value='<?php echo $this->moneysettings['bankerUID']; ?>' selected='selected'><?php echo JText::_('JOPENSIM_MONEY_OLDBANKER'); ?></option>
<?php
	}
}
?>
	</select>
	</td>
</tr>
<tr>
	<td><label><?php echo JText::_('JOPENSIM_MONEY_BALANCEALL'); ?>:</label></td>
	<td><?php echo $this->moneysettings['name'].number_format($this->balanceAll,0,JTEXT::_('JOPENSIM_MONEY_SEPERATOR_COMMA'),JTEXT::_('JOPENSIM_MONEY_SEPERATOR_THOUSAND')); ?></td>
</tr>
<tr>
	<td><label><?php echo JText::_('JOPENSIM_MONEY_BALANCEUSER'); ?>:</label></td>
	<td><?php echo $this->moneysettings['name'].number_format($this->balanceUser,0,JTEXT::_('JOPENSIM_MONEY_SEPERATOR_COMMA'),JTEXT::_('JOPENSIM_MONEY_SEPERATOR_THOUSAND')); ?></td>
</tr>
<tr>
	<td colspan='2'>
	<input type='submit' name='submit' value='<?php echo JText::_('JOPENSIM_MONEY_BALANCECORRECTION_BUTTON'); ?>' />
	</td>
</tr>
</table>
</form>
