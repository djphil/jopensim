<?php
/*
 * @package Joomla 2.5
 * @copyright Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html, see LICENSE.php
 *
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2013 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access'); ?>
<h1><?php echo JText::_('COM_JOPENSIMPAYPAL_SELL_IWC'); ?></h1>

<?php echo $this->pretext; ?>
<input type="hidden" name="minsellmessage" id="jopensimpaypal_minsellmessage" value="<?php echo JText::sprintf('COM_JOPENSIMPAYPAL_MINSELLERROR',$this->cparams['currency'],$this->cparams['minsell']); ?>" />
<input type="hidden" name="maxsellmessage" id="jopensimpaypal_maxsellmessage" value="<?php echo JText::sprintf('COM_JOPENSIMPAYPAL_MAXSELLERROR',$this->cparams['currency'],$this->cparams['maxsell']); ?>" />
<input type="hidden" name="balanceerrormsg" id="jopensimpaypal_balanceerrormsg" value="<?php echo JText::sprintf('COM_JOPENSIMPAYPAL_BALANCE_SELLERROR',COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME); ?>" />
<input type="hidden" name="paypalmessage" id="jopensimpaypal_paypalmessage" value="<?php echo JText::_('COM_JOPENSIMPAYPAL_PAYPALERROR'); ?>" />
<input type="hidden" name="negativeamounterror" id="jopensimpaypal_negativeamounterror" value="<?php echo JText::sprintf('COM_JOPENSIMPAYPAL_ERROR_NEGATIVE',COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME); ?>" />
<form action="index.php" method="post" name="paypalForm" id="paypalForm" onSubmit="return checkPayOutForm();">
<input type='hidden' id='currentbalance' name='currentbalance' value='<?php echo $this->balance; ?>' />
<input type="hidden" name="jopensimpaypal_xchangerate" id="jopensimpaypal_xchangerate" value="<?php echo $this->cparams['currencyratesell']; ?>" />
<input type="hidden" id="jopensimpaypal_iwcurrencyname" name="jopensimpaypal_iwcurrencyname" value="<?php echo COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME; ?>" />
<input type='hidden' name='currency_code' value='<?php echo $this->cparams['currency']; ?>' />
<input type='hidden' name='hasfee' id='jopensimpaypal_hasfee' value='<?php echo $this->cparams['hasfee']; ?>' />
<input type='hidden' name='transactionfee' id='jopensimpaypal_transactionfee' value='<?php echo $this->cparams['transactionfee']; ?>' />
<input type='hidden' name='transactionfeetype' id='jopensimpaypal_transactionfeetype' value='<?php echo $this->cparams['transactionfeetype']; ?>' />
<input type='hidden' name='minsell' id='jopensimpaypal_minsell' value='<?php echo sprintf("%01.2f",$this->cparams['minsell']); ?>' />
<input type='hidden' name='maxsell' id='jopensimpaypal_maxsell' value='<?php echo sprintf("%01.2f",$this->cparams['maxsell']); ?>' />
<input type='hidden' name='payoutvalue' id='jopensimpaypal_payoutvalue' value='0' />
<input type='hidden' name='option' id='jopensimpaypal_option' value='com_jopensimpaypal' />
<input type='hidden' name='view' id='jopensimpaypal_view' value='payout' />
<input type='hidden' name='task' id='jopensimpaypal_task' value='payout' />
<input type='hidden' name='Itemid' id='jopensimpaypal_Itemid' value='<?php echo $this->itemid; ?>' />
<input type='hidden' name='returnto' id='jopensimpaypal_returnto' value='<?php echo $this->returnto; ?>' />
<center>
<div id='jOpenSimPayPal_PayPalTable' class='jOpenSimPayPalPayPalTable'>
	<div class='jOpenSimPayPalPayPalTableRow'>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><nobr><?php echo JText::_('COM_JOPENSIMPAYPAL_CURRENTBALANCE').": "; ?></nobr></div>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><?php echo COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME." ".$this->balance; ?></div>
	</div>
	<div class='jOpenSimPayPalPayPalTableRow'>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><nobr><?php echo JText::sprintf('COM_JOPENSIMPAYPAL_SELL_IWCURRENCY',COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME); ?> <input type='text' align='absmiddle' name='jopensimpaypal_amount' id='jopensimpaypal_iwcurrency' class='jOpenSimPayPalIWcurrency' size='7' value='<?php echo $this->defaultvalueRL; ?>' onChange='jopensimpaypalUpdate2();' /></nobr></div>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'>= <?php echo $this->cparams['currency']; ?> <span id='jOpenSimPayPalRL1'><?php echo $this->defaultvalueRL; ?></span></div>
	</div>
<?php if($this->cparams['hasfee']): ?>
	<div class='jOpenSimPayPalPayPalTableRow'>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'>&nbsp;</div>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONFEE')." ".$this->cparams['currency']; ?> <span id='jOpenSimPayPalTransactionFee'><?php echo $this->transactionfee; ?></span></div>
	</div>
	<div class='jOpenSimPayPalPayPalTableRow'>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'>&nbsp;</div>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'>= <?php echo $this->cparams['currency']; ?> <span id='jOpenSimPayPalRL2'><?php echo $this->defaultvalueIW; ?></span></div>
	</div>
<?php endif; ?>
	<div class='jOpenSimPayPalPayPalTableRow'>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><?php echo JText::_('COM_JOPENSIMPAYPAL_USERPAYPALACCOUNT'); ?>:</div>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><input type='text' name='paypaluser' id='jopensimpaypal_paypaluser' value='' /></div>
	</div>
	<div class='jOpenSimPayPalPayPalTableRow'>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'>&nbsp;</div>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><input type='submit' name='submit' id='submitpayout' value='<?php echo JText::_('COM_JOPENSIMPAYPAL_PAYOUTSUBMIT'); ?>' /></div>
	</div>
</div>
</center>
</form>
<?php echo $this->posttext; ?>