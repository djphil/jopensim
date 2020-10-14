<?php
/*
 * @package Joomla 2.5
 * @copyright Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html, see LICENSE.php
 *
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2013 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" name="adminForm">
<input type='hidden' name='option' value='com_jopensimpaypal' />
<input type='hidden' name='view' value='jopensimpaypal' />
<input type='hidden' name='task' value='' />
</form>
<h1><?php echo JText::_('COM_JOPENSIMPAYPAL_MENU_OVERVIEW'); ?></h1>
<?php if($this->newtransactions > 0 || $this->newpayouts > 0 || (is_array($this->unsolvedpayouts) && count($this->unsolvedpayouts) > 0)): ?>
<ul class='jopensimpaypal_overviewlist'>
<?php if($this->newtransactions > 0): ?>
	<li class='jopensimpaypal_overviewlistitem'><a href='index.php?option=com_jopensimpaypal&view=transactions'><?php echo JText::sprintf('COM_JOPENSIMPAYPAL_NEWTRANSACTIONS',"<span class='jopensimpaypal_overview_transactionnumber'>".$this->newtransactions."</span>"); ?></a></li>
<?php endif; ?>
<?php if($this->newpayouts > 0): ?>
	<li class='jopensimpaypal_overviewlistitem'><a href='index.php?option=com_jopensimpaypal&view=payout'><?php echo JText::sprintf('COM_JOPENSIMPAYPAL_NEWPAYOUTS',"<span class='jopensimpaypal_overview_payoutnumber'>".$this->newpayouts."</span>"); ?></a></li>
<?php endif; ?>
<?php if(is_array($this->unsolvedpayouts) && count($this->unsolvedpayouts) > 0): ?>
	<li class='jopensimpaypal_overviewlistitem'><a href='index.php?option=com_jopensimpaypal&view=payout&filter.payoutstatus=unsolved'><?php echo JText::_('COM_JOPENSIMPAYPAL_UNSOLVEDPAYOUTS'); ?>:</a></li>
	<ul class='jopensimpaypal_overviewlist2'>
		<?php foreach($this->unsolvedpayouts AS $unsolved): ?>
		<li class='jopensimpaypal_overviewlistitem2'><a href='index.php?option=com_jopensimpaypal&view=payout&filter.payoutstatus=<?php echo $unsolved->filter; ?>'><?php echo "<span class='jopensimpaypal_overview_unsolvednumber'>".$unsolved->anzahl."</span> ".JText::_($unsolved->payoutstatus); ?></a></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
</ul>
<?php endif; ?>