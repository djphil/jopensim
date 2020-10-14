<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
// load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');

JFormHelper::loadFieldClass('list');
//Get daterange options
JFormHelper::addFieldPath(JPATH_COMPONENT . '/fields');
$daterangeselector		= JFormHelper::loadFieldType('daterangeselector', $this->state->get('filter.daterange'));
$daterangeOptions		= $daterangeselector->getOptions(); // works only if you set your field getOptions on public!!

$paymentstatusselector	= JFormHelper::loadFieldType('paymentstatusselector', $this->state->get('filter.paymentstatus'));
$paymentstatusOptions	= $paymentstatusselector->getOptions();

?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span12">
	<h1><?php echo JText::_('COM_JOPENSIMPAYPAL_MENU_TRANSACTIONS'); ?></h1>
	<form action="<?php echo JRoute::_('index.php?option=com_jopensimpaypal'); ?>" method="post" name="adminForm" id="adminForm">
	<input type='hidden' name='view' value='transactions' />
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<input type="text" name="filter_search" id="filter_search" class="hasTooltip"  placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->searchterms); ?>" title="<?php echo JHtml::tooltipText('COM_JOPENSIMPAYPAL_SEARCH_TRANSACTIONS'); ?>" />
		</div>
		<div class="btn-group pull-left">
			<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="filter_paymentstatus" class="element-invisible">
			<?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONS_FILTER_LABEL'); ?>
			</label>
			<select name="filter_paymentstatus" id="filter_paymentstatus" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONS_FILTER_SELECT_PAYMENTSTATUS'); ?></option>
				<?php echo JHtml::_('select.options',$paymentstatusOptions,'value','text',$this->state->get('filter.paymentstatus'), true);?>
			</select>
		</div>
		<div class="btn-group pull-right hidden-phone">
			<select name="filter_daterange" id="filter_daterange" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JOPENSIMPAYPAL_TRANSACTIONS_FILTER_SELECT_DATERANGE'); ?></option>
				<?php echo JHtml::_('select.options',$daterangeOptions,'value','text',$this->state->get('filter.daterange'));?>
			</select>
		</div>
	</div>

	<table class="table table-striped table-hover adminlist">
	<thead><?php echo $this->loadTemplate('head');?></thead>
	<tbody><?php echo $this->loadTemplate('body');?></tbody>
	<tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
	</table>
	</form>
</div>