<?php
/*
 * @component jOpenSim (Communication Interface with the OpenSim Server)
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
/* Initialize Joomla framework */
define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(realpath("../../index.php")));
define('JPATH_COMPONENT_ADMINISTRATOR', dirname("../../index.php").DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim');
define( 'DS', DIRECTORY_SEPARATOR );

// For JED
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

/* Required Files */
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'configuration.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'defines.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'framework.php' );
/* To use Joomla's Database Class */
require_once ( JPATH_ROOT .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'factory.php' );
/* Create the Application */
$mainframe =& JFactory::getApplication('site');

/* Load the language file from the component opensim */

$lang =& JFactory::getLanguage();
$extension = 'com_opensim';
$base_dir = JPATH_SITE;
$lang->load($extension, $base_dir, null, true);

/**************************************************/
// My code starts here...
/**************************************************/
require_once('includes/opensim.class.php');
//require_once(JPATH_BASE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'opensim.php');
//echo JPATH_COMPONENT_ADMINISTRATOR;
require_once(JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'maps.php');
require_once(JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'regions.php');


$opensim_model	= new OpenSimModelOpenSim();
$maps_model		= new opensimModelMaps();
$regions_model	= new opensimModelRegions();

function simpledebugzeile($zeile) {
	$zeit = date("Y-m-d H:i:s");
	$logfile = "./interface.log";
	$handle = fopen($logfile,"a+");
	$logzeile = $zeit."\t".$zeile."\n";
	fputs($handle,$logzeile);
	fclose($handle);
}

function debugzeile($zeile,$function = "") {
	if(!$function) $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##########\n";
	else $zeit = "\n\n########## ".date("d.m.Y H:i:s")." ##### ".$function." ##########\n";
	$zeile = var_export($zeile,TRUE);
	$logfile = "./interface.log";
	$handle = fopen($logfile,"a+");
	$logzeile = $zeit.$zeile."\n\n";
	fputs($handle,$logzeile);
	fclose($handle);
}

function mysqlsafestring($string) {
	return mysql_real_escape_string(stripslashes($string));
}

function jOpenSimSettings() {
	global $opensim_model;
	$settings = $opensim_model->getSettingsData();
//	$db =& JFactory::getDBO();
//	$query = "SELECT * FROM #__opensim_settings";
//	$db->setQuery($query);
//	$settings = $db->loadAssoc();
	return $settings;
}

$params = &JComponentHelper::getParams('com_opensim');

$osdbhost		= $params->get('opensim_dbhost');
$grp_readkey	= $params->get('grp_readkey');
$grp_writekey	= $params->get('grp_writekey');

$regions = $regions_model->regions;

//Creates XML string and XML document using the DOM 
$dom = new DomDocument('1.0', "UTF-8"); 

$map = $dom->appendChild($dom->createElement('Map')); 

if(is_array($regions) && count($regions) > 0) {
	foreach($regions AS $key => $region) {
//		$maps_model->refreshMap($region['uuid']);
		$maps_model->mapCacheRefresh($region['uuid']);
		$regioninfo	= $maps_model->getMapInfo($region['uuid']);
		if($regioninfo['hidemap'] == 1) continue;
		$article	= ($regioninfo['articleId'] > 0) ? $regioninfo['articleId']:"0";
		$hidemap	= $regioninfo['hidemap'];

		$grid		= $map->appendChild($dom->createElement('Grid'));

		$uuid		= $grid->appendChild($dom->createElement('Uuid'));
		$uuid->appendChild($dom->createTextNode($region['uuid']));

		$regionName	= $grid->appendChild($dom->createElement('RegionName')); 
		$regionName->appendChild($dom->createTextNode($region['regionName'])); 

		$locationX	= $grid->appendChild($dom->createElement('LocX')); 
		$locationX->appendChild($dom->createTextNode($region['locX']/256)); 

		$locationY	= $grid->appendChild($dom->createElement('LocY')); 
		$locationY->appendChild($dom->createTextNode($region['locY']/256)); 

        $sizeX		= $grid->appendChild($dom->createElement('SizeX')); 
        $sizeX->appendChild($dom->createTextNode($region['sizeX'])); 

        $sizeY		= $grid->appendChild($dom->createElement('SizeY')); 
        $sizeY->appendChild($dom->createTextNode($region['sizeY'])); 

        $articleID	= $grid->appendChild($dom->createElement('articleID')); 
        $articleID->appendChild($dom->createTextNode($article)); 

        $dohidemap	= $grid->appendChild($dom->createElement('hidemap')); 
        $dohidemap->appendChild($dom->createTextNode($hidemap)); 
	}
}

$dom->formatOutput	= TRUE; // set the formatOutput attribute of 
                            // domDocument to true 
// save XML as string or file 
$output				= $dom->saveXML(); // put string in test1 

header("Content-type: text/xml");
echo $output;


// debugprint($regions,"regions");


?>