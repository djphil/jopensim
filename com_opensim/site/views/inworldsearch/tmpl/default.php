<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access'); ?>

<h1><?php echo JText::_('JOPENSIM_INWORLDSEARCH'); ?></h1>

<?php if($this->searchform === TRUE): ?>
<div class="input-append">
<form class="form-search" role="search" name='inworldsearch' action='index.php' method='post'>
	<input type='hidden' name='option' value='com_opensim' />
    <?php if($this->tmpl == TRUE): ?>
    <input type='hidden' name='tmpl' value='component' />
    <?php endif; ?>
    <input type='hidden' name='view' value='inworldsearch' />
    <input type='hidden' name='Itemid' value='<?php echo $this->itemid; ?>' />
    <input class="span4 jopensim-searchfield" type='text' name='q' id='q' value='<?php echo $this->searchquery; ?>' />
	<button type='submit' name='submit' class='btn btn-default btn-primary btn-sm'>
	    <span class="icon-search" style="font-size:120%"></span>
	    <!-- <span class="icon-partnering"></span></span>
		<?php echo JText::_('JOPENSIM_SEARCH'); ?> -->
	</button>
</form>
</div>
<?php endif; ?>

<?php if($this->showcase === TRUE): ?>
<!-- <span class="icon-info"></span>
<p class="alert alert-info"><?php echo JText::_('JOPENSIM_SHOWCASE'); ?></p> -->
<?php elseif(is_array($this->result)): ?>
<h3><?php echo JText::_('JOPENSIM_SEARCHRESULTS'); ?>:</h3>

<?php if(count($this->results) > 0): ?>
<?php foreach($this->results AS $type => $lines): ?>
<h4><?php echo JText::_($type); ?>:</h4>
<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
    <?php foreach($lines AS $line): ?>
        <tr><?php echo $line; ?></tr>
    <?php endforeach; ?>
</table>
<?php endforeach; ?>
<?php endif; ?>
<?php else: ?>
<p class="alert"><?php echo JText::_('JOPENSIM_SEARCHRESULTS_ERROR'); ?></p>
<?php endif; ?>
<p class="text-center">jOpenSimSearch v<?php echo $this->jopensimversion; ?> powered by <a href='https://www.jopensim.com' target='_blank'>FoTo50</a></p>
