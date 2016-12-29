<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<p class="text-info">
	<?php echo JText::_('JOPENSIM_MISC_DESC'); ?>
	<a class="hasTooltip" href='http://wiki.jopensim.com/index.php/Com_opensim#Remote_Admin' title="More info ..." target='_blank'>More info ...</a>
</p>

<?php if(is_array($this->misclinks)): ?>
<div class='jopensim_misc_table'>
<ul class="btn-group-vertical">
    <?php foreach($this->misclinks AS $misclink): ?>
    <li class="hasTooltip"><span class="icon-plus"></span><?php echo $misclink; ?></li>
    <?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
</div>