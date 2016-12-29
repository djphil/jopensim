<?php
/**
 * @module      OpenSim Teleport (mod_opensim_teleport)
 * @copyright   Copyright (C) djphil 2016, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 * @creative    CC-BY-NC-SA 4.0
**/

// no direct access
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$doc = JFactory::getDocument();
$assetpath = JUri::base(true)."/modules/mod_opensim_teleport/assets/";
$doc->addStyleSheet($assetpath.'mod_opensim_teleport.css');
    
require_once __DIR__ . '/helper.php';
require_once __DIR__ . '/functions.php';

// get module params
$moduleclass_sfx = $params->get('moduleclass_sfx');
$layout = $params->get('layout', 'default');
require JModuleHelper::getLayoutPath('mod_opensim_teleport', $layout);
?>
