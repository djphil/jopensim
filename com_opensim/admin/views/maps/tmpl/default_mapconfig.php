<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
// No direct access
 
defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');
?>
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<form action="index.php" method="post" id="adminForm" name="adminForm">
<input type='hidden' name='option' value='com_opensim' />
<input type='hidden' name='view' value='maps' />
<input type='hidden' name='task' value='' />
<table>
<tr>
	<td><label for='map_cache_age' class='tooltip'><?php echo JHTML::tooltip(JText::_('JOPENSIM_MAP_CACHE_TT'),JText::_('JOPENSIM_MAP_CACHE_TTT'),'',JText::_('JOPENSIM_MAP_CACHE')); ?>:</label></td>
	<td align='right'><input type='text' name='map_cache_age' id='map_cache_age' value='<?php echo $this->settingsdata['map_cache_age']; ?>' size='4' maxlength='5' class='number_field' /></td>
</tr>
<tr>
	<td><label for='mapcontainer_width' class='tooltip'><?php echo JHTML::tooltip(JText::_('MAPCONTAINER_WIDTH_TT'),JText::_('MAPCONTAINER_WIDTH_TTT'),'',JText::_('MAPCONTAINER_WIDTH')); ?>:</label></td>
	<td align='right'><input type='text' name='mapcontainer_width' id='mapcontainer_width' value='<?php echo $this->settingsdata['mapcontainer_width']; ?>' size='3' maxlength='4' class='number_field' /></td>
</tr>
<tr>
	<td><label for='mapcontainer_height' class='tooltip'><?php echo JHTML::tooltip(JText::_('MAPCONTAINER_HEIGHT_TT'),JText::_('MAPCONTAINER_HEIGHT_TTT'),'',JText::_('MAPCONTAINER_HEIGHT')); ?>:</label></td>
	<td align='right'><input type='text' name='mapcontainer_height' id='mapcontainer_height' value='<?php echo $this->settingsdata['mapcontainer_height']; ?>' size='3' maxlength='4' class='number_field' /></td>
</tr>
<tr>
	<td><label for='map_defaultsize' class='tooltip'><?php echo JHTML::tooltip(JText::_('MAP_DEFAULTSIZE_TT'),JText::_('MAP_DEFAULTSIZE_TTT'),'',JText::_('MAP_DEFAULTSIZE')); ?>:</label></td>
	<td align='right'><input type='text' name='map_defaultsize' id='map_defaultsize' value='<?php echo $this->settingsdata['map_defaultsize']; ?>' size='2' maxlength='3' class='number_field' /></td>
</tr>
<tr>
	<td><label for='map_minsize' class='tooltip'><?php echo JHTML::tooltip(JText::_('MAP_MINSIZE_TT'),JText::_('MAP_MINSIZE_TTT'),'',JText::_('MAP_MINSIZE')); ?>:</label></td>
	<td align='right'><input type='text' name='map_minsize' id='map_minsize' value='<?php echo $this->settingsdata['map_minsize']; ?>' size='2' maxlength='3' class='number_field' /></td>
</tr>
<tr>
	<td><label for='map_maxsize' class='tooltip'><?php echo JHTML::tooltip(JText::_('MAP_MAXSIZE_TT'),JText::_('MAP_MAXSIZE_TTT'),'',JText::_('MAP_MAXSIZE')); ?>:</label></td>
	<td align='right'><input type='text' name='map_maxsize' id='map_maxsize' value='<?php echo $this->settingsdata['map_maxsize']; ?>' size='2' maxlength='3' class='number_field' /></td>
</tr>
<tr>
	<td><label for='map_zoomstep' class='tooltip'><?php echo JHTML::tooltip(JText::_('MAP_ZOOMSTEP_TT'),JText::_('MAP_ZOOMSTEP_TTT'),'',JText::_('MAP_ZOOMSTEP')); ?>:</label></td>
	<td align='right'><input type='text' name='map_zoomstep' id='map_zoomstep' value='<?php echo $this->settingsdata['map_zoomstep']; ?>' size='1' maxlength='2' class='number_field' /></td>
</tr>
<tr>
	<td><label for='mapcenter_offsetX' class='tooltip'><?php echo JHTML::tooltip(JText::_('MAPCENTER_OFFSETX_TT'),JText::_('MAPCENTER_OFFSETX_TTT'),'',JText::_('MAPCENTER_OFFSETX')); ?>:</label></td>
	<td align='right'><input type='text' name='mapcenter_offsetX' id='mapcenter_offsetX' value='<?php echo $this->settingsdata['mapcenter_offsetX']; ?>' size='2' maxlength='7' class='number_field' /></td>
</tr>
<tr>
	<td><label for='mapcenter_offsetY' class='tooltip'><?php echo JHTML::tooltip(JText::_('MAPCENTER_OFFSETY_TT'),JText::_('MAPCENTER_OFFSETY_TTT'),'',JText::_('MAPCENTER_OFFSETY')); ?>:</label></td>
	<td align='right'><input type='text' name='mapcenter_offsetY' id='mapcenter_offsetY' value='<?php echo $this->settingsdata['mapcenter_offsetY']; ?>' size='2' maxlength='7' class='number_field' /></td>
</tr>
</table>
</form>
</div>