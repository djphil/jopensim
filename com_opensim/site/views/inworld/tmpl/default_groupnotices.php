<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<?php if($this->settings['addons_groups'] == 1): ?>
<h2><a class="btn btn-danger pull-right" href='index.php?option=com_opensim&view=inworld&task=groupdetail&groupid=<?php echo $this->grouplist['groupid']; ?>&tmpl=component'><?php echo $this->grouplist['groupname']." ".JText::_('GROUPNOTICES'); ?></a></h2>
<?php if($this->grouplist['acceptnotices'] == 1 && $this->grouplist['power']['power_receivenotice'] == 1 && $this->grouplist['hasnotices'] > 0): ?>
<?php if(count($this->noticelist) > 0): ?>
<table class='noticetable<?php echo $this->pageclass_sfx; ?>'>
<?php foreach($this->noticelist AS $notice): ?>
<tr class='noticetable_row<?php echo $this->pageclass_sfx; ?>'>
	<td class='noticetable_cell<?php echo $this->pageclass_sfx; ?>'><?php echo JText::_('NOTICETIME').": ".date(JText::_('OS_DATE')." ".JText::_('OS_TIME'),$notice['Timestamp']); ?></td>
	<td class='noticetable_cell<?php echo $this->pageclass_sfx; ?>'><?php echo JText::_('NOTICEFROM').": ".$notice['FromName']; ?></td>
</tr>
<tr class='noticetable_row<?php echo $this->pageclass_sfx; ?>'>
	<td colspan='2' class='noticetable_cell<?php echo $this->pageclass_sfx; ?>'><?php echo JText::_('NOTICESUBJECT').": ".$notice['Subject']; ?></td>
</tr>
<tr class='noticetable_row<?php echo $this->pageclass_sfx; ?>'>
	<td colspan='2' class='noticetable_cell<?php echo $this->pageclass_sfx; ?>'><?php echo JText::_('NOTICEMESSAGE').":<br />".nl2br($notice['Message']); ?></td>
</tr>
<tr class='noticetable_row_blank<?php echo $this->pageclass_sfx; ?>'>
	<td colspan='2' class='noticetable_cell_blank<?php echo $this->pageclass_sfx; ?>'>&nbsp;</td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>