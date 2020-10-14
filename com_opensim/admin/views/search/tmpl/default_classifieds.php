<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<table class="table table-striped table-hover adminlist">
<thead>
<tr>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_NAME'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_DESCRIPTION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_CREATOR'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_CREATIONDATE'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_EXPIRATIONDATE'); ?></th>
	<th>&nbsp;</th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_SNAPSHOT'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_REGION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_SURL'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_FLAGS'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_LISTINGPRICE'); ?></th>
	<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php if(is_array($this->data) && count($this->data) > 0): ?>
<?php foreach($this->data AS $classified): ?>
<tr>
	<td><?php echo $classified['name']; ?></td>
	<td><?php echo $classified['description']; ?></td>
	<td><?php echo $classified['creatorName']; ?></td>
	<td><?php echo JFactory::getDate($classified['creationdate']); ?></td>
	<td><?php echo JFactory::getDate($classified['expirationdate']); ?> (<?php echo $classified['isexpired']; ?>)</td>
	<td>
	<?php if($this->canDo->get('core.edit') && $classified['isexpired'] === TRUE): ?>
	<a class="btn btn-default btn-success icon-loop hasTooltip" href='index.php?option=com_opensim&view=search&task=renewclassified&classifieduuid=<?php echo $classified['classifieduuid']; ?>' onClick='return confirm("<?php echo JText::_('JOPENSIM_CLASSIFIED_RENEWSURE'); ?>");' title="<?php echo JText::_('JOPENSIM_CLASSIFIED_RENEW'); ?>">
		<i class="hasTooltip" title="<?php echo JText::_('JOPENSIM_CLASSIFIED_RENEW'); ?>"></i>
	</a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td>
	<?php if(($this->settings['getTextureEnabled'] == 1) && $classified['snapshotuuid'] && $classified['snapshotuuid'] != $this->zerouuid): ?>
	<a href='index.php?option=com_opensim&view=opensim&task=gettexture&textureid=<?php echo $classified['snapshotuuid']; ?>&tmpl=component' title="<?php echo JText::_('JOPENSIM_CLASSIFIED_PREVIEWINSIGNIA'); ?>">
	<i class="icon-picture" title="<?php echo JText::_('JOPENSIM_CLASSIFIED_PREVIEWINSIGNIA'); ?>"></i>
	</a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
	<td><?php echo $classified['simname']; ?></td>
	<td><?php echo $classified['surl']; ?></td>
	<td><nobr>
	<?php if(($classified['classifiedflags'] & 8) == 8): ?>
	<span class="label label-info" title="<?php echo JText::_('JOPENSIM_MATURE'); ?>">M</span>
	<?php else: ?>
	<span class="label label-default" title="<?php echo JText::_('JOPENSIM_GENERAL'); ?>">G</span>
	<?php endif; ?>
	<?php if(($classified['classifiedflags'] & 32) == 32): ?>
	<span class="label label-success" title="<?php echo JText::_('JOPENSIM_CLASSIFIED_AUTORENEW'); ?>"><i class="icon-loop" style="width:auto;margin-right:0px;"></i></span>
	<?php endif; ?>
	</nobr></td>
	<td><?php echo $classified['priceforlisting']; ?></td>
	<td>
	<?php if($this->canDo->get('core.delete')): ?>
	<a class="btn btn-default icon-purge btn-danger hasTooltip" href='index.php?option=com_opensim&view=search&task=deleteclassified&classifieduuid=<?php echo $classified['classifieduuid']; ?>' onClick='return confirm("<?php echo JText::_('JOPENSIM_CLASSIFIED_DELETESURE'); ?>");' title="<?php echo JText::_('JOPENSIM_CLASSIFIED_DELETE'); ?>"></a>
	<?php else: ?>
	&nbsp;
	<?php endif; ?>
	</td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
	<td colspan='8'><?php echo JText::_('JOPENSIM_SEARCH_ERROR_NOCLASSIFIEDS'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
