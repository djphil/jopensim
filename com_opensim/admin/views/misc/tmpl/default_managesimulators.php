<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

$user       = JFactory::getUser();
$userId     = $user->get('id');
//$listOrder	= 'asc';
//$listOrder	= str_replace(' ' . $this->state->get('list.direction'), '', $this->state->get('list.fullordering'));
// error_log("listOrder: ".$listOrder);
//$listOrder2  = $this->escape($this->state->get('list.ordering'));
//$listDirn   = $this->escape($this->state->get('list.direction'));
$listOrder	= '#__opensim_simulators.ordering';
$listDirn	= 'asc';
// error_log("listOrder2: ".$listOrder2);
// error_log("listDirn: ".$listDirn);
$saveOrder	= $listOrder == '#__opensim_simulators.ordering';

$saveOrderingUrl = 'index.php?option=com_opensim&task=saveSimulatorOrderAjax&tmpl=component';
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>

<div id="j-main-container" class="span10">
<div class="form-inline">
	<?php JHtml::_('sortablelist.sortable', 'simulatorList', 'adminForm', strtolower($listDirn), $saveOrderingUrl); ?>
	<form action="index.php" method="post" id="adminForm" name="adminForm">
	<fieldset>
		<legend><?php echo JText::_('JOPENSIM_SIMULATORS'); ?></legend>
		<input type="hidden" name="option" value="com_opensim" />
		<input type="hidden" name="view" value="misc" />
		<input type="hidden" name="task" value="" />
		<input type='hidden' name='boxchecked' value='0' />
	</fieldset>
	<table class="table table-striped adminlist" id="simulatorList">
	<thead>
	<tr>
		<th width="1%" class="nowrap center hidden-phone">
		<?php echo JHtml::_('searchtools.sort', '', '#__opensim_simulators.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
		</th>
		<th width="1%" class="hidden-phone">
		<?php echo JHtml::_('grid.checkall'); ?>
		</th>
		<th width="1%" class="hidden-phone">&nbsp;</th>
		<th width="10%" class="nowrap" style="text-align:left">
		<?php echo JText::_('JOPENSIM_SIMULATORS'); ?>
		</th>
		<th width="10%" class="nowrap" style="text-align:left">
		<?php echo JText::_('JOPENSIM_SIMULATORS_ALIAS'); ?>
		</th>
		<th width="10%" class="nowrap">
		<?php echo JText::_('JOPENSIM_SIMULATORS_RADMINPORT'); ?>
		</th>
		<th width="30%" class="nowrap">
		<?php echo JText::_('JOPENSIM_SIMULATORS_RADMINPWD'); ?>
		</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($this->simulators as $i => $simulator) : ?>
	<tr class="row<?php echo $i % 2; ?>">
	<td class="order nowrap center hidden-phone">
		<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $simulator['ordering']; ?>" class="width-20 text-area-order " />
		<span class="sortable-handler">
			<span class="icon-menu" aria-hidden="true"></span>
		</span>
	</td>
	<td class="center hidden-phone">
		<?php echo JHtml::_('grid.id', $i, $simulator['simulator']); ?>
	</td>
	<td align='center' class='jgrid'>
	<?php if($simulator['connected'] == "true"): ?>
	<a data-original-title="<?php echo JText::_('JOPENSIM_SIMULATOR_CONNECTED'); ?>" class="btn btn-micro disabled hasTooltip" href="#" title='<?php echo JText::_('JOPENSIM_SIMULATOR_CONNECTED'); ?>'><i class="icon-publish" title="<?php echo JText::_('JOPENSIM_SIMULATOR_CONNECTED'); ?>"></i></a>
	<?php else: ?>
	<a data-original-title="<?php echo JText::_('JOPENSIM_SIMULATOR_OFFLINE'); ?>"   class="btn btn-micro disabled hasTooltip" href="#" title="<?php echo JText::_('JOPENSIM_SIMULATOR_OFFLINE'); ?>"><i class="icon-unpublish" title="<?php echo JText::_('JOPENSIM_SIMULATOR_OFFLINE'); ?>"></i></a>
	<?php endif; ?>
	</td>
	<td class="small hidden-phone">
		<input type="text" name="simulator['<?php echo $simulator['simulator']; ?>']" title="<?php echo $simulator['regions'];?>" readonly="readonly" value="<?php echo $simulator['simulator'];?>" />
	</td>
	<td class="small hidden-phone">
		<input type="text" name="alias['<?php echo $simulator['simulator']; ?>']" size="30" value="<?php echo $simulator['alias'];?>" />
	</td>
	<td class="small hidden-phone">
		<input type="text" name="radminport['<?php echo $simulator['simulator']; ?>']" size="30" value="<?php echo $simulator['radminport'];?>" />
	</td>
	<td class="small hidden-phone">
		<input type="text" name="radminpwd['<?php echo $simulator['simulator']; ?>']" size="30" value="<?php echo $simulator['radminpwd'];?>" />
	</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	</form>
</div>
</div>
