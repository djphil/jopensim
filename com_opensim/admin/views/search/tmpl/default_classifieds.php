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
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_NAME'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_DESCRIPTION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_CREATOR'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_CREATIONDATE'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_EXPIRATIONDATE'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_SNAPSHOT'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_REGION'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_SURL'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_FLAGS'); ?></th>
	<th><?php echo JText::_('JOPENSIM_SEARCH_CLASSIFIED_LISTINGPRICE'); ?></th>
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
	<td><?php echo JFactory::getDate($classified['expirationdate']); ?></td>
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
	<td><?php echo $classified['classifiedflags']; ?></td>
	<td><?php echo $classified['priceforlisting']; ?></td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
	<td colspan='6'><?php echo JText::_('JOPENSIM_SEARCH_ERROR_NOCLASSIFIEDS'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
