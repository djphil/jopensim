<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
 
defined('_JEXEC') or die('Restricted access'); ?>
<h1><?php echo JText::_('JOPENSIM_CREATE_ACCOUNT'); ?></h1>
<form action='index.php' name='createaccount' method='post'>
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='inworld' />
<input type='hidden' name='task' value='create' />
<input type='hidden' name='email' value='<?php echo $this->user->email; ?>' />
<table cellspacing='3'>
<tr>
	<td><?php echo JText::_('FIRST_NAME'); ?>:</td>
	<td><input type='text' name='firstname' id='firstname' /></td>
</tr>
<tr>
	<td><?php echo JText::_('LAST_NAME'); ?>:</td>
	<td><?php echo $this->lastnamefield; ?></td>
</tr>
<tr>
	<td><?php echo JText::_('EMAIL'); ?>:</td>
	<td><?php echo $this->user->email; ?></td>
</tr>
<tr>
	<td><?php echo JText::_('PASSWORD'); ?>:</td>
	<td><input type='password' name='pwd1' id='pwd1' /></td>
</tr>
<tr>
	<td><?php echo JText::_('PASSWORD2'); ?>:</td>
	<td><input type='password' name='pwd2' id='pwd2' /></td>
</tr>
<tr>
	<td colspan='2'>
	<input type='submit' class='button<?php echo $this->pageclass_sfx; ?>' value='<?php echo JText::_('JOPENSIM_CREATEINWORLDACCOUNT'); ?>' />
	</td>
</tr>
<?php if($this->settings['addons_inworldauth'] == 1): ?>
<tr>
	<td colspan='2'><br /><br /><?php echo JText::_('JOPENSIM_USER_OR'); ?><br /><br /><br /></td>
</tr>
<tr>
	<td colspan='2'><?php echo JText::_('JOPENSIM_DESCIDENTIFYINWORLD'); ?></td>
</tr>
<tr>
	<td colspan='2'>
	<table cellspacing='0' cellpadding='0' border='0'>
	<tr>
		<td><div class='identifybutton<?php echo $this->pageclass_sfx; ?>'><a href='index.php?option=com_opensim&view=inworld&Itemid=<?php echo $this->Itemid; ?>&task=createIdent'><?php echo JText::_('IDENTIFYINWORLD'); ?></a></div></td>
	</tr>
	</table>
	</td>
</tr>
<?php endif; ?>
</table>
</form>
