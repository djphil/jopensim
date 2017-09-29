<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.modal');
?>

<h1><?php echo JText::_('JOPENSIM_INWORLD_DETAILS'); ?></h1>

<?php if($this->settings['addons_profile'] == 1): ?>
<table>
<tr>
	<td>
		<?php echo $this->topbar; ?>
	</td>
</tr>
<tr>
	<td>
	<form class="form-group" role="form" name='opensimdetails' action='index.php' method='post'>
	<input type='hidden' name='option' value='com_opensim' />
	<input type='hidden' name='view' value='inworld' />
	<input type='hidden' name='task' value='updateprofile' />
	<input type='hidden' name='uuid' value='<?php echo $this->osdata['uuid']; ?>' />
	<input type='hidden' name='Itemid' value='<?php echo $this->Itemid; ?>' />
	<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td><label><?php echo JText::_('JOPENSIM_PROFILE_2NDLIFE'); ?></label></td>
		<td><?php echo $this->profiledata['image2nd']; ?></td>
	</tr>
	<tr>
		<td><label for='aboutText'><?php echo JText::_('JOPENSIM_PROFILE_ABOUTME'); ?>:</label></td>
		<td><textarea name='aboutText' id='aboutText' rows='5' cols='40'><?php echo $this->profiledata['aboutText']; ?></textarea></td>
	</tr>
	<tr>
		<td><label><?php echo JText::_('JOPENSIM_PROFILE_PARTNER'); ?>:</label></td>
		<td>
		    <?php echo $this->profiledata['partnername']; ?>
			<a class='btn btn-default btn-primary pull-right modal' id='partnerwindow' href='<?php echo JRoute::_("&option=com_opensim&view=inworld&task=".$this->newtask."&tmpl=component&Itemid=".$this->Itemid); ?>' rel="{handler: 'iframe', size: {x: <?php echo $this->modalwidth; ?>, y: <?php echo $this->modalheight; ?>}, overlayOpacity: 0.3}" style="position:relative;">
			    <img class="jopensim_partner" src='<?php echo $this->assetpath; ?>images/<?php echo $this->partnerimage; ?>' alt='<?php echo $this->partnerimgtitle; ?>' title='<?php echo $this->partnerimgtitle; ?>' />
			</a>
			
		</td>
	</tr>
	<tr>
		<td><label for='aboutText'><?php echo JText::_('JOPENSIM_PROFILE_URL'); ?>:</label></td>
		<td><input type='text' name='profile_url' id='profile_url' size='30' maxlength='255' value='<?php echo $this->profiledata['url']; ?>' /></td>
	</tr>
	<tr>
		<td colspan='2'><label><?php echo JText::_('JOPENSIM_PROFILE_INTERESTS'); ?>:</label></td>
	</tr>
	<tr>
		<td><label><?php echo JText::_('JOPENSIM_PROFILE_WANTTO'); ?>:</label></td>
		<td>
		
		<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
		<tr>
			<td><input type='checkbox' name='wantmask[]' id='build' value='<?php echo $this->wantmask['build']; ?>'<?php echo ($this->profiledata['wantmask'] & $this->wantmask['build']) ? " checked='checked'":""; ?> /></td>
			<td><label for='build'><?php echo JText::_('JOPENSIM_WANTTO_BUILD'); ?></label></td>
			<td><input type='checkbox' name='wantmask[]' id='explore' value='<?php echo $this->wantmask['explore']; ?>'<?php echo ($this->profiledata['wantmask'] & $this->wantmask['explore']) ? " checked='checked'":""; ?> /></td>
			<td><label for='explore'><?php echo JText::_('JOPENSIM_WANTTO_EXPLORE'); ?></label></td>
		</tr>
		<tr>
			<td><input type='checkbox' name='wantmask[]' id='meet' value='<?php echo $this->wantmask['meet']; ?>'<?php echo ($this->profiledata['wantmask'] & $this->wantmask['meet']) ? " checked='checked'":""; ?> /></td>
			<td><label for='meet'><?php echo JText::_('JOPENSIM_WANTTO_MEET'); ?></label></td>
			<td><input type='checkbox' name='wantmask[]' id='behired' value='<?php echo $this->wantmask['behired']; ?>'<?php echo ($this->profiledata['wantmask'] & $this->wantmask['behired']) ? " checked='checked'":""; ?> /></td>
			<td><label for='behired'><?php echo JText::_('JOPENSIM_WANTTO_BEHIRED'); ?></label></td>
		</tr>
		<tr>
			<td><input type='checkbox' name='wantmask[]' id='group' value='<?php echo $this->wantmask['group']; ?>'<?php echo ($this->profiledata['wantmask'] & $this->wantmask['group']) ? " checked='checked'":""; ?> /></td>
			<td><label for='group'><?php echo JText::_('JOPENSIM_WANTTO_GROUP'); ?></label></td>
			<td><input type='checkbox' name='wantmask[]' id='buy' value='<?php echo $this->wantmask['buy']; ?>'<?php echo ($this->profiledata['wantmask'] & $this->wantmask['buy']) ? " checked='checked'":""; ?> /></td>
			<td><label for='buy'><?php echo JText::_('JOPENSIM_WANTTO_BUY'); ?></label></td>
		</tr>
		<tr>
			<td><input type='checkbox' name='wantmask[]' id='sell' value='<?php echo $this->wantmask['sell']; ?>'<?php echo ($this->profiledata['wantmask'] & $this->wantmask['sell']) ? " checked='checked'":""; ?> /></td>
			<td><label for='sell'><?php echo JText::_('JOPENSIM_WANTTO_SELL'); ?></label></td>
			<td><input type='checkbox' name='wantmask[]' id='hire' value='<?php echo $this->wantmask['hire']; ?>'<?php echo ($this->profiledata['wantmask'] & $this->wantmask['hire']) ? " checked='checked'":""; ?> /></td>
			<td><label for='hire'><?php echo JText::_('JOPENSIM_WANTTO_HIRE'); ?></label></td>
		</tr>
		<tr>
			<td colspan='4'><input type='text' size='50' name='wanttext' id='wanttext' value='<?php echo $this->profiledata['wanttext']; ?>' /></td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td><label><?php echo JText::_('JOPENSIM_PROFILE_SKILLS'); ?>:</label></td>
		<td>
		
		<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
		<tr>
			<td><input type='checkbox' name='skillsmask[]' id='textures' value='<?php echo $this->skillsmask['textures']; ?>'<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['textures']) ? " checked='checked'":""; ?> /></td>
			<td><label for='textures'><?php echo JText::_('JOPENSIM_SKILLS_TEXTURES'); ?></label></td>
			<td><input type='checkbox' name='skillsmask[]' id='architecture' value='<?php echo $this->skillsmask['architecture']; ?>'<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['architecture']) ? " checked='checked'":""; ?> /></td>
			<td><label for='architecture'><?php echo JText::_('JOPENSIM_SKILLS_ARCHITECTURE'); ?></label></td>
		</tr>
		<tr>
			<td><input type='checkbox' name='skillsmask[]' id='modeling' value='<?php echo $this->skillsmask['modeling']; ?>'<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['modeling']) ? " checked='checked'":""; ?> /></td>
			<td><label for='modeling'><?php echo JText::_('JOPENSIM_SKILLS_MODELING'); ?></label></td>
			<td><input type='checkbox' name='skillsmask[]' id='eventplanning' value='<?php echo $this->skillsmask['eventplanning']; ?>'<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['eventplanning']) ? " checked='checked'":""; ?> /></td>
			<td><label for='eventplanning'><?php echo JText::_('JOPENSIM_SKILLS_EVENTPLANNING'); ?></label></td>
		</tr>
		<tr>
			<td><input type='checkbox' name='skillsmask[]' id='scripting' value='<?php echo $this->skillsmask['scripting']; ?>'<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['scripting']) ? " checked='checked'":""; ?> /></td>
			<td><label for='scripting'><?php echo JText::_('JOPENSIM_SKILLS_SCRIPTING'); ?></label></td>
			<td><input type='checkbox' name='skillsmask[]' id='customcharacters' value='<?php echo $this->skillsmask['customcharacters']; ?>'<?php echo ($this->profiledata['skillsmask'] & $this->skillsmask['customcharacters']) ? " checked='checked'":""; ?> /></td>
			<td><label for='customcharacters'><?php echo JText::_('JOPENSIM_SKILLS_CUSTOMCHARACTERS'); ?></label></td>
		</tr>
		<tr>
			<td colspan='4'>
			    <input type='text' size='50' name='skillstext' id='skillstext' value='<?php echo $this->profiledata['skillstext']; ?>' />
			</td>
		</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td><label for='languages'><?php echo JText::_('JOPENSIM_PROFILE_LANGUAGES'); ?>:</label></td>
		<td><input type='text' size='50' name='languages' id='languages' value='<?php echo $this->profiledata['languages']; ?>' /></td>
	</tr>
	<?php if($this->profiledata['image1st'] == "nbsp;"): ?>
	<tr>
		<td><label for='aboutText'><?php echo JText::_('JOPENSIM_PROFILE_REALLIFE'); ?>:</label></td>
		<td><textarea name='firstLifeText' id='firstLifeText' rows='5' cols='40'><?php echo $this->profiledata['firstLifeText']; ?></textarea></td>
	</tr>
	<?php else: ?>
	<tr>
		<td><label for='aboutText'><?php echo JText::_('JOPENSIM_PROFILE_REALLIFE'); ?>:</label></td>
		<td><?php echo $this->profiledata['image1st'] ?></td>
	</tr>
	<tr>
		<td><input type='checkbox' name='maturePublish' id='maturePublish' value='1'<?php echo ($this->profiledata['maturePublish'] == 1) ? " checked='checked'":""; ?> /></td>
		<td><label for='maturePublish'><?php echo JText::_('JOPENSIM_PROFILE_SHOW1STIMAGE'); ?>:</label></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><textarea name='firstLifeText' id='firstLifeText' rows='5' cols='40'><?php echo $this->profiledata['firstLifeText']; ?></textarea></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td colspan='2'>
		    <!-- <span class="icon-save"></span> -->
		    <button type='submit' class="btn btn-default btn-primary"/><?php echo JText::_('JOPENSIM_SAVECHANGES'); ?></button>
		</td>
	</tr>
	</table>
	</form>
	</td>
</tr>
</table>
<?php endif; ?>
