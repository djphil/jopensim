<?php
/*
 * @component jOpenSimPayPal
 * @copyright Copyright (C) 2017 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access'); ?>
<form action="index.php" method="post" name="adminForm">
<input type='hidden' name='option' value='com_jopensimpaypal' />
<input type='hidden' name='view' value='jopensimpaypal' />
<input type='hidden' name='task' value='' />
</form>
<h1><?php echo JText::_('COM_JOPENSIMPAYPAL_BUY_IWC'); ?></h1>
<ul>
	<li><?php echo $this->createlink; ?></li>
	<li><?php echo JText::_('COM_JOPENSIMPAYPAL_ERROR_NEEDRELATION_Q2'); ?></li>
</ul>
