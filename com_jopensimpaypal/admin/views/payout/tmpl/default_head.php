<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th width="5">
	&nbsp;
	</th>
	<th width="20">
	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->items); ?>);" />
	</th>                   
	<th>
	<?php echo JText::_('COM_JOPENSIMPAYPAL_HEADING_PAYOUT_OPENSIMUSER'); ?>
	</th>
	<th>
	<?php echo JText::_('COM_JOPENSIMPAYPAL_HEADING_PAYOUT_JOOMLAUSER'); ?>
	</th>
	<th>
	<?php echo JText::_('COM_JOPENSIMPAYPAL_HEADING_PAYOUT_PAYPALACCOUNT'); ?>
	</th>
	<th>
	<?php echo (defined('COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME')) ? COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME:JText::_('COM_JOPENSIMPAYPAL_HEADING_PAYOUT_IWC'); ?>
	</th>
	<th>
	<?php echo JText::_('COM_JOPENSIMPAYPAL_HEADING_PAYOUT_RWC'); ?>
	</th>
	<th>
	<?php echo (defined('COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME')) ? COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME." ":""; ?>
	<?php echo JText::_('COM_JOPENSIMPAYPAL_HEADING_PAYOUT_IWBALANCE'); ?>
	</th>
	<th>
	<?php echo JText::_('COM_JOPENSIMPAYPAL_HEADING_PAYOUT_TIME'); ?>
	</th>
	<th>
	<?php echo JText::_('COM_JOPENSIMPAYPAL_PAYOUTSTATUS'); ?>
	</th>
</tr>