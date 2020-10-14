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
	<th><?php echo JText::_('JOPENSIM_SEARCH_OBJECT_NAME'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_OBJECT_DESCRIPTION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_PARCEL'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_REGION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_SURL'); ?></th>
	<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php if(is_array($this->data) && count($this->data) > 0): ?>
<?php foreach($this->data AS $object): ?>
<tr>
	<td><?php echo $object['name']; ?></td>
	<td><?php echo $object['description']; ?></td>
	<td><?php echo $object['parcelName']; ?></td>
	<td><?php echo $object['regionName']; ?></td>
	<td><?php echo $object['surl']; ?></td>
	<td>&nbsp;</td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
	<td colspan='6'><?php echo JText::_('JOPENSIM_SEARCH_ERROR_NOOBJECTS'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
