<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
 
defined('_JEXEC') or die('Restricted access');
?>
<style type="text/css">
<!--
body.contentpane {
	min-width:100%;
	max-width:100%;
	width:100%;
}
-->
</style>
<div>
<?php echo $this->divorcetext; ?>
</div>
<br />
<div class='jopensim_button'><a href='index.php?option=com_opensim&view=inworld&task=divorcesure&partneruuid=<?php echo $this->partneruuid; ?>&Itemid=<?php echo $this->Itemid; ?>' target='_parent'><?php echo JText::_('JOPENSIM_PROFILE_PARTNER_DIVORCE_YES'); ?></a></div>
<div class='jopensim_button'><a href='Javascript:window.parent.SqueezeBox.close();'><?php echo JText::_('JOPENSIM_PROFILE_PARTNER_DIVORCE_NO'); ?></a></div>
<!--
<br /><br />
<button type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JOPENSIM_PROFILE_PARTNER_DIVORCE_NO'); ?></button>
<br /><br />
<input type='submit' class='button art-button' value='<?php echo JText::_('JOPENSIM_PROFILE_PARTNER_DIVORCE_NO'); ?>' onClick='window.parent.SqueezeBox.close();' />
-->