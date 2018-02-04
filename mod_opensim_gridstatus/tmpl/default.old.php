<?php
/*
 * @module OpenSim Gridstatus v 0.3.0.0
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

defined('_JEXEC') or die('Restricted access'); // no direct access
?>
<?php if($values['gridboxlines'] > 0): ?>
<table width='95%' border='0' class='jOpenSim_gridstatustable<?php echo $pageclass; ?>'>
<?php if($values['gridboxlines'] & 1): ?>
<tr class='jOpenSim_gridstatusrow<?php echo $pageclass; ?>'>
	<td align='left' class='jOpenSim_gridstatuscell<?php echo $pageclass; ?>'><?php echo JText::_('LABEL_GRIDSTATUS'); ?>:</td>
	<td align='right' style='text-align:right;' class='jOpenSim_gridstatuscell<?php echo $pageclass; ?>'><?php echo $values['statusmsg']; ?></td>
</tr>
<?php endif; ?>
<?php if($values['status'] == "online"): ?>
<?php if($values['gridboxlines'] & 2): ?>
<tr class='jOpenSim_gridstatusrow<?php echo $pageclass; ?>'>
	<td align='left' class='jOpenSim_gridstatuscell<?php echo $pageclass; ?>'><?php echo JText::_('LABEL_TOTALREGIONS'); ?>:</td>
	<td align='right' style='text-align:right;' class='jOpenSim_gridstatuscell<?php echo $pageclass; ?>'><?php echo $values['totalregions']; ?></td>
</tr>
<?php endif; ?>
<?php if($values['gridboxlines'] & 4): ?>
<tr class='jOpenSim_gridstatusrow<?php echo $pageclass; ?>'>
	<td align='left' class='jOpenSim_gridstatuscell<?php echo $pageclass; ?>'><?php echo JText::sprintf('LABEL_LASTXDAYS',$values['days']); ?>:</td>
	<td align='right' style='text-align:right;' class='jOpenSim_gridstatuscell<?php echo $pageclass; ?>'><?php echo $values['lastonline']; ?></td>
</tr>
<?php endif; ?>
<?php if($values['gridboxlines'] & 16): ?>
<tr class='jOpenSim_gridstatusrow<?php echo $pageclass; ?>'>
	<td align='left' class='jOpenSim_gridstatuscell<?php echo $pageclass; ?>'><?php echo JText::_('LABEL_TOTALUSERS'); ?>:</td>
	<td align='right' style='text-align:right;' class='jOpenSim_gridstatuscell<?php echo $pageclass; ?>'><?php echo $values['totalusers']; ?></td>
</tr>
<?php endif; ?>
<?php if($values['gridboxlines'] & 8): ?>
<tr class='jOpenSim_gridstatusrow<?php echo $pageclass; ?>'>
	<td align='left' class='jOpenSim_gridstatuscell<?php echo $pageclass; ?>'><?php echo JText::_('LABEL_ONLINENOW'); ?>:</td>
	<td align='right' style='text-align:right;' class='jOpenSim_gridstatuscell<?php echo $pageclass; ?>'><?php echo $values['online']; ?></td>
</tr>
<?php endif; ?>
<?php endif; ?>
</table>
<?php endif; ?>
