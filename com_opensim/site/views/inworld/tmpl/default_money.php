<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<h1><?php echo JText::_('JOPENSIM_INWORLD_DETAILS'); ?></h1>

<table>
<tr>
	<td>
	    <?php echo $this->topbar; ?>
	</td>
</tr>
<tr>
	<td>	
		<form class="navbar-form navbar-left" role="search" action='index.php' name='transactionrangeform' id='transactionrangeform' method='post'>
			<input type='hidden' name='option' value='com_opensim' />
			<input type='hidden' name='view' value='inworld' />
			<input type='hidden' name='task' value='money' />
			<input type='hidden' name='Itemid' value='<?php echo $this->Itemid; ?>' />
			<?php echo JText::_('JOPENSIM_MONEY_VIEWTRANSACTIONS'); ?>: 
			<select name='range'>
				<option value='1'<?php echo ($this->range == 1) ? " selected='selected'":""; ?>><?php echo JText::_('JOPENSIM_MONEY_VIEWTRANSACTIONS_1'); ?></option>
				<option value='30'<?php echo ($this->range == 30) ? " selected='selected'":""; ?>><?php echo JText::_('JOPENSIM_MONEY_VIEWTRANSACTIONS_30'); ?></option>
				<option value='365'<?php echo ($this->range == 365) ? " selected='selected'":""; ?>><?php echo JText::_('JOPENSIM_MONEY_VIEWTRANSACTIONS_365'); ?></option>
				<option value='0'<?php echo ($this->range == 0) ? " selected='selected'":""; ?>><?php echo JText::_('JOPENSIM_MONEY_VIEWTRANSACTIONS_ALL'); ?></option>
			</select>
			<!-- <span class="icon-search"></span> -->
			<button type='submit' class="btn btn-primary"/><?php echo JText::_('JOPENSIM_MONEY_SHOWTRANSACTIONBUTTON'); ?></button>
		</form>

		<hr class="hr-condensed">

		<div class='<?php echo $this->pageclass_sfx; ?>'><p><?php echo JText::_('JOPENSIM_MONEY_CURRENT_BALANCE'); ?>: <span class="label label-default"><?php echo $this->currencyname." ".number_format($this->balance,0,JText::_('JOPENSIM_MONEY_SEPERATOR_COMMA'),JText::_('JOPENSIM_MONEY_SEPERATOR_THOUSAND')); ?></span></p></div>
		<div class='<?php echo $this->pageclass_sfx; ?>'><p><?php echo $this->paypallink; ?></p></div>
		<div class='<?php echo $this->pageclass_sfx; ?>'><p><?php echo $this->payoutlink; ?></p></div>
		<div class='<?php echo $this->pageclass_sfx; ?>'></div>
	</td>
</tr>
<tr>
	<td>
	    <h4><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONHISTORY'); ?></h4>
	</td>
</tr>
<tr>
	<td>
	<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
	<?php if(is_array($this->transactions) && count($this->transactions) > 0): ?>
	<tr>
		<th><?php echo JText::_('JOPENSIM_MONEY_TRANSACTION_DATE'); ?></th>
		<th><?php echo JText::_('JOPENSIM_MONEY_TRANSACTION_SENDER'); ?></th>
		<th><?php echo JText::_('JOPENSIM_MONEY_TRANSACTION_RECEIVER'); ?></th>
		<th><?php echo JText::_('JOPENSIM_MONEY_TRANSACTION_AMOUNT'); ?></th>
		<th><?php echo JText::_('JOPENSIM_MONEY_TRANSACTION_DESCRIPTION'); ?></th>
	</tr>
	<?php foreach($this->transactions AS $transaction): ?>
	<tr>
		<td><?php echo $transaction['transactiontime']; ?></td>
		<td><?php echo $transaction['sendername']; ?></td>
		<td><?php echo $transaction['receivername']; ?></td>
		<td class='numbercell'><span class="label label-default"><?php echo $this->currencyname; ?> <?php echo number_format($transaction['amount'],0,JTEXT::_('JOPENSIM_MONEY_SEPERATOR_COMMA'),JTEXT::_('JOPENSIM_MONEY_SEPERATOR_THOUSAND')); ?></span></td>
		<td><?php echo $transaction['description']; ?></td>
	</tr>
	<?php endforeach; ?>
	
<?php else: ?>
	<tr>
		<td><?php echo JText::_('JOPENSIM_MONEY_NOTRANSACTIONS'); ?></td>
	</tr>
<?php endif; ?>
	</table>
	</td>
</tr>
</table>