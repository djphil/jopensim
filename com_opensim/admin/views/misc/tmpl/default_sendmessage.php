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
<div class="form-inline">
    <form action="index.php" method="post" id="adminForm" name="adminForm">
        <fieldset>
		    <legend><?php echo JText::_('SENDGLOBALMESSAGE'); ?></legend>
            <input type="hidden" name="option" value="com_opensim" />
            <input type="hidden" name="view" value="misc" />
            <input type="hidden" name="task" value="sendoutmessage" />
            <textarea class="form-control"  name='message'></textarea>
            <button type='submit' class='btn btn-default btn-success' />
                <span class='icon-checkmark'></span> <?php echo JText::_('SENDMESSAGE'); ?>
            </button>
		</fieldset>
    </form>
</div>
</div>