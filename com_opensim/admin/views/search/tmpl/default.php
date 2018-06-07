<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
if(is_array($this->searchoptions) && count($this->searchoptions) > 0) {
?>

<script type='text/javascript'>
function selectAll(top) {
    var i;
    for(i=0;i<top.options.length;i++) top.options[i].selected=true;
}

function moveTopUp(top) {
    var i, opt;
    for(i=1;i<top.options.length;i++) {
        if(top.options[i].selected && !top.options[i-1].selected) {
            opt=cloneOption(top.options[i]);
            top.options[i]=cloneOption(top.options[i-1]);
            top.options[i-1]=opt;
        }
    }
}

function moveTopDown(top) {
    var i, opt;
    for(i=top.options.length-2;i>=0;i--) {
        if(top.options[i].selected && !top.options[i+1].selected) {
            opt=cloneOption(top.options[i]);
            top.options[i]=cloneOption(top.options[i+1]);
            top.options[i+1]=opt;
        }
    }
    return false;
}
function cloneOption(opt) {
    fromStyle = opt.className;
    fromTitle = opt.title;
    newOpt = new Option(opt.text, opt.value, opt.defaultSelected, opt.selected);
    newOpt.className = fromStyle;
    newOpt.title = fromTitle;
    return newOpt;
}
</script>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<p class="text-info">
	<?php echo JText::_('JOPENSIM_SEARCH_DESC'); ?>
</p>

<h3><?php echo JText::_('JOPENSIM_SEARCH_SETTINGS_SORT_DESC'); ?>:</h3>

<form action="index.php" method="post" id="adminForm" name="adminForm" onSubmit='selectAll(document.adminForm.sortsearchoptions)'>
	<input type="hidden" name="option" value="com_opensim" />
	<input type="hidden" name="view" value="search" />
	<input type="hidden" name="task" value="" />

	<table>
	<tr>
		<td>
			<table>
			<tr>
				<td>
					<select name='sortsearchoptions[]' id='sortsearchoptions' size='<?php echo count($this->searchsort); ?>' multiple='multiple'>
						<?php foreach($this->searchsort AS $key => $option): ?>
						<option value='<?php echo $key; ?>'<?php echo ($option['enabled'] === FALSE) ? " class='jopensim_disabledoption' title='".JText::_('JOPENSIM_DISABLED_OPTION')."'":" class='jopensim_enabledoption'"; ?>><?php echo $option['name']; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td>
				<?php if($this->canDo->get('core.edit')): ?>
					<!-- Bootstrap Test -->
					<div class="btn-group-vertical" role="group" aria-label="...">
						<a type="button" class="btn btn-default hasTooltip" onClick="moveTopUp(document.getElementById('sortsearchoptions'));return false;" title='move up' alt='move up'><span class="icon-arrow-up" aria-hidden="true" ></span></a>
						<a type="button" class="btn btn-default hasTooltip" onClick="moveTopDown(document.getElementById('sortsearchoptions'));return false;" title='move down' alt='move down'><span class="icon-arrow-down" aria-hidden="true"></span></a>
					</div>
				<?php else: ?>
				&nbsp;
				<?php endif; ?>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
</form>

<h3><?php echo JText::_('JOPENSIM_SEARCH_OVERVIEW'); ?>:</h3>
<table class="table table-striped table-hover adminlist">
<tr>
	<td><nobr><?php echo JText::_('JOPENSIM_SEARCH_REGISTEREDHOSTS'); ?>:</nobr></td>
	<td><?php echo count($this->registeredHosts); ?></td>
	<td>
	<a class='hasTooltip modal' id='registeredhostswindow' href='index.php?option=com_opensim&view=search&task=viewregisteredhosts&tmpl=component' rel="{handler: 'iframe', size: {x: 600, y: 600}, overlayOpacity: 0.3}" title="<?php echo JText::_('JOPENSIM_VIEW'); ?>">
	<span class="btn btn-default icon-eye"></span>
	</a>
	</td>
	<td colspan='2' width='99%'>&nbsp;</td>
</tr>
</table>

<h3><?php echo JText::_('JOPENSIM_SEARCH_DATABASECONTENT'); ?>:</h3>
<table class="table table-striped table-hover adminlist">
<tr>
	<td><nobr><?php echo JText::_('JOPENSIM_SEARCHOBJECTS'); ?>:</nobr></td>
	<td><?php echo $this->searchcount['objects']; ?></td>
	<td>
	<?php if($this->searchcount['objects'] > 0): ?>
	<a class='hasTooltip modal' id='searchdataviewwindow' href='index.php?option=com_opensim&view=search&task=viewsearchdata&searchdata=objects&tmpl=component' rel="{handler: 'iframe', size: {x: 600, y: 600}, overlayOpacity: 0.3}" title="<?php echo JText::_('JOPENSIM_VIEW'); ?>">
	<span class="btn btn-default icon-eye"></span>	
	</a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td>
	<?php if($this->searchcount['objects'] > 0 && $this->canDo->get('core.delete')): ?>
	<a class="btn btn-default icon-purge btn-danger hasTooltip" href='index.php?option=com_opensim&view=search&task=purgedata&searchdata=objects' onClick='return confirm("<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA_SURE'); ?>");' title="<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA'); ?>"></a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td width='99%'>&nbsp;</td>
