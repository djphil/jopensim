<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<h2><?php echo JText::_('JOPENSIM_PROFILE_PARTNER_TITLE'); ?></h2>

<?php if($this->settings['addons_profile'] == 1): ?>
<table>
<tr>
	<td>
<?php if(is_array($this->friends) && count($this->friends) > 0): ?>
	<table class='table table-striped group <?php echo $this->pageclass_sfx; ?>'>
	<?php foreach($this->friends AS $friend): ?>
	<tr class='<?php echo $this->pageclass_sfx; ?>'>
		<td class='<?php echo $this->pageclass_sfx; ?>'><?php echo $friend['name']; ?></td>
		<td class='<?php echo $this->pageclass_sfx; ?>'>
		<?php if($friend['profile']['partner']): ?>
		<img src='<?php echo $this->assetpath; ?>images/partnering_no_small.png' width='20' height='20' align='absmiddle' alt='<?php echo JText::_('JOPENSIM_PROFILE_HASPARTNER'); ?>' border='0' title='<?php echo JText::_('JOPENSIM_PROFILE_HASPARTNER'); ?>' />
		<?php else: ?>
		<?php if($friend['ignore'] == 1): ?>
		<a href='index.php?option=com_opensim&view=inworld&task=cancelpartnerignore&frienduuid=<?php echo $friend['uuid']; ?>&Itemid=<?php echo $this->Itemid; ?>' target='_parent'><img src='<?php echo $this->assetpath; ?>images/partnering_ignore_small.png' width='20' height='20' align='absmiddle' alt='<?php echo JText::_('JOPENSIM_PROFILE_PARTNER_IGNORE'); ?>' border='0' title='<?php echo JText::_('JOPENSIM_PROFILE_PARTNER_IGNORE'); ?>' /></a>
		<?php elseif($friend['ignore'] == -1): ?>
		<img src='<?php echo $this->assetpath; ?>images/partnering_no_small.png' width='20' height='20' align='absmiddle' alt='<?php echo JText::_('JOPENSIM_PROFILE_PARTNER_IGNORED'); ?>' border='0' title='<?php echo JText::_('JOPENSIM_PROFILE_PARTNER_IGNORED'); ?>' />
		<?php else: ?>
		<a href='index.php?option=com_opensim&view=inworld&task=sendpartnerrequest&frienduuid=<?php echo $friend['uuid']; ?>&Itemid=<?php echo $this->Itemid; ?>' target='_parent' onClick='return confirm("<?php echo addslashes(JText::sprintf('JOPENSIM_PROFILE_PARTNER_SEND_REQUEST_SURE',$friend['name'])); ?>");'><img src='<?php echo $this->assetpath; ?>images/partnering_small.png' width='20' height='20' align='absmiddle' alt='<?php echo JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST'); ?>' border='0' title='<?php echo JText::_('JOPENSIM_PROFILE_PARTNER_REQUEST'); ?>' /></a>
		<?php endif; ?>
		<?php endif; ?>
		</td>
	</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
	</td>
</tr>
</table>
<?php endif; ?>