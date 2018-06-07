<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('formbehavior.chosen', 'select');
$listOrder		= $this->sortColumn;
$listDirn		= $this->sortDirection;
$sortFields 	= $this->getSortFields();
$filtersearch	= $this->escape($this->state->get('groups_filter_search'));
?>

<script type="text/javascript">
    Joomla.submitbutton = function(task) {Joomla.submitform(task);}
</script>

<script type="text/javascript">
Joomla.orderTable = function()
{
	table = document.getElementById("sortTable");
	direction = document.getElementById("directionTable");
	order = table.options[table.selectedIndex].value;
	if (order != '<?php echo $listOrder; ?>') {dirn = 'asc';}
	else {dirn = direction.options[direction.selectedIndex].value;}
	Joomla.tableOrdering(order, dirn, '');
}
</script>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<form action="index.php" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='groups' />
<input type='hidden' name='task' value='' />
<input type='hidden' name='boxchecked' value='0' />
<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
<div id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group pull-left">
		<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $filtersearch; ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('JOPENSIM_GROUPS_SEARCH'); ?>" />
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
			<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
			<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');  ?></option>
		</select>
	</div>
	<div class="btn-group pull-right">
		<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
		<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
			<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
			<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
		</select>
	</div>
</div>
<div class="clearfix"></div>

<p class="alert alert-info">
    <a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo JText::_('JOPENSIM_GROUPLISTEXPLAIN'); ?>
</p>

<table class="table table-striped table-hover adminlist">
<colgroup>
	<col width='10'>
	<col width='10'>
	<col width='200'>
	<col width='200'>
	<col width='10'>
	<col width='10'>
	<col width='64'>
	<col width='10'>
	<col width='*'>
</colgroup>
<thead>
<tr>
	<th width="5"><?php echo JText::_('Num'); ?></th>
	<th>&nbsp;</th>
	<th class='title'><?php echo JHTML::_( 'grid.sort', JText::_('JOPENSIM_GROUPNAME'), '#__opensim_group.Name', $this->sortDirection, $this->sortColumn); ?></th>
	<th class='title'><?php echo JHTML::_( 'grid.sort', JText::_('JOPENSIM_GROUPFOUNDER'), '#__opensim_group.FounderID', $this->sortDirection, $this->sortColumn); ?></th>
	<th class='title'><nobr><?php echo JHTML::_( 'grid.sort', JText::_('JOPENSIM_GROUPOWNERS'), 'COUNT(DISTINCT(#__opensim_grouprolemembership.AgentID))', $this->sortDirection, $this->sortColumn); ?></nobr></th>
	<th class='title'><nobr><?php echo JHTML::_( 'grid.sort', JText::_('JOPENSIM_GROUPMEMBERS'), 'COUNT(DISTINCT(#__opensim_groupmembership.AgentID))', $this->sortDirection, $this->sortColumn); ?></nobr></th>
	<th><?php echo JText::_('JOPENSIM_MATURE'); ?></th>
	<th><?php echo JText::_('JOPENSIM_GROUP_INSIGNIA'); ?></th>
	<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php if(count($this->groupList) > 0): ?>
<?php foreach($this->groupList AS $key => $group): ?>
<tr>
	<td><?php echo $key+1; ?></td>
	<td width='10'><input type='checkbox' name='checkGroup[]' id='group_<?php echo $group['GroupID']; ?>' value='<?php echo $group['GroupID']; ?>' onClick='Joomla.isChecked(this.checked);' /></td>
	<td><nobr><a class='modal' id='groupdetailwindow' href='index.php?option=com_opensim&view=groups&task=charta&groupID=<?php echo $group['GroupID']; ?>&tmpl=component' rel="{handler: 'iframe', size: {x: 400, y: 400}, overlayOpacity: 0.3}"><?php echo $group['Name']; ?></a></nobr></td>
	<td><nobr><?php echo $group['FounderName']; ?></nobr></td>
	<td align='right'><?php echo $group['owners']; ?></td>
	<td align='right'><?php echo $group['members']; ?></td>
	<td align='center'><?php echo ($group['MaturePublish'] == 1) ? JText::_('JYES'):JText::_('JNO'); ?></td>
	<td>
	<?php if(($this->settings['getTextureEnabled'] == 1) && $group['InsigniaID'] && $group['InsigniaID'] != $this->zerouuid): ?>
	<a class='hasTooltip modal' id='groupinsigniawindow' href='index.php?option=com_opensim&view=opensim&task=gettexture&textureid=<?php echo $group['InsigniaID']; ?>&tmpl=component' rel="{handler: 'iframe', size: {x: 600, y: 600}, overlayOpacity: 0.3}" title="<?php echo JText::_('JOPENSIM_GROUPS_PREVIEWINSIGNIA'); ?>">
	    <i class="icon-picture" title="<?php echo JText::_('JOPENSIM_GROUPS_PREVIEWINSIGNIA'); ?>"></i>
	</a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td><?php echo ($group['owners'] == 0 && $group['members'] > 0) ? "<a class='modal' id='groupdetailwindow' href='index.php?option=com_opensim&view=groups&task=grouprepair&groupID=".$group['GroupID']."&tmpl=component' rel=\"{handler: 'iframe', size: {x: 400, y: 400}, overlayOpacity: 0.3}\"><img src='".$this->assetpath."images/repair_icon.png' width='16' height='16' alt='".JText::_('GROUPREPAIR')."' title='".JText::_('GROUPREPAIR')."' border='0' /></a>":"&nbsp;"; ?></td>
</tr>
<?php endforeach; ?>
<tfoot>
<tr>
	<td colspan='9'><?php echo $this->pagination->getListFooter(); ?></td>
</tr>
</tfoot>
<?php else: ?>
<tr>
	<td colspan='9'><?php echo JText::_('JOPENSIM_NOGROUPSFOUND'); ?></th>
</tr>
<?php endif; ?>
</tbody>
</table>
</form>
</div>