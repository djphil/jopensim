<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
?>
<div>
<?php if($this->getTextureEnabled == 1): ?>
	<?php if($this->textureID != $this->zeroUUID): ?>
		<?php if($this->fileinfo === FALSE): ?>
		<?php echo JText::_('JOPENSIM_GETTEXTURE_BROKENRESPONSE'); ?>
		<?php else: ?>
		<img src='<?php echo $this->opensimhost; ?>:<?php echo $this->robust_port; ?>/CAPS/GetTexture/?texture_id=<?php echo $this->textureID; ?>&format=<?php echo $this->textureFormat; ?>' />
		<?php endif; ?>
	<?php else: ?>
	<?php echo JText::_('JOPENSIM_GETTEXTURE_ZEROUUID'); ?>
	<?php endif; ?>
<?php else: ?>
<?php echo JText::_('JOPENSIM_GETTEXTURE_DISABLED'); ?>
<?php endif; ?>
</div>