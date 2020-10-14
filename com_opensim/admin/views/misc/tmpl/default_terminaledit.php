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
<!--<h1><?php // echo JText::_('EDITTERMINAL'); ?></h1>-->
<form action="index.php" method="post" id="adminForm" name="adminForm">
    <fieldset>
        <legend><?php echo JText::_('EDITTERMINAL'); ?></legend>
        <input type="hidden" name="option" value="com_opensim" />
        <input type="hidden" name="view" value="misc" />
        <input type="hidden" name="task" value="saveTerminal" />
        <input type="hidden" id="terminalKey" name="terminalKey" value="<?php echo $this->terminal['terminalKey']; ?>" />

        <table class="table table-striped table-hover">
            <colgroup>
                <col width='150' /><col>
            </colgroup>

            <tr>
                <td><label for='terminalName'><?php echo JText::_('TERMINALNAME'); ?>:</label></td>
                <td><input type='text' name='terminalName' id='terminalName' size='25' maxlength='50' value='<?php echo $this->terminal['terminalName']; ?>' /></td>
            </tr>
            <tr>
                <td><label for='terminalDescription'><?php echo JText::_('TERMINALDESCRIPTION'); ?>:</label></td>
                <td><input type='text' name='terminalDescription' id='terminalDescription' size='40' maxlength='255' value='<?php echo $this->terminal['terminalDescription']; ?>' /></td>
            </tr>
            <tr>
                <td colspan='2'><?php echo JText::_('TERMINALLOCATION')." ".$this->terminal['region']; ?>:</td>
            </tr>
            <tr>
                <td colspan='2'>
                <span class="label label-important">X:</span> <input type='text' name='location_x' id='location_x' size='2' maxlength='3' value='<?php echo $this->terminal['location_x']; ?>' class='location_field' /> 
                <span class="label label-success">Y:</span> <input type='text' name='location_y' id='location_y' size='2' maxlength='3' value='<?php echo $this->terminal['location_y']; ?>' class='location_field' />
                <span class="label label-info">X:</span> <input type='text' name='location_z' id='location_z' size='2' maxlength='3' value='<?php echo $this->terminal['location_z']; ?>' class='location_field' />
                </td>
            </tr>
            <tr>
                <td colspan='2'>(<?php echo JText::_('DESCTERMINALLOCATION'); ?>)</td>
            </tr>
            <tr>
                <td><label for='active'><?php echo JText::_('TERMINALACTIVE'); ?>:</label></td>
                <td><input type='checkbox' name='active' id='active' value='1'<?php echo ($this->terminal['active'] == 1) ? " checked='checked'" : ""; ?>'' /></td>
            </tr>
            <tr>
                <td><label for='staticLocation'><?php echo JText::_('AUTOUPDATE'); ?>:</label></td>
                <td><input type='checkbox' name='staticLocation' id='staticLocation' value='1'<?php echo ($this->terminal['staticLocation'] == 0) ? " checked='checked'" : ""; ?>'' /></td>
            </tr>
        </table>
    </fieldset>
</form>
</div>
</div>
