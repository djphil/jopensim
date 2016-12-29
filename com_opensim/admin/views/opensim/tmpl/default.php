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
                <div class="well well-small">
                    <div class="module-title nav-header">
                        <?php echo JText::_('JOPENSIM_WELCOME'); ?>
                    </div>

					<hr class="hr-condensed">

					<div id="cpanel">
		    			<div class="btn-inline" id="jOpenSimQuickIcons">
			    	    	<?php if (is_array($this->adminbuttons) && count($this->adminbuttons) > 0) {
			    	    	    foreach($this->adminbuttons AS $button)
			    	    	        echo "<div class='icon-wrapper '><div class='icon'>".$button."</div></div>\n";
							} ?>
						</div>
			    	</div>
					<div class="clearfix"></div>
				</div>

				<div class="well well-small">
				    <div class="module-title nav-header ">Getting Started</div>
					<hr class="hr-condensed">
					<ul class=" pull-left">
						<li><i class="icon icon-question"></i><a class="hasTooltip" href="index.php?option=com_opensim&view=addons" title="Addons" target="_parent"><?php echo JText::_('JOPENSIM_FAQ'); ?>: Addons</a></li>
						<li><i class="icon icon-question"></i><a class="hasTooltip" href="http://wiki.jopensim.com" title="Wiki" target="_blank"><?php echo JText::_('JOPENSIM_FAQ'); ?>: Wiki</a></li>
						<li><i class="icon icon-question"></i><a class="hasTooltip" href="http://www.jopensim.com/forum/index.html" title="Forum" target="_blank"><?php echo JText::_('JOPENSIM_FAQ'); ?>: Forum</a></li>
						<li><i class="icon icon-question"></i><a class="hasTooltip" href="http://mantis.jopensim.com" title="Mantis" target="_blank"><?php echo JText::_('JOPENSIM_FAQ'); ?>: Mantis</a></li>
					</ul>
					<div class="clearfix"></div>
				</div>
			</div>

			<div class="span5">
			    <div class="well well-small">
			        <div class="center"> 
					    <img src="components/com_opensim/assets/images/jOpenSim.png"/>
					</div>
					
					<dl class="dl-horizontal">
					<hr class="hr-condensed">
					<dt><?php echo JText::_('Version');?>:</dt>
					<dd><?php echo $this->version ;?></dd>
					<hr class="hr-condensed">
					
					<dt><?php echo JText::_('OS');?>:</dt>
					<dd>Running on <?php if(1 << 32 == 1) echo "32Bit"; elseif(1 << 64 == 1) echo "64Bit"; else echo "unknown"; ?> System</dd>
					<hr class="hr-condensed">
					
					<dt>Update:</dt>
					<dd><?php echo $this->recentversion; ?></dd>
					<hr class="hr-condensed">
					<dt><?php echo JText::_('Copyright');?>:</dt>
					<dd>
					    &copy; 2010 - <?php echo date("Y"); ?>
						<a href="http://www.jopensim.com" target="_blank">FoTo50</a>
					</dd>
					<hr class="hr-condensed">
					
					<dt><?php echo JText::_('License');?>:</dt>
					<dd>General Public License <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></dd>
					<hr class="hr-condensed">
					
					<dt>More Info:</dt>
					<dd><a href="http://www.jopensim.com" target="_blank">www.jopensim.com</a></dd>
					<hr class="hr-condensed">
					</dl>
				</div>
			</div>
			
			
		</div>
	</section>


	
	<?php if ($this->settings['jopensim_debug_settings'] == true): ?>
	<table class='table table-striped group<?php echo $this->pageclass_sfx; ?>'>
		<thead>
			<tr>
				<th>Variable:</th>
				<th>Value:</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->settings AS $key => $value): ?>
			<tr>
				<td class='jopensim_debugcol1'><?php echo $key; ?></th>
				<td class='jopensim_debugcol2'><?php echo ($key == "osdbpasswd" || $key == "remotepasswd") ? "**hidden**":var_export($value,TRUE); ?></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>
	<form class="form-group" action="index.php" method="post" id="adminForm" name="adminForm">
		<input type='hidden' name='option' value='com_opensim' />
		<input type='hidden' name='view' value='opensim' />
		<input type='hidden' name='boxchecked' id='boxchecked' value='1' />
		<input type='hidden' name='task' value='' />
	</form>
</div>
</div>