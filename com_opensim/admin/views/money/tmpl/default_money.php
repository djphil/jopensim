<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
// load tooltip behavior
JHtml::_('behavior.tooltip');

JFormHelper::loadFieldClass('list');
JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
$daterangeselector		= JFormHelper::loadFieldType('daterangeselector', $this->daterange);
$daterangeOptions		= $daterangeselector->getOptions(); // works only if you set your field getOptions on public!!
?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php if(is_array($this->transactionlist) && count($this->transactionlist) > 0): ?>
<form action="<?php echo JRoute::_('index.php?option=com_opensim'); ?>" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='view' value='money' />
<fieldset id="filter-bar">
<div class="filter-search fltlft">
</div>
<div class="filter-select fltrt">
	<select name="filter_daterange" id="filter_daterange" class="inputbox" onchange="this.form.submit()">
		<option value=""><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_FILTER_SELECT_DATERANGE'); ?></option>
		<?php echo JHtml::_('select.options',$daterangeOptions,'value','text',$this->daterange);?>
	</select>
</div>
</fieldset>


<table class="adminlist">
<thead><?php echo $this->loadTemplate('transactionhead');?></thead>
<tbody><?php echo $this->loadTemplate('transactionbody');?></tbody>
<tfoot><?php echo $this->loadTemplate('transactionfoot');?></tfoot>
</table>
</form>
<?php else: ?><br /><br /><br />
<?php echo JText::_('JOPENSIM_MONEY_NOTRANSACTIONS'); ?>
<?php endif; ?>
</div>