<?php
/*
 * @component jOpenSim
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

define( '_JEXEC', 1 );
define('JPATH_BASE', dirname("../../../../index.php") );
define( 'DS', DIRECTORY_SEPARATOR );

// For JED
defined('_JEXEC') or die('Restricted Access');

require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'configuration.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'defines.php' );
require_once ( JPATH_BASE .DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'framework.php' );
$jversion	= new JVersion();
$version	= $jversion->getShortVersion();
//error_log("joomla version: ".$version);
//$test = var_export(version_compare($version,"4.0.0", '>='),TRUE);
//error_log("versioncompare1: ".$test);
/* To use Joomla's Database Class */
if(version_compare($version,"3.99.99", '>')) {
	require_once ( JPATH_ROOT .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'DatabaseFactory.php' );
} elseif(version_compare($version,"3.7.5", '>=')) {
	require_once ( JPATH_ROOT .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'factory.php' );
} else {
	require_once ( JPATH_ROOT .DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'joomla'.DIRECTORY_SEPARATOR.'factory.php' );
}
/* Create the Application */
$mainframe	= JFactory::getApplication('site');
$lang		= JFactory::getLanguage();
$extension	= 'com_opensim';
$base_dir	= JPATH_SITE;
// $language_tag = 'en-GB';
// $lang->load($extension, $base_dir, $language_tag, true);
$lang->load($extension, $base_dir, null, true);

if (!is_dir(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim")) {
	$iconfile		= "./jopensim_quickicon_notfound.png";
} else {
	require_once(JPATH_BASE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_opensim'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'opensim.class.php');

	$cparams	= JComponentHelper::getParams('com_opensim');
	$os_host	= $cparams->get('opensimgrid_dbhost', $cparams->get('opensim_dbhost', null));
	$os_db		= $cparams->get('opensimgrid_dbname', $cparams->get('opensim_dbname', null));
	$os_user	= $cparams->get('opensimgrid_dbuser', $cparams->get('opensim_dbuser', null));
	$os_pwd		= $cparams->get('opensimgrid_dbpasswd', $cparams->get('opensim_dbpasswd', null));
	$os_port	= $cparams->get('opensimgrid_dbport', $cparams->get('opensim_dbport', null));
	$opensim	= new opensim($os_host,$os_user,$os_pwd,$os_db,$os_port);

	$regions	= $opensim->countRegions();
	$users		= $opensim->countPresence();

	if($regions == 0) {
		$iconfile		= "./jopensim_quickicon_offline.png";
	} elseif($users == 0) {
		$iconfile		= "./jopensim_quickicon_nouser.png";
	} else {
		$iconfile		= "./jopensim_quickicon.png";
	}
}

$img = imagecreatefrompng($iconfile);

$white = imagecolorallocate($img, 255, 255, 255);

if($regions > 0) {
	$length = strlen($regions);
	if($length > 2) $font = 3;
	else $font = 5;
	$x = 10 - (4 * $length);
	imagestring($img, $font, $x, 3, $regions, $white);
}

if($users > 0) {
	$length = strlen($users);
	if($length > 2) $font = 3;
	else $font = 5;
	$x = 37 - (4 * $length);
	imagestring($img, $font, $x, 31, $users, $white);
}

header("Content-Type: image/png");
imagepng($img);
imagedestroy($img);
?>