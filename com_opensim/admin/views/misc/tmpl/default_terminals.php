<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<h3><?php echo JText::_('LISTOFTERMINALS'); ?></h3>
<?php if(count($this->terminalList) > 0): ?>
<form action="index.php" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='misc' />
<input type='hidden' name='task' value='updateterminal' />
<input type='hidden' name='boxchecked' value='0' />
<table class="adminlist" cellspacing="1">
<thead>
<tr>
	<th width="5"><?php echo JText::_('Num'); ?></th>
	<th class='title' width='10'>&nbsp;</td>
	<th><?php echo JText::_('NAME'); ?></th>
	<th><?php echo JText::_('REGION'); ?></th>
	<th><?php echo JText::_('SURL'); ?></th>
	<th><?php echo JText::_('ACTIVE'); ?></th>
	<th><?php echo JText::_('AUTOUPDATE'); ?></th>
	<th><?php echo JText::_('PINGTERMINAL'); ?></th>
</tr>
</thead>
<tbody>
<?php foreach($this->terminalList AS $key => $terminal): ?>
<tr>
	<td><?php echo ($key+1); ?></td>
	<td width='10'><input type='checkbox' name='checkTerminal[]' id='terminal_<?php echo $terminal['terminalKey']; ?>' value='<?php echo $terminal['terminalKey']; ?>' onClick='Joomla.isChecked(this.checked);' /></td>
	<td><label for='terminal_<?php echo $terminal['terminalKey']; ?>'><acronym title='<?php echo $terminal['terminalDescription']; ?>'><?php echo $terminal['terminalName']; ?></acronym></label></td>
	<td><?php echo $terminal['region']; ?></td>
	<td><a href='<?php echo $terminal['surl']; ?>'><?php echo $terminal['surl']; ?></a></td>
	<td class="jgrid" align='center'><a href='index.php?option=com_opensim&view=misc&task=toggleTerminal&terminalKey=<?php echo $terminal['terminalKey']; ?>'><span class='state <?php echo ($terminal['active'] == 1) ? "publish":"unpublish"; ?>'><span class='text'><?php echo ($terminal['active'] == 1) ? JText::_('TERMINALACTIVE'):JText::_('TERMINALINACTIVE'); ?></span></span></a></td>
	<td class="jgrid" align='center'><a href='index.php?option=com_opensim&view=misc&task=saveTerminalStatic&terminalKey=<?php echo $terminal['terminalKey']; ?>&staticValue=<?php echo ($terminal['staticLocation'] == 1) ? "0":"1"; ?>'><span class='state <?php echo ($terminal['staticLocation'] == 0) ? "publish":"unpublish"; ?>'><span class='text'><?php echo ($terminal['staticLocation'] == 0) ? JText::_('TERMINALAUTOUPDATE'):JText::_('TERMINALNOAUTOUPDATE'); ?></span></span></a></td>
	<td align='center'><a class='modal' id='terminalpingwindow' href='index.php?option=com_opensim&view=misc&task=pingTerminal&terminalKey=<?php echo $terminal['terminalKey']; ?>&tmpl=component' rel="{handler: 'iframe', size: {x: 300, y: 200}, overlayOpacity: 0.3}"><img src='<?php echo $this->assetpath; ?>images/terminalping.png' width='19' height='16' alt='<?php echo sprintf(JText::_('PINGTERMINAL2'),$terminal['terminalName'])." (".$terminal['terminalUrl'].")"; ?>' border='0' title='<?php echo sprintf(JText::_('PINGTERMINAL2'),$terminal['terminalName'])." (".$terminal['terminalUrl'].")"; ?>' /></a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</form>
<?php else: ?>
<?php echo JText::_('NOTERMINALS'); ?>
<?php endif; ?>