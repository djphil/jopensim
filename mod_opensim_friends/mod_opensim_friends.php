<?php
/*
 * @module OpenSim Friends
 * @copyright Copyright (C) 2016 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
//no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

if(!defined('DS')) define("DS",DIRECTORY_SEPARATOR);

// require the opensim class
define("_OPENSIMCLASS",JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_opensim".DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR."opensim.class.php");
require_once(_OPENSIMCLASS);

// include the helper file
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');

$doc = JFactory::getDocument();
$assetpath = JUri::base(true)."/modules/mod_opensim_friends/assets/";
$doc->addStyleSheet($assetpath.'mod_opensim_friends.css');

$assetpath = JUri::base(true)."/components/com_opensim/assets/";
$doc->addStyleSheet($assetpath.'opensim.css');
$doc->addStyleSheet($assetpath.'opensim.override.css');


// get a parameter from the module's configuration
$lastDays		= $params->get('lastDays');
$offlinecolor	= $params->get('offlineColor');
$onlinecolor	= $params->get('onlineColor');
$linkprofile	= $params->get('linkprofile');
$nofollow		= $params->get('nofollow',1);
$namelength		= $params->get('namelength');
// get the values to display from the helper
// $values = ModOpenSimFriendsHelper::getStatus($offlinecolor,$onlinecolor);
    
$os_friends = new ModOpenSimFriendsHelper($namelength);

if(!$offlinecolor) $offlinecolor	= $os_friends->offlinecolor;
if(!$onlinecolor)  $onlinecolor		= $os_friends->onlinecolor;
$settings = $os_friends->os_settings;

$pageclass = $params->get('moduleclass_sfx');

$friendlist = $os_friends->getFriendList();
$user =& JFactory::getUser();
$userid = $user->id;

$onlineText = "<font color='".$onlinecolor."'>".JText::_('MOD_OPENSIM_FRIENDS_ONLINE')."</font>";
$offlineText = "<font color='".$offlinecolor."'>".JText::_('MOD_OPENSIM_FRIENDS_OFFLINE')."</font>";

// include the template for display
require(JModuleHelper::getLayoutPath('mod_opensim_friends'));
?>
