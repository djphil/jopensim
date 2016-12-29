<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
 
defined('_JEXEC') or die('Restricted access');

?>
<h2>IM <?php echo JText::_('IMFROM'). " <span>" .$this->fromAgentName; ?></span></h2>

<table class='table table-striped group <?php echo $this->pageclass_sfx; ?>'>
<?php foreach($this->messagedetails AS $timestamp => $messagedetail): ?>
<tr class='<?php echo $this->pageclass_sfx; ?>'>
	<td valign='top' class='<?php echo $this->pageclass_sfx; ?>'><nobr><?php echo date(JText::_('OS_DATE')." ".JText::_('OS_TIME'),$timestamp); ?></nobr></td>
	<td valign='top' class='<?php echo $this->pageclass_sfx; ?>'>&nbsp;&raquo;&nbsp;</td>
	<td valign='top' class='<?php echo $this->pageclass_sfx; ?>'><?php echo $messagedetail; ?></td>
</tr>
<?php endforeach; ?>
</table>
