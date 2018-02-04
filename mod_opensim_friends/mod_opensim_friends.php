<?php
/*
 * @module OpenSim Friends
 * @copyright Copyright (C) 2017 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
//no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

if (!defined('DS')) define("DS", DIRECTORY_SEPARATOR);

// require the opensim class
define("_OPENSIMCLASS",JPATH_SITE.DS."components".DS."com_opensim".DS."includes".DS."opensim.class.php");
require_once(_OPENSIMCLASS);

// include the helper file
require_once(dirname(__FILE__).DS.'helper.php');

$doc = JFactory::getDocument();
$assetpath = JUri::base(true)."/modules/mod_opensim_friends/assets/";
$doc->addStyleSheet($assetpath.'mod_opensim_friends.css');

$assetpath = JUri::base(true)."/components/com_opensim/assets/";
$doc->addStyleSheet($assetpath.'opensim.css');
$doc->addStyleSheet($assetpath.'opensim.override.css');

// get a parameter from the module's configuration
$lastDays           = $params->get('lastDays');
$offlinecolor       = $params->get('offlineColor');
$onlinecolor        = $params->get('onlineColor');
$linkprofile        = $params->get('linkprofile');
$nofollow           = $params->get('nofollow',1);
$namelength         = $params->get('namelength');
$useaccordion       = $params->get('useaccordion');
$accordionstyle     = $params->get('accordionstyle',1);
$accordionblock     = $params->get('accordionblock');
$accordionicon      = $params->get('accordionicon');
$accordioniconup    = $params->get('accordioniconup');
$accordionicondown  = $params->get('accordionicondown');
$accordioninit      = $params->get('accordionstyle_initheight');
$accordiontime      = $params->get('accordionstyle_duration');
$layout				= $params->get('layout', 'default');

if ($useaccordion == 1) {JHtml::_('jquery.framework');}

$nofollowattr = ($nofollow == 1) ? " rel='nofollow'":"";
$stylebold = ($params->get('stylebold')) ? "text-bold":"";

$accordionclass = "";
if ($accordionstyle == 1) {$accordionclass .= " btn-primary";}
else if ($accordionstyle == 2) {$accordionclass .= " btn-secondary";}
else if ($accordionstyle == 3) {$accordionclass .= " btn-info";}
else if ($accordionstyle == 4) {$accordionclass .= " btn-success";}
else if ($accordionstyle == 5) {$accordionclass .= " btn-warning";}
else if ($accordionstyle == 6) {$accordionclass .= " btn-danger";}
else if ($accordionstyle == 7) {$accordionclass .= " btn-inverse";}
else $accordionclass .= " btn-default";

if ($accordionblock == 1) {$accordionclass .= " btn-block";}

if ($accordionicon == 1) {
	if ($accordioniconup != "") $accordionupclass = $accordioniconup;
	if ($accordionicondown != "") $accordiondownclass = $accordionicondown;
} else {
	$accordionupclass = "";
	$accordiondownclass = "";
}

$user		= JFactory::getUser();
if($user->guest) return; // Dont display for guests

$os_friends = new ModOpenSimFriendsHelper($namelength);

if(!$offlinecolor) $offlinecolor	= $os_friends->offlinecolor;
if(!$onlinecolor)  $onlinecolor		= $os_friends->onlinecolor;
$settings = $os_friends->os_settings;

$pageclass	= $params->get('moduleclass_sfx');
$itemid		= JFactory::getApplication()->input->get('Itemid');

$friendlist	= $os_friends->getFriendList();
if(count($friendlist[0]) == 0 && count($friendlist[1]) == 0) return; // Dont display empty lists

$onlineText		= "<font color='".$onlinecolor."'>".JText::_('MOD_OPENSIM_FRIENDS_ONLINE')."</font>";
$offlineText	= "<font color='".$offlinecolor."'>".JText::_('MOD_OPENSIM_FRIENDS_OFFLINE')."</font>";

// include the template for display
require JModuleHelper::getLayoutPath('mod_opensim_friends', $layout);
?>
