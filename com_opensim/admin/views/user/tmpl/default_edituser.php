<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<span class='com_opensim_title'><?php echo $this->ueberschrift; ?></span><?php echo $this->zusatztext; ?><br />
<form action="index.php" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='user' />
<input type='hidden' name='task' value='saveuseredit' />
<input type='hidden' name='userid' value='<?php echo $this->userid; ?>' />
<div class='jopensim_useredittable'>
<div class='jopensim_useredittable_caption'><?php echo JText::_('JOPENSIM_USERDATA'); ?></div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='firstname'><?php echo JText::_('FIRST_NAME'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='text' name='firstname' id='firstname' value='<?php echo $this->firstname; ?>' /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='lastname'><?php echo JText::_('LAST_NAME'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='text' name='lastname' id='lastname' value='<?php echo $this->lastname; ?>' /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='email'><?php echo JText::_('EMAIL'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='text' name='email' id='email' value='<?php echo $this->email; ?>' class="required validate-email" /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='pwd1'><?php echo JText::_('PASSWORD'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='password' name='pwd1' id='pwd1' /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='pwd2'><?php echo JText::_('PASSWORD2'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='password' name='pwd2' id='pwd2' /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='UserLevel'><?php echo JText::_('JOPENSIM_LOGINLEVEL'); ?></label></div>
	<div class='jopensim_useredittable_td2'>
	<select name='UserLevel' id='UserLevel'>
<?php
if(is_array($this->userlevellist) && count($this->userlevellist) > 0) {
	foreach($this->userlevellist AS $userlevel) {
?>
		<option value='<?php echo $userlevel['userlevel']; ?>'<?php echo ($this->userlevel == $userlevel['userlevel']) ? " selected='selected'":''; ?>><?php echo JText::_($userlevel['description']); ?></option>
<?php
	}
} else {
?>
		<option value='0'><?php echo JText::_('JOPENSIM_ERROR_NOUSERLEVEL'); ?></option>
<?php
}
?>
	</select>
	</div>
</div>
</div>
<div class='jopensim_useredittable'>
<div class='jopensim_useredittable_caption'><?php echo JText::_('JOPENSIM_USERFLAGS'); ?></div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='jopensim_usersetting_flag3'><?php echo JText::_('JOPENSIM_USERSETTING_FLAG_PIOF'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='checkbox' name='jopensim_usersetting_flag3' id='jopensim_usersetting_flag3' value='4'<?php echo (($this->userdata['userflags'] & 4) == 4) ? " checked='checked'":""; ?> /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='jopensim_usersetting_flag4'><?php echo JText::_('JOPENSIM_USERSETTING_FLAG_PIU'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='checkbox' name='jopensim_usersetting_flag4' id='jopensim_usersetting_flag4' value='8'<?php echo (($this->userdata['userflags'] & 8) == 8) ? " checked='checked'":""; ?> /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='jopensim_usersetting_flag5'><?php echo JText::_('JOPENSIM_USERSETTING_FLAG_CO'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='checkbox' name='jopensim_usersetting_flag5' id='jopensim_usersetting_flag5' value='16'<?php echo (($this->userdata['userflags'] & 16) == 16) ? " checked='checked'":""; ?> /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='jopensim_usersetting_flag9'><?php echo JText::_('JOPENSIM_USERSETTING_FLAG_TRIAL'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='checkbox' name='jopensim_usersetting_flag9' id='jopensim_usersetting_flag9' value='256'<?php echo (($this->userdata['userflags'] & 256) == 256) ? " checked='checked'":""; ?> /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='jopensim_usersetting_flag10'><?php echo JText::_('JOPENSIM_USERSETTING_FLAG_CHARTERMEMBER'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='checkbox' name='jopensim_usersetting_flag10' id='jopensim_usersetting_flag10' value='512'<?php echo (($this->userdata['userflags'] & 512) == 512) ? " checked='checked'":""; ?> /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='jopensim_usersetting_flag11'><?php echo JText::_('JOPENSIM_USERSETTING_FLAG_EMPLOYEE1'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='checkbox' name='jopensim_usersetting_flag11' id='jopensim_usersetting_flag11' value='1024'<?php echo (($this->userdata['userflags'] & 1024) == 1024) ? " checked='checked'":""; ?> /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='jopensim_usersetting_flag12'><?php echo JText::_('JOPENSIM_USERSETTING_FLAG_EMPLOYEE2'); ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='checkbox' name='jopensim_usersetting_flag12' id='jopensim_usersetting_flag12' value='2048'<?php echo (($this->userdata['userflags'] & 2048) == 2048) ? " checked='checked'":""; ?> /></div>
</div>
<div class='jopensim_useredittable_tr'>
	<div class='jopensim_useredittable_td1'><label for='usertitle' class='tooltip'><?php echo $this->usertitle; ?></label></div>
	<div class='jopensim_useredittable_td2'><input type='text' name='usertitle' id='usertitle' value='<?php echo $this->userdata['usertitle']; ?>' /></div>
</div>
</div>
<br /><br />
</form>
</div>
