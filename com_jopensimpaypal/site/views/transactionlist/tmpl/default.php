<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.tooltip');
?>
<h1><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST'); ?></h1>

<!-- ##### paypal request table -->
<table class='jopensimpaypal_transactiontable'>
<thead>
<tr>
	<th colspan='4' class='jopensimpaypal_transactiontablehead'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_PAYPAL'); ?></th>
</tr>
<?php if(is_array($this->paypalList) && count($this->paypalList) > 0): ?>
<tr>
	<th class='jopensimpaypal_transactiontablehead'><?php echo $this->currencyname; ?></th>
	<th class='jopensimpaypal_transactiontablehead'><?php echo (defined('COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME')) ? COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME:JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_IWC'); ?></th>
	<th class='jopensimpaypal_transactiontablehead'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_DATE'); ?></th>
	<th class='jopensimpaypal_transactiontablehead'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_ACCOUNT'); ?></th>
</tr>
</thead>
<tbody>
<?php foreach($this->paypalList AS $paypaltransaction): ?>
<tr>
	<td class='jopensimpaypal_transactiontablecell jopensimpaypal_numbercell jopensimpaypal_cell_first'><?php echo $paypaltransaction->amount_rlc; ?></td>
	<td class='jopensimpaypal_transactiontablecell jopensimpaypal_numbercell'><?php echo $paypaltransaction->amount_iwc; ?></td>
	<td class='jopensimpaypal_transactiontablecell'><?php echo $paypaltransaction->transactiontime; ?></td>
	<td class='jopensimpaypal_transactiontablecell jopensimpaypal_cell_last'><?php echo $paypaltransaction->payer_email; ?></td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tbody>
<tr>
	<td class='jopensimpaypal_transactiontablecell jopensimpaypal_cell_all' colspan='4'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_PAYPAL_EMPTY'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
<br /><br />

