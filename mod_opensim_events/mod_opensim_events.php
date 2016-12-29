<?php
/**
 * @module OpenSim Events
 * @copyright Copyright (C) 2015 FoTo50 http://www.jopensim.com/
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

if(!defined('DS')) define("DS",DIRECTORY_SEPARATOR);

// require the opensim class

// include the helper file
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');

// get a parameter from the module's configuration
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

$user =& JFactory::getUser();
$userid = $user->id;

$os_events		= new ModOpenSimEventsHelper($params);
$usersettings	= $os_events->getUserTime($userid);
$events			= $os_events->getEventList();
$durations		= $os_events->getDurations();
$icons			= $os_events->getEventIcons();

// include the template for display
require JModuleHelper::getLayoutPath('mod_opensim_events', $layout);
?>
