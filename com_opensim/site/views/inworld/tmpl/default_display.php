<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<h1 class='<?php echo $this->pageclass_sfx; ?>'><?php echo JText::_('JOPENSIM_INWORLD_DETAILS'); ?></h1>

<table>
<tr>
	<td>
	<?php echo $this->topbar; ?>
	</td>
</tr>
<tr>
	<td>
	<form class="form-group" name='opensimdetails' action='index.php' method='post'>
	<input type='hidden' name='option' value='com_opensim' />
	<input type='hidden' name='view' value='inworld' />
	<input type='hidden' name='task' value='update' />
	<input type='hidden' name='uuid' value='<?php echo $this->osdata['uuid']; ?>' />
	<input type='hidden' name='Itemid' value='<?php echo $this->Itemid; ?>' />
	<input type='hidden' name='oldlastname' value='<?php echo $this->osdata['lastname']; ?>' />
	<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td class='<?php echo $this->pageclass_sfx; ?>'><label for='firstname'><?php echo JText::_('FIRST_NAME'); ?>:</label></td>
		<td class='<?php echo $this->pageclass_sfx; ?>'><?php echo ($this->settings['userchange_firstname'] == 1) ? "<input type='text' name='firstname' id='firstname' value='".$this->osdata['firstname']."' />":$this->osdata['firstname']; ?></td>
	</tr>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td class='<?php echo $this->pageclass_sfx; ?>'><label for='lastname'><?php echo JText::_('LAST_NAME'); ?>:</label></td>
		<td class='<?php echo $this->pageclass_sfx; ?>'><?php echo ($this->settings['userchange_lastname'] == 1) ? $this->lastnamefield:$this->osdata['lastname']; ?></td>
	</tr>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td class='<?php echo $this->pageclass_sfx; ?>'><label for='email'><?php echo JText::_('EMAIL'); ?>:</label></td>
		<td class='<?php echo $this->pageclass_sfx; ?>'><?php echo ($this->settings['userchange_email'] == 1) ? "<input type='text' name='email' id='email' value='".$this->osdata['email']."' />":$this->osdata['email']; ?></td>
	</tr>
	<?php if($this->settings['addons_messages'] == 1): ?>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td class='<?php echo $this->pageclass_sfx; ?>'><input type='checkbox' name='im2email' id='im2email' value='1' <?php echo ($this->osdata['im2email'] == 1) ? " checked='checked'":""; ?>/></td>
		<td class='<?php echo $this->pageclass_sfx; ?>'><label for='im2email'><?php echo JText::_('SENDMESSAGE2EMAIL'); ?></label></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td valign='top'><label for='timezone'><?php echo JText::_('JOPENSIM_TIMEZONE'); ?>:</label></td>
		<td>
		<select name='timezone' id='timezone' >
<?php if(is_array($this->timezones) && count($this->timezones) > 0): ?>
<?php foreach($this->timezones AS $timezone): ?>
			<option value='<?php echo $timezone; ?>' <?php echo ($timezone == $this->osdata['timezone']) ? " selected='selected'":""; ?>><?php echo $timezone; ?></option>
<?php endforeach; ?>
<?php else: ?>
			<option value='???'><?php echo JText::_('JOPENSIM_TIMEZONE_ERROR'); ?></option>
<?php endif; ?>
		</select>
		</td>
	</tr>
	<?php if($this->settings['userchange_password'] == 1): ?>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td class='<?php echo $this->pageclass_sfx; ?>'><label for='pwd1'><?php echo JText::_('PASSWORD'); ?>:</label></td>
		<td class='<?php echo $this->pageclass_sfx; ?>'><input type='password' id='pwd1' name='pwd1' value='' /></td>
	</tr>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td class='<?php echo $this->pageclass_sfx; ?>'><label for='pwd2'><?php echo JText::_('PASSWORD2'); ?>:</label></td>
		<td class='<?php echo $this->pageclass_sfx; ?>'><input type='password' id='pwd2' name='pwd2' value='' /></td>
	</tr>
	<?php endif; ?>
	<?php if($this->settings > 0): ?>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td colspan='2' class='<?php echo $this->pageclass_sfx; ?>'>&nbsp;</td>
	</tr>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td colspan='2' class='<?php echo $this->pageclass_sfx; ?>'>
		    <!-- <span class="icon-save"></span> -->
    		<button type='submit' class="btn btn-default btn-primary"/><?php echo JText::_('JOPENSIM_SAVECHANGES'); ?></button>
		</td>
	</tr>
	<?php endif; ?>
	</table>
	</form>
	</td>
</tr>
</table>
