<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="jopensim-adminpanel">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<p class="text-info"><?php echo JText::_('JOPENSIM_ADDONS_MAIN_DESC'); ?></p>

<div class='jopensim_addon_table'>
<div class='jopensim_addon_tr'>
<div class='jopensim_addon_td1'>
<ul class="btn-group-vertical">
    <li>
	    <span class="icon-info" title='Robust.ini' alt='Robust.ini'></span>
	    <a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=robustini' title='Robust.ini' alt='Robust.ini'>Robust.ini</a>
	</li>
	<li>
	    <span class="icon-info" title='OpenSim.ini' alt='OpenSim.ini'></span>
		<a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=opensimini' title='OpenSim.ini' alt='OpenSim.ini'>OpenSim.ini</a>
	</li>
	<li>
	    <span class="icon-info" title='getTexture' alt='getTexture'></span>
	    <a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=gettexture' title='getTexture' alt='getTexture'>getTexture</a>
	</li>
	<li>
	    <span class="icon-info"></span>
		<a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=ominfo' title='<?php echo JText::_('JOPENSIM_ADDONS_MESSAGES_INFO'); ?>' alt='<?php echo JText::_('JOPENSIM_ADDONS_MESSAGES_INFO'); ?>'><?php echo JText::_('JOPENSIM_ADDONS_MESSAGES'); ?></a>
	</li>
	<li>
	    <span class="icon-info"></span>
		<a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=pinfo' title='<?php echo JText::_('JOPENSIM_ADDONS_PROFILE_INFO'); ?>' alt='<?php echo JText::_('JOPENSIM_ADDONS_PROFILE_INFO'); ?>'><?php echo JText::_('JOPENSIM_ADDONS_PROFILE'); ?></a>
	</li>
	<li>
	    <span class="icon-info"></span>
		<a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=ginfo' title='<?php echo JText::_('JOPENSIM_ADDONS_GROUPS_INFO'); ?>' alt='<?php echo JText::_('JOPENSIM_ADDONS_GROUPS_INFO'); ?>'><?php echo JText::_('JOPENSIM_ADDONS_GROUPS'); ?></a>
	</li>
	<li>
	    <span class="icon-info"></span>
		<a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=sinfo' title='<?php echo JText::_('JOPENSIM_ADDONS_SEARCH_INFO'); ?>' alt='<?php echo JText::_('JOPENSIM_ADDONS_SEARCH_INFO'); ?>'><?php echo JText::_('JOPENSIM_ADDONS_SEARCH'); ?></a>
	</li>
	<li>
	    <span class="icon-info"></span>
		<a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=dinfo' title='<?php echo JText::_('JOPENSIM_ADDONS_GUIDE_INFO'); ?>' alt='<?php echo JText::_('JOPENSIM_ADDONS_SEARCH_INFO'); ?>'><?php echo JText::_('JOPENSIM_ADDONS_GUIDE'); ?></a>
	</li>
	<li>
	    <span class="icon-info"></span>
		<a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=mapinfo' title='<?php echo JText::_('JOPENSIM_ADDONS_MAP_INFO'); ?>' alt='<?php echo JText::_('JOPENSIM_ADDONS_MAP_INFO'); ?>'><?php echo JText::_('JOPENSIM_ADDONS_MAP'); ?></a>
	</li>
	<li>
	    <span class="icon-info"></span>
		<a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=ainfo' title='<?php echo JText::_('JOPENSIM_ADDONS_AUTHORIZE_INFO'); ?>' alt='<?php echo JText::_('JOPENSIM_ADDONS_AUTHORIZE_INFO'); ?>'><?php echo JText::_('JOPENSIM_ADDONS_AUTHORIZE'); ?></a>
	</li>
	<li>
	    <span class="icon-info"></span>
		<a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=minfo' title='<?php echo JText::_('JOPENSIM_ADDONS_MONEY_INFO'); ?>' alt='<?php echo JText::_('JOPENSIM_ADDONS_MONEY_INFO'); ?>'><?php echo JText::_('JOPENSIM_MONEY'); ?></a>
	</li>
	<li>
	    <span class="icon-info"></span>
		<a class="hasTooltip" href='index.php?option=com_opensim&view=addons&task=rainfo' title='<?php echo JText::_('JOPENSIM_ADDONS_REMOTE_ADMIN_INFO'); ?>' alt='<?php echo JText::_('JOPENSIM_ADDONS_REMOTE_ADMIN_INFO'); ?>'><?php echo JText::_('JOPENSIM_REMOTE_ADMIN'); ?></a>
	</li>
</ul>
</div>

<pre class='alert alert-info'><?php echo $this->infotext; ?></pre>

<form action="index.php" method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="option" value="com_opensim" />
    <input type="hidden" name="view" value="addons" />
    <input type="hidden" name="task" value="" />
</form>

</div>
</div>
</div>
</div>
