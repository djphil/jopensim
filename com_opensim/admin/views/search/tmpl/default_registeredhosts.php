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
	<th><?php echo JText::_('JOPENSIM_SEARCH_HOST'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_PORT'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_REGISTERED'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_LASTCHECK'); ?></th>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
</tr>
</thead>
<tbody>
<?php if(is_array($this->registeredHosts) && count($this->registeredHosts) > 0): ?>
<?php foreach($this->registeredHosts AS $registeredhost): ?>
<tr>
	<td><?php echo $registeredhost['host']; ?></td>
	<td><?php echo $registeredhost['port']; ?></td>
	<td><?php echo JFactory::getDate($registeredhost['register']); ?></td>
	<td><?php echo JFactory::getDate($registeredhost['lastcheck']); ?></td>
	<td><a href='index.php?option=com_opensim&view=search&task=removehost&host=<?php echo $registeredhost['host']; ?>&port=<?php echo $registeredhost['port']; ?>' onClick='return confirm("<?php echo JText::_('JOPENSIM_SEARCH_REMOVEHOST_SURE'); ?>");' class="btn btn-micro hasTooltip"><i class="icon-unpublish" title="<?php echo JText::_('JOPENSIM_SEARCH_REMOVEHOST'); ?>"></i></a></td>
	<td><a href='index.php?option=com_opensim&view=search&task=rebuildhost&host=<?php echo $registeredhost['host']; ?>&port=<?php echo $registeredhost['port']; ?>' class="btn btn-micro hasTooltip" title="<?php echo JText::_('JOPENSIM_SEARCH_REBUILDHOST'); ?>"><i class="jopensim-icon-refresh" title="<?php echo JText::_('JOPENSIM_SEARCH_REBUILDHOST'); ?>"></i></a></td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
	<td colspan='6'><?php echo JText::_('JOPENSIM_SEARCH_ERROR_NOHOSTS'); ?></td>
</tr>
<?php endif; ?>
<tbody>
</tbody>
</table>
