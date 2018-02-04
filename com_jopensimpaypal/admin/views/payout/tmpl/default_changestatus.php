<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');

JFormHelper::loadFieldClass('list');
//Get daterange options
JFormHelper::addFieldPath(JPATH_COMPONENT . '/fields');
$payoutstatusselector	= JFormHelper::loadFieldType('payoutstatusselector', $this->item->status);
$payoutstatusOptions	= $payoutstatusselector->getOptions();
?>
<h1><?php echo JText::sprintf('COM_JOPENSIMPAYPAL_PAYOUT_CHANGESTATUS',$this->item->opensimname); ?></h1>
<form action="<?php echo JRoute::_('index.php?option=com_jopensimpaypal'); ?>" method="post" name="adminForm" target='_parent'>
<input type='hidden' name='view' value='payout' />
<input type='hidden' name='task' value='changepayout' />
<input type='hidden' name='payoutid' value='<?php echo $this->item->id; ?>' />
<table>
<tr>
	<td><?php echo JText::_('COM_JOPENSIMPAYPAL_PAYOUT_CURRENT'); ?>:</td>
	<td><?php echo $this->item->currentstatus; ?></td>
</tr>
<tr>
	<td><?php echo JText::_('COM_JOPENSIMPAYPAL_PAYOUT_NEW'); ?>:</td>
	<td>
	<select name='newstatus'>
		<?php echo JHtml::_('select.options', $payoutstatusOptions, 'value', 'text', $this->item->status);?>
	</select>
	</td>
</tr>
<tr>
	<td valign='top'><span title='<?php echo JText::_('COM_JOPENSIMPAYPAL_PAYOUT_REMARKS_DESC'); ?>'><?php echo JText::_('COM_JOPENSIMPAYPAL_PAYOUT_REMARKS'); ?>:</span></td>
	<td><textarea name='remarks' cols='30' rows='7' title='<?php echo JText::_('COM_JOPENSIMPAYPAL_PAYOUT_REMARKS_DESC'); ?>'><?php echo $this->item->remarks; ?></textarea></td>
</tr>
<tr>
	<td colspan='2' align='right'>
	<input type='submit' value='<?php echo JText::_('COM_JOPENSIMPAYPAL_PAYOUT_SAVE'); ?>' />&nbsp;<input type='reset' value='<?php echo JText::_('COM_JOPENSIMPAYPAL_PAYOUT_RESET'); ?>' />
	</td>
</tr>
</table>
</form>