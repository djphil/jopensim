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
	<th><?php echo JText::_('JOPENSIM_SEARCH_PARCEL_NAME'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_PARCEL_DESCRIPTION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_PARCEL_SEARCHCATEGORY'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_PARCEL_BUILD'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_PARCEL_SCRIPT'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_PARCEL_PUBLIC'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_REGION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_SURL'); ?></th>
	<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php if(is_array($this->data) && count($this->data) > 0): ?>
<?php foreach($this->data AS $parcel): ?>
<tr>
	<td><?php echo $parcel['parcelname']; ?></td>
	<td><?php echo $parcel['description']; ?></td>
	<td><?php echo $this->searchcategory[$parcel['searchcategory']]; ?></td>
	<td><?php echo ($parcel['build'] == "true") ? "<i class='icon-publish' title='".JText::_('JYES')."'></i>":"<i class='icon-unpublish' title='".JText::_('JNO')."'></i>"; ?></td>
	<td><?php echo ($parcel['script'] == "true") ? "<i class='icon-publish' title='".JText::_('JYES')."'></i>":"<i class='icon-unpublish' title='".JText::_('JNO')."'></i>"; ?></td>
	<td><?php echo ($parcel['public'] == "true") ? "<i class='icon-publish' title='".JText::_('JYES')."'></i>":"<i class='icon-unpublish' title='".JText::_('JNO')."'></i>"; ?></td>
	<td><?php echo $parcel['regionName']; ?></td>
	<td><?php echo $parcel['surl']; ?></td>
	<td>&nbsp;</td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
	<td colspan='9'><?php echo JText::_('JOPENSIM_SEARCH_ERROR_NOPARCELS'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
