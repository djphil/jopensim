<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
$counter = 0;
$i=0;
?>
<?php foreach($this->transactions as $transaction): ?>
<tr class="row<?php echo $i % 2; ?>">
	<td>
	<?php echo ++$counter; ?>
	</td>
	<td>
	<?php echo date(JText::_('JOPENSIM_MONEY_TIMEFORMAT'),$transaction['time']); ?>
	</td>
	<td>
	<?php echo $transaction['sendername']; ?>
	</td>
	<td>
	<?php echo $transaction['receivername']; ?>
	</td>
	<td>
	<?php echo number_format($transaction['amount'],0,JTEXT::_('JOPENSIM_MONEY_SEPERATOR_COMMA'),JTEXT::_('JOPENSIM_MONEY_SEPERATOR_THOUSAND')); ?>
	</td>
	<td>
	<?php echo $transaction['description']; ?>
	</td>
</tr>
<?php
$i++;
?>
<?php endforeach; ?>