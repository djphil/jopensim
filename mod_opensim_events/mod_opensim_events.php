<?php
/**
 * @module OpenSim Events
 * @copyright Copyright (C) 2018 FoTo50 https://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

if(!defined('DS')) define("DS",DIRECTORY_SEPARATOR);

// Require the opensim class

// Include the helper file
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');

// Get a parameter from the module's configuration
$moduleTag          = $params->get('module_tag', 'div');
$bootstrapSize      = (int) $params->get('bootstrap_size', 0);
$moduleClass        = $bootstrapSize != 0 ? ' span' . $bootstrapSize : '';
$headerTag          = htmlspecialchars($params->get('header_tag', 'h3'));
$headerClass        = htmlspecialchars($params->get('header_class', 'page-header'));
$moduleclass_sfx    = htmlspecialchars($params->get('moduleclass_sfx'));
$layout				= $params->get('layout', 'default');

$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::base( true ).'/modules/mod_opensim_events/assets/mod_opensim_events.css');
$doc->addStyleSheet(JURI::base( true ).'/media/jui/css/icomoon.css');

$assetpath = JUri::base(true)."/components/com_opensim/assets/";
$doc->addStyleSheet($assetpath.'opensim.css');
$doc->addStyleSheet($assetpath.'opensim.override.css');

$user			= JFactory::getUser();
$userid			= $user->id;
$guesthint		= $params->get('showguesthint', 1);
$hidewhenempty	= $params->get('hidewhenempty', 1);


$os_events		= new ModOpenSimEventsHelper($params);
$usersettings	= $os_events->getUserTime($userid);
$events			= $os_events->getEventList();

if (count($events) > 0) {
	$durations	= $os_events->getDurations();
	$icons		= $os_events->getEventIcons();

	// Get robust host and port for TP buttons
    $robustHost = $os_events->getComponentParameter('opensim_host');
    $robustPort = $os_events->getComponentParameter('robust_port');

	if ($userid || $guesthint == 0) {
		$timezonetext = JText::sprintf('MOD_OPENSIM_EVENTS_DATEDESC', $os_events->usertimezone);
	} else {
		$timezonetext = JText::sprintf('MOD_OPENSIM_EVENTS_DATEDESC_GUESTS', $os_events->usertimezone);
	}

	// Include the template for display
	require JModuleHelper::getLayoutPath('mod_opensim_events', $layout);
} else {
	if($hidewhenempty == 1) return;
	else require JModuleHelper::getLayoutPath('mod_opensim_events', 'noevents');
}
?>