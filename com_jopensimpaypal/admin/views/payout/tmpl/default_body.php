<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted Access');
$counter = 0;
?>
<?php foreach($this->items as $i => $item): ?>
<tr class="row<?php echo $i % 2; ?>">
	<td>
	<?php echo ++$counter; ?>
	</td>
	<td>
	<?php echo JHtml::_('grid.id', $i, $item->id); ?>
	</td>
	<td>
	<?php echo $item->opensimname; ?>
	</td>
	<td>
	<?php echo $item->joomlausername; ?>
	</td>
	<td<?php if($item->status < 1) echo " class='jopensimpaypalstatus_".$item->paypalstatus."'"; ?>>
	<?php echo $item->paypalaccount; ?>
	</td>
	<td class='jopensimpaypal_numbercell'>
	<?php echo number_format($item->amount_iwc,0,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND')); ?>
	</td>
	<td class='jopensimpaypal_numbercell'>
	<?php echo $item->symbol." ".number_format($item->amount_rlc,2,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND')); ?>
	</td>
	<td<?php echo ($item->status < 2 && $item->status > -2) ? " class='jopensimpayoutstatus_".$item->payoutstatus." jopensimpaypal_numbercell'":" class='jopensimpaypal_numbercell'"; ?>>
	<?php
	echo $item->iwbalance;
//	echo number_format(intval($item->iwbalance),0,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND'));
	?>
	</td>
	<td>
	<?php echo $item->requesttime; ?>
	</td>
	<td class="jgrid" align="center">
	<a class='modal' id='payoutstatuschange' href='index.php?option=com_jopensimpaypal&view=payout&task=changestatus&payoutID=<?php echo $item->id; ?>&tmpl=component' rel="{handler: 'iframe', size: {x: 400, y: 400}, overlayOpacity: 0.3}">
	<?php echo $item->payoutsymbol; ?>
	</a>
	</td>
</tr>
<?php endforeach; ?>