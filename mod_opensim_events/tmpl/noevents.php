<?php
/*
 * @module OpenSim Events
 * @copyright Copyright (C) 2018 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

echo "<".$moduleTag ." class='mod_opensim_events_table table-responsive '>\n";

if ($params->get('showextratitle') == 1) {
	echo "<div class='jmoddiv jmodinside ".$moduleclass_sfx."'>\n";
	echo "<".$params->get('header_tag').">".$module->title."</".$params->get('header_tag').">\n";
	echo "</div>";
}
echo '<div class="alert alert-info">';
echo JText::_('MOD_OPENSIM_EVENTS_NOUPCOMING');
echo "</div>\n";
echo "</".$moduleTag .">\n";
?>
