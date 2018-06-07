<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<!-- LOGINSCREEN DYNAMIC CSS -->
<style type="text/css">

html, body {
	width: 100%;
	height: 100%;
	margin: 0px;
	padding: 0px;
	background-repeat: no-repeat;
	background-attachment: fixed;
	background-position:center center;
	background-size: cover;
}

html, body {
    <?php if (!$this->settingsdata['loginscreen_image']): ?>
        <?php if ($this->settingsdata['loginscreen_color']): ?>
            background-color:<?php echo $this->settingsdata['loginscreen_color']; ?>;
        <?php endif; ?>
    <?php else: ?>
    
    <?php if ($this->settingsdata['loginscreen_color']): ?>
    <?php else: ?>
    <?php endif; ?>

	background-image: url("<?php echo JUri::base(true)."/".$this->settingsdata['loginscreen_image']; ?>");

    <?php endif; ?>
}

.jopensim_loginscreen {
    <?php if(!$this->settingsdata['loginscreen_image']): ?>
        <?php if ($this->settingsdata['loginscreen_color']): ?>
            background-color: <?php echo $this->settingsdata['loginscreen_color']; ?>;
        <?php endif; ?>
    <?php else: ?>

    <?php if ($this->settingsdata['loginscreen_color']): ?>
    <?php else: ?>
    <?php endif; ?>

    background-image: url("<?php echo JUri::base(true)."/".$this->settingsdata['loginscreen_image']; ?>");
    <?php endif; ?>
}

.jopensim_loginscreen a {
    color: <?php echo $this->settingsdata['jopensim_loginscreen_color_links']; ?>;
}

.jopensim_loginscreen table td {
    <!--TODO-->
}

.welcomebox_gridstatus,
.welcomebox_messages, 
.welcomebox_regions {
    background: <?php echo $this->settingsdata['loginscreen_msgbox_color']; ?>;
    color: <?php echo $this->settingsdata['loginscreen_text_color']; ?>;
    <?php echo $this->settingsdata['loginscreen_boxborder_inline']; ?>
}

.welcomebox_gridstatus_title, 
.welcomebox_messages_title, 
.welcomebox_regions_title {
    background: <?php echo $this->settingsdata['loginscreen_msgbox_title_background']; ?>;
    color: <?php echo $this->settingsdata['loginscreen_msgbox_title_text']; ?>;
    <?php echo $this->settingsdata['jopensim_loginscreen_boxborder_title']; ?>
}

<?php if ($this->settingsdata['jopensim_loginscreen_matrix_fx']): ?>
#matrix {
    background: url("<?php echo JUri::base(true); ?>/components/com_opensim/assets/images/matrix.png");
    height: 100%;
    width: 100%;
    position: fixed;
    top: 0px;
    z-index: 21;
}
<?php endif; ?>
</style>
