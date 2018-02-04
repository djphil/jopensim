<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

JHtml::_('formbehavior.chosen', 'select');

$listOrder		= $this->sortColumn;
$listDirn		= $this->sortDirection;
$sortFields 	= $this->getSortFields();
$filtersearch	= $this->escape($this->state->get('regions_filter_search'));
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
<form class="form-group" action="index.php" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='maps' />
<input type='hidden' name='task' value='' />
<input type='hidden' name='boxchecked' value='0' />
<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />
<div id="filter-bar" class="btn-toolbar">
	<div class="filter-search btn-group pull-left">
		<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $filtersearch; ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('JOPENSIM_MAPS_SEARCH'); ?>" />
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

<?php if(is_array($this->regions) && count($this->regions) > 0): ?>

<p class="text-info">
	<?php echo JText::_('SELECT_MAP'); ?>
</p>
<table cellspacing="5" border='0' class="table table-striped table-hover adminlist">
<thead>
<tr>
	<th width="5"><?php echo JText::_('Num'); ?></th>
	<th><!-- <?php echo JHtml::_('grid.checkall'); ?> --></th>
	<th><?php echo JHtml::_('grid.sort', 'JOPENSIM_REGION_NAME', 'regions.regionName', $listDirn, $listOrder); ?></th>
	<th class='title'><?php echo JText::_('JOPENSIM_REGION_OWNER'); ?></th>
	<th class='title'><?php echo JText::_('REGION_DEFAULT'); ?></th>
	<th class='title'><?php echo JText::_('JOPENSIM_REGION_PUBLIC'); ?></th>
	<th class='title'><?php echo JText::_('JOPENSIM_REGION_GUIDE'); ?></th>
	<th class='title'>&nbsp;</th>
	<th class='title'><?php echo JText::_('REGION_VISIBLE'); ?></th>
	<th class='title'><nobr><?php echo JHtml::_('grid.sort', 'JOPENSIM_REGION_POSITION_X', 'regions.locX', $listDirn, $listOrder); ?></nobr></th>
	<th class='title'><nobr><?php echo JHtml::_('grid.sort', 'JOPENSIM_REGION_POSITION_Y', 'regions.locY', $listDirn, $listOrder); ?></nobr></th>
	<th class='title'><?php echo JText::_('REGION_ARTICLE'); ?></th>
