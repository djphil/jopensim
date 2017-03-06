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
<h1><?php echo JText::_('COM_JOPENSIMPAYPAL_BUY_IWC'); ?></h1>

<?php echo $this->pretext; ?>
<input type="hidden" name="minbuymessage" id="minbuymessage" value="<?php echo JText::sprintf('COM_JOPENSIMPAYPAL_MINBUYERROR',$this->cparams['currency'],$this->cparams['minbuy']); ?>" />
<input type="hidden" name="maxbuymessage" id="maxbuymessage" value="<?php echo JText::sprintf('COM_JOPENSIMPAYPAL_MAXBUYERROR',$this->cparams['currency'],$this->cparams['maxbuy']); ?>" />
<form action="<?php echo $this->cparams['paypal_action']; ?>" method="post" name="paypalForm" id="paypalForm" onSubmit="return checkPayPalForm();">
<input type="hidden" name="jopensimpaypal_xchangerate" id="jopensimpaypal_xchangerate" value="<?php echo $this->cparams['currencyratebuy']; ?>" />
<input type="hidden" name="cmd" value="_cart" />
<input type="hidden" name="custom" value="<?php echo $this->userUUID.":".$this->user->id; ?>" />
<input type="hidden" id="jopensimpaypal_iwcurrencyname" name="jopensimpaypal_iwcurrencyname" value="<?php echo COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME; ?>" />
<input type="hidden" id="jopensimpaypal_item" name="item_name_1" value="<?php echo COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME; ?> <?php echo $this->defaultvalueIW; ?>" />
<input type="hidden" name="upload" value="1" />
<input type="hidden" name="business" value="<?php echo $this->cparams['paypal_account']; ?>" />
<input type='hidden' name='currency_code' value='<?php echo $this->cparams['currency']; ?>' />
<input type='hidden' name='notify_url' value='<?php echo $this->notifyurl; ?>' />
<input type='hidden' name='return' value='<?php echo $this->successurl; ?>' />
<input type='hidden' name='cancel_return' value='<?php echo $this->cancelurl; ?>' />
<input type='hidden' name='hasfee' id='jopensimpaypal_hasfee' value='<?php echo $this->cparams['hasfee']; ?>' />
<input type='hidden' name='transactionfee' id='jopensimpaypal_transactionfee' value='<?php echo $this->cparams['transactionfee']; ?>' />
<input type='hidden' name='transactionfeetype' id='jopensimpaypal_transactionfeetype' value='<?php echo $this->cparams['transactionfeetype']; ?>' />
<input type='hidden' name='minbuy' id='jopensimpaypal_minbuy' value='<?php echo sprintf("%01.2f",$this->cparams['minbuy']); ?>' />
<input type='hidden' name='maxbuy' id='jopensimpaypal_maxbuy' value='<?php echo sprintf("%01.2f",$this->cparams['maxbuy']); ?>' />
<input type='hidden' name='amount_1' id='jopensimpaypal_amount_1' value='<?php echo $this->total; ?>' />
<input type='hidden' name='rm' value='2' />
<!-- <input type='hidden' name='lc' value='US' /> -->
<center>
<div id='jOpenSimPayPal_PayPalTable' class='jOpenSimPayPalPayPalTable'>
	<div class='jOpenSimPayPalPayPalTableRow'>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><?php echo JText::_('COM_JOPENSIMPAYPAL_BUYFOR')." ".$this->cparams['currency']; ?> <input type='text' align='absmiddle' name='jopensimpaypal_amount' id='jopensimpaypal_rlcurrency' class='jOpenSimPayPalRLcurrency' size='5' value='<?php echo $this->defaultvalueRL; ?>' onChange='jopensimpaypalUpdate();' /></div>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'>= <?php echo COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME; ?> <span id='jOpenSimPayPalIW1'><?php echo $this->defaultvalueIW; ?></span></div>
	</div>
<?php if($this->cparams['hasfee']): ?>
	<div class='jOpenSimPayPalPayPalTableRow'>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONFEE')." ".$this->cparams['currency']; ?> <span id='jOpenSimPayPalTransactionFee'><?php echo $this->transactionfee; ?></span></div>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'>&nbsp;</div>
	</div>
	<div class='jOpenSimPayPalPayPalTableRow'>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><?php echo JText::_('COM_JOPENSIMPAYPAL_PAYTOTAL')." ".$this->cparams['currency']; ?> <span id='jOpenSimPayPalRLtotal'><?php echo $this->total; ?></span></div>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'><?php echo JText::_('COM_JOPENSIMPAYPAL_PAYFOR')." ".COM_JOPENSIMPAYPAL_INWORLD_CURRENCYNAME; ?> <span id='jOpenSimPayPalIW2'><?php echo $this->defaultvalueIW; ?></span></div>
	</div>
<?php endif; ?>
	<div class='jOpenSimPayPalPayPalTableRow'>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'>&nbsp;</div>
		<div class='jOpenSimPayPalPayPalTableCell jOpenSimPayPalCurrencyCell'>
		<input type="image" src="https://www.paypalobjects.com/en_US/AT/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" title="PayPal - The safer, easier way to pay online!" />
		<!--<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" alt="<?php echo JText::_('COM_JOPENSIMPAYPAL_CHECKOUT'); ?>" />-->
		</div>
	</div>
</div>
</center>
</form>
<?php echo $this->posttext; ?>
