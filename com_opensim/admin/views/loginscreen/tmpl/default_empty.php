<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<form action="index.php" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='loginscreen' />
<input type='hidden' name='task' value='list' />
<input type='hidden' name='boxchecked' value='0' />
</form>
<p><?php echo JText::_('JOPENSIM_LOGINSCREEN_EMPTY'); ?></p>
<p><?php echo JText::sprintf('LOGINSCREEN_HELP_HINT',LOGINSCREEN_HELP_LINK); ?></p>
</div>