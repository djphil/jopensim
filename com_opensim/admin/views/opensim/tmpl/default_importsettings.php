<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<div id="jopensim" class="jopensim-cpanel">
    <section class="content-block" role="main">
        <div class="row-fluid">
            <div class="span7">
            <h1><?php echo $this->pagetitle; ?></h1>
            <div class="alert alert-warn">
            <a class="close" data-dismiss="alert" href="#">×</a>
            <?php echo $this->pagenote; ?>
            </div>
            <form name='importSettings' id='importSettings' method='post' enctype='multipart/form-data'>
            <input type='hidden' name='option' value='com_opensim' />
            <input type='hidden' name='view' value='opensim' />
            <input type='hidden' name='task' value='saveimport' />
            <input class="btn btn-default btn-primary" style="width:auto;" type='file' name='settingsimport' /><br /><br />
            <input class="btn btn-default btn-primary" style="width:auto;height:auto;" type='submit' value='<?php echo JText::_('JOPENSIM_IMPORTBUTTON'); ?>' onClick="return jOpenSimImport();" />
            <a class="btn btn-default btn-danger" style="width:auto;height:auto;" href="index.php?option=com_opensim"><?php echo JText::_('JCANCEL'); ?></a>
            </form>
            </div>
        </div>
    </section>
</div>
</div>
<script>
function jOpenSimImport() {
	var form = document.getElementById("importSettings");

	// do field validation 
	if (form.settingsimport.value == "") {
		alert("<?php echo JText::_('JOPENSIM_IMPORT_SELECTFILE'); ?>");
		return false;
	} else {
		return true;
	}
}
</script>