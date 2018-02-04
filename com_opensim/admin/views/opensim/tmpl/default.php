<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
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
					<dt><?php echo JText::_('JVERSION');?>:</dt>
					<dd><?php echo $this->version ;?></dd>
					<hr class="hr-condensed">
					
					<dt><?php echo JText::_('JOPENSIM_OSINFO');?>:</dt>
					<dd>Running on <?php if(PHP_INT_SIZE == 4) echo "32Bit"; elseif(PHP_INT_SIZE == 8) echo "64Bit"; else echo "unknown (".PHP_INT_SIZE.")"; ?> System</dd>
					<hr class="hr-condensed">
					
					<dt><?php echo JText::_('JOPENSIM_UPDATE');?>:</dt>
					<dd><?php echo $this->recentversion; ?></dd>
					<hr class="hr-condensed">
					<dt><?php echo JText::_('Copyright');?>:</dt>
					<dd>
					    &copy; 2010 - <?php echo date("Y"); ?>
						<a href="http://www.jopensim.com" target="_blank">FoTo50</a>
					</dd>
					<hr class="hr-condensed">
					
					<dt><?php echo JText::_('JOPENSIM_LICENSE');?>:</dt>
					<dd>General Public License <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GPLv2</a></dd>
					<hr class="hr-condensed">
					
					<dt><?php echo JText::_('JOPENSIM_MOREINFO');?>:</dt>
					<dd><a href="https://www.jopensim.com" target="_blank">www.jopensim.com</a></dd>
					</dl>
			    </div>
			    <div class="well well-small">

					<dt><?php echo JText::_('JOPENSIM_CREDITS');?>:</dt>
					<hr class="hr-condensed">
					<dd><?php echo JText::_('JOPENSIM_CREDITS_DESC');?>:</dd>
					<hr class="hr-condensed">
					<dl>
					<dd>
					<p>
					<ul>
						<li><i class="icon-thumbs-up"></i><span class='jopensimcontributors'><a href="http://digigrids.free.fr/" target="_blank">dj phil</a></span>French Translation and design face lifting</li>
						<li><i class="icon-thumbs-up"></i><span class='jopensimcontributors'><a href="http://www.clonelife.eu" target="_blank">Druskus War</a></span>Italian Translation</li>
						<li><i class="icon-thumbs-up"></i><span class='jopensimcontributors'><a href="http://3dlifz.com/" target="_blank">Roaming Sim</a></span>Documentation / Wiki</li>
					</ul>
					</p>
					</dd>
					<hr class="hr-condensed">

					</dl>
				</div>
			</div>
			
			
		</div>
	</section>
	
	<?php if ($this->settings['jopensim_debug_settings'] == true): ?>
	<a class="btn btn-default btn-primary icon-download" style="width:auto;" href='index.php?option=com_opensim&view=opensim&task=exportsettings' title='<?php echo JText::_('JOPENSIM_EXPORT_DESC'); ?>'>&nbsp;<?php echo JText::_('JOPENSIM_EXPORT'); ?></a>
	<a class="btn btn-default btn-danger icon-upload" style="width:auto;" href='index.php?option=com_opensim&view=opensim&task=importsettings' title='<?php echo JText::_('JOPENSIM_IMPORT_DESC'); ?>'>&nbsp;<?php echo JText::_('JOPENSIM_IMPORT'); ?></a>
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
		<tfoot>
			<tr>
				<td colspan='2'>
				<a class="btn btn-default btn-primary icon-download" style="width:auto;" href='index.php?option=com_opensim&view=opensim&task=exportsettings' title='<?php echo JText::_('JOPENSIM_EXPORT_DESC'); ?>'>&nbsp;<?php echo JText::_('JOPENSIM_EXPORT'); ?></a>
				<a class="btn btn-default btn-danger icon-upload" style="width:auto;" href='index.php?option=com_opensim&view=opensim&task=importsettings' title='<?php echo JText::_('JOPENSIM_IMPORT_DESC'); ?>'>&nbsp;<?php echo JText::_('JOPENSIM_IMPORT'); ?></a>
				</td>
			</tr>
		</tfoot>
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