</tr>
<tr>
	<td><nobr><?php echo JText::_('JOPENSIM_SEARCHPARCELS'); ?>:</nobr></td>
	<td><?php echo $this->searchcount['parcels']; ?></td>
	<td>
	<?php if($this->searchcount['parcels'] > 0): ?>
	<a class='hasTooltip modal' id='searchdataviewwindow' href='index.php?option=com_opensim&view=search&task=viewsearchdata&searchdata=parcels&tmpl=component' rel="{handler: 'iframe', size: {x: 800, y: 600}, overlayOpacity: 0.3}" title="<?php echo JText::_('JOPENSIM_VIEW'); ?>">
	<span class="btn btn-default icon-eye"></span>
	</a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td>
	<?php if($this->searchcount['parcels'] > 0 && $this->canDo->get('core.delete')): ?>
	<a class="btn btn-default icon-purge btn-danger hasTooltip" href='index.php?option=com_opensim&view=search&task=purgedata&searchdata=parcels' onClick='return confirm("<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA_SURE'); ?>");' title="<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA'); ?>"></a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td width='99%'>&nbsp;</td>
</tr>
<tr>
	<td><nobr><?php echo JText::_('JOPENSIM_SEARCHPARCELSALES'); ?>:</nobr></td>
	<td><?php echo $this->searchcount['parcelsales']; ?></td>
	<td>
	<?php if($this->searchcount['parcelsales'] > 0): ?>
	<a class='hasTooltip modal' id='searchdataviewwindow' href='index.php?option=com_opensim&view=search&task=viewsearchdata&searchdata=parcelsales&tmpl=component' rel="{handler: 'iframe', size: {x: 600, y: 600}, overlayOpacity: 0.3}" title="<?php echo JText::_('JOPENSIM_VIEW'); ?>">
	<span class="btn btn-default icon-eye"></span>
	</a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td>
	<?php if($this->searchcount['parcelsales'] > 0 && $this->canDo->get('core.delete')): ?>
	<a class="btn btn-default icon-purge btn-danger hasTooltip" href='index.php?option=com_opensim&view=search&task=purgedata&searchdata=parcelsales' onClick='return confirm("<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA_SURE'); ?>");' title="<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA'); ?>"></a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td width='99%'>&nbsp;</td>
