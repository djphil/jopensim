<?php
// No direct access to this file
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
	<td>
	<?php echo $item->name; ?>
	</td>
	<td>
	<?php echo $item->payer_email; ?>
	</td>
	<td class='jopensimpaypal_numbercell'>
	<?php echo $item->symbol." ".number_format($item->amount_rlc,2,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND')); ?>
	</td>
	<td class='jopensimpaypal_numbercell'>
	<?php echo $item->symbol." ".number_format($item->paypalfee,2,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND')); ?>
	</td>
	<td class='jopensimpaypal_numbercell'>
	<?php echo $item->symbol." ".number_format(($item->amount_rlc - $item->paypalfee),2,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND')); ?>
	</td>
	<td class='jopensimpaypal_numbercell'>
	<?php echo number_format($item->amount_iwc,0,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND')); ?>
	</td>
	<td class='jopensimpaypal_numbercell'>
	<?php echo number_format($item->iwbalance,0,JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_COMMA'),JText::_('COM_JOPENSIMPAYPAL_SEPERATOR_THOUSAND')); ?>
	</td>
	<td>
	<?php echo $item->transactiontime; ?>
	</td>
</tr>
<?php endforeach; ?>