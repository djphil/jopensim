<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once ('css.php');

// Added by djphil
if ($this->settingsdata['jopensim_loginscreen_stylebold']) $stylebold = "text-bold";
if ($this->settingsdata['jopensim_loginscreen_styleicon']) $styleicon = TRUE;
?>
<style type="text/css">
.loginscreenCustomPosition {
	display:inline-block;
	background-color:<?php echo $this->settingsdata['loginscreen_msgbox_color']; ?>;
	color:<?php echo $this->settingsdata['loginscreen_text_color']; ?>;
	padding:<?php echo $this->settingsdata['loginscreen_box_padding']; ?>px;
	border-radius:<?php echo $this->settingsdata['loginscreen_box_padding']; ?>px;
	border-radius:<?php echo $this->settingsdata['loginscreen_box_radius']; ?>px;
}
.loginscreenCustomPosition h1, .loginscreenCustomPosition h2, .loginscreenCustomPosition h3, .loginscreenCustomPosition h4, .loginscreenCustomPosition h5, .loginscreenCustomPosition h6 {
	background-color:<?php echo $this->settingsdata['loginscreen_msgbox_title_background']; ?>;
	color:<?php echo $this->settingsdata['loginscreen_msgbox_title_text']; ?>;
	padding:<?php echo $this->settingsdata['loginscreen_title_padding']; ?>px;
	border-radius:<?php echo $this->settingsdata['loginscreen_title_radius']; ?>px;
}
</style>
<div class='jopensim_loginscreen'>
<?php if(is_array($this->loginscreenpositions) && count($this->loginscreenpositions) > 0): ?>
<?php foreach($this->loginscreenpositions AS $loginscreenposition): ?>
<?php
if (count($loginscreenposition['modules'])) {
	$positioncontent = JHtml::_('content.prepare', '{loadposition '.$loginscreenposition['positionname'].'}');
	if($positioncontent) {
?>
<div id="<?php echo $loginscreenposition['positionname']; ?>" class="loginscreenCustomPosition" style="position:absolute;<?php echo $loginscreenposition['alignH'].':'.$loginscreenposition['posX']; ?>px;<?php echo $loginscreenposition['alignV'].':'.$loginscreenposition['posY']; ?>px;z-index:<?php echo $loginscreenposition['zindex']; ?>">
<?php echo $positioncontent; ?>
</div>
<?php
	}
}
?>
<?php endforeach; ?>
<?php endif; ?>

</div>
