<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>
<h1><?php echo JText::_('IDENTIFYINWORLD'); ?></h1>
<table cellspacing='0' cellpadding='0' border='0'>
<tr>
	<td>
	<p><?php echo sprintf(JText::_('TERMINALCOMMANDTEXT'),$this->settings['addons_identminutes']); ?>
	<table cellspacing='0' cellpadding='0' border='0'>
	<tr>
		<td><div class='identcommand<?php echo $this->pageclass_sfx; ?>'><?php echo $this->identcommand; ?></div></td>
	</tr>
	</table>
	<?php echo sprintf(JText::_('TERMINALCOMMANDTEXT2'),$this->settings['addons_identminutes']); ?></p>
	</td>
</tr>
</table>
<?php echo JText::_('JOPENSIM_USER_OR'); ?><br /><br />
<?php echo JText::_('CANCELIDENTIFYINWORLDTEXT'); ?>
<table cellspacing='0' cellpadding='0' border='0'>
<tr>
	<td><div class='identifybutton<?php echo $this->pageclass_sfx; ?>'><a href='index.php?option=com_opensim&view=inworld&Itemid=<?php echo $this->Itemid; ?>&task=cancelIdent'><?php echo JText::_('CANCELIDENTIFYINWORLD'); ?></a></div></td>
</tr>
</table>
<br /><br />
<h3><?php echo JText::_('LISTOFTERMINALS'); ?></h3>
<?php if(count($this->terminalList) > 0): ?>
<table class='terminaltable<?php echo $this->pageclass_sfx; ?>' rules='groups'>
<thead>
<tr>
	<th><?php echo JText::_('NAME'); ?></th>
	<th><?php echo JText::_('REGION'); ?></th>
	<th><?php echo JText::_('SURL'); ?></th>
</tr>
</thead>
<tbody>
<?php foreach($this->terminalList AS $terminal): ?>
<tr>
	<td><acronym title='<?php echo $terminal['terminalDescription']; ?>'><?php echo $terminal['terminalName']; ?></acronym></td>
	<td><?php echo $terminal['region']; ?></td>
	<td><a href='<?php echo $terminal['surl']; ?>'><?php echo $terminal['surl']; ?></a></td>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot>
<tr>
	<td colspan='3'><?php echo JText::_('HOWTOSURL'); ?></td>
</tr>
</tfoot>
</table>
<?php else: ?>
<?php echo JText::_('NOTERMINALS'); ?>
<?php endif; ?>