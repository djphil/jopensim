<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

 // No direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal');
$row_class = "even";
?>

<h1><?php echo JText::_('JOPENSIM_INWORLD_DETAILS'); ?></h1>

<?php if($this->settings['addons_messages'] == 1): ?>
<table>
<tr>
	<td>
	    <?php echo $this->topbar; ?>
	</td>
</tr>
<tr>
	<td>
	<p><?php echo JText::_('MESSAGES_DESC'); ?></p>
	<?php if(count($this->messages) > 0): ?>
	<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<th class='<?php echo $this->pageclass_sfx; ?>'><?php echo JText::_('MESSAGEFROM'); ?></th>
		<th class='<?php echo $this->pageclass_sfx; ?>'><?php echo JText::_('MESSAGETIME'); ?></th>
		<th class='<?php echo $this->pageclass_sfx; ?>'>&nbsp;</th>
	</tr>
	<?php
	foreach($this->messages AS $message) {
		if($row_class == "odd") $row_class = "even";
		else $row_class = "odd";
	?>
	<tr class='<?php echo $row_class; ?><?php echo $this->pageclass_sfx; ?>'>
		<td class='<?php echo $row_class; ?><?php echo $this->pageclass_sfx; ?>'><?php echo $message['fromAgentName']; ?></td>
		<td class='<?php echo $row_class; ?><?php echo $this->pageclass_sfx; ?>'><?php echo date(JText::_('OS_DATE')." ".JText::_('OS_TIME'),$message['timestamp']); ?></td>
		<td class='<?php echo $row_class; ?><?php echo $this->pageclass_sfx; ?>'>
		    <!-- <span class="icon-search btn-large pull-right"></span> -->
		    <a class='btn btn-default btn-primary btn-xs pull-right modal' id='groupdetailwindow' href='index.php?option=com_opensim&view=inworld&task=messagedetail&imSessionID=<?php echo $message['imSessionID']; ?>&fromAgentID=<?php echo $message['fromAgentID']; ?>&tmpl=component' rel="{handler: 'iframe', size: {x: 500, y: 350}, overlayOpacity: 0.3}" style="position:relative;" alt='<?php echo JText::_('VIEWMESSAGE'); ?>' title='<?php echo JText::_('VIEWMESSAGE'); ?>'>
			    Show
			</a>
		</td>
	</tr>
	<?php } ?>
	</table>
	<?php else: ?>
	<p class="alert alert-warning"><?php echo JText::_('NOMESSAGES'); ?></p>
	<?php endif; ?>
	</td>
</tr>
</table>
<?php endif; ?>
	