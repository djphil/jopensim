<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
?>
<div class="jopensim-adminpanel">

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php echo JText::_('JOPENSIM_LOGINSCREEN_DISABLED'); ?>
</div>
</div>
