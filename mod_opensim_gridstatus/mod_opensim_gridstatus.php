<?php
/*
 * @module OpenSim Gridstatus
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('DS')) define("DS",DIRECTORY_SEPARATOR);

// include the opensim class if present
if (!defined('_OPENSIMCLASS_GS')) define("_OPENSIMCLASS_GS",JPATH_BASE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."opensim.class.php");

$connect	= array();
$mystatus	= "";

// the opensim component is installed, get all values from there
if (is_file(_OPENSIMCLASS_GS)) {
	$mystatus = "default";
	include_once(_OPENSIMCLASS_GS);
	if (!defined('_OPENSIMCOMPONENT')) define("_OPENSIMCOMPONENT", TRUE);
	jimport('joomla.application.component.helper');
	$comp_params = JComponentHelper::getParams('com_opensim');
	$component = array();

	$component['gridstatus']        = $comp_params->get('loginscreen_gridstatus');
	$component['showgridstatus']    = $comp_params->get('loginscreen_show_status');
	$component['showregions']       = $comp_params->get('loginscreen_show_regions');
	$component['hiddenregions']		= $comp_params->get('loginscreen_show_counthidden');
    
	$component['showtodayvisitors'] = $comp_params->get('loginscreen_show_uniquevisitors');

    $component['showlastvisitors']  = $comp_params->get('loginscreen_show_uniquevisitors');
	$component['lastDays']          = $comp_params->get('loginscreen_show_uniquevisitors_days');
	$component['showtotalusers']    = $comp_params->get('loginscreen_show_totalusers');
	$component['showonline']        = $comp_params->get('loginscreen_show_onlinenow');
	$component['onlinecolor']       = $comp_params->get('jopensim_loginscreen_color_online');
	$component['offlinecolor']      = $comp_params->get('jopensim_loginscreen_color_offline');

	$connect['test']                = 1;
	$connect['osgriddbhost']        = $comp_params->get('opensimgrid_dbhost');  
	$connect['osgriddbuser']        = $comp_params->get('opensimgrid_dbuser');  
	$connect['osgriddbpasswd']      = $comp_params->get('opensimgrid_dbpasswd');
	$connect['osgriddbname']        = $comp_params->get('opensimgrid_dbname');  
	$connect['osgriddbport']        = $comp_params->get('opensimgrid_dbport');  

	// get the rest parameters from the module's configuration but default to components loginscreen settings if not set yet
	$parameter['lastDays']          = $params->get('lastDays',$component['lastDays']);
	$parameter['offlinecolor']      = $params->get('offlinecolor',$component['offlinecolor']);
	$parameter['onlinecolor']       = $params->get('onlinecolor',$component['onlinecolor']);
	$parameter['gridstatus']        = $params->get('gridstatus',$component['gridstatus']);
	$parameter['showgridstatus']    = $params->get('showgridstatus',$component['showgridstatus']);
	$parameter['showregions']       = $params->get('showregions',$component['showregions']);
	
    $parameter['showtodayvisitors'] = $params->get('showtodayvisitors',$component['showtodayvisitors']);
    
    $parameter['showlastvisitors']  = $params->get('showlastvisitors',$component['showlastvisitors']);
	$parameter['showtotalusers']    = $params->get('showtotalusers',$component['showtotalusers']);
	$parameter['showonline']        = $params->get('showonline',$component['showonline']);
	$showonlinehg = (array_key_exists("showonlinehg",$component)) ? $component['showonlinehg']:0;
    $parameter['showonlinehg']      = $params->get('showonlinehg', $showonlinehg);
	$parameter['hiddenregions']		= $params->get('hiddenregions',$component['hiddenregions']);
	$parameter['hgregions']			= $params->get('hgregions',0);
    $parameter['striped']     		= $params->get('striped',1);

	// $assetpath = JUri::base(true)."/components/com_opensim/assets/";
    // $doc = JFactory::getDocument();
	// $doc->addStyleSheet($assetpath.'opensim.css');
	// $doc->addStyleSheet($assetpath.'opensim.override.css');
    
    $doc = JFactory::getDocument();
    $assetpath = JUri::base(true)."/modules/mod_opensim_gridstatus/assets/";
    $doc->addStyleSheet($assetpath.'mod_opensim_gridstatus.css');

	$assetpath = JUri::base(true)."/components/com_opensim/assets/";
	$doc->addStyleSheet($assetpath.'opensim.css');
	$doc->addStyleSheet($assetpath.'opensim.override.css');
} else {
// opensim component is not installed, get the values from the module parameters
	$mystatus = "standalone";
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'opensim.minclass.php');
	define("_OPENSIMCOMPONENT",FALSE);

	$connect['test'] = 2;
	$connect['osgriddbhost']        = $params->get('osgriddbhost');
	$connect['osgriddbuser']        = $params->get('osgriddbuser');
	$connect['osgriddbpasswd']      = $params->get('osgriddbpasswd');
	$connect['osgriddbname']        = $params->get('osgriddbname');
	$connect['osgriddbport']        = $params->get('osgriddbport');
	
	// get the rest parameters from the module's configuration
	$parameter['lastDays']          = $params->get('lastDays');
	$parameter['offlinecolor']      = $params->get('offlinecolor');
	$parameter['onlinecolor']       = $params->get('onlinecolor');
	$parameter['gridstatus']        = $params->get('gridstatus');
	$parameter['showgridstatus']    = $params->get('showgridstatus');
	$parameter['showregions']       = $params->get('showregions');
	$parameter['showlastvisitors']  = $params->get('showlastvisitors');
	$parameter['showtotalusers']    = $params->get('showtotalusers');
	$parameter['showonline']        = $params->get('showonline');
    $parameter['showonlinehg']      = $params->get('showonlinehg');
    $parameter['striped']     		= $params->get('striped',1);

	// $parameter['hiddenregions']  = $params->get('hiddenregions');
	$parameter['hiddenregions']     = 1; // in standalone, the database table most probably will not exist
}

// include the helper file
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');
 
$gridstatus = new ModOpenSimHelper($connect,$parameter);

$pageclass = $params->get('moduleclass_sfx');

// get the values to display from the helper
$values = $gridstatus->getStatus();

$regions = $gridstatus->regions;
// Module display parameters override component
if($parameter['showgridstatus'] == "0") {
	$test['showgridstatus'] = 0;
	if(($values['gridboxlines'] & 1) == 1) $values['gridboxlines'] -= 1;
} else {
	$test['showgridstatus'] = 1;
	$values['gridboxlines'] |= 1;
}
if($parameter['showregions'] == "0") {
	$test['showregions'] = 0;
	if(($values['gridboxlines'] & 2) == 2) $values['gridboxlines'] -= 2;
} else {
	$test['showregions'] = 1;
	$values['gridboxlines'] |= 2;
}

// Visitors today
if($parameter['showtodayvisitors'] == "0") {
	$test['showtodayvisitors'] = 0;
	if(($values['gridboxlines'] & 64) == 64) $values['gridboxlines'] -= 64;
} else {
	$test['showtodayvisitors'] = 1;
	$values['gridboxlines'] |= 64;
}

// Visitors last x days
if($parameter['showlastvisitors'] == "0") {
	$test['showlastvisitors'] = 0;
	if(($values['gridboxlines'] & 4) == 4) $values['gridboxlines'] -= 4;
} else {
	$test['showlastvisitors'] = 1;
	$values['gridboxlines'] |= 4;
}

if($parameter['showtotalusers'] == "0") {
	$test['showtotalusers'] = 0;
	if(($values['gridboxlines'] & 16) == 16) $values['gridboxlines'] -= 16;
} else {
	$test['showtotalusers'] = 1;
	$values['gridboxlines'] |= 16;
}
if($parameter['showonline'] == "0") {
	$test['showonline'] = 0;
	if(($values['gridboxlines'] & 8) == 8) $values['gridboxlines'] -= 8;
} else {
	$test['showonline'] = 1;
	$values['gridboxlines'] |= 8;
}
if($parameter['showonlinehg'] == "0") {
	$test['showonlinehg'] = 0;
	if(($values['gridboxlines'] & 32) == 32) $values['gridboxlines'] -= 32;
} else {
	$test['showonlinehg'] = 1;
	$values['gridboxlines'] |= 32;
}
$values['test'] = $test;
$values['parameter'] = $parameter;

if(!isset($values['gridboxlines'])) $values['gridboxlines'] = 0;

// include the template for display
require(JModuleHelper::getLayoutPath('mod_opensim_gridstatus'));
?>
