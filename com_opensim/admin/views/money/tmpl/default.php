<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
// load tooltip behavior
JHtml::_('behavior.tooltip');

JFormHelper::loadFieldClass('list');
JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

JHtml::_('formbehavior.chosen', 'select');

$senderselector			= JFormHelper::loadFieldType('senderselector');
$senderOptions			= $senderselector->getOptions($this->opensimdb);

$sortFields 			= $this->getSortFields();

$receiverselector		= JFormHelper::loadFieldType('receiverselector');
$receiverOptions		= $receiverselector->getOptions($this->opensimdb);

$ornameselector		= JFormHelper::loadFieldType('ornameselector');
$ornameOptions		= $ornameselector->getOptions();

$daterangeselector	= JFormHelper::loadFieldType('daterangeselector');
$daterangeOptions	= $daterangeselector->getOptions(); // works only if you set your field getOptions on public!!
$filtersearch		= $this->escape($this->state->get('money_filter_search'));
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task);
	}
</script>
<script type="text/javascript">
Joomla.orderTable = function() {
	table = document.getElementById("sortTable");
	direction = document.getElementById("directionTable");
	order = table.options[table.selectedIndex].value;
	if (order != '<?php echo $this->sortColumn; ?>') {dirn = 'asc';}
	else {dirn = direction.options[direction.selectedIndex].value;}
	Joomla.tableMoneyOrdering(order, dirn, '');
}

Joomla.tableMoneyOrdering = function(order, dir, task, form) {
    if (typeof(form) === 'undefined') {form = document.getElementById('adminForm');}
    form.mfilter_order.value = order;
	form.mfilter_order_Dir.value = dir;
	Joomla.submitform(task, form);
}
</script>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<form class="search-form" action="index.php" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='task' value='' />
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='money' />
<input type="hidden" name="mfilter_order" value="<?php echo $this->sortColumn; ?>" />
<input type="hidden" name="mfilter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
<div id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group pull-left">
		<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $filtersearch; ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('JOPENSIM_MONEY_SEARCH'); ?>" />
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
		<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
		<select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
			<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
			<option value="asc" <?php if ($this->sortDirection == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
			<option value="desc" <?php if ($this->sortDirection == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');  ?></option>
		</select>
	</div>
	<div class="btn-group pull-right">
		<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
		<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
			<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
			<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $this->sortColumn); ?>
		</select>
	</div>
	<div class="clearfix"></div>

	<div class="btn-group pull-left hidden-phone">
		<select name="filter_sender" id="filter_sender" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_FILTER_SENDER'); ?></option>
			<?php echo JHtml::_('select.options',$senderOptions,'value','text',$this->state->get('money_filter_sender'));?>
		</select>
		<select name="filter_orname" id="filter_orname" class="inputbox" onchange="this.form.submit()">
			<?php echo JHtml::_('select.options',$ornameOptions,'value','text',$this->state->get('money_filter_orname'));?>
		</select>
		<select name="filter_receiver" id="filter_receiver" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_FILTER_RECEIVER'); ?></option>
			<?php echo JHtml::_('select.options',$receiverOptions,'value','text',$this->state->get('money_filter_receiver'));?>
		</select>
		<select name="filter_daterange" id="filter_daterange" class="inputbox" onchange="this.form.submit()">
			<option value=""><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_FILTER_SELECT_DATERANGE'); ?></option>
			<?php echo JHtml::_('select.options',$daterangeOptions,'value','text',$this->state->get('money_filter_daterange'));?>
		</select>
	</div>
	
</div>
<div class="clearfix"></div>
<hr class="hr-condensed">
<div class="alert alert-info">
    <a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo JText::_('JOPENSIM_MONEY_BALANCEUSER').": <span class='label label-info'>".number_format($this->balanceUser,0,JTEXT::_('JOPENSIM_MONEY_SEPERATOR_COMMA'),JTEXT::_('JOPENSIM_MONEY_SEPERATOR_THOUSAND')); ?></span>
</div>

<?php if(is_array($this->transactions) && count($this->transactions) > 0): ?>
<table class="table table-striped table-hover adminlist">
    <thead><?php echo $this->loadTemplate('transactionhead');?></thead>
    <tbody><?php echo $this->loadTemplate('transactionbody');?></tbody>
    <tfoot><?php echo $this->loadTemplate('transactionfoot');?></tfoot>
</table>
</form>
<?php else: ?>
<p class="alert"><?php echo JText::_('JOPENSIM_MONEY_NOTRANSACTIONS'); ?></p>
<?php endif; ?>
</div>