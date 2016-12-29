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
	<th><?php echo JText::_('JOPENSIM_SEARCH_REGION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_REGION_OWNER'); ?></th>
</tr>
</thead>
<tbody>
<?php if(is_array($this->data) && count($this->data) > 0): ?>
<?php foreach($this->data AS $region): ?>
<tr>
	<td><?php echo $region['regionname']; ?></td>
	<td><?php echo $region['owner']; ?></td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
	<td colspan='2'><?php echo JText::_('JOPENSIM_SEARCH_ERROR_NOREGIONS'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
