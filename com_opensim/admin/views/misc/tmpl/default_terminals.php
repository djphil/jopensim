<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="jopensim-adminpanel">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>

<div id="j-main-container" class="span10">
	<?php if(count($this->terminalList) > 0): ?>
	<form action="index.php" method="post" id="adminForm" name="adminForm">
		<fieldset>
			<legend><?php echo JText::_('LISTOFTERMINALS'); ?></legend>
			<input type='hidden' name='option' value='com_opensim' />
			<input type='hidden' name='view' value='misc' />
			<input type='hidden' name='task' value='updateterminal' />
			<input type='hidden' name='boxchecked' value='0' />

			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>#</th>
						<th class='title' width='10'>&nbsp;</td>
						<th><?php echo JText::_('Name'); ?></th>
						<th><?php echo JText::_('Region'); ?></th>
						<th><?php echo JText::_('sUrl'); ?></th>
						<th><?php echo JText::_('Active'); ?></th>
						<th><?php echo JText::_('AUTOUPDATE'); ?></th>
						<th><?php echo JText::_('PINGTERMINAL'); ?></th>
					</tr>
				</thead>

				<tbody>
					<?php foreach($this->terminalList AS $key => $terminal): ?>
					<tr>
						<td><?php echo ($key + 1); ?></td>
						<td><input type='checkbox' name='checkTerminal[]' id='terminal_<?php echo $terminal['terminalKey']; ?>' value='<?php echo $terminal['terminalKey']; ?>' onClick='Joomla.isChecked(this.checked);' /></td>
						<td><label for='terminal_<?php echo $terminal['terminalKey']; ?>'><acronym title='<?php echo $terminal['terminalDescription']; ?>'> <?php echo $terminal['terminalName']; ?></acronym></label></td>
						<td><?php echo $terminal['region']; ?></td>
						<td><a href='<?php echo $terminal['surl']; ?>'><?php echo $terminal['surl']; ?></a></td>

						<td class="jgrid">
							<?php if($this->canDo->get('core.edit')): ?>
							<a href='index.php?option=com_opensim&view=misc&task=toggleTerminal&terminalKey=<?php echo $terminal['terminalKey']; ?>'>
							<?php endif; ?>
							<span class='state <?php echo ($terminal['active'] == 1) ? "publish":"unpublish"; ?>'></span>
							<span class='text'><?php echo ($terminal['active'] == 1) ? JText::_('TERMINALACTIVE'): JText::_('TERMINALINACTIVE'); ?></span>
							<?php if($this->canDo->get('core.edit')): ?>
							</a>
							<?php endif; ?>
						</td>

						<td class="jgrid">
							<?php if($this->canDo->get('core.edit')): ?>
							<a href='index.php?option=com_opensim&view=misc&task=saveTerminalStatic&terminalKey=<?php echo $terminal['terminalKey']; ?>&staticValue=<?php echo ($terminal['staticLocation'] == 1) ? "0":"1"; ?>'>
							<?php endif; ?>
							<span class='state <?php echo ($terminal['staticLocation'] == 0) ? "publish":"unpublish"; ?>'></span>
							<span class='text'><?php echo ($terminal['staticLocation'] == 0) ? JText::_('TERMINALAUTOUPDATE'):JText::_('TERMINALNOAUTOUPDATE'); ?></span>
							<?php if($this->canDo->get('core.edit')): ?>
							</a>
							<?php endif; ?>
						</td>

						<td>
							<!--
								<a class='modal' id='terminalpingwindow' href='index.php?option=com_opensim&view=misc&task=pingTerminal&terminalKey=<?php // echo $terminal['terminalKey']; ?>&tmpl=component' rel="{handler: 'iframe', size: {x: 300, y: 200}, overlayOpacity: 0.3}"><img src='<?php // echo $this->assetpath; ?>images/terminalping.png' width='19' height='16' alt='<?php // echo sprintf(JText::_('PINGTERMINAL2'),$terminal['terminalName'])." (".$terminal['terminalUrl'].")"; ?>' border='0' title='<?php // echo sprintf(JText::_('PINGTERMINAL2'), $terminal['terminalName'])." (".$terminal['terminalUrl'].")"; ?>' /></a>
							-->
							<a class='modal modal-big' rel="{handler: 'iframe', size: {x: 400, y: 100}}" id='terminalpingwindow' href='index.php?option=com_opensim&view=misc&task=pingTerminal&terminalKey=<?php echo $terminal['terminalKey']; ?>&tmpl=component' rel="{handler: 'iframe', size: {x: 300, y: 200}, overlayOpacity: 0.3}"><span class="icon-eye" style="font-size: 24px;" alt='<?php echo sprintf(JText::_('PINGTERMINAL2'),$terminal['terminalName'])." (".$terminal['terminalUrl'].")"; ?>' title='<?php echo sprintf(JText::_('PINGTERMINAL2'), $terminal['terminalName'])." (".$terminal['terminalUrl'].")"; ?>'></span></a>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</fieldset>
	</form>
	<?php else: ?>
	<?php echo JText::_('NOTERMINALS'); ?>
	<?php endif; ?>
</div>
</div>
