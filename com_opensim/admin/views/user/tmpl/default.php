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
$filtersearch	= $this->escape($this->state->get('users_filter_search'));
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		Joomla.submitform(task);
	}
</script>
<script type="text/javascript">
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>')
		{
			dirn = 'asc';
		}
		else
		{
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
<div class="jopensim-adminpanel">

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<form action="index.php" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='user' />
<input type='hidden' name='task' value='updateuser' />
<input type='hidden' name='boxchecked' value='0' />
<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
<div id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group pull-left">
		<input type="text" name="filter_search" id="filter_search" class="hasTooltip"  placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $filtersearch; ?>" title="<?php echo JHtml::tooltipText('JOPENSIM_USER_SEARCH'); ?>" />
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
<div class="clearfix"> </div>

<table class="table table-striped table-hover adminlist">
<thead>
<tr>
	<th width="5"><?php echo JText::_('Num'); ?></th>
	<th class='title' width='10'>&nbsp;</td>
	<th class='title'><?php echo JHTML::_( 'grid.sort', JText::_('FIRST_NAME'), 'UserAccounts.FirstName', $this->sortDirection, $this->sortColumn); ?></th>
	<th class='title'><?php echo JHTML::_( 'grid.sort', JText::_('LAST_NAME'), 'UserAccounts.LastName', $this->sortDirection, $this->sortColumn); ?></th>
	<th class='title'><?php echo JHTML::_( 'grid.sort', JText::_('ONLINE'), 'GridUser.Online', $this->sortDirection, $this->sortColumn); ?></th>
	<th class='title'><?php echo JHTML::_( 'grid.sort', JText::_('EMAIL'), 'UserAccounts.Email', $this->sortDirection, $this->sortColumn); ?></th>
	<th class='title'><?php echo JHTML::_( 'grid.sort', JText::_('CREATIONDATE'), 'UserAccounts.Created', $this->sortDirection, $this->sortColumn); ?></th>
	<th class='title'><?php echo JHTML::_( 'grid.sort', JText::_('LASTLOGIN'), 'GridUser.Login', $this->sortDirection, $this->sortColumn); ?></th>
	<th class='title'><?php echo JHTML::_( 'grid.sort', JText::_('LASTLOGOUT'), 'GridUser.Logout', $this->sortDirection, $this->sortColumn); ?></th>
</tr>
</thead>

<?php if(isset($this->users) && is_array($this->users) && count($this->users) > 0): ?>
<tbody>
<?php
$i=0;
$num	= $this->limitstart;
if(is_array($this->users)) {
	foreach($this->users AS $user) {
		$num++;
		$created		= JHTML::_('date',$user['created'],JText::_('DATE_FORMAT_LC2'),null);
		$last_login		= ($user['last_login']) ? JHTML::_('date',$user['last_login'],JText::_('DATE_FORMAT_LC2'),null):JText::_('NEVER');
		$last_logout	= ($user['last_logout']) ? JHTML::_('date',$user['last_logout'],JText::_('DATE_FORMAT_LC2'),null):JText::_('NEVER');
?>
<tr class="row<?php echo $i % 2; ?>">
	<td><?php echo $this->pagination->getRowOffset($i); ?></td>
	<td>
    	<input type='checkbox' name='checkUser[]' id='checkUser_<?php echo $this->pagination->getRowOffset($i); ?>' value='<?php echo $user['userid']; ?>' onClick='Joomla.isChecked(this.checked);' style='margin-top:0px;margin-right:5px;' />
	</td>
	<td><label for='checkUser_<?php echo $this->pagination->getRowOffset($i); ?>'><span class="hasTooltip" title='<?php echo $user['userid']; ?>'><?php echo $user['firstname']; ?></span></label></td>
	<td><label onDblClick="alert('<?php echo $user['userid']; ?>');"><span class="hasTooltip" title='<?php echo $user['userid']; ?>'><?php echo $user['lastname']; ?></span></label></td>
	<td align='center' class='jgrid'>
	<?php if($user['online'] == "true"): ?>
	<a data-original-title="<?php echo JText::_('JOPENSIM_USER_ONLINE'); ?>" class="btn btn-micro active hasTooltip" href="index.php?option=com_opensim&view=user&task=setUserOffline&userid=<?php echo $user['userid']; ?>" title='<?php echo JText::_('JOPENSIM_SETUSEROFFLINE'); ?>'><i class="icon-publish"></i></a>
	<?php else: ?>
	<a data-original-title="<?php echo JText::_('JOPENSIM_USER_OFFLINE'); ?>" class="btn btn-micro disabled hasTooltip" href="#" title="<?php echo JText::_('JOPENSIM_USER_OFFLINE'); ?>"><i class="icon-unpublish" title="<?php echo JText::_('JOPENSIM_USER_OFFLINE'); ?>"></i></a>
	<?php endif; ?>
	</td>
	<td><?php echo $user['email']; ?></td>
	<td><?php echo $created; ?></td>
	<td><?php echo $last_login; ?></td>
	<td><?php echo $last_logout; ?></td>
</tr>
<?php
		$i++;
	}
}
?>
</tbody>
<?php endif; ?>
<tfoot>
<tr>
	<td colspan='9'><?php echo $this->pagination->getListFooter(); ?></td>
</tr>
</tfoot>
</table>
</form>
</div>

</div>
