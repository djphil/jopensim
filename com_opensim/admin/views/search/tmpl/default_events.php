<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<table class="table table-striped table-hover adminlist">
<thead>
<tr>
	<th><?php echo JText::_('JOPENSIM_SEARCH_EVENTS_NAME'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_EVENTS_DESCRIPTION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_EVENTS_OWNER'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_EVENTS_CREATOR'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_EVENTS_DATE'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_EVENTS_DURATION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_REGION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_SURL'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_EVENT_FLAGS'); ?></th>
	<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php if(is_array($this->data) && count($this->data) > 0): ?>
<?php foreach($this->data AS $event): ?>
<tr>
	<td><?php echo $event['name']; ?></td>
	<td><?php echo $event['description']; ?></td>
	<td><?php echo $event['ownerName']; ?></td>
	<td><?php echo $event['creatorName']; ?></td>
	<td><?php echo JFactory::getDate($event['dateUTC']); ?></td>
	<td><?php echo $event['duration']; ?></td>
	<td><?php echo $event['simname']; ?></td>
	<td><?php echo $event['surl']; ?></td>
	<td><?php echo $event['eventflags']; ?></td>
	<td>&nbsp;</td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
	<td colspan='10'><?php echo JText::_('JOPENSIM_SEARCH_ERROR_NOEVENTS'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
