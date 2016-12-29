<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php if($this->cssmsg): ?>
<div class="alert alert-success">
    <a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo $this->cssmsg; ?>
</div>
<?php endif; ?>
<form action="index.php" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='opensim' />
<input type='hidden' name='task' value='' />
<textarea name='csscontent' style='width:100%;height:500px' cols='110' rows='25' class='inputbox'<?php if($this->csswritable === FALSE) echo " readonly='readonly'"; ?>><?php echo $this->csscontent; ?></textarea>
</form>

<div class="alert alert-info">
    <a class="close" data-dismiss="alert" href="#">&times;</a>
	<?php echo JText::_('JOPENSIM_EDITCSS').": ".$this->cssfile; ?>
</div>
</div>