</tr>
<tr>
	<td><nobr><?php echo JText::_('JOPENSIM_SEARCHCLASSIFIED'); ?>:</nobr></td>
	<td><nobr><?php echo $this->searchcount['classifieds']; ?> (<?php echo JText::sprintf('JOPENSIM_SEARCHCLASSIFIED_NUMBER_EXPIRED',$this->searchcount['classifieds_expired']); ?>)</nobr></td>
	<td>
	<?php if($this->searchcount['classifieds'] > 0): ?>
	<a class='hasTooltip modal' id='searchdataviewwindow' href='index.php?option=com_opensim&view=search&task=viewsearchdata&searchdata=classifieds&tmpl=component' rel="{handler: 'iframe', size: {x: 1000, y: 600}, overlayOpacity: 0.3}" title="<?php echo JText::_('JOPENSIM_VIEW'); ?>">
	<span class="btn btn-default icon-eye"></span>
	</a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td>
	<?php if($this->canDo->get('core.delete')): ?>
	<a class="btn btn-default icon-purge btn-warning hasTooltip" href='index.php?option=com_opensim&view=search&task=purgedata&searchdata=oldclassified' onClick='return confirm("<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA_SURE'); ?>");' title="<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA_OLDCLASSIFIED'); ?>"></a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td width='99%'>&nbsp;</td>
</tr>
<tr>
	<td><nobr><?php echo JText::_('JOPENSIM_SEARCHEVENTS'); ?>:</nobr></td>
	<td><nobr><?php echo $this->searchcount['events']; ?> (<?php echo JText::sprintf('JOPENSIM_SEARCHEVENTS_NUMBER_INPAST',$this->searchcount['events_old']); ?>)</nobr></td>
	<td>
	<?php if($this->searchcount['events'] > 0): ?>
	<a class='hasTooltip modal' id='searchdataviewwindow' href='index.php?option=com_opensim&view=search&task=viewsearchdata&searchdata=events&tmpl=component' rel="{handler: 'iframe', size: {x: 1000, y: 600}, overlayOpacity: 0.3}" title="<?php echo JText::_('JOPENSIM_VIEW'); ?>">
	<span class="btn btn-default icon-eye"></span>
	</a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td>
	<?php if($this->canDo->get('core.delete')): ?>
	<a class="btn btn-default icon-purge btn-warning hasTooltip" href='index.php?option=com_opensim&view=search&task=purgedata&searchdata=oldevents' onClick='return confirm("<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA_SURE'); ?>");' title="<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA_OLDEVENTS'); ?>"></a>
	</td>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	<td width='99%'>&nbsp;</td>
</tr>
<tr>
	<td><nobr><?php echo JText::_('JOPENSIM_SEARCHREGIONS'); ?>:</nobr></td>
	<td><?php echo $this->searchcount['regions']; ?></td>
	<td>
	<?php if($this->searchcount['regions'] > 0): ?>
	<a class='hasTooltip modal' id='searchdataviewwindow' href='index.php?option=com_opensim&view=search&task=viewsearchdata&searchdata=regions&tmpl=component' rel="{handler: 'iframe', size: {x: 600, y: 600}, overlayOpacity: 0.3}" title="<?php echo JText::_('JOPENSIM_VIEW'); ?>">
	<span class="btn btn-default icon-eye"></span>
	</a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td>
	<?php if($this->searchcount['regions'] > 0 && $this->canDo->get('core.delete')): ?>
	<a class="btn btn-default icon-purge btn-danger hasTooltip" href='index.php?option=com_opensim&view=search&task=purgedata&searchdata=regions' onClick='return confirm("<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA_SURE'); ?>");' title="<?php echo JText::_('JOPENSIM_SEARCH_PURGEDATA'); ?>"></a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td width='99%'>&nbsp;</td>
</tr>
<tr>
	<td colspan='3'>&nbsp;</td>
	<td>
	<?php if($this->canDo->get('core.edit')): ?>
	<a class="btn btn-default btn-success icon-loop hasTooltip" href='index.php?option=com_opensim&view=search&task=rebuildallhosts' title="<?php echo JText::_('JOPENSIM_SEARCH_REBUILDALLHOSTS'); ?>">
		<i class="hasTooltip" title="<?php echo JText::_('JOPENSIM_SEARCH_REBUILDALLHOSTS'); ?>"></i>
	</a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td width='99%'>&nbsp;</td>
</tr>
</table>
<?php
} else {
	echo JText::_('JOPENSIM_SEARCH_OPTIONERROR');
}
?>
</div>