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

<!-- ##### payout request table -->
<table class='jopensimpaypal_transactiontable'>
<thead>
<tr>
	<th colspan='6' class='jopensimpaypal_transactiontablehead'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_PAYOUT'); ?></th>
</tr>
<?php if(is_array($this->payoutList) && count($this->payoutList) > 0): ?>
<tr>
	<th class='jopensimpaypal_transactiontablehead'><?php echo (defined('COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME')) ? COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME:JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_IWC'); ?></th>
	<th class='jopensimpaypal_transactiontablehead'><?php echo $this->currencyname; ?></th>
	<th class='jopensimpaypal_transactiontablehead'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_DATE'); ?></th>
	<th class='jopensimpaypal_transactiontablehead'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_ACCOUNT'); ?></th>
	<th class='jopensimpaypal_transactiontablehead' colspan='2'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_PAYOUT_STATUS'); ?></th>
</tr>
</thead>
<tbody>
<?php foreach($this->payoutList AS $payouttransaction): ?>
<tr>
	<td class='jopensimpaypal_transactiontablecell jopensimpaypal_numbercell'><?php echo $payouttransaction->amount_iwc; ?></td>
	<td class='jopensimpaypal_transactiontablecell jopensimpaypal_numbercell jopensimpaypal_cell_first'><?php echo $payouttransaction->amount_rlc; ?></td>
	<td class='jopensimpaypal_transactiontablecell'><?php echo $payouttransaction->requesttime; ?></td>
	<td class='jopensimpaypal_transactiontablecell'><?php echo $payouttransaction->paypalaccount; ?></td>
	<td class='jopensimpaypal_transactiontablecell jopensimpaypal_centercell'><?php echo $payouttransaction->statusimage; ?></td>
	<td class='jopensimpaypal_transactiontablecell jopensimpaypal_centercell jopensimpaypal_cell_last'>
	<?php if($payouttransaction->status == 0): ?>
	<a href='<?php echo JRoute::_('index.php?option=com_jopensimpaypal&task=removerequest&id='.$payouttransaction->id); ?>&Itemid=<?php echo $this->itemid; ?>' onClick='return confirm("<?php echo JText::_('COM_JOPENSIMPAYPAL_PAYOUT_REVOKESURE'); ?>");'><?php echo $this->revokeimage; ?></a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tbody>
<tr>
	<td class='jopensimpaypal_transactiontablecell jopensimpaypal_cell_all' colspan='5'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONLIST_PAYOUT_EMPTY'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
