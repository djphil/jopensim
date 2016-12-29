<?php
/*
 * @component jOpenSim Component
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
	<th colspan='6'>
	<?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONHISTORY'); ?>
	</th>
</tr>
<tr>
	<th width="5">
	&nbsp;
	</th>
	<th class='title'>
	<a data-original-title="<strong><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_DATE'); ?></strong><br />Click to sort by this column" href="#" onclick="Joomla.tableMoneyOrdering('#__opensim_moneytransactions.time','<?php echo ($this->sortDirection == "asc") ? "desc":"asc"; ?>','');return false;" class="hasTooltip" title=""><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_DATE'); ?><?php if($this->sortColumn == "#__opensim_moneytransactions.time"): ?><i class="icon-arrow-<?php echo ($this->sortDirection == "asc") ? "up":"down"; ?>-3"></i><?php endif; ?></a>
	</th>
	<th>
	<a data-original-title="<strong><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_SENDER'); ?></strong><br />Click to sort by this column" href="#" onclick="Joomla.tableMoneyOrdering('#__opensim_moneytransactions.sender','<?php echo ($this->sortDirection == "asc") ? "desc":"asc"; ?>','');return false;" class="hasTooltip" title=""><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_SENDER'); ?><?php if($this->sortColumn == "#__opensim_moneytransactions.sender"): ?><i class="icon-arrow-<?php echo ($this->sortDirection == "asc") ? "up":"down"; ?>-3"></i><?php endif; ?></a>
	</th>
	<th>
	<a data-original-title="<strong><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_RECEIVER'); ?></strong><br />Click to sort by this column" href="#" onclick="Joomla.tableMoneyOrdering('#__opensim_moneytransactions.receiver','<?php echo ($this->sortDirection == "asc") ? "desc":"asc"; ?>','');return false;" class="hasTooltip" title=""><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_RECEIVER'); ?><?php if($this->sortColumn == "#__opensim_moneytransactions.receiver"): ?><i class="icon-arrow-<?php echo ($this->sortDirection == "asc") ? "up":"down"; ?>-3"></i><?php endif; ?></a>
	</th>
	<th>
	<a data-original-title="<strong><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_AMOUNT'); ?></strong><br />Click to sort by this column" href="#" onclick="Joomla.tableMoneyOrdering('#__opensim_moneytransactions.amount','<?php echo ($this->sortDirection == "asc") ? "desc":"asc"; ?>','');return false;" class="hasTooltip" title=""><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_AMOUNT'); ?><?php if($this->sortColumn == "#__opensim_moneytransactions.amount"): ?><i class="icon-arrow-<?php echo ($this->sortDirection == "asc") ? "up":"down"; ?>-3"></i><?php endif; ?></a>
	</th>
	<th>
	<a data-original-title="<strong><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_DESC'); ?></strong><br />Click to sort by this column" href="#" onclick="Joomla.tableMoneyOrdering('#__opensim_moneytransactions.description','<?php echo ($this->sortDirection == "asc") ? "desc":"asc"; ?>','');return false;" class="hasTooltip" title=""><?php echo JText::_('JOPENSIM_MONEY_TRANSACTIONS_DESC'); ?><?php if($this->sortColumn == "#__opensim_moneytransactions.description"): ?><i class="icon-arrow-<?php echo ($this->sortDirection == "asc") ? "up":"down"; ?>-3"></i><?php endif; ?></a>
	</th>
</tr>