<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<h1><?php echo JText::_('JOPENSIM_PROFILE_DETAILS'); ?></h1>

<?php if($this->settingsdata['addons'] & 2): ?>
<table>
<tr>
	<td>
	<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
	<tr>
		<td><?php echo JText::_('JOPENSIM_PROFILE_NAME'); ?>:</td>
		<td><?php echo $this->profiledata['firstname']." ".$this->profiledata['lastname']; ?></td>
	</tr>
	<?php if($this->image2nd): ?>
	<tr>
	    <td><?php echo JText::_('JOPENSIM_PROFILE_2NDLIFE'); ?>:</td>
		<td colspan='2'><?php echo $this->image2nd; ?></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td><?php echo JText::_('JOPENSIM_PROFILE_ABOUTME'); ?>:</td>
		<td><?php echo nl2br($this->profiledata['aboutText']); ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('JOPENSIM_PROFILE_PARTNER'); ?>:</td>
		<td><?php echo $this->profiledata['partnername']; ?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('JOPENSIM_PROFILE_URL'); ?>:</td>
		<td><?php echo $this->profiledata['url']; ?></td>
	</tr>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td colspan='2'><?php echo JText::_('JOPENSIM_PROFILE_INTERESTS'); ?>:</td>
	</tr>
	<tr>
		<td><?php echo JText::_('JOPENSIM_PROFILE_WANTTO'); ?>:</td>
		<td>
		
		<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
		<tr class='<?php echo $this->pageclass_sfx; ?>'>
			<td><?php echo JText::_('JOPENSIM_WANTTO_BUILD'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['wantmask'] & $this->wantmask['build']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['build']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['build']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
			<td>&nbsp;</td>
			<td><?php echo JText::_('JOPENSIM_WANTTO_EXPLORE'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['wantmask'] & $this->wantmask['explore']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['explore']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['explore']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
		</tr>
		<tr class='<?php echo $this->pageclass_sfx; ?>'>
			<td><?php echo JText::_('JOPENSIM_WANTTO_MEET'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['wantmask'] & $this->wantmask['meet']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['meet']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['meet']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
			<td>&nbsp;</td>
			<td><?php echo JText::_('JOPENSIM_WANTTO_BEHIRED'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['wantmask'] & $this->wantmask['behired']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['behired']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['behired']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
		</tr>
		<tr class='<?php echo $this->pageclass_sfx; ?>'>
			<td><?php echo JText::_('JOPENSIM_WANTTO_GROUP'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['wantmask'] & $this->wantmask['group']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['group']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['group']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
			<td>&nbsp;</td>
			<td><?php echo JText::_('JOPENSIM_WANTTO_BUY'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['wantmask'] & $this->wantmask['buy']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['buy']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['buy']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
		</tr>
		<tr class='<?php echo $this->pageclass_sfx; ?>'>
			<td><?php echo JText::_('JOPENSIM_WANTTO_SELL'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['wantmask'] & $this->wantmask['sell']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['sell']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['sell']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
			<td>&nbsp;</td>
			<td><?php echo JText::_('JOPENSIM_WANTTO_HIRE'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['wantmask'] & $this->wantmask['hire']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['hire']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['wantmask'] & $this->wantmask['hire']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
		</tr>
		<tr>
			<td colspan='5'><?php echo $this->profiledata['wanttext']; ?></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td><?php echo JText::_('JOPENSIM_PROFILE_SKILLS'); ?>:</td>
		<td>
		
		<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
		<tr>
			<td><?php echo JText::_('JOPENSIM_SKILLS_TEXTURES'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['textures']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['textures']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['textures']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
			<td>&nbsp;</td>
			<td><?php echo JText::_('JOPENSIM_SKILLS_ARCHITECTURE'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['architecture']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['architecture']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['architecture']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
		</tr>
		<tr>
			<td><?php echo JText::_('JOPENSIM_SKILLS_MODELING'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['modeling']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['modeling']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['modeling']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
			<td>&nbsp;</td>
			<td><?php echo JText::_('JOPENSIM_SKILLS_EVENTPLANNING'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['eventplanning']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['eventplanning']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['eventplanning']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
		</tr>
		<tr>
			<td><?php echo JText::_('JOPENSIM_SKILLS_SCRIPTING'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['scripting']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['scripting']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['scripting']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
			<td>&nbsp;</td>
			<td><?php echo JText::_('JOPENSIM_SKILLS_CUSTOMCHARACTERS'); ?></td>
			<td><span class='jopensim_profile_state <?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['customcharacters']) ? "jopensim_profile_yes":"jopensim_profile_no"; ?>'><img src='<?php echo JURI::root(); ?>components/com_opensim/assets/images/null.gif' width='16' height='16' alt='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['customcharacters']) ? JText::_('JYES'):JText::_('JNO'); ?>' title='<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['customcharacters']) ? JText::_('JYES'):JText::_('JNO'); ?>' /></span></td>
		</tr>
		<tr>
			<td colspan='5'><?php echo $this->profiledata['skillstext']; ?></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td><?php echo JText::_('JOPENSIM_PROFILE_LANGUAGES'); ?>:</td>
		<td><?php echo $this->profiledata['languages']; ?></td>
	</tr>
	<?php if($this->image1st && $this->profiledata['maturePublish'] == 1): ?>
	<tr>
		<td><?php echo JText::_('JOPENSIM_PROFILE_REALLIFE'); ?>:</td>
		<td><?php echo $this->image1st; ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><?php echo nl2br($this->profiledata['firstLifeText']); ?></td>
	</tr>
	<?php else: ?>
	<tr>
		<td><?php echo JText::_('JOPENSIM_PROFILE_REALLIFE'); ?>:</td>
		<td><?php echo nl2br($this->profiledata['firstLifeText']); ?></td>
	</tr>
	<?php endif; ?>
	</table>
	</td>
</tr>
</table>

<?php endif; ?>
