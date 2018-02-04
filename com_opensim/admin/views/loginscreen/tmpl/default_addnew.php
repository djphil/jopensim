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
<div class="control-group">
	<div class="control-label">
	<?php echo $this->form->getLabel('id'); ?>
	</div>
	<div class="controls">
	<?php echo $this->form->getInput('id'); ?>
	</div>
</div>
<div class="control-group">
	<div class="control-label">
	<?php echo $this->form->getLabel('positionname'); ?>
	</div>
	<div class="controls">
	<?php echo $this->form->getInput('positionname'); ?>
	</div>
	<div class="control-label">
	<?php echo $this->form->getLabel('alignH'); ?>
	</div>
	<div class="controls">
	<?php echo $this->form->getInput('alignH'); ?>
	</div>
	<div class="control-label">
	<?php echo $this->form->getLabel('posX'); ?>
	</div>
	<div class="controls">
	<?php echo $this->form->getInput('posX'); ?>
	</div>
	<div class="control-label">
	<?php echo $this->form->getLabel('alignV'); ?>
	</div>
	<div class="controls">
	<?php echo $this->form->getInput('alignV'); ?>
	</div>
	<div class="control-label">
	<?php echo $this->form->getLabel('posY'); ?>
	</div>
	<div class="controls">
	<?php echo $this->form->getInput('posY'); ?>
	</div>
	<div class="control-label">
	<?php echo $this->form->getLabel('zindex'); ?>
	</div>
	<div class="controls">
	<?php echo $this->form->getInput('zindex'); ?>
	</div>
</div>

</form>
</div>