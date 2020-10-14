<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2020 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<script language="javascript" type="text/javascript">
function jOpenSimSelectArticle(buttonval,buttonText,unknownVal) {
    //	alert(buttonval);
    document.getElementById("regionArticle").value = buttonval;
    document.adminForm.submit();
}
</script>
<div class="jopensim-adminpanel">

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<form action="index.php" method="post" id="adminForm" name="adminForm">
	<fieldset>
		<legend>
		<?php echo $this->ueberschrift; ?>: 
		<?php echo JText::_('EDITREGIONSETTINGSFOR'); ?> <span class='text-info'><?php echo $this->mapdetails['regionName']; ?></span>
		</legend>
	</fieldset>

	<p><?php echo $this->mapdetails['image']; ?></p>
	<input type="hidden" name="option" value="com_opensim" />
	<input type="hidden" name="view" value="maps" />
	<input type="hidden" name="task" value="save_regionsettings" />
	<input type="hidden" name="regionUUID" value="<?php echo $this->mapinfo['regionUUID']; ?>" />
	<input type="hidden" name="regionArticle" id="regionArticle" value="<?php echo $this->mapinfo['articleId']; ?>" />

	<div>
		<label for='mappublic'>
		<input type='checkbox' name='mappublic' id='mappublic' value='1'<?php echo ($this->mapinfo['public'] == 1) ? " checked='checked'":""; ?> />
		<?php echo JText::_('JOPENSIM_PUBLICMAP'); ?>
		</label>
	</div>

	<div>
		<label for='mapguide'>
		<input type='checkbox' name='mapguide' id='mapguide' value='1'<?php echo ($this->mapinfo['guide'] == 1) ? " checked='checked'":""; ?> />
		<?php echo JText::_('JOPENSIM_REGION_DESTINATIONGUIDE'); ?>
		</label>
	</div>

	<div>
		<label for='mapinvisible'>
		<input type='checkbox' name='mapinvisible' id='mapinvisible' value='1'<?php echo ($this->mapinfo['hidemap'] == 1) ? " checked='checked'":""; ?> />
		<?php echo JText::_('JOPENSIM_HIDEMAP'); ?>
		</label>
	</div>

	<hr class="hr-condensed">

	<p><strong ><?php echo JText::_('CURRENT_ARTICLE')."</strong>: <span class='text-info'>".$this->contentTitle; ?></span></p>
	<div class="form-inline <?php echo $this->selectArticle->name; ?>">
		<a class="btn btn-default btn-success <?php echo $this->selectArticle->modalname; ?>" title="<?php echo $this->selectArticle->text; ?>" href="<?php echo $this->selectArticle->link; ?>" rel="<?php echo $this->selectArticle->options; ?>"  >
			<span class='icon-apply'></span><?php echo $this->selectArticle->text; ?>
		</a>
		<?php if($this->mapinfo['articleId']): ?>
		<a class="btn btn-default btn-danger" href='index.php?option=com_opensim&view=maps&task=removemaparticle&regionUUID=<?php echo $this->mapinfo['regionUUID']; ?>'>
			<span class='icon-remove'></span><?php echo JText::_('REMOVE_ARTICLE'); ?>
		</a>
		<?php endif; ?>
	</div>
</form>
</div>
</div>
