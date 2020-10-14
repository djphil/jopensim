<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="jopensim-adminpanel">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>

<div id="j-main-container" class="span10">
<div class="form-inline">
    <form action="index.php" method="post" id="adminForm" name="adminForm">
        <fieldset>
		    <legend><?php echo JText::_('JOPENSIM_SENDMESSAGE'); ?></legend>
            <input type="hidden" name="option" value="com_opensim" />
            <input type="hidden" name="view" value="misc" />
            <input type="hidden" name="task" value="sendoutmessage" />
            <?php if($this->settings['remoteadminsystem'] == "multiple"): ?>
            <input type="hidden" name="radminsystem" value="multiple" />
            <?php if(is_array($this->simulators) && count($this->simulators) > 0): ?>
            <?php echo JText::_('JOPENSIM_REMOTEADMINSYSTEM_MULTIPLE_SENDTO'); ?>:&nbsp;<select name='simulator'>
            <?php foreach($this->simulators AS $simulator): ?>
            	<option value='<?php echo $simulator['simulator']; ?>' title='<?php echo $simulator['regions']; ?>'><?php echo ($simulator['alias']) ? $simulator['alias']:$simulator['simulator']; ?></option>
            <?php endforeach; ?>
	        </select>
            <?php else: ?>
            <input type="hidden" name="simulator" value="" />
            <?php echo JText::_('JOPENSIM_REMOTEADMINSYSTEM_MULTIPLE_ERROR_NOSIMULATORS'); ?>
            <?php endif; ?>
            <?php else: ?>
            <input type="hidden" name="radminsystem" value="single" />
            <?php endif; ?>
            <textarea class="form-control"  name='message'></textarea>
            <button type='submit' class='btn btn-default btn-success' />
                <span class='icon-checkmark'></span> <?php echo JText::_('JOPENSIM_SENDMESSAGE'); ?>
            </button>
		</fieldset>
    </form>
</div>
</div>
</div>
