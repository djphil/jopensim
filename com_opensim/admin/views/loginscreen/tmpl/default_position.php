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

<form action="index.php" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='loginscreen' />
<input type='hidden' name='task' value='savePos' />
<input type='hidden' name='posType' value='<?php echo $this->posType; ?>' />
<input type='hidden' name='posID' value='<?php echo $this->id; ?>' />
<?php if($this->posType == "setX"): ?>
<div class="control-group">
	<div class="control-label">
	<?php echo $this->form->getLabel('alignH'); ?>
	</div>
	<div class="controls">
	<?php echo $this->form->getInput('alignH'); ?>
	</div>
</div>
<?php else: ?>
<div class="control-group">
	<div class="control-label">
	<?php echo $this->form->getLabel('alignV'); ?>
	</div>
	<div class="controls">
	<?php echo $this->form->getInput('alignV'); ?>
	</div>
</div>
<?php endif; ?>
<div class="control-group">
	<div class="control-label">
	<?php echo $this->form->getLabel('distance'); ?>
	</div>
	<div class="controls">
	<?php echo $this->form->getInput('distance'); ?>
	</div>
</div>
<input type="submit" value="<?php echo JText::_('JSAVE'); ?>" name="save" />
</form>