</tr>
</thead>
<tbody>
<?php
$k = 0;
$i=0;
$num	= $this->limitstart;
foreach($this->regions AS $key => $region) {
	$num++;
	$debug = var_export($region,TRUE);
	$publicclass	= ($region['public'] == 1) ? "public_yes":"public_no";
	$publictitle	= ($region['public'] == 1) ? JText::_('JOPENSIM_REGION_PUBLIC_YES_TITLE'):JText::_('JOPENSIM_REGION_PUBLIC_NO_TITLE');
	$publicicon		= ($region['public'] == 1) ? "fa-calendar-check-o":"fa-calendar-times-o";

	$guideclass		= ($region['guide'] == 1) ? "guide_yes":"guide_no";
	$guidetitle		= ($region['guide'] == 1) ? JText::_('JOPENSIM_REGION_GUIDE_YES_TITLE'):JText::_('JOPENSIM_REGION_GUIDE_NO_TITLE');
	$guideicon		= ($region['guide'] == 1) ? "fa-bookmark":"fa-bookmark-o";
?>
<tr class="row<?php echo $i % 2; ?>">
	<td><?php echo $num; ?></td>
	<td>
	    <input type='checkbox' name='selectedRegion[]' id='selectedRegion_<?php echo $key; ?>' value='<?php echo $region['uuid']; ?>' onClick='Joomla.isChecked(this.checked);' style='margin-top:0px;margin-right:5px;' />
	</td>
	<td>
	    <a href='index.php?option=com_opensim&view=maps&task=editinfo&selectedRegion=<?php echo $region['uuid']; ?>'>
		    <nobr><?php echo $region['regionName']; ?></nobr>
		</a>
	</td>
	<td><nobr><?php echo $region['owner']; ?></nobr></td>
	<td align='center'>
		<?php if($this->defaultregion == $region['uuid']): ?>
		<a class="btn btn-micro disabled hasTooltip" data-original-title="Default" title=""><i class="icon-featured"></i></a>
		<?php else: ?>
		<a class="btn btn-micro hasTooltip" href="index.php?option=com_opensim&view=maps&task=selectdefault&region=<?php echo $region['uuid']; ?>" data-original-title="<?php echo JText::_('JLIB_HTML_SETDEFAULT_ITEM'); ?>" title="<?php echo JText::_('JLIB_HTML_SETDEFAULT_ITEM'); ?>"><i class="icon-unfeatured"></i></a>
		<!-- <span class="jgrid" title="<?php // echo JText::_('JLIB_HTML_SETDEFAULT_ITEM'); ?>"><span class="state notdefault"><span class="text"><?php // echo JText::_('JLIB_HTML_SETDEFAULT_ITEM'); ?></span></span></span> -->
		<?php endif; ?>
	</td>
	<td align="center">
		<a class="btn btn-micro hasTooltip" href="index.php?option=com_opensim&view=maps&task=<?php echo (array_key_exists("public",$region) && $region['public'] == 1) ? "setRegionUnpublic":"setRegionPublic"; ?>&region=<?php echo $region['uuid']; ?>" data-original-title="<?php echo $publictitle; ?>" title=""><i class="fa <?php echo $publicicon." ".$publicclass; ?>"></i></a>
	</td>
	<td align="center">
		<a class="btn btn-micro hasTooltip" href="index.php?option=com_opensim&view=maps&task=<?php echo (array_key_exists("guide",$region) && $region['guide'] == 1) ? "setRegionGuideHide":"setRegionGuideShow"; ?>&region=<?php echo $region['uuid']; ?>" data-original-title="<?php echo $guidetitle; ?>" title=""><i class="fa <?php echo $guideicon." ".$guideclass; ?>"></i></a>
	</td>
	<td><?php echo $region['image']; ?></td>
	<td align='center' class='jgrid'>
	    <a class="btn btn-micro active hasTooltip" href="index.php?option=com_opensim&view=maps&task=<?php echo (array_key_exists("hidemap",$region) && $region['hidemap'] == 1) ? "setRegionVisible":"setRegionInvisible"; ?>&region=<?php echo $region['uuid']; ?>" data-original-title="<?php JText::_('REGION_TOGGLE_VISIBILITY'); ?>" title='<?php echo JText::_('REGION_TOGGLE_VISIBILITY'); ?>'><i class="icon-<?php echo ($region['hidemap'] == 1) ? "unpublish":"publish"; ?>"></i></a>
	</td>
	<td><?php echo $region['posX']; ?></td>
	<td><?php echo $region['posY']; ?></td>
	<td>
		<nobr><?php echo ($region['articleId']) ? "<a href='index.php?option=com_content&task=article.edit&id=".$region['articleId']."'>".$region['articleTitle']."</a>":JText::_('JNONE'); ?></nobr>
	</td>
</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
</tbody>
<tfoot>
<tr>
	<td colspan='11'><?php echo $this->pagination->getListFooter(); ?></td>
</tr>
</tfoot>
</table>

<?php else: ?>
<?php
echo "<p class='alert alert-danger'>".JText::_('ERROR_NOREGION')."</p>\n";
if(!$filtersearch) {
	echo "<p class='alert alert-warning'>".JText::_('ERRORQUESTION1')."</p>\n";
	echo "<p class='alert alert-warning'>".JText::_('ERRORQUESTION2')."</p>\n";
}
?>
<?php if(isset($this->os_setting['errormsg'])): ?>
<p class="alert alert-danger"><?php echo JText::_('ERRORMSG').": ".$this->os_setting['errormsg']; ?></p>
<?php endif; ?>
<?php endif; ?>
</form>

<?php if(is_array($this->unusedImages) && count($this->unusedImages) > 0): ?>
<h3><?php echo JText::_('JOPENSIM_UNUSEDREGIONIMAGES'); ?>:</h3>
<div class="alert alert-warning">
    <a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo JText::_('JOPENSIM_UNUSEDREGIONIMAGES_DESC'); ?>
</div>

<?php foreach($this->unusedImages AS $unused): ?>
<div class="inline text-center">
<table>
<tr>
    <td>
	    <img class="img-thumbnail hasTooltip" src='<?php echo JUri::root(true)."/images/jopensim/regions/".$unused; ?>' width='128' title='<?php echo $unused; ?>' alt='<?php echo $unused; ?>' align='absmiddle' />
	</td>
</tr>
<tr>
    <td>
	<a class="btn btn-default btn-danger" href='index.php?option=com_opensim&view=maps&task=removeCacheImage&img=<?php echo $unused; ?>'>
		<span class='icon-delete'></span><?php echo JText::_('JOPENSIM_MAPDELETE'); ?>
	</a>
	</td>
</tr>
</table>


</div>
<?php endforeach; ?>
<?php endif; ?>
</div>