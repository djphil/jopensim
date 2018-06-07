<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

require_once ('css.php');

?>
<style type="text/css">
.loginscreenCustomPosition {
	display:inline-block;
	background-color:<?php echo $this->settingsdata['loginscreen_msgbox_color']; ?>;
	color:<?php echo $this->settingsdata['loginscreen_text_color']; ?>;
	padding:<?php echo $this->settingsdata['loginscreen_box_padding']; ?>px;
	border-radius:<?php echo $this->settingsdata['loginscreen_box_radius']; ?>px;
	<?php if($this->settingsdata['loginscreen_boxborder_inline']): ?>
	border: <?php echo $this->settingsdata['loginscreen_boxborder_inline']; ?>;
	<?php endif; ?>
}
.loginscreenCustomPosition h1, .loginscreenCustomPosition h2, .loginscreenCustomPosition h3, .loginscreenCustomPosition h4, .loginscreenCustomPosition h5, .loginscreenCustomPosition h6 {
    margin: 0px 0px 5px 0px;
    background-color:<?php echo $this->settingsdata['loginscreen_msgbox_title_background']; ?>;
	color:<?php echo $this->settingsdata['loginscreen_msgbox_title_text']; ?>;
	padding:<?php echo $this->settingsdata['loginscreen_title_padding_vertical']; ?>px <?php echo $this->settingsdata['loginscreen_title_padding_horizontal']; ?>px;
	border-radius:<?php echo $this->settingsdata['loginscreen_title_radius']; ?>px;
	<?php if($this->settingsdata['jopensim_loginscreen_boxborder_title']): ?>
	border: <?php echo $this->settingsdata['jopensim_loginscreen_boxborder_title']; ?>;
	<?php endif; ?>
}


<?php if ($this->settingsdata['jopensim_loginscreen_table_optimize']): ?>
.loginscreenCustomPosition th {
    font-weight: bold;
    text-align: left;
}

.table {border: 0px !important;}
.table td {
    border-top: 1px rgba(255, 255, 255, 0.15) solid;
    border-bottom: 1px rgba(255, 255, 255, 0.15) solid;
}

.table tbody tr:hover td, 
.table tbody tr:hover th {
    background-color: rgba(255, 255, 255, 0.15);
}

.table-striped > tbody > tr:nth-child(odd) > td, 
.table-striped > tbody > tr:nth-child(odd) > th {
    background-color: rgba(255, 255, 255, 0.1);
}
<?php endif; ?>

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

<?php if ($this->settingsdata['jopensim_loginscreen_matrix_fx']): ?>
    <div id="matrix"></div>
<?php endif; ?>

<div class='jopensim_loginscreen'>
    <?php if (is_array($this->loginscreenpositions) && count($this->loginscreenpositions) > 0): ?>
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
